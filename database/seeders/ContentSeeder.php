<?php

namespace Database\Seeders;

use App\Models\HeroSlide;
use App\Models\Industry;
use App\Models\JobPost;
use App\Models\Member;
use App\Models\Partner;
use App\Models\Post;
use App\Models\Project;
use App\Models\Service;
use App\Models\Setting;
use App\Models\Tag;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/**
 * Seeds every public-facing content table with the real copy and media
 * paths carried over from the original static Empetix site. The data
 * lives in database/seeders/data/content.json so it stays reviewable
 * and editable without touching PHP. Running the seeder again updates
 * rows in place (keyed by slug/key) instead of duplicating them.
 */
class ContentSeeder extends Seeder
{
    /** Decoded seed payload. */
    private array $data;

    public function __construct()
    {
        $this->data = json_decode(
            file_get_contents(database_path('seeders/data/content.json')),
            true,
            flags: JSON_THROW_ON_ERROR
        );
    }

    public function run(): void
    {
        $this->seedHeroSlides();
        $this->seedServices();
        $this->seedIndustries();
        $this->seedProjects();
        $this->seedMembers();
        $this->seedJobs();
        $this->seedPosts();
        $this->seedPartners();
        $this->seedSettings();
    }

    /** Hero slider slides, keyed by sort order + tab label. */
    private function seedHeroSlides(): void
    {
        foreach ($this->data['hero_slides'] as $row) {
            HeroSlide::updateOrCreate(
                ['title' => $row['title']],
                [
                    'tag' => $row['tag'],
                    'description' => $row['desc'],
                    'cta_label' => $row['cta_label'],
                    'cta_url' => $row['cta_url'],
                    'image' => $row['image'],
                    'tab_label' => $row['tab_label'] ?? null,
                    'tab_title' => $row['tab_title'] ?? null,
                    'tab_sub' => $row['tab_sub'] ?? null,
                    'sort_order' => $row['sort_order'],
                    'is_active' => $row['is_active'],
                ]
            );
        }
    }

    /** The five service pillars shown as home-page solution tabs. */
    private function seedServices(): void
    {
        foreach ($this->data['services'] as $row) {
            Service::updateOrCreate(
                ['slug' => Str::slug($row['title'])],
                [
                    'num' => $row['num'],
                    'title' => $row['title'],
                    'tag' => $row['tag'],
                    'kicker' => $row['kicker'],
                    'heading' => $row['heading'],
                    'description' => $row['desc'],
                    'deliverables' => $row['deliverables'],
                    'metric_num' => $row['metric_num'],
                    'metric_label' => $row['metric_label'],
                    'cta_label' => $row['cta_label'],
                    'cta_url' => $row['cta_url'],
                    'image' => $row['image'],
                    'sort_order' => $row['sort_order'],
                    'is_active' => $row['is_active'],
                ]
            );
        }
    }

    /** Sector list rendered on the home industries wall. */
    private function seedIndustries(): void
    {
        foreach ($this->data['industries'] as $row) {
            Industry::updateOrCreate(
                ['title' => $row['title']],
                [
                    'num' => $row['num'],
                    'subtitle' => $row['sub'],
                    'sort_order' => $row['sort_order'],
                    'is_active' => $row['is_active'],
                ]
            );
        }
    }

    /**
     * Case studies. The NilePay record carries the full structured case
     * study (facts, stats, narrative sections, quote, video); the others
     * carry their card content and can be completed from the dashboard.
     */
    private function seedProjects(): void
    {
        foreach ($this->data['projects'] as $row) {
            $tags = $row['tags'] ?? [];
            unset($row['tags']);

            $project = Project::updateOrCreate(['slug' => $row['slug']], $row);

            // Attach the taxonomy tags (Software / Marketing / Commerce).
            $tagIds = collect($tags)
                ->map(fn (string $name) => Tag::firstOrCreate(['slug' => Str::slug($name)], ['name' => $name])->id)
                ->all();
            $project->tags()->sync($tagIds);
        }
    }

    /** Founders (kind = founder, full profile) and studio team members. */
    private function seedMembers(): void
    {
        foreach ($this->data['members'] as $row) {
            Member::updateOrCreate(['slug' => $row['slug']], $row);
        }
    }

    /** Open positions with their numbered specification sections. */
    private function seedJobs(): void
    {
        foreach ($this->data['job_posts'] as $row) {
            JobPost::updateOrCreate(['slug' => $row['slug']], $row);
        }
    }

    /** Journal posts; body is the article HTML shown on the detail page. */
    private function seedPosts(): void
    {
        foreach ($this->data['posts'] as $row) {
            unset($row['sort_order']);   // posts order by published_at, not a sort column
            Post::updateOrCreate(['slug' => $row['slug']], $row);
        }
    }

    /** Client logos for the partners wall. */
    private function seedPartners(): void
    {
        foreach ($this->data['partners'] as $row) {
            Partner::updateOrCreate(['name' => $row['name']], $row);
        }
    }

    /** Global key/value settings (contact details, social links...). */
    private function seedSettings(): void
    {
        foreach ($this->data['settings'] as $row) {
            Setting::updateOrCreate(
                ['key' => $row['key']],
                ['group' => $row['group'], 'value' => $row['value']]
            );
        }
    }
}
