<?php

namespace App\Ai\Tools\Dashboard;

use App\Models\Post;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Ai\Approvals\Approval;
use Laravel\Ai\Concerns\InteractsWithApprovals;
use Laravel\Ai\Contracts\Approvable;
use Laravel\Ai\Tools\Request;
use Stringable;

/**
 * Publish or hide a content record on the public website. Changes what
 * visitors see, so it always pauses for admin approval.
 */
class SetContentVisibility extends DashboardTool implements Approvable
{
    use InteractsWithApprovals;

    public function permission(): ?string
    {
        return null;
    }

    /**
     * Available whenever the admin manages at least one content entity.
     */
    public function isAvailable(): bool
    {
        foreach (ContentRegistry::keys() as $entity) {
            if ($this->user->can(ContentRegistry::get($entity)['permission'])) {
                return true;
            }
        }

        return false;
    }

    public function description(): Stringable|string
    {
        return 'Show (publish) or hide a record on the public website. Supported entities: '.implode(', ', ContentRegistry::keys()).'. Find the id with SearchSiteContent first. The admin must approve before it runs.';
    }

    protected function needsApproval(Request $request): Approval|bool
    {
        $config = ContentRegistry::get((string) ($request['entity'] ?? ''));
        $record = $config ? $config['model']::query()->find($request['id'] ?? null) : null;
        $action = filter_var($request['visible'] ?? false, FILTER_VALIDATE_BOOLEAN) ? 'Publish' : 'Hide';

        return Approval::required($record
            ? "{$action} the {$request['entity']} record \"{$record->getAttribute($config['title'])}\" on the public website."
            : "{$action} a content record on the public website.");
    }

    protected function run(Request $request): string
    {
        $validated = $request->validate([
            'entity' => ['required', 'in:'.implode(',', ContentRegistry::keys())],
            'id' => ['required', 'integer'],
            'visible' => ['required', 'boolean'],
        ]);

        $entity = $validated['entity'];
        $config = ContentRegistry::get($entity);

        if (! $this->user->can($config['permission'])) {
            return "Permission denied: your account cannot manage {$entity}.";
        }

        $record = $config['model']::query()->findOrFail($validated['id']);
        $visible = (bool) $validated['visible'];
        $updates = [$config['visibility'] => $visible];

        if ($record instanceof Post && $visible && $record->published_at === null) {
            $updates['published_at'] = now();
        }

        $record->forceFill($updates)->save();
        ContentRegistry::flushCaches($entity);

        return $this->json([
            'result' => $visible ? 'now visible on the public site' : 'now hidden from the public site',
            'record' => ContentRegistry::summarize($entity, $record->refresh()),
        ]);
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'entity' => $schema->string()->enum(ContentRegistry::keys())->required(),
            'id' => $schema->integer()->required()->description('Record id from SearchSiteContent.'),
            'visible' => $schema->boolean()->required()->description('true = publish/show, false = hide.'),
        ];
    }
}
