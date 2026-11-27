<?php

namespace App\Ai\Tools\Dashboard;

use App\Models\SupportChatSession;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Ai\Tools\Request;
use Stringable;

/**
 * Review what visitors asked the public AI support chatbot.
 */
class ListSupportChats extends DashboardTool
{
    public function permission(): ?string
    {
        return 'manage support chats';
    }

    public function description(): Stringable|string
    {
        return 'List recent conversations visitors had with the public AI support chatbot, optionally only those handed off to the team. Includes visitor contact details, hand-off summary and message counts.';
    }

    protected function run(Request $request): string
    {
        $validated = $request->validate([
            'status' => ['nullable', 'in:open,handed_off,closed,any'],
            'limit' => ['nullable', 'integer', 'min:1', 'max:25'],
        ]);

        $status = $validated['status'] ?? 'any';

        $chats = SupportChatSession::query()
            ->where('message_count', '>', 0)
            ->when($status !== 'any', fn ($query) => $query->where('status', $status))
            ->latest('last_message_at')
            ->limit($validated['limit'] ?? 10)
            ->get();

        return $this->json([
            'count' => $chats->count(),
            'chats' => $chats->map(fn (SupportChatSession $chat): array => [
                'id' => $chat->id,
                'channel' => $chat->channel,
                'visitor' => $chat->visitorLabel(),
                'phone' => $chat->visitor_phone,
                'email' => $chat->visitor_email,
                'status' => $chat->status,
                'ai_paused' => $chat->ai_paused,
                'handoff_summary' => $chat->handoff_summary,
                'messages' => $chat->message_count,
                'last_message_at' => $chat->last_message_at?->toDateTimeString(),
                'admin_url' => $chat->isWhatsapp()
                    ? route('admin.whatsapp.index', ['chat' => $chat->id])
                    : route('admin.website-chat.index', ['chat' => $chat->id]),
            ])->all(),
        ]);
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'status' => $schema->string()->enum(['open', 'handed_off', 'closed', 'any'])->description('Status filter (default any).'),
            'limit' => $schema->integer()->min(1)->max(25)->description('Maximum results (default 10).'),
        ];
    }
}
