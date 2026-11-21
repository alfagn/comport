<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Modules\Billing\Models\Plan;
use App\Modules\CMS\Models\Category;
use App\Modules\CMS\Models\ContactMessage;
use App\Modules\CMS\Models\JobApplication;
use App\Modules\CMS\Models\JobPosting;
use App\Modules\CMS\Models\Partner;
use App\Modules\CMS\Models\PortfolioProject;
use App\Modules\CMS\Models\Post;
use App\Modules\CMS\Models\SiteSetting;
use App\Modules\CMS\Models\TeamMember;
use App\Modules\CMS\Models\Testimonial;
use App\Modules\CRM\Models\CrmClient;
use App\Modules\CRM\Models\CrmDeal;
use App\Modules\CRM\Models\CrmLead;
use App\Modules\Finance\Models\Invoice;
use App\Modules\Finance\Models\InvoiceItem;
use App\Modules\Finance\Models\Payment;
use App\Modules\HRM\Models\Attendance;
use App\Modules\HRM\Models\Department;
use App\Modules\HRM\Models\Designation;
use App\Modules\HRM\Models\Employee;
use App\Modules\HRM\Models\LeaveType;
use App\Modules\HRM\Models\Payroll;
use App\Modules\HRM\Models\PayrollItem;
use App\Modules\LandlordAdmin\Models\AdminUser;
use App\Modules\Marketing\Models\MarketingCampaign;
use App\Modules\Marketing\Models\MarketingClient;
use App\Modules\Marketing\Models\MarketingKpiLog;
use App\Modules\Projects\Models\Project;
use App\Modules\Projects\Models\Task;
use App\Modules\Tenancy\DTOs\ProvisionTenantData;
use App\Modules\Tenancy\Models\Tenant;
use App\Modules\Tenancy\Pipelines\TenantProvisioningPipeline;
use App\Modules\TenantCore\Models\CompanySetting;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(TenantProvisioningPipeline $pipeline): void
    {
        $this->command->info('Seeding Landlord Super Admin & Plans...');

        // 1. Super Admin
        AdminUser::updateOrCreate(
            ['email' => 'admin@empetix.com'],
            [
                'name' => 'Mohanad Super Admin',
                'password' => Hash::make('password'),
                'role' => 'super_admin',
                'is_active' => true,
            ]
        );

        // 2. SaaS Plans
        $starterPlan = Plan::updateOrCreate(
            ['slug' => 'starter'],
            [
                'name' => 'Starter ERP',
                'description' => 'Perfect for small teams and startups starting out.',
                'price_monthly_minor' => 149900, // 1,499 EGP
                'price_yearly_minor' => 1499000,
                'currency' => 'EGP',
                'trial_days' => 14,
                'features' => ['hr', 'finance'],
                'limits' => [
                    'max_users' => 5,
                    'max_employees' => 15,
                    'max_projects' => 5,
                ],
                'is_active' => true,
                'sort_order' => 1,
            ]
        );

        $proPlan = Plan::updateOrCreate(
            ['slug' => 'professional'],
            [
                'name' => 'Professional Growth',
                'description' => 'For growing software houses and established agencies.',
                'price_monthly_minor' => 349900, // 3,499 EGP
                'price_yearly_minor' => 3499000,
                'currency' => 'EGP',
                'trial_days' => 14,
                'features' => ['hr', 'crm', 'projects', 'finance'],
                'limits' => [
                    'max_users' => 25,
                    'max_employees' => 50,
                    'max_projects' => 30,
                ],
                'is_active' => true,
                'is_featured' => true,
                'sort_order' => 2,
            ]
        );

        $enterprisePlan = Plan::updateOrCreate(
            ['slug' => 'enterprise-agency'],
            [
                'name' => 'Enterprise & Full Agency',
                'description' => 'Complete suite for marketing agencies and multi-team software companies.',
                'price_monthly_minor' => 699900, // 6,999 EGP
                'price_yearly_minor' => 6999000,
                'currency' => 'EGP',
                'trial_days' => 14,
                'features' => ['hr', 'crm', 'marketing', 'projects', 'finance'],
                'limits' => [
                    'max_users' => 0, // unlimited
                    'max_employees' => 0,
                    'max_projects' => 0,
                ],
                'is_active' => true,
                'sort_order' => 3,
            ]
        );

        // 3. CMS Site Settings
        $this->command->info('Seeding CMS Content for Empetix...');

        SiteSetting::set('site_name', 'Empetix', 'general');
        SiteSetting::set('site_tagline', 'Empowering Digital Ventures & Software Solutions', 'general');
        SiteSetting::set('contact_email', 'contact@empetix.com', 'contact');
        SiteSetting::set('contact_phone', '+20 100 000 0000', 'contact');
        SiteSetting::set('contact_address', 'New Cairo, Egypt', 'contact');
        SiteSetting::set('hero_headline', 'We Build Scalable Software & Elevate Marketing Brands', 'home');
        SiteSetting::set('hero_subheadline', 'Empetix delivers high-impact software engineering, performance marketing, and enterprise SaaS solutions designed for sustainable growth.', 'home');

        // CMS Categories
        $catSoftware = Category::updateOrCreate(['slug' => 'software-engineering'], ['name' => 'Software Engineering', 'type' => 'blog']);
        $catMarketing = Category::updateOrCreate(['slug' => 'growth-marketing'], ['name' => 'Growth & Marketing', 'type' => 'blog']);
        $catFintech = Category::updateOrCreate(['slug' => 'fintech'], ['name' => 'FinTech & Payments', 'type' => 'portfolio']);

        // Team & Founders
        TeamMember::updateOrCreate(['slug' => 'mohanad'], [
            'name' => 'Mohanad Shehata',
            'role_title' => 'Founder & Chief Executive Officer',
            'bio' => 'Enterprise software architect and product strategist driving large-scale distributed platforms and high-growth ventures.',
            'avatar' => 'assets/img/team/mohanad-shehata.jpg',
            'is_founder' => true,
            'social_links' => ['linkedin' => 'https://linkedin.com/in/mohanad-shehata', 'github' => 'https://github.com/mohanad-shehata'],
            'sort_order' => 1,
        ]);

        TeamMember::updateOrCreate(['slug' => 'ayman'], [
            'name' => 'Ayman Ibrahim',
            'role_title' => 'Co-Founder & Chief Technology Officer',
            'bio' => 'Principal backend engineer specializing in high-concurrency Laravel microservices, multi-database architecture, and cloud infrastructure.',
            'avatar' => 'assets/img/team/ayman-ibrahim.jpg',
            'is_founder' => true,
            'social_links' => ['linkedin' => 'https://linkedin.com/in/ayman-ibrahim'],
            'sort_order' => 2,
        ]);

        TeamMember::updateOrCreate(['slug' => 'bishoy'], [
            'name' => 'Bishoy Magdy',
            'role_title' => 'Co-Founder & Chief Creative Officer',
            'bio' => 'Design systems architect and creative director shaping modern digital brands, interaction design, and high-converting user interfaces.',
            'avatar' => 'assets/img/team/bishoy-magdy.jpg',
            'is_founder' => true,
            'social_links' => ['linkedin' => 'https://linkedin.com/in/bishoy-magdy'],
            'sort_order' => 3,
        ]);

        TeamMember::updateOrCreate(['slug' => 'farida-zaki'], [
            'name' => 'Farida Zaki',
            'role_title' => 'Lead Product Designer',
            'bio' => 'Expert in UX research, design systems, and crafting responsive web & mobile application interfaces.',
            'avatar' => 'assets/img/team/farida-zaki.jpg',
            'is_founder' => false,
            'social_links' => ['linkedin' => 'https://linkedin.com'],
            'sort_order' => 4,
        ]);

        TeamMember::updateOrCreate(['slug' => 'ahmed-elgamal'], [
            'name' => 'Ahmed Elgamal',
            'role_title' => 'Principal Backend Engineer',
            'bio' => 'Deep mastery in Laravel, event-driven pipelines, caching strategies, and database optimization.',
            'avatar' => 'assets/img/team/ahmed-elgamal.jpg',
            'is_founder' => false,
            'social_links' => ['github' => 'https://github.com'],
            'sort_order' => 5,
        ]);

        TeamMember::updateOrCreate(['slug' => 'mariam-nabil'], [
            'name' => 'Mariam Nabil',
            'role_title' => 'VP of Growth & Performance Marketing',
            'bio' => 'Data-driven growth strategist leading multi-channel acquisition funnels with high-ROAS returns across GCC & EMEA.',
            'avatar' => 'assets/img/team/mariam-nabil.jpg',
            'is_founder' => false,
            'social_links' => ['linkedin' => 'https://linkedin.com'],
            'sort_order' => 6,
        ]);

        // Job Postings
        $jobs = [
            [
                'title' => 'Senior Laravel Engineer',
                'slug' => 'senior-laravel-engineer',
                'department' => 'Software Engineering',
                'location' => 'Cairo / Hybrid',
                'employment_type' => 'full_time',
                'salary_range' => 'Competitive + Equity',
                'description' => 'Architect and scale multi-tenant SaaS backends using clean architecture, event-driven pipelines, and high-performance databases.',
                'requirements' => '5+ years Laravel/PHP experience, strong understanding of Clean Architecture, DDD, and performance tuning.',
            ],
            [
                'title' => 'Flutter Mobile Developer',
                'slug' => 'flutter-developer',
                'department' => 'Mobile Engineering',
                'location' => 'Cairo / Remote',
                'employment_type' => 'full_time',
                'salary_range' => 'Competitive',
                'description' => 'Develop responsive cross-platform iOS and Android applications with state-of-the-art animations and offline sync capabilities.',
                'requirements' => '3+ years experience with Flutter, BLoC/Riverpod, and REST/GraphQL APIs.',
            ],
            [
                'title' => 'Senior Frontend Engineer',
                'slug' => 'senior-frontend-engineer',
                'department' => 'Frontend Engineering',
                'location' => 'Cairo / Remote',
                'employment_type' => 'full_time',
                'salary_range' => 'Competitive',
                'description' => 'Craft lightning-fast user experiences using modern Tailwind, Alpine.js, Blade, and component design systems.',
                'requirements' => '4+ years building high-traffic, accessible web interfaces.',
            ],
            [
                'title' => 'Senior UI/UX Designer',
                'slug' => 'ui-ux-designer',
                'department' => 'Design & Creative',
                'location' => 'Cairo / Hybrid',
                'employment_type' => 'full_time',
                'salary_range' => 'Competitive',
                'description' => 'Design comprehensive design systems, user flows, and wireframes for web applications and client dashboards.',
                'requirements' => 'Proficiency in Figma, design tokens, and human-centered design principles.',
            ],
            [
                'title' => 'Performance Marketing Lead',
                'slug' => 'performance-marketing-lead',
                'department' => 'Growth & Marketing',
                'location' => 'Cairo / Hybrid',
                'employment_type' => 'full_time',
                'salary_range' => 'Competitive + Bonus',
                'description' => 'Manage multi-channel paid acquisition campaigns across Meta, Google Ads, and TikTok with high ROI and low CAC.',
                'requirements' => 'Proven track record of managing $50k+ monthly ad spend with measurable conversion rates.',
            ],
            [
                'title' => 'Media & Video Producer',
                'slug' => 'media-video-producer',
                'department' => 'Media & Creative',
                'location' => 'Cairo / Hybrid',
                'employment_type' => 'full_time',
                'salary_range' => 'Competitive',
                'description' => 'Produce engaging video reels, motion graphics, and product demos showcasing client stories and brand narratives.',
                'requirements' => 'Expertise in Premiere Pro, After Effects, and creative storytelling.',
            ],
        ];

        $createdJobs = [];
        foreach ($jobs as $job) {
            $createdJobs[$job['slug']] = JobPosting::updateOrCreate(['slug' => $job['slug']], $job);
        }

        // Job Applications
        if (isset($createdJobs['senior-laravel-engineer'])) {
            JobApplication::updateOrCreate(
                ['candidate_email' => 'kareem.dev@example.com'],
                [
                    'job_posting_id' => $createdJobs['senior-laravel-engineer']->id,
                    'candidate_name' => 'Kareem Adel',
                    'candidate_phone' => '+20 101 234 5678',
                    'portfolio_url' => 'https://github.com/kareem-adel',
                    'cover_letter' => 'I have 6 years building high-scale Laravel platforms and microservices with clean architecture. Excited about Empetix!',
                    'status' => 'new',
                ]
            );
        }

        if (isset($createdJobs['ui-ux-designer'])) {
            JobApplication::updateOrCreate(
                ['candidate_email' => 'nour.designer@example.com'],
                [
                    'job_posting_id' => $createdJobs['ui-ux-designer']->id,
                    'candidate_name' => 'Nour El-Sherif',
                    'candidate_phone' => '+20 112 345 6789',
                    'portfolio_url' => 'https://behance.net/nourelsherif',
                    'cover_letter' => 'Passionate about typography, design systems, and Figma tokens. Looking forward to joining your creative team.',
                    'status' => 'shortlisted',
                ]
            );
        }

        // Portfolio Projects
        PortfolioProject::updateOrCreate(
            ['slug' => 'nilepay-fintech-solution'],
            [
                'title' => 'NilePay — Digital Payment & Wallet Platform',
                'client_name' => 'NilePay Financial Inc.',
                'category_id' => $catFintech->id,
                'featured_image' => 'assets/img/work/nilepay.jpg',
                'project_url' => 'https://nilepay.example.com',
                'excerpt' => 'A scalable digital payment infrastructure processing thousands of secure card transactions and wallet transfers daily.',
                'description' => 'NilePay needed a resilient financial platform adhering to strict compliance and sub-second latency. Empetix architected the multi-region micro-services, fraud detection engine, and native iOS/Android mobile wallets.',
                'is_featured' => true,
                'is_published' => true,
                'completion_date' => Carbon::now()->subMonths(3),
            ]
        );

        PortfolioProject::updateOrCreate(
            ['slug' => 'dar-ceramics-luxury-ecommerce'],
            [
                'title' => 'Dar Ceramics — Luxury Architectural E-Commerce',
                'client_name' => 'Dar Ceramics Group',
                'category_id' => $catFintech->id,
                'featured_image' => 'assets/img/work/dar-ceramics.jpg',
                'excerpt' => 'High-end interactive 3D product visualizer and bespoke checkout for luxury architectural finishes.',
                'description' => 'Empetix built the full custom e-commerce solution with WebGL product previews, custom cart flows, and real-time inventory synchronization.',
                'is_featured' => true,
                'is_published' => true,
                'completion_date' => Carbon::now()->subMonths(6),
            ]
        );

        PortfolioProject::updateOrCreate(
            ['slug' => 'medlink-healthtech-platform'],
            [
                'title' => 'MedLink — Clinical HealthTech & Telemedicine',
                'client_name' => 'MedLink Healthcare',
                'category_id' => $catFintech->id,
                'featured_image' => 'assets/img/work/medlink.jpg',
                'excerpt' => 'Unified electronic health records and HIPAA-compliant video consultations platform.',
                'description' => 'Connecting hundreds of doctors with patients seamlessly across Egypt and Saudi Arabia with instant booking and automated prescription generation.',
                'is_featured' => true,
                'is_published' => true,
                'completion_date' => Carbon::now()->subMonths(8),
            ]
        );

        // Blog Posts
        Post::updateOrCreate(
            ['slug' => 'building-scalable-saas-architectures'],
            [
                'title' => 'Architecting Scalable Multi-Tenant SaaS with Clean Architecture',
                'category_id' => $catSoftware->id,
                'type' => 'article',
                'featured_image' => 'assets/img/blog/article-cover.jpg',
                'excerpt' => 'Insights into isolating customer data with multi-database tenancy, modular monoliths, and repository patterns.',
                'body' => 'In enterprise software, scaling multitenancy requires strict physical database separation or dynamic tenant schema routing...',
                'is_published' => true,
                'views_count' => 1420,
                'published_at' => Carbon::now()->subDays(5),
            ]
        );

        Post::updateOrCreate(
            ['slug' => 'high-roas-paid-acquisition-framework-2026'],
            [
                'title' => 'The 2026 High-ROAS Performance Marketing Blueprint',
                'category_id' => $catMarketing->id,
                'type' => 'article',
                'featured_image' => 'assets/img/blog/article-cover.jpg',
                'excerpt' => 'How we optimize \$18M+ in managed ad spend across Meta, Google Ads, and TikTok with conversion rate optimization.',
                'body' => 'Modern performance marketing requires strict attribution models, creative velocity, and full-funnel CRO alignment...',
                'is_published' => true,
                'views_count' => 2890,
                'published_at' => Carbon::now()->subDays(12),
            ]
        );

        // Partners & Client Logos
        $partnersList = [
            ['name' => 'Amazon Web Services', 'logo' => 'assets/img/partners/aws.svg', 'sort_order' => 1],
            ['name' => 'Meta Business Partner', 'logo' => 'assets/img/partners/meta.svg', 'sort_order' => 2],
            ['name' => 'Google Cloud & Marketing', 'logo' => 'assets/img/partners/google.svg', 'sort_order' => 3],
            ['name' => 'Stripe Payments', 'logo' => 'assets/img/partners/stripe.svg', 'sort_order' => 4],
            ['name' => 'Apple Developer Enterprise', 'logo' => 'assets/img/partners/apple.svg', 'sort_order' => 5],
            ['name' => 'Laravel Certified Partner', 'logo' => 'assets/img/partners/laravel.svg', 'sort_order' => 6],
        ];

        foreach ($partnersList as $p) {
            Partner::updateOrCreate(['name' => $p['name']], $p);
        }

        // Contact Inquiries
        ContactMessage::updateOrCreate(
            ['email' => 'tarek.ceo@gulfretail.com'],
            [
                'name' => 'Tarek Mansour',
                'phone' => '+966 50 123 4567',
                'subject' => 'Building Custom Enterprise Mobile App & Web Platform',
                'message' => 'Hello Empetix team, we run an omnichannel retail brand in Riyadh with 40 stores. We want to architect a unified loyalty app and modern web store with sub-second performance. Looking to start next month.',
                'is_read' => false,
                'is_replied' => false,
            ]
        );

        ContactMessage::updateOrCreate(
            ['email' => 'sarah.h@fintech-ventures.co.uk'],
            [
                'name' => 'Sarah Henderson',
                'phone' => '+44 20 7946 0912',
                'subject' => 'Performance Marketing & Brand Scalability',
                'message' => 'We are scaling our B2B SaaS in London and require an agency capable of handling paid social acquisition and CRO optimization with high accountability.',
                'is_read' => true,
                'is_replied' => true,
            ]
        );

        // Testimonials
        Testimonial::updateOrCreate(
            ['client_name' => 'Hassan El-Ghazaly'],
            [
                'client_position' => 'Chief Technology Officer',
                'client_company' => 'NilePay Group',
                'content' => 'Empetix delivered our payment infrastructure with incredible speed and technical rigor. Their clean architecture and zero-downtime deployment saved us months of development.',
                'avatar' => 'assets/img/team/seif-hany.jpg',
                'rating' => 5,
                'is_published' => true,
            ]
        );

        Testimonial::updateOrCreate(
            ['client_name' => 'Laila Mostafa'],
            [
                'client_position' => 'Managing Director',
                'client_company' => 'Dar Luxury Interiors',
                'content' => 'Our online sales surged by 340% within 90 days after Empetix took over our digital experience and paid growth channels. Outstanding team.',
                'avatar' => 'assets/img/team/mariam-nabil.jpg',
                'rating' => 5,
                'is_published' => true,
            ]
        );

        // 4. Provision Holding Company Tenant (Empetix HQ)
        $this->command->info('Provisioning Holding Company Tenant (empetix-team)...');

        $hqTenant = Tenant::on('landlord')->where('slug', 'empetix-team')->first();
        if (! $hqTenant) {
            $hqTenant = $pipeline->handle(new ProvisionTenantData(
                companyName: 'Empetix HQ',
                slug: 'empetix-team',
                ownerName: 'Mohanad',
                ownerEmail: 'mohanad@empetix.com',
                ownerPassword: 'password',
                phone: '+20 100 000 0000',
                planId: $enterprisePlan->id,
                isInternal: true,
                locale: 'ar',
                currency: 'EGP'
            ));
        }

        // 5. Provision Demo Client Tenant (Acme Corp)
        $this->command->info('Provisioning Demo Client Tenant (acme)...');

        $acmeTenant = Tenant::on('landlord')->where('slug', 'acme')->first();
        if (! $acmeTenant) {
            $acmeTenant = $pipeline->handle(new ProvisionTenantData(
                companyName: 'Acme Software & Marketing',
                slug: 'acme',
                ownerName: 'Tarek Owner',
                ownerEmail: 'owner@acme.com',
                ownerPassword: 'password',
                phone: '+20 111 222 3333',
                planId: $enterprisePlan->id,
                isInternal: false,
                locale: 'ar',
                currency: 'EGP',
                trialDays: 30
            ));
        }

        // 6. Seed rich demo data inside Acme Tenant Database
        $this->command->info('Seeding ERP modules inside Acme tenant database...');

        $acmeTenant->makeCurrent();

        try {
            CompanySetting::set('company_name', 'Acme Software & Marketing');
            CompanySetting::set('tax_number', 'EG-84930219');
            CompanySetting::set('currency', 'EGP');

            if (Employee::on('tenant')->count() === 0) {
                // Departments & Designations
                $deptEngineering = Department::on('tenant')->create(['name' => 'Software Engineering', 'code' => 'ENG']);
                $deptMarketing = Department::on('tenant')->create(['name' => 'Marketing & Growth', 'code' => 'MKT']);

                $desBackend = Designation::on('tenant')->create(['name' => 'Backend Architect', 'department_id' => $deptEngineering->id]);
                $desLeadMkt = Designation::on('tenant')->create(['name' => 'Growth Lead', 'department_id' => $deptMarketing->id]);

                // Leave Types
                $annualLeave = LeaveType::on('tenant')->create(['name' => 'Annual Leave', 'days_allowed' => 21, 'is_paid' => true]);
                $sickLeave = LeaveType::on('tenant')->create(['name' => 'Sick Leave', 'days_allowed' => 10, 'is_paid' => true]);

                // Employees
                $emp1 = Employee::on('tenant')->create([
                    'employee_code' => 'EMP-001',
                    'first_name' => 'Kareem',
                    'last_name' => 'Hassan',
                    'email' => 'kareem@acme.com',
                    'phone' => '+20 101 234 5678',
                    'gender' => 'male',
                    'department_id' => $deptEngineering->id,
                    'designation_id' => $desBackend->id,
                    'employment_status' => 'active',
                    'basic_salary_minor' => 3500000, // 35,000 EGP
                    'currency' => 'EGP',
                    'joining_date' => Carbon::now()->subYear(),
                ]);

                $emp2 = Employee::on('tenant')->create([
                    'employee_code' => 'EMP-002',
                    'first_name' => 'Sara',
                    'last_name' => 'Adel',
                    'email' => 'sara@acme.com',
                    'phone' => '+20 102 345 6789',
                    'gender' => 'female',
                    'department_id' => $deptMarketing->id,
                    'designation_id' => $desLeadMkt->id,
                    'employment_status' => 'active',
                    'basic_salary_minor' => 2800000, // 28,000 EGP
                    'currency' => 'EGP',
                    'joining_date' => Carbon::now()->subMonths(8),
                ]);

                // Attendance
                Attendance::on('tenant')->create([
                    'employee_id' => $emp1->id,
                    'date' => Carbon::today(),
                    'clock_in' => '09:00:00',
                    'status' => 'present',
                ]);

                // Payroll
                $payroll = Payroll::on('tenant')->create([
                    'employee_id' => $emp1->id,
                    'month' => (int) Carbon::now()->format('m'),
                    'year' => (int) Carbon::now()->format('Y'),
                    'basic_salary_minor' => 3500000,
                    'allowances_total_minor' => 300000, // 3,000 allowance
                    'deductions_total_minor' => 150000, // 1,500 tax/social
                    'net_salary_minor' => 3650000, // 36,500 net
                    'currency' => 'EGP',
                    'status' => 'paid',
                    'paid_at' => Carbon::now(),
                ]);

                PayrollItem::on('tenant')->create([
                    'payroll_id' => $payroll->id,
                    'type' => 'allowance',
                    'title' => 'Performance Bonus',
                    'amount_minor' => 300000,
                ]);

                PayrollItem::on('tenant')->create([
                    'payroll_id' => $payroll->id,
                    'type' => 'deduction',
                    'title' => 'Social Insurance',
                    'amount_minor' => 150000,
                ]);

                // CRM Clients & Leads
                $clientA = CrmClient::on('tenant')->create([
                    'name' => 'El-Nour Logistics Group',
                    'company_name' => 'El-Nour Logistics Inc.',
                    'email' => 'procurement@elnour.com',
                    'phone' => '+20 120 111 2222',
                    'website' => 'https://elnour.com',
                    'address' => 'Alexandria, Egypt',
                    'currency' => 'EGP',
                ]);

                CrmLead::on('tenant')->create([
                    'title' => 'Fleet Tracking ERP System',
                    'source' => 'google',
                    'status' => 'qualified',
                    'estimated_value_minor' => 18000000, // 180,000 EGP
                    'currency' => 'EGP',
                    'contact_name' => 'Eng. Ahmed El-Nour',
                    'email' => 'ahmed@elnour.com',
                    'phone' => '+20 122 333 4444',
                ]);

                CrmDeal::on('tenant')->create([
                    'title' => 'Supply Chain ERP Phase 1',
                    'crm_client_id' => $clientA->id,
                    'stage' => 'proposal',
                    'amount_minor' => 12000000, // 120,000 EGP
                    'currency' => 'EGP',
                    'expected_close_date' => Carbon::now()->addDays(20),
                ]);

                // Marketing Clients & Campaigns
                $mktClient = MarketingClient::on('tenant')->create([
                    'name' => 'FitLife Nutrition',
                    'company_name' => 'FitLife Brands LLC',
                    'industry' => 'Health & E-commerce',
                    'monthly_retainer_minor' => 2500000, // 25,000 EGP
                    'currency' => 'EGP',
                    'status' => 'active',
                ]);

                $campaign = MarketingCampaign::on('tenant')->create([
                    'name' => 'Summer Fitness Launch 2026',
                    'marketing_client_id' => $mktClient->id,
                    'channel' => 'meta',
                    'budget_minor' => 5000000, // 50,000 EGP
                    'spent_minor' => 1850000, // 18,500 EGP
                    'currency' => 'EGP',
                    'target_audience' => 'Men & Women 22-45 interested in fitness in Egypt & Gulf',
                    'status' => 'active',
                    'starts_at' => Carbon::now()->subDays(10),
                    'ends_at' => Carbon::now()->addDays(20),
                ]);

                MarketingKpiLog::on('tenant')->create([
                    'marketing_campaign_id' => $campaign->id,
                    'date' => Carbon::today(),
                    'impressions' => 148500,
                    'clicks' => 3820,
                    'leads_generated' => 142,
                    'conversions' => 45,
                    'spend_minor' => 1850000,
                    'currency' => 'EGP',
                ]);

                // Software Projects & Tasks
                $project = Project::on('tenant')->create([
                    'name' => 'Logistics Portal & Mobile App',
                    'slug' => 'logistics-portal',
                    'client_id' => $clientA->id,
                    'budget_minor' => 15000000,
                    'currency' => 'EGP',
                    'status' => 'in_progress',
                    'start_date' => Carbon::now()->subMonth(),
                    'deadline' => Carbon::now()->addMonths(2),
                ]);

                Task::on('tenant')->create([
                    'project_id' => $project->id,
                    'title' => 'Design real-time driver GPS tracking socket API',
                    'priority' => 'high',
                    'status' => 'in_progress',
                    'due_date' => Carbon::now()->addDays(4),
                    'estimated_hours' => 16.0,
                    'logged_hours' => 8.5,
                ]);

                // Invoices & Payments
                $invoice = Invoice::on('tenant')->create([
                    'client_id' => $clientA->id,
                    'project_id' => $project->id,
                    'invoice_number' => 'INV-2026-001',
                    'issue_date' => Carbon::now()->subDays(10),
                    'due_date' => Carbon::now()->addDays(5),
                    'subtotal_minor' => 5000000, // 50,000 EGP
                    'tax_minor' => 700000, // 7,000 (14% VAT)
                    'discount_minor' => 0,
                    'total_minor' => 5700000, // 57,000 EGP
                    'amount_paid_minor' => 5700000,
                    'currency' => 'EGP',
                    'status' => 'paid',
                    'notes' => 'Milestone 1 Deliverables accepted.',
                ]);

                InvoiceItem::on('tenant')->create([
                    'invoice_id' => $invoice->id,
                    'description' => 'System Architecture, UI/UX Prototypes & Database Setup',
                    'quantity' => 1,
                    'unit_price_minor' => 5000000,
                    'total_minor' => 5000000,
                ]);

                Payment::on('tenant')->create([
                    'invoice_id' => $invoice->id,
                    'amount_minor' => 5700000,
                    'currency' => 'EGP',
                    'payment_method' => 'bank_transfer',
                    'transaction_reference' => 'CIB-TXN-948102',
                    'payment_date' => Carbon::now()->subDays(8),
                ]);
            }

        } finally {
            $acmeTenant->forgetCurrent();
        }

        $this->command->info('✓ Database seeding completed with 100% success!');
    }
}
