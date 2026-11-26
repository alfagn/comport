<?php

namespace App\Services;

use App\Ai\Agents\DashboardAssistantAgent;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Laravel\Ai\Approvals\Decision;
use Laravel\Ai\Approvals\Decisions;
use Laravel\Ai\Approvals\PendingApproval;
use Laravel\Ai\Contracts\ConversationStore;
use Laravel\Ai\Contracts\ResolvesPendingApprovals;
use Laravel\Ai\Contracts\VerifiesConversationOwnership;
use Laravel\Ai\Enums\MessageStatus;
use Laravel\Ai\Files\Document;
use Laravel\Ai\Files\Image;
use Laravel\Ai\Models\Conversation;
use Laravel\Ai\Models\ConversationMessage;
use RuntimeException;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

/**
 * Orchestrates the Dashboard AI Copilot: conversation history, ownership
 * checks, prompt execution, session-scoped file attachments (with automatic
 * disk cleanup on session delete), and human-in-the-loop tool approvals.
 */
class DashboardAssistantService
{
    public const ATTACHMENTS_DISK = 'local';

    public const ATTACHMENTS_DIR = 'copilot-attachments';

    /**
     * File extensions whose text content is inlined into the prompt so any AI
     * provider can inspect code, logs, configs, and data files reliably.
     *
     * @var list<string>
     */
    private const TEXT_EXTENSIONS = [
        'txt', 'md', 'log', 'json', 'csv', 'xml', 'sql', 'php', 'js', 'ts',
        'jsx', 'tsx', 'css', 'scss', 'html', 'htm', 'yml', 'yaml', 'ini',
        'conf', 'sh', 'bat', 'ps1', 'vue',
    ];

    public function __construct(private AiRuntimeGuard $guard) {}

    /**
     * Provider & tool readiness diagnostics for the admin UI header.
     *
     * @return array{provider: string, model: ?string, ready: bool, hint: ?string, tools: list<string>}
     */
    public function diagnostics(User $user): array
    {
        $agent = new DashboardAssistantAgent($user);
        $rawProvider = $agent->provider();
        $primaryProvider = is_array($rawProvider) ? (string) array_key_first($rawProvider) : $rawProvider;
        $provider = $this->guard->resolvedProvider($primaryProvider);
        $ready = $this->guard->isReady($provider, DashboardAssistantAgent::isFaked());

        $tools = array_values(array_map(
            fn (object $tool): string => class_basename($tool),
            [...$agent->tools()],
        ));

        return [
            'provider' => $provider,
            'model' => $agent->model(),
            'ready' => $ready,
            'hint' => $ready ? null : $this->guard->missingCredentialsHint($provider),
            'tools' => $tools,
        ];
    }

    /**
     * Recent conversations belonging to the signed-in admin.
     *
     * @return Collection<int, array{id: string, title: string, updated_human: string, updated_at: ?string}>
     */
    public function listConversations(User $user, int $limit = 25): Collection
    {
        return $user->conversations()
            ->whereHas('messages', fn ($query) => $query->where('agent', DashboardAssistantAgent::class))
            ->latest('updated_at')
            ->limit($limit)
            ->get()
            ->map(fn (Conversation $conversation): array => [
                'id' => $conversation->id,
                'title' => $conversation->title,
                'updated_human' => $conversation->updated_at?->diffForHumans() ?? 'just now',
                'updated_at' => $conversation->updated_at?->toIso8601String(),
            ]);
    }

    /**
     * Load a single owned conversation with formatted messages and pending approvals.
     *
     * @return array{id: string, title: string, messages: list<array<string, mixed>>, pending_approvals: list<array<string, mixed>>}
     */
    public function getConversation(User $user, string $conversationId): array
    {
        $this->assertOwnership($user, $conversationId);

        $conversation = Conversation::query()->findOrFail($conversationId);

        return $this->serializeConversation($conversation);
    }

    /**
     * Prompt the Dashboard Copilot (starting or continuing a conversation),
     * storing any uploaded attachments under the session's private directory.
     *
     * @param  array<int, UploadedFile>  $uploadedFiles
     * @return array{id: string, title: string, messages: list<array<string, mixed>>, pending_approvals: list<array<string, mixed>>}
     */
    public function sendMessage(
        User $user,
        string $prompt,
        ?string $conversationId = null,
        array $uploadedFiles = [],
    ): array {
        $agent = new DashboardAssistantAgent($user);
        $rawProvider = $agent->provider();
        $primaryProvider = is_array($rawProvider) ? (string) array_key_first($rawProvider) : $rawProvider;
        $provider = $this->guard->resolvedProvider($primaryProvider);

        if (! $this->guard->isReady($provider, DashboardAssistantAgent::isFaked())) {
            throw new RuntimeException($this->guard->missingCredentialsHint($provider));
        }

        $cleanPrompt = trim($prompt);
        if ($cleanPrompt === '' && $uploadedFiles !== []) {
            $cleanPrompt = 'Please analyze the attached file(s) and explain your technical findings.';
        }

        if ($conversationId !== null && $conversationId !== '') {
            $this->assertOwnership($user, $conversationId);
        } elseif ($uploadedFiles !== []) {
            $conversationId = resolve(ConversationStore::class)->storeConversation(
                Conversation::participantType($user),
                Conversation::participantKey($user),
                Str::limit($cleanPrompt, 50, preserveWords: true),
            );
        }

        if ($conversationId !== null && $conversationId !== '') {
            $agent->continue($conversationId, as: $user);
        } else {
            $agent->forUser($user);
        }

        [$sdkAttachments, $storedMeta, $inlineBlocks] = $this->storeTurnAttachments(
            $conversationId,
            $uploadedFiles,
        );

        $effectivePrompt = $cleanPrompt;
        if ($inlineBlocks !== []) {
            $effectivePrompt .= "\n\n".implode("\n\n", $inlineBlocks);
        }

        try {
            $response = $agent->prompt($effectivePrompt, attachments: $sdkAttachments);
        } catch (Throwable $exception) {
            throw new RuntimeException('AI request failed: '.$exception->getMessage(), previous: $exception);
        }

        $resolvedId = $response->conversationId ?? $conversationId;
        $conversation = Conversation::query()->findOrFail($resolvedId);

        if ($storedMeta !== []) {
            $this->recordAttachmentMetadataOnLatestUserMessage(
                $conversation->id,
                $cleanPrompt,
                $storedMeta,
            );
        }

        return $this->serializeConversation($conversation, $response->pendingApprovals);
    }

    /**
     * Resume a paused conversation with admin approval/rejection decisions.
     *
     * @param  array<string, bool>  $decisionsMap
     * @return array{id: string, title: string, messages: list<array<string, mixed>>, pending_approvals: list<array<string, mixed>>}
     */
    public function resolveApprovals(User $user, string $conversationId, array $decisionsMap): array
    {
        $this->assertOwnership($user, $conversationId);

        $agent = new DashboardAssistantAgent($user);
        $rawProvider = $agent->provider();
        $primaryProvider = is_array($rawProvider) ? (string) array_key_first($rawProvider) : $rawProvider;
        $provider = $this->guard->resolvedProvider($primaryProvider);

        if (! $this->guard->isReady($provider, DashboardAssistantAgent::isFaked())) {
            throw new RuntimeException($this->guard->missingCredentialsHint($provider));
        }

        $normalized = [];
        foreach ($decisionsMap as $callId => $approved) {
            $normalized[(string) $callId] = $approved
                ? Decision::approve()
                : Decision::reject('Action declined by the administrator.');
        }

        $decisions = Decisions::from($normalized)->rejectRemaining('Action declined by the administrator.');

        try {
            $response = $agent
                ->continue($conversationId, as: $user)
                ->prompt($decisions);
        } catch (Throwable $exception) {
            throw new RuntimeException('Could not resume approval turn: '.$exception->getMessage(), previous: $exception);
        }

        $conversation = Conversation::query()->findOrFail($conversationId);

        return $this->serializeConversation($conversation, $response->pendingApprovals);
    }

    /**
     * Delete an owned conversation, its stored messages, and all files uploaded in that session.
     */
    public function deleteConversation(User $user, string $conversationId): void
    {
        $this->assertOwnership($user, $conversationId);

        $this->purgeConversationAttachments($conversationId);

        ConversationMessage::query()->where('conversation_id', $conversationId)->delete();
        Conversation::query()->where('id', $conversationId)->delete();
    }

    /**
     * Stream or download an attachment belonging to an owned Copilot session.
     */
    public function streamAttachment(User $user, string $conversationId, string $filename): StreamedResponse
    {
        $this->assertOwnership($user, $conversationId);

        $safeFilename = basename($filename);
        if ($safeFilename === '' || $safeFilename !== $filename) {
            throw new NotFoundHttpException('Attachment not found.');
        }

        $relativePath = self::ATTACHMENTS_DIR.'/'.$conversationId.'/'.$safeFilename;
        $disk = Storage::disk(self::ATTACHMENTS_DISK);

        if (! $disk->exists($relativePath)) {
            throw new NotFoundHttpException('Attachment not found.');
        }

        return $disk->response($relativePath, $safeFilename, [
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }

    /**
     * Permanently delete every attachment uploaded during the given Copilot session.
     */
    protected function purgeConversationAttachments(string $conversationId): void
    {
        $messages = ConversationMessage::query()
            ->where('conversation_id', $conversationId)
            ->get(['attachments', 'meta']);

        foreach ($messages as $message) {
            $metaAttachments = is_array($message->meta) ? ($message->meta['copilot_attachments'] ?? []) : [];
            foreach ($metaAttachments as $att) {
                if (is_array($att) && ! empty($att['path'])) {
                    $diskName = (string) ($att['disk'] ?? self::ATTACHMENTS_DISK);
                    Storage::disk($diskName)->delete((string) $att['path']);
                }
            }

            $sdkAttachments = is_array($message->attachments) ? $message->attachments : [];
            foreach ($sdkAttachments as $att) {
                if (is_array($att) && ! empty($att['path'])) {
                    $diskName = (string) ($att['disk'] ?? self::ATTACHMENTS_DISK);
                    Storage::disk($diskName)->delete((string) $att['path']);
                }
            }
        }

        $safeId = basename($conversationId);
        if ($safeId !== '') {
            Storage::disk(self::ATTACHMENTS_DISK)->deleteDirectory(self::ATTACHMENTS_DIR.'/'.$safeId);
            Storage::disk('public')->deleteDirectory(self::ATTACHMENTS_DIR.'/'.$safeId);
        }
    }

    /**
     * Store uploaded files under the conversation's directory and prepare SDK / inline payloads.
     *
     * @param  array<int, UploadedFile>  $uploadedFiles
     * @return array{0: list<Image|Document>, 1: list<array<string, mixed>>, 2: list<string>}
     */
    protected function storeTurnAttachments(?string $conversationId, array $uploadedFiles): array
    {
        if ($uploadedFiles === [] || ! $conversationId) {
            return [[], [], []];
        }

        $sdkAttachments = [];
        $storedMeta = [];
        $inlineBlocks = [];

        $directory = self::ATTACHMENTS_DIR.'/'.basename($conversationId);

        foreach ($uploadedFiles as $file) {
            if (! $file instanceof UploadedFile || ! $file->isValid()) {
                continue;
            }

            $originalName = $file->getClientOriginalName() ?: 'attachment';
            $ext = strtolower($file->getClientOriginalExtension() ?: pathinfo($originalName, PATHINFO_EXTENSION));
            $mime = (string) ($file->getMimeType() ?: $file->getClientMimeType() ?: 'application/octet-stream');
            $size = (int) $file->getSize();

            $safeBase = preg_replace('/[^A-Za-z0-9._-]/', '_', pathinfo($originalName, PATHINFO_FILENAME)) ?: 'file';
            $storedFilename = substr((string) Str::uuid(), 0, 8).'_'.Str::limit($safeBase, 48, '').($ext !== '' ? '.'.$ext : '');
            $storedPath = $file->storeAs($directory, $storedFilename, self::ATTACHMENTS_DISK);

            if (! is_string($storedPath) || $storedPath === '') {
                continue;
            }

            $isImage = str_starts_with($mime, 'image/') || in_array($ext, ['png', 'jpg', 'jpeg', 'webp', 'gif'], true);
            $isPdf = $mime === 'application/pdf' || $ext === 'pdf';
            $isText = in_array($ext, self::TEXT_EXTENSIONS, true) || str_starts_with($mime, 'text/');

            if ($isImage) {
                $sdkAttachments[] = Image::fromStorage($storedPath, self::ATTACHMENTS_DISK)->as($originalName);
            } elseif ($isPdf) {
                $sdkAttachments[] = Document::fromStorage($storedPath, self::ATTACHMENTS_DISK)->as($originalName);
            } elseif ($isText) {
                $rawContent = (string) Storage::disk(self::ATTACHMENTS_DISK)->get($storedPath);
                $snippet = Str::limit($rawContent, 20000, "\n... [truncated]");
                $lang = $ext !== '' ? $ext : 'text';
                $inlineBlocks[] = "### Attached File: `{$originalName}`\n```{$lang}\n{$snippet}\n```";
            } else {
                $sdkAttachments[] = Document::fromStorage($storedPath, self::ATTACHMENTS_DISK)->as($originalName);
            }

            $storedMeta[] = [
                'name' => $originalName,
                'filename' => $storedFilename,
                'path' => $storedPath,
                'disk' => self::ATTACHMENTS_DISK,
                'mime' => $mime,
                'size' => $size,
                'size_human' => $this->formatBytes($size),
                'is_image' => $isImage,
            ];
        }

        return [$sdkAttachments, $storedMeta, $inlineBlocks];
    }

    /**
     * Save attachment UI metadata on the latest user message while keeping its visible content clean.
     *
     * @param  list<array<string, mixed>>  $storedMeta
     */
    protected function recordAttachmentMetadataOnLatestUserMessage(
        string $conversationId,
        string $cleanPrompt,
        array $storedMeta,
    ): void {
        $latestUserRow = ConversationMessage::query()
            ->where('conversation_id', $conversationId)
            ->where('role', 'user')
            ->orderByDesc('id')
            ->first();

        if (! $latestUserRow) {
            return;
        }

        $meta = is_array($latestUserRow->meta) ? $latestUserRow->meta : [];
        $meta['copilot_attachments'] = $storedMeta;

        $latestUserRow->forceFill([
            'content' => $cleanPrompt,
            'meta' => $meta,
        ])->save();
    }

    /**
     * Ensure the conversation belongs to the acting admin.
     */
    protected function assertOwnership(User $user, string $conversationId): void
    {
        $store = resolve(ConversationStore::class);

        if ($store instanceof VerifiesConversationOwnership) {
            $owns = $store->conversationBelongsTo(
                $conversationId,
                Conversation::participantType($user),
                Conversation::participantKey($user),
            );

            if (! $owns) {
                throw new NotFoundHttpException('Conversation not found.');
            }

            return;
        }

        $exists = Conversation::query()
            ->where('id', $conversationId)
            ->where('participant_type', Conversation::participantType($user))
            ->where('participant_id', Conversation::participantKey($user))
            ->exists();

        if (! $exists) {
            throw new NotFoundHttpException('Conversation not found.');
        }
    }

    /**
     * Format a conversation and its messages for Blade / JSON rendering.
     *
     * @param  Collection<int, PendingApproval>|null  $liveApprovals
     * @return array{id: string, title: string, messages: list<array<string, mixed>>, pending_approvals: list<array<string, mixed>>}
     */
    protected function serializeConversation(Conversation $conversation, ?Collection $liveApprovals = null): array
    {
        $rows = ConversationMessage::query()
            ->where('conversation_id', $conversation->id)
            ->orderBy('id')
            ->get();

        $messages = $rows->map(function (ConversationMessage $row) use ($conversation): array {
            $content = (string) ($row->content ?? '');

            $toolBadges = collect($row->tool_calls)
                ->map(fn (array $call): array => [
                    'id' => $call['id'] ?? '',
                    'name' => $this->humanToolName((string) ($call['name'] ?? 'Tool')),
                    'raw_name' => (string) ($call['name'] ?? ''),
                    'denied' => (bool) ($call['denied'] ?? false),
                    'failed' => (bool) ($call['failed'] ?? false),
                    'pending' => PendingApproval::isPending($call),
                ])
                ->values()
                ->all();

            return [
                'id' => $row->id,
                'role' => $row->role,
                'content' => $content,
                'html' => $row->role === 'assistant'
                    ? Str::markdown($content, ['html_input' => 'strip', 'allow_unsafe_links' => false])
                    : nl2br(e($content)),
                'status' => $row->status instanceof MessageStatus ? $row->status->value : (string) $row->status,
                'tools' => $toolBadges,
                'attachments' => $this->serializeMessageAttachments($conversation->id, $row),
                'created_human' => $row->created_at?->format('H:i') ?? '',
            ];
        })->values()->all();

        $approvals = $liveApprovals?->all();
        if ($approvals === null) {
            $store = resolve(ConversationStore::class);
            $approvals = $store instanceof ResolvesPendingApprovals
                ? $store->pendingApprovalsFor($conversation->id)
                : [];
        }

        $formattedApprovals = array_values(array_map(fn (PendingApproval $approval): array => [
            'id' => $approval->id,
            'tool' => $approval->tool,
            'tool_label' => $this->humanToolName($approval->tool),
            'arguments' => $approval->arguments,
            'reason' => $approval->reason ?: 'Administrator confirmation is required before running this action.',
        ], $approvals));

        return [
            'id' => $conversation->id,
            'title' => $conversation->title,
            'messages' => $messages,
            'pending_approvals' => $formattedApprovals,
        ];
    }

    /**
     * Format stored message attachments with authenticated download/preview URLs.
     *
     * @return list<array<string, mixed>>
     */
    protected function serializeMessageAttachments(string $conversationId, ConversationMessage $row): array
    {
        $rawList = is_array($row->meta) && ! empty($row->meta['copilot_attachments'])
            ? $row->meta['copilot_attachments']
            : (is_array($row->attachments) ? $row->attachments : []);

        $formatted = [];
        foreach ($rawList as $item) {
            if (! is_array($item)) {
                continue;
            }

            $filename = (string) ($item['filename'] ?? basename((string) ($item['path'] ?? '')));
            if ($filename === '') {
                continue;
            }

            $isImage = (bool) ($item['is_image'] ?? (($item['type'] ?? '') === 'stored-image'));

            $formatted[] = [
                'name' => (string) ($item['name'] ?? $filename),
                'filename' => $filename,
                'mime' => (string) ($item['mime'] ?? ($isImage ? 'image/png' : 'application/octet-stream')),
                'size_human' => (string) ($item['size_human'] ?? ''),
                'is_image' => $isImage,
                'url' => route('admin.assistant.attachment', [
                    'conversation' => $conversationId,
                    'filename' => $filename,
                ]),
            ];
        }

        return $formatted;
    }

    protected function formatBytes(int $bytes): string
    {
        if ($bytes < 1024) {
            return $bytes.' B';
        }
        if ($bytes < 1024 * 1024) {
            return round($bytes / 1024, 1).' KB';
        }

        return round($bytes / (1024 * 1024), 1).' MB';
    }

    protected function humanToolName(string $classBasename): string
    {
        return trim((string) preg_replace('/(?<!^)[A-Z]/', ' $0', $classBasename));
    }
}
