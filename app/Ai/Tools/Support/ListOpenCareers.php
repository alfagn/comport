<?php

namespace App\Ai\Tools\Support;

use App\Models\JobPost;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Ai\Contracts\Tool;
use Laravel\Ai\Tools\Request;
use Stringable;

/**
 * Lists currently open career roles on the public site.
 */
class ListOpenCareers implements Tool
{
    public function description(): Stringable|string
    {
        return 'List currently active career / job openings at Empetix, with department, location, employment type, summary, and direct application link.';
    }

    public function handle(Request $request): Stringable|string
    {
        if (! page_active('careers')) {
            return (string) json_encode([
                'careers_page_active' => false,
                'openings' => [],
            ]);
        }

        $jobs = JobPost::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get()
            ->map(fn (JobPost $job): array => [
                'title' => $job->title,
                'department' => $job->department,
                'location' => $job->location,
                'type' => $job->type,
                'summary' => $job->summary,
                'requirements' => array_slice($job->requirements ?? [], 0, 5),
                'apply_url' => route('jobs.show', $job->slug),
            ])->all();

        return (string) json_encode([
            'careers_url' => route('careers'),
            'count' => count($jobs),
            'openings' => $jobs,
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
    }

    public function schema(JsonSchema $schema): array
    {
        return [];
    }
}
