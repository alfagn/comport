<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Replace marketing services with real Empetix software, LMS/WebRTC, ERP & Cloud/AI services
        if (Schema::hasTable('services')) {
            DB::table('services')->where('title', 'like', '%Performance Marketing%')->update([
                'title' => 'Enterprise LMS & WebRTC Platforms',
                'slug' => 'enterprise-lms-webrtc',
                'tag' => 'EdTech & Live Streaming',
                'kicker' => 'PILLAR 03 · EDTECH & WEBRTC',
                'heading' => 'High-Concurrency Learning Platforms, Proctored Exams & Secure Streaming',
                'description' => 'We architect multi-tenant academies, interactive live WebRTC classrooms, Redis-buffered exam engines, and DRM/forensic video protection serving tens of thousands of concurrent learners.',
                'deliverables' => json_encode([
                    'Interactive WebRTC Virtual Classrooms & Live Whiteboards',
                    'High-Concurrency Timed Exam & Assessment Engines',
                    'Forensic Watermarking & Encrypted Video Streaming',
                    'Automated QR-Verified Academic Credential Issuance',
                ]),
                'metric_num' => '45K+',
                'metric_label' => '45,000+ active students served with sub-180ms exam response latency',
                'cta_label' => 'Explore Alfagn & Western Case Studies',
                'cta_url' => '/work',
            ]);

            DB::table('services')->where('title', 'like', '%Brand Systems%')->update([
                'title' => 'ERP Systems & Payment Integrations',
                'slug' => 'erp-systems',
                'tag' => 'ERP & FinTech',
                'kicker' => 'PILLAR 04 · ENTERPRISE CORE',
                'heading' => 'Custom ERP Modules, Multi-Currency Ledgers & API Gateways',
                'description' => 'Our backend architects build auditable financial cores, automated invoicing, inventory orchestration, and seamless integrations across MENA & global payment rails.',
                'deliverables' => json_encode([
                    'Modular ERP, Inventory & Multi-Branch Workflows',
                    'Double-Entry Payment Ledgers & Automated Reconciliation',
                    'Regional Payment Gateways (Paymob, Moyasar, Tap, Stripe)',
                    'Zero-Downtime Legacy Database Migration & API Bridges',
                ]),
                'metric_num' => '99.98%',
                'metric_label' => '99.98% audited production uptime across enterprise transactional systems',
                'cta_label' => 'Start Architecture Brief',
                'cta_url' => '/contact',
            ]);

            DB::table('services')->where('title', 'like', '%Conversion Optimization%')->orWhere('slug', 'ai-cloud-devops')->update([
                'title' => 'AI Automation & Data Pipelines',
                'slug' => 'ai-data-pipelines',
                'tag' => 'Cloud & AI',
                'kicker' => 'PILLAR 05 · CLOUD & AI',
                'heading' => 'Production AI Workflows, Cloud Infrastructure & Real-Time Observability',
                'description' => 'We deploy resilient cloud clusters, automated CI/CD pipelines, intelligent internal copilots, and real-time telemetry engineered for security and predictable scaling.',
                'deliverables' => json_encode([
                    'AWS, Cloudflare & Bare-Metal High-Availability Clusters',
                    'Automated CI/CD Pipelines, Zero-Downtime Releases & Backups',
                    'LLM Copilots, RAG Knowledge Engines & Workflow Automation',
                    'Full-Stack Telemetry, Tracing & SLA Monitoring',
                ]),
                'metric_num' => '<180ms',
                'metric_label' => 'Sub-180ms p95 API latency under peak concurrent production load',
                'cta_label' => 'Explore Engineering Capabilities',
                'cta_url' => '/work',
            ]);
        }

        // 2. Replace "Performance marketing" ServicePage title/body with Cloud & AI Architecture (keeping slug/count intact for unpublished archive routes)
        if (Schema::hasTable('service_pages')) {
            DB::table('service_pages')
                ->where('slug', 'performance-marketing')
                ->where('locale', 'en')
                ->update([
                    'title' => 'Cloud, DevOps and AI systems',
                    'summary' => 'High-availability cloud architecture, automated CI/CD delivery pipelines, and production AI workflow integrations.',
                    'meta_title' => 'Cloud, DevOps & AI Systems | Empetix Solutions',
                    'meta_description' => 'Enterprise cloud infrastructure, DevOps automation, and AI systems engineered by Empetix.',
                ]);
        }

        // 3. Clean Hero Slides (remove marketing slide & update copy to real software platforms)
        if (Schema::hasTable('hero_slides')) {
            DB::table('hero_slides')->where('tag', 'like', '%SOFTWARE & GROWTH%')->update([
                'tag' => 'ENTERPRISE SOFTWARE & CLOUD STUDIO',
                'title' => 'We architect high-concurrency web, mobile, and cloud platforms engineered for scale.',
            ]);

            DB::table('hero_slides')->where('tag', 'like', '%PERFORMANCE MARKETING%')->update([
                'tag' => 'SUCCESS STORY: WESTERN ACADEMY',
                'title' => 'Architecting multi-tenant WebRTC classrooms and verified credentials across MENA & UK.',
                'description' => 'Interactive virtual lecture halls, AI-proctored certification exams, and automated QR credential verification.',
                'tab_label' => 'WESTERN ACADEMY',
                'tab_title' => 'Enterprise WebRTC LMS',
                'tab_sub' => 'Live classrooms & verified credentials',
                'cta_label' => 'Read Western Case Study',
                'cta_url' => '/work/western-elearning-academy',
            ]);
        }

        // 4. Clean Mega-Menu NavItems in DB
        if (Schema::hasTable('nav_items')) {
            $mktCols = DB::table('nav_items')
                ->where('menu', 'mega-capabilities')
                ->whereNull('parent_id')
                ->where('label', 'like', '%Marketing%')
                ->pluck('id');

            if ($mktCols->isNotEmpty()) {
                DB::table('nav_items')->whereIn('parent_id', $mktCols)->delete();
                DB::table('nav_items')->whereIn('id', $mktCols)->update([
                    'label' => 'Cloud & Enterprise Systems',
                ]);
            }

            DB::table('nav_items')
                ->where('menu', 'mega-capabilities')
                ->where(function ($q) {
                    $q->where('label', 'like', '%Paid Acquisition%')
                        ->orWhere('label', 'like', '%Campaign Production%')
                        ->orWhere('label', 'like', '%CRO, SEO%')
                        ->orWhere('sub', 'like', '%ROAS%');
                })
                ->delete();
        }

        // 5. Clean Job Posts (replace Growth & Marketing roles with Cloud/DevOps & AI Engineering roles)
        if (Schema::hasTable('job_posts')) {
            DB::table('job_posts')->where('department', 'like', '%Marketing%')->where('slug', 'paid-media-lead')->update([
                'title' => 'Principal Cloud & DevOps Infrastructure Engineer',
                'slug' => 'cloud-devops-lead',
                'department' => 'Cloud & DevOps',
                'summary' => 'Architect high-availability Linux/AWS clusters, Redis queues, Cloudflare edge security, and zero-downtime CI/CD pipelines.',
            ]);

            DB::table('job_posts')->where('department', 'like', '%Marketing%')->update([
                'title' => 'Senior AI & Distributed Data Systems Engineer',
                'department' => 'Cloud & DevOps',
                'summary' => 'Design production LLM workflows, high-throughput telemetry pipelines, and automated background processing engines.',
            ]);

            DB::table('job_posts')->where('title', 'like', '%CRO%')->update([
                'title' => 'Senior Product Designer (Bilingual SaaS & Design Systems)',
            ]);
        }

        // 6. Clean Blog Posts categories/titles referencing CRO or Campaign shoots
        if (Schema::hasTable('posts')) {
            DB::table('posts')->where('category', 'like', '%CRO%')->update([
                'category' => 'DESIGN SYSTEMS & ARCHITECTURE',
                'title' => 'Engineering Arabic-First Design Systems & Resilient SaaS Frontends',
            ]);
            DB::table('posts')->where('category', 'like', '%CAMPAIGN%')->update([
                'category' => 'SYSTEM ARCHITECTURE',
                'title' => 'Inside Empetix Engineering: High-Concurrency LMS & Cloud Operations',
            ]);
            DB::table('posts')->where('title', 'like', '%Growth Sprint%')->update([
                'title' => 'Inside an Empetix 5-Day Product & Architecture Sprint',
            ]);
        }

        // 7. Clean Partners & Team Member roles referencing marketing
        if (Schema::hasTable('partners')) {
            DB::table('partners')->where('name', 'like', '%Meta Business%')->update([
                'name' => 'Cloudflare Enterprise Edge',
            ]);
            DB::table('partners')->where('name', 'like', '%HubSpot%')->update([
                'name' => 'Redis & Distributed Data',
            ]);
        }

        if (Schema::hasTable('members')) {
            DB::table('members')->where('department', 'marketing')->update(['department' => 'engineering']);
            DB::table('members')->where('role', 'like', '%marketing%')->update([
                'role' => 'Staff QA & Reliability Engineer',
                'focus' => 'Automated testing, load benchmarking, SLA reliability',
            ]);
            DB::table('members')->where('role', 'like', '%film producer%')->update([
                'role' => 'Platform Security Engineer',
                'focus' => 'API security, DRM streaming protection, OWASP audits',
            ]);
            DB::table('members')->where('role', 'like', '%attribution%')->update([
                'role' => 'Data & Observability Engineer',
                'focus' => 'Distributed tracing, telemetry pipelines, PostgreSQL tuning',
            ]);
            DB::table('members')->where('role', 'like', '%SEO%')->update([
                'role' => 'Technical Documentation & API Architect',
                'focus' => 'OpenAPI specs, SDK architecture, developer portals',
            ]);
        }

        // 8. Clean Content List Items
        if (Schema::hasTable('content_list_items')) {
            DB::table('content_list_items')->where('group', 'about.pillars')->where('title', 'like', '%marketing%')->update([
                'title' => 'Cloud & AI architecture',
                'body' => 'High-concurrency infrastructure, WebRTC streaming, automated CI/CD, and intelligent enterprise workflows engineered for uptime.',
            ]);
            DB::table('content_list_items')->where('group', 'about.principles')->where('title', 'like', '%campaigns%')->update([
                'title' => 'Architecture and user experience share one scoreboard.',
                'body' => 'Backend engineers, mobile developers, and product designers work together from day one around real latency, reliability, and usability metrics.',
            ]);
            DB::table('content_list_items')->where('group', 'careers.tracks')->where('title', 'like', '%Marketing%')->update([
                'title' => 'Cloud, DevOps & AI Systems',
                'body' => 'Linux clusters, Docker/Kubernetes, Cloudflare Stream, Redis queues, and production AI pipelines.',
            ]);
            DB::table('content_list_items')->where('group', 'about.timeline')->where('title', 'like', '%Marketing%')->update([
                'title' => 'Founded Around Enterprise Software & Cloud Engineering',
                'body' => 'Established in New Cairo to architect resilient SaaS platforms, EdTech ecosystems, and mission-critical mobile applications.',
            ]);
        }

        // 9. Clean Page Sections (`sec()` stored values) containing legacy marketing phrases
        if (Schema::hasTable('page_sections')) {
            $replacements = [
                'software engineering and performance marketing' => 'software engineering, SaaS, and cloud architecture',
                'software engineering, growth marketing,' => 'software engineering, cloud architecture,',
                'software engineering and growth marketing' => 'software engineering and cloud architecture',
                'Software Engineering & Growth Marketing' => 'Software Engineering & Cloud Architecture',
                'SOFTWARE ENGINEERING · GROWTH MARKETING' => 'SOFTWARE ENGINEERING · CLOUD & AI ARCHITECTURE',
                'performance marketers and commercial directors' => 'cloud architects and product designers',
                'and performance marketers' => 'and cloud infrastructure architects',
                'and performance marketing' => 'and cloud infrastructure',
                'high-ROAS growth engines' => 'high-availability cloud engines',
                'Performance Marketing' => 'Cloud & AI Engineering',
                'performance marketing' => 'cloud architecture',
                'Growth Marketing' => 'Cloud Architecture',
                'growth marketing' => 'cloud architecture',
                'marketers' => 'cloud engineers',
                'marketing' => 'cloud engineering',
            ];

            $sections = DB::table('page_sections')->get(['id', 'value']);
            foreach ($sections as $sec) {
                if (! is_string($sec->value) || $sec->value === '') {
                    continue;
                }
                $cleaned = str_replace(array_keys($replacements), array_values($replacements), $sec->value);
                if ($cleaned !== $sec->value) {
                    DB::table('page_sections')->where('id', $sec->id)->update(['value' => $cleaned]);
                }
            }
        }

        // Flush cached menus, sections, lists, and SEO discovery docs
        Cache::forget('page_sections.map');
        foreach (['utility', 'drawer', 'mega-capabilities', 'mega-journal'] as $menu) {
            Cache::forget('nav.menu.'.$menu);
        }
        if (class_exists(\App\Services\ContentListItemService::class)) {
            foreach (array_keys(\App\Services\ContentListItemService::GROUPS) as $group) {
                Cache::forget('content-list.'.$group);
            }
        }
        Cache::forget('seo_sitemap_xml');
        Cache::forget('seo_llms_txt');
        Cache::forget('seo_llms_full_txt');
    }

    public function down(): void
    {
        // Data normalization migration; reversible via CMS dashboard.
    }
};
