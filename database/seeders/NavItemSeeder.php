<?php

namespace Database\Seeders;

use App\Models\NavItem;
use Illuminate\Database\Seeder;

/**
 * Seeds the navigation links that match the current public header so the
 * dashboard starts from the same menus the static site shipped with.
 */
class NavItemSeeder extends Seeder
{
    /** Mega-menu panels: columns with child links/feature cards. */
    private const MEGA = [
        'mega-capabilities' => [
            ['Software Engineering', [
                ['Web & Mobile Platforms', '/work', 'Laravel, Next.js, Flutter & high-concurrency APIs', null, null],
                ['E-Commerce & FinTech Core', '/work', 'Custom checkouts, wallets, ERP & payment gateways', null, null],
                ['AI Automation & Data Pipelines', '/work', 'Internal copilots, CRM workflows & BI telemetry', null, null],
            ]],
            ['Marketing & Growth', [
                ['Performance & Paid Acquisition', '/work', 'Full-funnel ROAS across Meta, Google, TikTok & LinkedIn', null, null],
                ['Brand Systems & Campaign Production', '/blog/photos', 'Visual identity, commercial video shoots & art direction', null, null],
                ['CRO, SEO & Lifecycle Retention', '/blog/post', 'Arabic/English technical SEO, A/B testing & CRM flows', null, null],
            ]],
            ['Featured Case Study', [
                ['High-concurrency EdTech platform serving 45,000+ active students.', '/work/alfagn', null, 'ALFAGN EDTECH · 45K+ STUDENTS', 'assets/img/work/alfagn/alfagn_seo.png'],
            ]],
        ],
        'mega-journal' => [
            ['Full Article + Media', [
                ['Engineering Arabic-First Design Systems & High-Conversion Funnels', '/blog/post', null, 'DESIGN & CRO · 8 MIN READ', 'assets/img/blog/arabic-first.jpg'],
            ]],
            ['Video Episode', [
                ['Inside an Empetix 5-Day Product & Growth Sprint', '/blog/video', null, 'VIDEO + TRANSCRIPT · 14:20', 'assets/img/blog/design-sprint.jpg'],
            ]],
            ['Photo Essay', [
                ['Behind the Lens: Directing the Qahwa Roasters Launch Shoot', '/blog/photos', null, 'PHOTO GALLERY + VIDEO · CAMPAIGN', 'assets/img/blog/behind-the-lens.jpg'],
            ]],
        ],
    ];

    public function run(): void
    {
        $utility = [
            ['label' => 'Careers', 'url' => '/careers'],
            ['label' => 'Projects', 'url' => '/work'],
            ['label' => 'Partners', 'url' => '/partners'],
            ['label' => 'Team', 'url' => '/team'],
            ['label' => 'Blog', 'url' => '/blog'],
            ['label' => 'Contact Us', 'url' => '/contact'],
        ];

        foreach ($utility as $i => $link) {
            NavItem::updateOrCreate(
                ['menu' => 'utility', 'url' => $link['url']],
                $link + ['sort_order' => $i, 'is_active' => true]
            );
        }

        $drawer = [
            ['label' => 'Projects & Platforms', 'url' => '/work'],
            ['label' => 'About & Founders', 'url' => '/about'],
            ['label' => 'Our Team', 'url' => '/team'],
            ['label' => 'Partners & Industries', 'url' => '/partners'],
            ['label' => 'Blogs & Media', 'url' => '/blog'],
            ['label' => 'Careers', 'url' => '/careers'],
            ['label' => 'Contact Us', 'url' => '/contact'],
        ];

        foreach ($drawer as $i => $link) {
            NavItem::updateOrCreate(
                ['menu' => 'drawer', 'url' => $link['url']],
                $link + ['sort_order' => $i, 'is_active' => true]
            );
        }

        foreach (self::MEGA as $menu => $columns) {
            foreach ($columns as $i => [$columnTitle, $links]) {
                $column = NavItem::updateOrCreate(
                    ['menu' => $menu, 'parent_id' => null, 'label' => $columnTitle],
                    ['url' => '#', 'sort_order' => $i, 'is_active' => true]
                );

                foreach ($links as $j => [$label, $url, $sub, $meta, $image]) {
                    NavItem::updateOrCreate(
                        ['menu' => $menu, 'parent_id' => $column->id, 'label' => $label],
                        compact('url', 'sub', 'meta', 'image') + ['sort_order' => $j, 'is_active' => true]
                    );
                }
            }
        }
    }
}
