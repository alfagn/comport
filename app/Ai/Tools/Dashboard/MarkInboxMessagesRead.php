<?php

namespace App\Ai\Tools\Dashboard;

use App\Models\ContactMessage;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Ai\Tools\Request;
use Stringable;

/**
 * Mark inbox briefs read or unread (reversible, so no approval needed).
 */
class MarkInboxMessagesRead extends DashboardTool
{
    public function permission(): ?string
    {
        return 'manage inbox';
    }

    public function description(): Stringable|string
    {
        return 'Mark one or more inbox briefs as read (or back to unread with read=false).';
    }

    protected function run(Request $request): string
    {
        $validated = $request->validate([
            'ids' => ['required', 'array', 'min:1', 'max:50'],
            'ids.*' => ['integer'],
            'read' => ['nullable', 'boolean'],
        ]);

        $read = $validated['read'] ?? true;

        $updated = ContactMessage::query()
            ->whereIn('id', $validated['ids'])
            ->update(['read_at' => $read ? now() : null]);

        return $this->json([
            'updated' => $updated,
            'state' => $read ? 'read' : 'unread',
        ]);
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'ids' => $schema->array()->items($schema->integer())->required()->description('Inbox brief ids to update.'),
            'read' => $schema->boolean()->description('true = mark read (default), false = mark unread.'),
        ];
    }
}
