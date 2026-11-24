<?php

namespace Database\Seeders;

use App\Models\PageSection;
use Illuminate\Database\Seeder;

/**
 * Registers curated editorial sec() keys used by the public Blade templates
 * with clean, executive human-readable labels.
 */
class PageSectionSeeder extends Seeder
{
    /** page => [key, label, type] rows. */
    private const SECTIONS = [
        'home' => [
            ['hero_kicker', 'Hero Kicker Badge', 'text'],
            ['hero_secondary_cta', 'Hero Secondary Button Label', 'text'],
            ['who_we_are', '"Who We Are" Section Heading', 'text'],
            ['solutions_heading', 'Solutions Hub Heading', 'text'],
            ['work_heading', 'Selected Work Heading', 'text'],
            ['impact_heading', 'Impact Metrics Heading', 'text'],
            ['partners_heading', 'Partners Section Heading', 'text'],
            ['journal_heading', 'Journal Section Heading', 'text'],
            ['careers_band_title', 'Careers Banner Heading', 'text'],
        ],
        'header' => [
            ['search_placeholder', 'Site Search Input Placeholder', 'text'],
            ['capabilities_title', 'Capabilities Mega-Menu Heading', 'text'],
            ['capabilities_desc', 'Capabilities Mega-Menu Description', 'textarea'],
            ['journal_title', 'Journal Mega-Menu Heading', 'text'],
            ['journal_desc', 'Journal Mega-Menu Description', 'textarea'],
        ],
        'footer' => [
            ['banner_title', 'CTA Banner Heading', 'text'],
            ['banner_sub', 'CTA Banner Subtitle', 'textarea'],
            ['banner_cta_primary', 'CTA Banner Primary Button', 'text'],
            ['banner_cta_secondary', 'CTA Banner Secondary Button', 'text'],
            ['brand_summary', 'Company Summary Paragraph', 'textarea'],
            ['newsletter_title', 'Newsletter Block Heading', 'text'],
            ['newsletter_summary', 'Newsletter Block Summary', 'textarea'],
            ['newsletter_placeholder', 'Newsletter Email Placeholder', 'text'],
            ['newsletter_button', 'Newsletter Subscribe Button', 'text'],
            ['copyright', 'Legal Copyright Notice', 'text'],
        ],
        'about' => [
            ['hero_title', 'Hero Heading', 'textarea'],
            ['who_we_are_heading', '"Who We Are" Section Heading', 'text'],
            ['principles_heading', 'Operating Principles Heading', 'text'],
            ['timeline_heading', 'Studio Timeline Heading', 'text'],
        ],
        'work' => [
            ['hero_eyebrow', 'Hero Eyebrow Badge', 'text'],
            ['hero_title', 'Hero Heading', 'text'],
            ['hero_lead', 'Hero Lead Paragraph', 'textarea'],
            ['hero_meta', 'Hero Side Meta Badge', 'text'],
        ],
        'team' => [
            ['hero_title', 'Hero Heading', 'text'],
            ['practices_heading', 'Practice Teams Heading', 'text'],
            ['culture_heading', 'Team Culture Heading', 'text'],
            ['join_heading', 'Join Section Heading', 'text'],
        ],
        'careers' => [
            ['tracks_heading', 'Practice Areas Heading', 'text'],
            ['roles_heading', 'Open Roles Heading', 'text'],
            ['benefits_heading', 'Benefits & Compensation Heading', 'text'],
            ['process_heading', 'Hiring Process Heading', 'text'],
            ['apply_consent', 'Job Application Consent Notice', 'textarea'],
        ],
        'partners' => [
            ['hero_eyebrow', 'Hero Eyebrow Badge', 'text'],
            ['hero_title', 'Hero Heading', 'text'],
            ['hero_lead', 'Hero Lead Paragraph', 'textarea'],
            ['cta_button', 'Hero CTA Button Label', 'text'],
        ],
        'blog' => [
            ['hero_eyebrow', 'Hero Eyebrow Badge', 'text'],
            ['hero_title', 'Hero Heading', 'text'],
            ['hero_lead', 'Hero Lead Paragraph', 'textarea'],
            ['hero_meta', 'Hero Side Meta Badge', 'text'],
        ],
        'contact' => [
            ['hero_title', 'Hero Heading', 'textarea'],
            ['channels_heading', 'Practice Desks Section Heading', 'text'],
            ['form_heading', 'Project Brief Form Heading', 'text'],
            ['form_budget_1', 'Budget Option 1', 'text'],
            ['form_budget_2', 'Budget Option 2', 'text'],
            ['form_budget_3', 'Budget Option 3', 'text'],
            ['form_budget_4', 'Budget Option 4', 'text'],
            ['form_consent', 'Project Brief NDA & Privacy Notice', 'textarea'],
            ['form_button', 'Project Brief Submit Button Label', 'text'],
            ['promise_title', 'Executive Promise Heading', 'text'],
            ['hotline_title', 'Executive Hotline Heading', 'text'],
            ['hotline_hours', 'Executive Hotline Hours', 'text'],
            ['studios_heading', 'Headquarters Section Heading', 'text'],
            ['hq_city', 'Headquarters City Heading', 'text'],
            ['hq_desc', 'Headquarters Description', 'textarea'],
            ['hq_hours', 'Headquarters Working Hours', 'text'],
            ['visit_cta', 'Studio Visit Button Label', 'text'],
            ['faq_heading', 'FAQ Section Heading', 'text'],
        ],
    ];

    /** Expose curated [page => [[key, label, type], ...]] definitions for migrations & audits. */
    public static function definitions(): array
    {
        return self::SECTIONS;
    }

    public function run(): void
    {
        $this->call(AboutContentSeeder::class);
        $this->call(EditorialContentSeeder::class);
        foreach (self::SECTIONS as $page => $sections) {
            foreach ($sections as $i => [$key, $label, $type]) {
                PageSection::firstOrCreate(
                    ['page' => $page, 'key' => $key],
                    ['label' => $label, 'type' => $type, 'sort_order' => $i]
                );
            }
        }
        $this->call(EditorialDefaultsBackfillSeeder::class);
    }
}
