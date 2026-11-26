<?php

namespace App\Ai\Tools\Dashboard;

use App\Models\JobApplication;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Ai\Tools\Request;
use Stringable;

/**
 * Move job applications between the "new" and "reviewed" states.
 */
class UpdateJobApplicationStatus extends DashboardTool
{
    public function permission(): ?string
    {
        return 'manage applications';
    }

    public function description(): Stringable|string
    {
        return 'Set the review status of one or more job applications to "reviewed" or back to "new".';
    }

    protected function run(Request $request): string
    {
        $validated = $request->validate([
            'ids' => ['required', 'array', 'min:1', 'max:50'],
            'ids.*' => ['integer'],
            'status' => ['required', 'in:new,reviewed'],
        ]);

        $updated = JobApplication::query()
            ->whereIn('id', $validated['ids'])
            ->update(['status' => $validated['status']]);

        return $this->json([
            'updated' => $updated,
            'status' => $validated['status'],
        ]);
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'ids' => $schema->array()->items($schema->integer())->required()->description('Job application ids.'),
            'status' => $schema->string()->enum(['new', 'reviewed'])->required(),
        ];
    }
}
