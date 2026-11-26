<?php

namespace App\Services;

use App\Ai\Agents\DashboardAssistantAgent;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Laravel\Ai\Approvals\Decision;
use Laravel\Ai\Approvals\Decisions;
use Laravel\Ai\Approvals\PendingApproval;
use Laravel\Ai\Contracts\ConversationStore;
use Laravel\Ai\Contracts\ResolvesPendingApprovals;
use Laravel\Ai\Contracts\VerifiesConversationOwnership;
use Laravel\Ai\Enums\MessageStatus;
use Laravel\Ai\Models\Conversation;
use Laravel\Ai\Models\ConversationMessage;
use RuntimeException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

/**
 * Orchestrates the Dashboard AI Copilot: conversation history, ownership
 * checks, prompt execution, and human-in-the-loop tool approvals.
 */
class DashboardAssistantService
{
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
     * Prompt the Dashboard Copilot (starting or continuing a conversation).
     *
     * @return array{id: string, title: string, messages: list<array<string, mixed>>, pending_approvals: list<array<string, mixed>>}
     */
    public function sendMessage(User $user, string $prompt, ?string $conversationId = null): array
    {
        $agent = new DashboardAssistantAgent($user);
        $rawProvider = $agent->provider();
        $primaryProvider = is_array($rawProvider) ? (string) array_key_first($rawProvider) : $rawProvider;
        $provider = $this->guard->resolvedProvider($primaryProvider);

        if (! $this->guard->isReady($provider, DashboardAssistantAgent::isFaked())) {
            throw new RuntimeException($this->guard->missingCredentialsHint($provider));
        }

        if ($conversationId !== null && $conversationId !== '') {
            $this->assertOwnership($user, $conversationId);
            $agent->continue($conversationId, as: $user);
        } else {
            $agent->forUser($user);
        }

        try {
            $response = $agent->prompt($prompt);
        } catch (Throwable $exception) {
            throw new RuntimeException('AI request failed: '.$exception->getMessage(), previous: $exception);
        }

        $resolvedId = $response->conversationId ?? $conversationId;
        $conversation = Conversation::query()->findOrFail($resolvedId);

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
     * Delete an owned conversation and its stored messages.
     */
    public function deleteConversation(User $user, string $conversationId): void
    {
        $this->assertOwnership($user, $conversationId);

        ConversationMessage::query()->where('conversation_id', $conversationId)->delete();
        Conversation::query()->where('id', $conversationId)->delete();
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

        $messages = $rows->map(function (ConversationMessage $row): array {
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

    protected function humanToolName(string $classBasename): string
    {
        return trim((string) preg_replace('/(?<!^)[A-Z]/', ' $0', $classBasename));
    }
}
