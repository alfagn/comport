<?php

namespace App\Ai\Tools\Dashboard;

use App\Models\ContactMessage;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Ai\Approvals\Approval;
use Laravel\Ai\Concerns\InteractsWithApprovals;
use Laravel\Ai\Contracts\Approvable;
use Laravel\Ai\Tools\Request;
use Stringable;

/**
 * Permanently delete an inbox brief. Always pauses for admin approval.
 */
class DeleteInboxMessage extends DashboardTool implements Approvable
{
    use InteractsWithApprovals;

    public function permission(): ?string
    {
        return 'manage inbox';
    }

    public function description(): Stringable|string
    {
        return 'Permanently delete one inbox brief by id. The admin must approve before it runs.';
    }

    protected function needsApproval(Request $request): Approval|bool
    {
        $message = ContactMessage::query()->find($request['id'] ?? null);

        return Approval::required($message
            ? "Permanently delete the brief from {$message->name} <{$message->email}> received {$message->created_at?->toFormattedDateString()}."
            : 'Permanently delete an inbox brief.');
    }

    protected function run(Request $request): string
    {
        $validated = $request->validate(['id' => ['required', 'integer']]);

        $message = ContactMessage::query()->findOrFail($validated['id']);
        $label = "#{$message->id} from {$message->name}";
        $message->delete();

        return "Deleted inbox brief {$label}.";
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'id' => $schema->integer()->required()->description('The inbox brief id to delete.'),
        ];
    }
}
