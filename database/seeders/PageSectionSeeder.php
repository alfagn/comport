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
            ['who_we_are_heading', '"Who we are" film section title', 'text'],
            ['principles_heading', "Principles section heading", 'text'],
            ['timeline_heading', "Timeline section heading", 'text'],
            ['directory_heading', "Team directory heading", 'text'],
        ],
        'work' => [
            ['hero_title', 'Hero title', 'text'],
            ['hero_eyebrow', 'Hero eyebrow (small label)', 'text'],
            ['hero_lead', 'Hero lead paragraph', 'textarea'],
            ['hero_meta', 'Hero side meta line', 'text'],
            ['filter_label', 'Filter bar label', 'text'],
        ],
        'team' => [
            ['hero_title', 'Hero title', 'text'],
            ['practices_heading', "Practice teams heading", 'text'],
            ['culture_heading', "Culture section heading", 'text'],
            ['join_heading', "Join-us section heading", 'text'],
        ],
        'blog' => [
            ['hero_title', 'Hero title', 'text'],
            ['hero_eyebrow', 'Hero eyebrow (small label)', 'text'],
            ['hero_lead', 'Hero lead paragraph', 'textarea'],
            ['hero_meta', 'Hero side meta line', 'text'],
        ],
        'partners' => [
            ['hero_title', 'Hero title', 'text'],
            ['hero_eyebrow', 'Hero eyebrow (small label)', 'text'],
            ['hero_lead', 'Hero lead paragraph', 'textarea'],
            ['cta_button', '"Become a Partner" button label', 'text'],
        ],
        'contact' => [
            ['hero_title', 'Hero title', 'textarea'],
            ['channels_heading', 'Practice desks heading', 'text'],
            ['form_heading', 'Brief form heading', 'text'],
            ['promise_title', '"What happens next" title', 'text'],
            ['hotline_title', 'Hotline block title', 'text'],
            ['hotline_hours', 'Hotline hours line', 'text'],
            ['studios_heading', 'Headquarters section heading', 'text'],
            ['hq_city', 'Headquarters city title', 'text'],
            ['hq_desc', 'Headquarters description', 'textarea'],
            ['hq_hours', 'Headquarters working hours', 'text'],
            ['visit_cta', 'Studio visit button label', 'text'],
            ['faq_heading', 'FAQ section heading', 'text'],
            ['form_legend_a', 'Brief form: capabilities legend', 'text'],
            ['form_legend_b', 'Brief form: budget legend', 'text'],
            ['form_budget_1', 'Brief form: budget option 1 label', 'text'],
            ['form_budget_2', 'Brief form: budget option 2 label', 'text'],
            ['form_budget_3', 'Brief form: budget option 3 label', 'text'],
            ['form_budget_4', 'Brief form: budget option 4 label', 'text'],
            ['form_label_name', 'Brief form: name field label', 'text'],
            ['form_label_email', 'Brief form: email field label', 'text'],
            ['form_label_company', 'Brief form: company field label', 'text'],
            ['form_label_timeline', 'Brief form: timeline field label', 'text'],
            ['form_label_brief', 'Brief form: brief field label', 'text'],
            ['form_ph_name', 'Brief form: name placeholder', 'text'],
            ['form_ph_email', 'Brief form: email placeholder', 'text'],
            ['form_ph_company', 'Brief form: company placeholder', 'text'],
            ['form_ph_brief', 'Brief form: brief placeholder', 'textarea'],
            ['form_timeline_1', 'Brief form: timeline option 1 label', 'text'],
            ['form_timeline_2', 'Brief form: timeline option 2 label', 'text'],
            ['form_timeline_3', 'Brief form: timeline option 3 label', 'text'],
            ['form_timeline_4', 'Brief form: timeline option 4 label', 'text'],
            ['form_consent', 'Brief form: consent line', 'textarea'],
            ['form_button', 'Brief form: submit button label', 'text'],
        ],
        'careers' => [
            ['tracks_heading', "Practice areas heading", 'text'],
            ['roles_heading', "Open roles heading", 'text'],
            ['benefits_heading', "Benefits heading", 'text'],
            ['process_heading', "Hiring process heading", 'text'],
            ['other_jobs_heading', '"Other positions" heading (job page)', 'text'],
            ['apply_button_open', 'Job page: "Apply for this Job" button', 'text'],
            ['apply_eyebrow', 'Job form: eyebrow line', 'text'],
            ['apply_badge', 'Job form: step badge', 'text'],
            ['apply_label_name', 'Job form: name label', 'text'],
            ['apply_label_email', 'Job form: email label', 'text'],
            ['apply_label_phone', 'Job form: phone label', 'text'],
            ['apply_label_city', 'Job form: city label', 'text'],
            ['apply_label_linkedin', 'Job form: LinkedIn label', 'text'],
            ['apply_label_portfolio', 'Job form: portfolio label', 'text'],
            ['apply_label_cv', 'Job form: CV upload label', 'text'],
            ['apply_label_exp', 'Job form: experience label', 'text'],
            ['apply_label_notice', 'Job form: notice period label', 'text'],
            ['apply_label_achievement', 'Job form: achievement label', 'text'],
            ['apply_ph_name', 'Job form: name placeholder', 'text'],
            ['apply_ph_email', 'Job form: email placeholder', 'text'],
            ['apply_ph_phone', 'Job form: phone placeholder', 'text'],
            ['apply_ph_city', 'Job form: city placeholder', 'text'],
            ['apply_ph_linkedin', 'Job form: LinkedIn placeholder', 'text'],
            ['apply_ph_portfolio', 'Job form: portfolio placeholder', 'text'],
            ['apply_ph_achievement', 'Job form: achievement placeholder', 'textarea'],
            ['apply_cv_title', 'Job form: CV dropzone title', 'text'],
            ['apply_cv_sub', 'Job form: CV dropzone sub-line', 'text'],
            ['apply_exp_1', 'Job form: experience option 1', 'text'],
            ['apply_exp_2', 'Job form: experience option 2', 'text'],
            ['apply_exp_3', 'Job form: experience option 3', 'text'],
            ['apply_notice_1', 'Job form: notice option 1', 'text'],
            ['apply_notice_2', 'Job form: notice option 2', 'text'],
            ['apply_notice_3', 'Job form: notice option 3', 'text'],
            ['apply_notice_4', 'Job form: notice option 4', 'text'],
            ['apply_consent', 'Job form: consent line', 'textarea'],
            ['apply_button', 'Job form: submit button label', 'text'],
        ],
        'founder' => [
            ['work_heading', '"Selected work" heading (founder profile)', 'text'],
            ['other_founders_heading', '"Other founders" heading (founder profile)', 'text'],
        ],
        'mega' => [
            ['capabilities_title', 'Mega menu: Capabilities intro title', 'text'],
            ['capabilities_desc', 'Mega menu: Capabilities intro paragraph', 'textarea'],
            ['journal_title', 'Mega menu: Journal intro title', 'text'],
            ['journal_desc', 'Mega menu: Journal intro paragraph', 'textarea'],
        ],
        'footer' => [
            ['banner_title', 'CTA banner title', 'text'],
            ['banner_sub', 'CTA banner subtitle', 'textarea'],
            ['brand_summary', 'Company summary paragraph', 'textarea'],
            ['search_placeholder', 'Search panel: input placeholder', 'text'],
            ['search_quickjump', 'Search panel: "Quick jump" label', 'text'],
            ['newsletter_title', 'Newsletter block title', 'text'],
            ['newsletter_summary', 'Newsletter block summary', 'textarea'],
            ['newsletter_placeholder', 'Newsletter: email placeholder', 'text'],
            ['newsletter_button', 'Newsletter: subscribe button label', 'text'],
        ],
    ];

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
