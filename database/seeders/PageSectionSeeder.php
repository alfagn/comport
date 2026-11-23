<?php

namespace Database\Seeders;

use App\Models\PageSection;
use Illuminate\Database\Seeder;

/**
 * Registers every sec() key used by the public Blade templates so the
 * dashboard Pages editor lists them with friendly labels. Values start
 * null: the template defaults render until the team edits a block.
 */
class PageSectionSeeder extends Seeder
{
    /** page => [key, label, type] rows. */
    private const SECTIONS = [
        'home' => [
            ['hero_kicker', 'Hero kicker (small line above the slides)', 'text'],
            ['hero_secondary_cta', 'Hero secondary button label', 'text'],
            ['who_we_are', '"Who we are" film section title', 'text'],
            ['solutions_heading', 'Solutions section heading', 'text'],
            ['work_heading', 'Selected work section heading', 'text'],
            ['impact_heading', 'Impact stats section heading', 'text'],
            ['partners_heading', 'Partners section heading', 'text'],
            ['journal_heading', 'Journal section heading', 'text'],
            ['careers_band_title', 'Careers banner title', 'text'],
        ],
        'about' => [
            ['hero_title', 'Hero title', 'textarea'],
        ],
        'work' => [
            ['hero_title', 'Hero title', 'text'],
        ],
        'team' => [
            ['hero_title', 'Hero title', 'text'],
        ],
        'blog' => [
            ['hero_title', 'Hero title', 'text'],
        ],
        'partners' => [
            ['hero_title', 'Hero title', 'text'],
        ],
        'contact' => [
            ['hero_title', 'Hero title', 'textarea'],
        ],
        'footer' => [
            ['banner_title', 'CTA banner title', 'text'],
            ['banner_sub', 'CTA banner subtitle', 'textarea'],
            ['brand_summary', 'Company summary paragraph', 'textarea'],
        ],
    ];

    public function run(): void
    {
        foreach (self::SECTIONS as $page => $sections) {
            foreach ($sections as $i => [$key, $label, $type]) {
                PageSection::firstOrCreate(
                    ['page' => $page, 'key' => $key],
                    ['label' => $label, 'type' => $type, 'sort_order' => $i]
                );
            }
        }
    }
}
