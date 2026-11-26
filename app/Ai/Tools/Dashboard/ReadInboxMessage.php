<?php

namespace App\Ai\Tools\Dashboard;

use App\Models\ContactMessage;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Ai\Tools\Request;
use Stringable;

/**
 * Read one inbox brief in full (does not change its read state).
 */
class ReadInboxMessage extends DashboardTool
{
    public function permission(): ?string
    {
        return 'manage inbox';
    }

    public function description(): Stringable|string
    {
        return 'Read the full content of one inbox brief by id. Reading does not mark it as read; use MarkInboxMessagesRead for that.';
    }

    protected function run(Request $request): string
    {
        $validated = $request->validate(['id' => ['required', 'integer']]);

        $message = ContactMessage::query()->findOrFail($validated['id']);

        return $this->json([
            'id' => $message->id,
            'name' => $message->name,
            'email' => $message->email,
            'company' => $message->company,
            'services' => $message->services,
            'budget' => $message->budget_tier,
            'timeline' => $message->timeline,
            'brief' => $message->brief,
            'is_read' => $message->read_at !== null,
            'received_at' => $message->created_at?->toDateTimeString(),
            'admin_url' => route('admin.inbox.show', $message->id),
        ]);
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'id' => $schema->integer()->required()->description('The inbox brief id.'),
        ];
    }
}
