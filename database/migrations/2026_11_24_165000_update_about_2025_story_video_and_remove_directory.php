<?php

use App\Models\ContentListItem;
use App\Models\PageSection;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Cache;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Upgrade overview video controls on About & Home to dual-mode Private Storage video uploader
        PageSection::query()
            ->where('page', 'about')
            ->where('key', 'overview_video')
            ->update([
                'type' => 'video',
                'label' => 'Overview video (Upload to Private Storage or Link)',
            ]);

        PageSection::query()
            ->where('page', 'home')
            ->where('key', 'copy_29166363eeca')
            ->update([
                'type' => 'video',
                'label' => 'Company overview video (Upload to Private Storage or Link)',
            ]);

        // 2. Update About badges & timeline heading from legacy mock values to authentic 2025 Software & Marketing story
        PageSection::query()
            ->where('page', 'about')
            ->where('key', 'established')
            ->whereIn('value', ['Established 2018 · Cairo', 'Established 2018 &middot; Cairo', null])
            ->update(['value' => 'Established 2025 · Cairo']);

        PageSection::query()
            ->where('page', 'about')
            ->where('key', 'hero_meta')
            ->whereIn('value', ['48 SPECIALISTS · 3 STUDIOS', '48 SPECIALISTS &middot; 3 STUDIOS', null])
            ->update(['value' => 'SOFTWARE ENGINEERING · GROWTH MARKETING']);

        PageSection::query()
            ->where('page', 'about')
            ->where('key', 'timeline_heading')
            ->where(function ($q) {
                $q->whereNull('value')
                    ->orWhere('value', 'Eight years of compounding craft.');
            })
            ->update(['value' => 'From 2025 onward: software engineering and growth marketing under one roof.']);

        // 3. Hide removed Senior Principals & Discipline Heads directory control & clean mock rows
        PageSection::query()
            ->where('page', 'about')
            ->where('key', 'directory_heading')
            ->update(['type' => 'internal']);

        ContentListItem::query()->where('group', 'about.directory')->delete();

        // 4. Replace legacy 2018-2026 mock timeline rows with authentic 2025-2026 Engineering & Marketing milestones
        $hasLegacyTimeline = ContentListItem::query()
            ->where('group', 'about.timeline')
            ->where(function ($q) {
                $q->where('meta', '2018')
                    ->orWhere('title', 'Founded in Downtown Cairo');
            })
            ->exists();

        if ($hasLegacyTimeline || ! ContentListItem::query()->where('group', 'about.timeline')->exists()) {
            ContentListItem::query()->where('group', 'about.timeline')->delete();

            $milestones = [
                [
                    'meta' => '2025',
                    'title' => 'Founded Around Software & Growth Marketing',
                    'body' => 'Launched Empetix Solution in Cairo uniting full-stack software engineering and performance marketing under one roof—closing the gap between building digital products and scaling customer acquisition.',
                ],
                [
                    'meta' => '2025',
                    'title' => 'Enterprise Platforms & E-Learning Ecosystems',
                    'body' => 'Architected and shipped high-concurrency web, mobile, and learning platforms—including AlphaGen Academy and Western International Academy—paired with conversion-engineered funnels.',
                ],
                [
                    'meta' => '2026',
                    'title' => 'Scaling Custom SaaS, AI Workflows & Growth Operations',
                    'body' => 'Delivering full-stack Laravel, React, and Flutter software alongside data-driven SEO, paid acquisition, and commercial growth partnerships for ambitious regional brands.',
                ],
            ];

            foreach ($milestones as $i => $row) {
                ContentListItem::query()->create($row + [
                    'group' => 'about.timeline',
                    'sort_order' => $i,
                    'is_active' => true,
                ]);
            }
        }

        Cache::forget('page_sections.map');
        Cache::forget('content-list.about.timeline');
        Cache::forget('content-list.about.directory');
    }

    public function down(): void
    {
        // Non-destructive editorial data migration.
    }
};
