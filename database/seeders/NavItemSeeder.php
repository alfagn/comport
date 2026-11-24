<?php

namespace Database\Seeders;

use App\Models\NavItem;
use Illuminate\Database\Seeder;

/**
 * Seeds default navigation links and pure software/cloud mega-menu fallbacks.
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
            ['Cloud & Enterprise Systems', [
                ['Enterprise LMS & WebRTC Classrooms', '/work/western-elearning-academy', 'Live virtual lecture halls, proctoring & verified credentials', null, null],
                ['ERP Systems & Payment Gateways', '/work', 'Multi-branch ledgers, invoicing & API integrations', null, null],
                ['Cloud DevOps & Observability', '/work', 'AWS/Cloudflare clusters, CI/CD & sub-200ms SLA engineering', null, null],
            ]],
            ['Featured Case Study', [
                ['High-concurrency EdTech platform serving 45,000+ active students.', '/work/alfagn', null, 'ALFAGN EDTECH · 45K+ STUDENTS', 'assets/img/work/alfagn/alfagn_seo.png'],
            ]],
        ],
        'mega-journal' => [
            ['System Architecture', [
                ['Engineering Arabic-First Design Systems & Resilient SaaS Frontends', '/blog', null, 'DESIGN SYSTEMS · 8 MIN READ', 'assets/img/blog/arabic-first.jpg'],
            ]],
            ['Engineering Episode', [
                ['Inside an Empetix 5-Day Product & Architecture Sprint', '/blog', null, 'VIDEO + TRANSCRIPT · 14:20', 'assets/img/blog/design-sprint.jpg'],
            ]],
            ['Studio Chronicle', [
                ['Behind the Lens: Engineering Studio Operations in New Cairo', '/blog', null, 'STUDIO CHRONICLE · ARCHITECTURE', 'assets/img/blog/behind-the-lens.jpg'],
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
