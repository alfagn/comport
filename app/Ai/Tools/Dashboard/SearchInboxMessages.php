<?php

namespace App\Ai\Tools\Dashboard;

use App\Models\ContactMessage;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Illuminate\Support\Str;
use Laravel\Ai\Tools\Request;
use Stringable;

/**
 * Search the contact-form inbox (project briefs).
 */
class SearchInboxMessages extends DashboardTool
{
    public function permission(): ?string
    {
        return 'manage inbox';
    }

    public function description(): Stringable|string
    {
        return 'Search the inbox of project briefs sent through the contact form or handed off by the support chatbot. Filter by keyword (name, email, company or brief text) and unread state. Returns newest first with a short excerpt; use ReadInboxMessage for the full brief.';
    }

    protected function run(Request $request): string
    {
        $validated = $request->validate([
            'query' => ['nullable', 'string', 'max:120'],
            'unread_only' => ['nullable', 'boolean'],
            'limit' => ['nullable', 'integer', 'min:1', 'max:25'],
        ]);

        $term = trim((string) ($validated['query'] ?? ''));

        $messages = ContactMessage::query()
            ->when($validated['unread_only'] ?? false, fn ($query) => $query->unread())
            ->when($term !== '', function ($query) use ($term) {
                $query->where(function ($inner) use ($term) {
                    foreach (['name', 'email', 'company', 'brief'] as $column) {
                        $inner->orWhere($column, 'like', "%{$term}%");
                    }
                });
            })
            ->latest()
            ->limit($validated['limit'] ?? 10)
            ->get();

        return $this->json([
            'count' => $messages->count(),
            'messages' => $messages->map(fn (ContactMessage $message): array => [
                'id' => $message->id,
                'name' => $message->name,
                'email' => $message->email,
                'company' => $message->company,
                'services' => $message->services,
                'budget' => $message->budget_tier,
                'timeline' => $message->timeline,
                'excerpt' => Str::limit((string) $message->brief, 180),
                'is_read' => $message->read_at !== null,
                'received_at' => $message->created_at?->toDateTimeString(),
                'admin_url' => route('admin.inbox.show', $message->id),
            ])->all(),
        ]);
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'query' => $schema->string()->description('Optional keyword to match against name, email, company or brief text.'),
            'unread_only' => $schema->boolean()->description('Only return briefs nobody has read yet.'),
            'limit' => $schema->integer()->min(1)->max(25)->description('Maximum results (default 10).'),
        ];
    }
}
