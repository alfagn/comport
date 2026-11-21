<?php

declare(strict_types=1);

namespace App\Modules\LandlordAdmin\Livewire;

use App\Core\Livewire\BaseComponent;
use App\Modules\CMS\Models\SiteSetting;
use Livewire\Attributes\Url;

/**
 * Class CmsSettings
 *
 * Comprehensive Website Pages, Sections, Hero Carousel, Solutions Hub, and Brand Identity Studio.
 * Controls 100% of every page and section on the public company website.
 */
class CmsSettings extends BaseComponent
{
    #[Url]
    public string $activeTab = 'brand';

    /**
     * All editable website settings across the 7 tabs.
     *
     * @var array<string, string>
     */
    public array $form = [];

    /**
     * Complete default dictionary for every page and section of the website.
     *
     * @return array<string, string>
     */
    public static function defaultSettings(): array
    {
        return [
            // TAB 1: Brand, Header, Mega Menu & Footer
            'site_name' => 'Empetix',
            'site_tagline' => 'Software Engineering & Growth Marketing Studio',
            'logo_dark' => 'assets/img/logo/empetix-logo-dark.png',
            'logo_light' => 'assets/img/logo/empetix-logo.png',
            'announcement_text' => 'Now Partnering with Series A-C Startups & Enterprise Groups Across Cairo, Riyadh & London',
            'ticker_item_1' => 'LARAVEL & MULTI-TENANT ARCHITECTURE',
            'ticker_item_2' => 'FLUTTER 60FPS MOBILE ENGINEERING',
            'ticker_item_3' => 'SERVER-SIDE CONVERSIONS API (CAPI)',
            'ticker_item_4' => 'ARABIC-FIRST (RTL/LTR) DESIGN SYSTEMS',
            'ticker_item_5' => 'FINTECH PAYMENT CORE & HIGH-CONCURRENCY LEDGERS',
            'mega_cap_title' => 'One studio. Code and growth under one roof.',
            'mega_cap_desc' => 'We pair senior software engineers with performance marketers and commercial directors so products ship fast and scale profitably.',
            'mega_journal_title' => 'Articles, video episodes & photo essays.',
            'mega_journal_desc' => 'Field notes from our engineers, product designers, and performance marketers.',
            'footer_banner_title' => 'Tell us about the product you want to build.',
            'footer_banner_sub' => 'Speak directly with our co-founders and engineering leads. We respond to all project briefs within 24 business hours.',
            'footer_cta_primary_text' => 'Start a Project',
            'footer_cta_secondary_text' => 'Careers Portal',
            'footer_summary' => 'Empetix Solutions is a software engineering and performance marketing partner. We build enterprise web & mobile platforms and scale the brands behind them.',
            'footer_newsletter_title' => 'Empetix Dispatch',
            'footer_newsletter_desc' => 'Monthly engineering monographs, video episodes, and growth playbooks.',
            'footer_copyright' => 'Empetix Solutions LLC. All rights reserved.',
            'social_linkedin' => 'https://www.linkedin.com/company/empetix',
            'social_github' => 'https://www.github.com/empetix',
            'social_youtube' => 'https://www.youtube.com/@empetix',
            'social_twitter' => 'https://x.com/empetix',
            'social_instagram' => 'https://www.instagram.com/empetix',

            // TAB 2: Homepage 4-Slide Interactive Hero Carousel
            'hero_tag' => 'SOFTWARE & GROWTH PARTNER',
            'hero_kicker' => 'NEW CAIRO, EGYPT',
            'hero_headline' => 'We build web and mobile software and run growth marketing.',
            'hero_subheadline' => 'Empetix is an end-to-end software engineering and performance marketing partner. Our engineers and marketers work on the same team to build your software and grow your customer base.',
            'hero_bg' => 'assets/img/home/hero.jpg',
            'hero_cta_primary_text' => 'Start a Project',
            'hero_cta_primary_url' => '/contact',
            'hero_cta_secondary_text' => 'Watch Company Overview',
            'hero_cta_secondary_url' => '#who-we-are',
            'hero_tab1_num' => 'OVERVIEW',
            'hero_tab1_title' => 'Software & Growth Studio',
            'hero_tab1_sub' => 'End-to-end digital transformation',

            'hero2_tag' => 'PERFORMANCE MARKETING & CRO',
            'hero2_kicker' => '3.8X AUDITED MEDIAN ROAS',
            'hero2_headline' => 'Paid media, video production, and conversion work for growing brands.',
            'hero2_subheadline' => 'We replace guesswork with server-side Conversions API (CAPI) engineering, in-house commercial film shoots, and bilingual conversion optimization across MENA and Europe.',
            'hero2_bg' => 'assets/img/blog/performance-playbook.jpg',
            'hero2_cta_primary_text' => 'View Growth Case Studies',
            'hero2_cta_primary_url' => '/work',
            'hero2_cta_secondary_text' => 'Inside Our Production Lab',
            'hero2_cta_secondary_url' => '/blog',
            'hero_tab2_num' => 'MARKETING',
            'hero_tab2_title' => 'Performance & CRO Engine',
            'hero_tab2_sub' => '3.8x median ROAS & server-side CAPI',

            'hero3_tag' => 'SUCCESS STORY: NILEPAY',
            'hero3_kicker' => 'FINTECH · 1.4M ACTIVE WALLETS',
            'hero3_headline' => 'How NilePay reached 1.4 million active mobile wallet users.',
            'hero3_subheadline' => 'Pairing a 60fps Flutter mobile wallet and event-sourced Laravel 11 settlement core with a nationwide paid acquisition engine that lowered CAC by 41%.',
            'hero3_bg' => 'assets/img/work/nilepay.jpg',
            'hero3_cta_primary_text' => 'Read Full Case Dossier',
            'hero3_cta_primary_url' => '/work/nilepay',
            'hero3_cta_secondary_text' => 'All Projects',
            'hero3_cta_secondary_url' => '/work',
            'hero_tab3_num' => 'CASE STUDY',
            'hero_tab3_title' => 'NilePay FinTech Platform',
            'hero_tab3_sub' => '1.4M wallets · +310% activation',

            'hero4_tag' => 'STUDIO VIDEO DOCUMENTARY',
            'hero4_kicker' => 'RUNTIME 14:20 · FULL EPISODE',
            'hero4_headline' => 'From product problem to tested prototype in five days.',
            'hero4_subheadline' => 'Watch how our software architects, product designers, and media buyers take a product from commercial bottleneck to user-tested prototype in one week.',
            'hero4_bg' => 'assets/img/blog/design-sprint.jpg',
            'hero4_cta_primary_text' => 'Watch Full Video Episode',
            'hero4_cta_primary_url' => '/blog',
            'hero4_cta_secondary_text' => 'Explore All Blogs',
            'hero4_cta_secondary_url' => '/blog',
            'hero_tab4_num' => 'VIDEO EPISODE',
            'hero_tab4_title' => 'Inside a 5-Day Sprint',
            'hero_tab4_sub' => 'Watch 14:20 studio documentary',

            // TAB 3: "Who We Are" Film & 3 Core Pillars
            'overview_title' => 'Who we are',
            'overview_lead' => 'Empetix Solution brings software engineering, growth marketing, and strategic investment together under one roof in New Cairo. Watch our 40-second overview to see how we build and scale digital businesses.',
            'video_reel_url' => 'assets/video/empetix-overview.mp4',
            'video_poster_url' => 'assets/img/home/overview-poster.jpg',
            'pillar1_title' => 'Software engineering',
            'pillar1_desc' => 'Full-stack web and mobile platforms built for reliability, speed, and long-term scale.',
            'pillar2_title' => 'Growth marketing',
            'pillar2_desc' => 'Engineers and marketers working as one unified team to turn clean data into customer acquisition.',
            'pillar3_title' => 'Strategic investment',
            'pillar3_desc' => 'Backing bold founders with capital, senior technical leadership, and go-to-market execution.',

            // TAB 4: Interactive Solutions Hub (5 Pillars)
            'solutions_heading' => 'End-to-end engineering and growth capabilities.',
            'solutions_lead' => 'Select a practice pillar below to inspect our architecture, deliverables, and verified client outcomes.',
            'solutions_cta_text' => 'Request Custom Scope',

            'sol1_tab_title' => 'Custom Web, SaaS & Cloud APIs',
            'sol1_tab_tag' => 'Engineering',
            'sol1_kicker' => 'PILLAR SOFTWARE ARCHITECTURE',
            'sol1_title' => 'Enterprise Web Platforms, Laravel APIs & Cloud Modernization',
            'sol1_desc' => 'We build modular web platforms, B2B portals, and multi-tenant SaaS products in Laravel 11, Next.js, and AWS—engineered for 99.96% uptime and clean ERP/CRM integration.',
            'sol1_bullets' => "Domain-Driven Laravel & Node.js Microservices\nNext.js / TypeScript SSR & Headless Storefronts\nAWS ECS, RDS & Zero-Downtime CI/CD Pipelines\nAutomated Pest / PHPUnit & Security Audits",
            'sol1_image' => 'assets/img/home/studio.jpg',
            'sol1_metric_num' => '99.96%',
            'sol1_metric_text' => 'Audited production availability across deployed enterprise platforms',
            'sol1_cta_text' => 'Explore Software Architecture',

            'sol2_tab_title' => 'Mobile Apps & FinTech Ledgers',
            'sol2_tab_tag' => 'Flutter / Core',
            'sol2_kicker' => 'PILLAR MOBILE & FINTECH',
            'sol2_title' => '60fps Consumer Mobile Apps & Event-Sourced Payment Ledgers',
            'sol2_desc' => 'From biometric KYC and NFC merchant checkouts to offline-first fleet logistics tablets, our Flutter and native iOS/Android squads build apps used by 1.4M+ monthly active users.',
            'sol2_bullets' => "Flutter & Native Swift / Kotlin Mobile Clients\nDouble-Entry Payment Wallets & Regional Gateways\n90-Second Biometric & OCR Onboarding Flows\nOffline-First SQLite Sync for Field Operations",
            'sol2_image' => 'assets/img/work/nilepay.jpg',
            'sol2_metric_num' => '+310%',
            'sol2_metric_text' => 'First-transaction wallet activation after onboarding rebuild',
            'sol2_cta_text' => 'See NilePay Mobile Case Study',

            'sol3_tab_title' => 'Performance Marketing & Paid Media',
            'sol3_tab_tag' => 'Growth',
            'sol3_kicker' => 'PILLAR GROWTH & ACQUISITION',
            'sol3_title' => 'Full-Funnel Performance Media & Server-Side Attribution',
            'sol3_desc' => 'We wire server-side Conversions API (CAPI) events straight from your backend database into Meta, Google, TikTok, and LinkedIn—bidding on real net revenue instead of vanity clicks.',
            'sol3_bullets' => "Meta, Google PMax, TikTok & Snapchat Media Buying\nFirst-Party Server-Side CAPI & CRM Attribution\nWeekly Creative Hook & Angle Testing Matrices\nCohort LTV:CAC & Payback Period Dashboards",
            'sol3_image' => 'assets/img/blog/performance-playbook.jpg',
            'sol3_metric_num' => '3.8x',
            'sol3_metric_text' => 'Audited median Return on Ad Spend across active growth retainers',
            'sol3_cta_text' => 'Examine Growth Outcomes',

            'sol4_tab_title' => 'Brand Systems, Film & Photography',
            'sol4_tab_tag' => 'Creative',
            'sol4_kicker' => 'PILLAR BRAND & PRODUCTION',
            'sol4_title' => 'Bilingual Brand Systems, Commercial Film & 35mm Photography',
            'sol4_desc' => 'Our in-house cinema and design unit crafts authoritative Arabic/English brand identities, product launch films, and high-converting paid-social video assets.',
            'sol4_bullets' => "Arabic & Latin Brand Identity & Design Guidelines\nIn-House Commercial Film & Documentary Crew\nProduct Tabletop & Editorial Lifestyle Photography\n9:16 Paid Social Video Hook Variations",
            'sol4_image' => 'assets/img/blog/behind-the-lens.jpg',
            'sol4_metric_num' => '+190%',
            'sol4_metric_text' => 'Lift in recurring D2C subscription orders post-campaign launch',
            'sol4_cta_text' => 'View Production Essays',

            'sol5_tab_title' => 'Conversion Optimization (CRO) & AI',
            'sol5_tab_tag' => 'Data / AI',
            'sol5_kicker' => 'PILLAR CRO & AI AUTOMATION',
            'sol5_title' => 'Arabic-First Conversion Optimization & AI Workflow Agents',
            'sol5_desc' => 'We eliminate checkout friction through native RTL/LTR design systems, multivariate A/B testing, and domain-trained AI assistants that automate customer triage and internal operations.',
            'sol5_bullets' => "Bilingual Checkout & Onboarding CRO Experiments\n100% CSS Logical Property Design Systems\nDomain-Trained RAG AI Copilots & Support Triage\nExecutive BigQuery & Looker Telemetry Pipelines",
            'sol5_image' => 'assets/img/blog/arabic-first.jpg',
            'sol5_metric_num' => '+38.4%',
            'sol5_metric_text' => 'Mobile checkout completion lift on Arabic-language sessions',
            'sol5_cta_text' => 'Read CRO Analysis',

            // TAB 5: Impact Stats, Section Headings & Careers CTA Banner
            'home_work_heading' => 'Platforms built to perform. Campaigns built to convert.',
            'home_impact_heading' => 'Measurable engineering velocity and commercial return.',
            'stats_delivered' => '140+',
            'stats_delivered_label' => 'Software platforms & growth engines shipped',
            'stats_ad_spend' => '3.8x',
            'stats_ad_spend_label' => 'Audited median return on ad spend across active retainers',
            'stats_uptime' => '99.96%',
            'stats_uptime_label' => 'Production uptime across client SaaS and FinTech applications',
            'stats_rating' => '4.9/5',
            'stats_rating_label' => 'Verified client satisfaction across multi-year engagements',
            'home_founders_heading' => 'The founders',
            'home_founders_lead' => 'Every engagement at Empetix Solution is led directly by our co-founders across executive strategy, operations, and software engineering.',
            'home_partners_heading' => 'Trusted by category leaders and global technology alliances.',
            'home_testimonials_heading' => 'What chief executives and product leaders say about Empetix.',
            'home_blog_heading' => 'Engineering essays, video episodes & behind-the-scenes dispatches.',
            'home_careers_eyebrow' => '07 / Careers Portal · jobs.empetix.com',
            'home_careers_title' => 'Do the sharpest work of your career without the agency theatre.',
            'home_careers_desc' => 'Explore open roles in Laravel, Next.js, Flutter, Product Design, and Performance Marketing on our dedicated talent portal.',
            'home_careers_btn' => 'Explore Careers (Open Roles)',
            'home_careers_bg' => 'assets/img/careers/culture.jpg',

            // TAB 6: About Page, Operating Principles, Timeline & Global Offices
            'about_hero_eyebrow' => 'Established 2018 · Cairo',
            'about_hero_title' => 'Built by engineers and storytellers who got tired of the hand-off gap.',
            'about_hero_lead' => 'Empetix Solution was founded in New Cairo by our co-founders uniting executive strategy, operational excellence, and software engineering under one roof—helping ambitious businesses build technology, scale growth, and grow sustainably.',
            'about_hero_badge' => '48 SPECIALISTS · 3 STUDIOS',
            'about_principles_heading' => 'How we work when the stakes are high.',
            'about_p1_title' => 'Code and campaigns share one scoreboard.',
            'about_p1_text' => 'We don’t measure engineering in story points and marketing in impressions. Both teams are accountable for activation, retention, and net revenue.',
            'about_p2_title' => 'Senior principals on the tools.',
            'about_p2_text' => 'We don’t bait-and-switch with senior pitch decks and junior execution. Our founders and staff engineers write production code and direct campaigns daily.',
            'about_p3_title' => 'Arabic and English as equal first-class citizens.',
            'about_p3_text' => 'We engineer bidirectional RTL/LTR interfaces and bilingual brand systems natively—never as a rushed translation layer at the end.',
            'about_p4_title' => 'Full IP ownership and clean handovers.',
            'about_p4_text' => 'Every line of Laravel/React code, Figma token, ad account, and raw camera file belongs to our client, documented so your internal team can run with it.',
            'about_timeline_heading' => 'Eight years of compounding craft.',
            'about_t1_year' => '2018',
            'about_t1_title' => 'Founded in Downtown Cairo',
            'about_t1_text' => 'Launched Empetix in Cairo, shipping FinTech and e-commerce platforms in year one.',
            'about_t2_year' => '2021',
            'about_t2_title' => 'In-House Film & Performance Lab',
            'about_t2_text' => 'Added a dedicated commercial film crew, photo studio, and server-side attribution practice to close the loop between software launches and customer acquisition.',
            'about_t3_year' => '2024',
            'about_t3_title' => 'Riyadh Studio & London Desk',
            'about_t3_text' => 'Expanded on-the-ground engineering and growth squads to Riyadh and London to support GCC and European enterprise clients.',
            'about_t4_year' => '2026',
            'about_t4_title' => '48 Specialists & 140+ Shipped Products',
            'about_t4_text' => 'Operating across 14 countries with audited client platforms processing over USD 1.9B in annual gross transaction volume.',
            'contact_email' => 'hello@empetix.com',
            'contact_phone' => '+20 2 2792 8400',
            'contact_address' => 'Building 14, District 5 Campus, New Cairo, Cairo, Egypt',
            'office_cairo' => 'Building 14, District 5 Campus, New Cairo, Cairo, Egypt',
            'office_riyadh' => 'Digital City, Building C3, Level 4, Riyadh, Saudi Arabia',
            'office_london' => '1 Canada Square, Level 24, Canary Wharf, London, UK',

            // TAB 7: Inner Pages (Work, Blog, Team, Careers, Partners, Contact)
            'work_hero_eyebrow' => 'Portfolio 2023–2026',
            'work_hero_title' => 'Selected software & growth case studies.',
            'work_hero_lead' => 'Every project below pairs production software engineering with measurable commercial growth. Click any project to inspect the full architecture, creative assets, and business outcomes.',
            'blog_hero_eyebrow' => 'Studio Publications',
            'blog_hero_title' => 'The Empetix Journal: Engineering, Growth, Video & Photo Essays.',
            'blog_hero_lead' => 'Long-form technical articles, full video episodes with chapter transcripts, and behind-the-scenes photography galleries from our software and marketing teams.',
            'team_hero_title' => 'The people behind Empetix',
            'team_hero_lead' => 'Engineers, product designers, and performance marketers working as one multidisciplinary team from New Cairo—without agency layers or external hand-offs.',
            'team_hero_photo' => 'assets/img/about/office.jpg',
            'careers_hero_eyebrow' => 'Empetix Talent Portal · jobs.empetix.com',
            'careers_hero_title' => 'Build software that endures and campaigns that move numbers.',
            'careers_hero_lead' => 'Welcome to our dedicated careers portal. Explore open engineering, product design, and performance marketing roles across our New Cairo studios—or apply remotely across EMEA.',
            'careers_stat_turnaround' => '48h',
            'careers_stat_leave' => '28d',
            'careers_ben1_title' => 'Top-Tier Hardware',
            'careers_ben1_desc' => 'Latest Apple M3 Max MacBook Pro or high-end Linux workstations with 4K displays.',
            'careers_ben2_title' => 'Flexible Remote / Hybrid',
            'careers_ben2_desc' => 'Work from our sunny New Cairo studio or anywhere across EMEA timezones.',
            'careers_ben3_title' => 'Learning & Growth',
            'careers_ben3_desc' => 'Annual USD 2,000 personal budget for conferences, Laracasts, books, and courses.',
            'careers_ben4_title' => 'Premium Health Cover',
            'careers_ben4_desc' => '100% employer-funded comprehensive health insurance for you and your dependents.',
            'partners_hero_eyebrow' => 'Enterprise Ecosystem',
            'partners_hero_title' => 'Technology alliances and corporate partners.',
            'partners_hero_lead' => 'Empetix works directly alongside leading global cloud providers, payment networks, and growth infrastructure platforms to architect resilient digital operations.',
            'partners_cta_title' => 'Become an Alliance Partner',
            'partners_cta_lead' => 'We collaborate with software vendors, venture studios, and cloud providers across EMEA.',
            'contact_hero_eyebrow' => 'Direct Executive Access',
            'contact_hero_title' => 'Start a project, request a technical audit, or partner with our studios.',
            'contact_hero_lead' => 'At Empetix, you never speak with junior sales reps. Every inquiry goes directly to our co-founders and practice heads in New Cairo.',
            'contact_hero_badge' => 'AVG RESPONSE: < 6 BUSINESS HOURS',
            'contact_desk1_title' => 'Software & Platform Architecture',
            'contact_desk1_desc' => 'For web platforms, mobile apps, multi-tenant SaaS architecture, cloud infrastructure, and technical code audits.',
            'contact_desk1_email' => 'hello@empetix.com',
            'contact_desk2_title' => 'Performance Marketing & Media',
            'contact_desk2_desc' => 'For full-funnel media buying, server-side CAPI attribution, conversion rate optimization, and creative shoots.',
            'contact_desk2_email' => 'growth@empetix.com',
            'contact_desk3_title' => 'Engineering & Creative Talent',
            'contact_desk3_desc' => 'For senior engineers, product designers, filmmakers, and performance marketers joining our studios.',
            'contact_promise_title' => 'Direct founder review within 24 hours.',
        ];
    }

    public function mount(): void
    {
        SiteSetting::flushCache();
        $defaults = static::defaultSettings();
        $this->form = [];

        foreach ($defaults as $key => $defaultVal) {
            $this->form[$key] = (string) SiteSetting::get($key, $defaultVal);
        }
    }

    public function setTab(string $tab): void
    {
        $this->activeTab = $tab;
    }

    public function save(): void
    {
        foreach ($this->form as $key => $value) {
            SiteSetting::set((string) $key, (string) $value, 'cms');
        }

        // Also keep legacy aliases in sync
        SiteSetting::set('announcement_ticker', $this->form['announcement_text'] ?? '', 'cms');
        SiteSetting::set('stat_projects', $this->form['stats_delivered'] ?? '140+', 'stats');
        SiteSetting::set('stat_revenue', $this->form['stats_ad_spend'] ?? '3.8x', 'stats');
        SiteSetting::set('stat_uptime', $this->form['stats_uptime'] ?? '99.96%', 'stats');
        SiteSetting::set('stat_rating', $this->form['stats_rating'] ?? '4.9/5', 'stats');

        SiteSetting::flushCache();

        $this->toastSuccess(
            app()->getLocale() === 'ar'
                ? 'تم حفظ وتحديث جميع محتويات وأقسام الموقع الحي بنجاح!'
                : 'All website pages, sections, and settings saved and published live!'
        );
    }

    public function render()
    {
        return view('admin::livewire.cms-settings')->layout('layouts.admin', [
            'title' => 'Website Pages, Hero & Sections Studio — Empetix CMS',
            'header' => app()->getLocale() === 'ar' ? 'استوديو التحكم الكامل بصفحات وأقسام الموقع' : 'Website Pages, Hero & Sections Studio',
        ]);
    }
}
