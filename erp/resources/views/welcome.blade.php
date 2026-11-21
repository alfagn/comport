<x-layouts.site :title="'Empetix — Software Engineering, Growth Marketing & Enterprise SaaS'">
    <!-- Hero Section -->
    <section class="relative overflow-hidden pt-20 pb-28 lg:pt-32 lg:pb-36 bg-gradient-to-b from-white via-indigo-50/20 to-white dark:from-slate-950 dark:via-indigo-950/20 dark:to-slate-950">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center relative z-10">
            <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full text-xs font-semibold bg-indigo-50 dark:bg-indigo-950/60 text-indigo-700 dark:text-indigo-300 border border-indigo-200/60 dark:border-indigo-800/60 mb-8">
                <span class="w-2 h-2 rounded-full bg-indigo-600 animate-pulse"></span>
                {{ app()->getLocale() === 'ar' ? 'منظومة SaaS متكاملة لشركات البرمجيات والتسويق' : 'Next-Generation SaaS for Software & Marketing Agencies' }}
            </div>

            <h1 class="text-4xl sm:text-6xl lg:text-7xl font-extrabold tracking-tight text-slate-950 dark:text-white max-w-4xl mx-auto leading-[1.15]">
                {{ app()->getLocale() === 'ar' ? 'نبني برمجيات الأعمال ونقود نمو علامتك التجارية' : 'We Build Scalable Software & Elevate Marketing Brands' }}
            </h1>

            <p class="mt-6 text-lg sm:text-xl text-slate-600 dark:text-slate-300 max-w-2xl mx-auto leading-relaxed">
                {{ app()->getLocale() === 'ar' 
                    ? 'إمبتكس تقدم حلولاً هندسية متقدمة في تطوير البرمجيات، وإدارة الحملات التسويقية، ولوحة تحكم SaaS مخصصة تدير شركتك بالكامل من الموارد البشرية إلى الفواتير.'
                    : 'Empetix delivers high-impact software engineering, performance marketing, and a unified SaaS ERP workspace managing your entire company from HRM to financial invoices.' }}
            </p>

            <div class="mt-10 flex flex-wrap items-center justify-center gap-4">
                <a href="{{ route('site.pricing') }}" class="px-7 py-3.5 rounded-xl font-bold text-white bg-indigo-600 hover:bg-indigo-700 shadow-lg shadow-indigo-600/25 transition-all hover:scale-105">
                    {{ app()->getLocale() === 'ar' ? 'اشترك الآن وجرب مجاناً' : 'Start Free 14-Day Trial' }}
                </a>
                <a href="{{ route('site.work') }}" class="px-7 py-3.5 rounded-xl font-bold text-slate-700 dark:text-slate-200 bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-800 transition-all">
                    {{ app()->getLocale() === 'ar' ? 'شاهد مشاريعنا الناجحة' : 'Explore Case Studies' }}
                </a>
            </div>

            <!-- Highlights / Metrics -->
            <div class="mt-20 grid grid-cols-2 md:grid-cols-4 gap-6 max-w-4xl mx-auto border-t border-slate-200 dark:border-slate-800 pt-10 text-start">
                <div class="p-4">
                    <p class="text-3xl font-extrabold text-indigo-600 dark:text-indigo-400">99.9%</p>
                    <p class="text-xs font-semibold text-slate-500 mt-1">{{ app()->getLocale() === 'ar' ? 'استقرار السيرفرات والتطبيقات' : 'Cloud Architecture Uptime' }}</p>
                </div>
                <div class="p-4">
                    <p class="text-3xl font-extrabold text-indigo-600 dark:text-indigo-400">50K+</p>
                    <p class="text-xs font-semibold text-slate-500 mt-1">{{ app()->getLocale() === 'ar' ? 'معاملة يومية عبر منصاتنا' : 'Daily Processed Operations' }}</p>
                </div>
                <div class="p-4">
                    <p class="text-3xl font-extrabold text-indigo-600 dark:text-indigo-400">5x</p>
                    <p class="text-xs font-semibold text-slate-500 mt-1">{{ app()->getLocale() === 'ar' ? 'عائد الاستثمار التسويقي (ROI)' : 'Average Marketing ROI' }}</p>
                </div>
                <div class="p-4">
                    <p class="text-3xl font-extrabold text-indigo-600 dark:text-indigo-400">100%</p>
                    <p class="text-xs font-semibold text-slate-500 mt-1">{{ app()->getLocale() === 'ar' ? 'عزل كامل لبيانات كل شركة' : 'Multi-DB Physical Isolation' }}</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Services & Capabilities Section -->
    <section class="py-24 bg-white dark:bg-slate-900 border-y border-slate-200/80 dark:border-slate-800/80">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-2xl mx-auto mb-16">
                <h2 class="text-3xl sm:text-4xl font-extrabold text-slate-900 dark:text-white">
                    {{ app()->getLocale() === 'ar' ? 'خدماتنا التقنية والتسويقية' : 'Our Core Expertise' }}
                </h2>
                <p class="mt-4 text-slate-600 dark:text-slate-400">
                    {{ app()->getLocale() === 'ar' ? 'منظومة متكاملة تدير دورة حياة الأعمال البرمجية والحملات التسويقية.' : 'End-to-end engineering, brand growth, and business operations under one roof.' }}
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Card 1: Software -->
                <div class="p-8 rounded-2xl bg-slate-50 dark:bg-slate-950 border border-slate-200/80 dark:border-slate-800 hover:shadow-xl transition-all">
                    <div class="w-12 h-12 rounded-xl bg-indigo-100 dark:bg-indigo-950/60 text-indigo-600 flex items-center justify-center text-2xl font-bold mb-6">
                        💻
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-3">
                        {{ app()->getLocale() === 'ar' ? 'هندسة وتطوير البرمجيات' : 'Custom Software Engineering' }}
                    </h3>
                    <p class="text-sm text-slate-600 dark:text-slate-400 leading-relaxed">
                        {{ app()->getLocale() === 'ar' ? 'بناء أنظمة الويب المعقدة، وتطبيقات الموبايل بـ Flutter، وبوابات الدفع الإلكتروني، وتطبيقات الـ SaaS ذات البنية النظيفة Clean Architecture.' : 'High-performance web backends, responsive Flutter mobile apps, fintech integrations, and scalable multi-tenant SaaS platforms.' }}
                    </p>
                </div>

                <!-- Card 2: Marketing -->
                <div class="p-8 rounded-2xl bg-slate-50 dark:bg-slate-950 border border-slate-200/80 dark:border-slate-800 hover:shadow-xl transition-all">
                    <div class="w-12 h-12 rounded-xl bg-indigo-100 dark:bg-indigo-950/60 text-indigo-600 flex items-center justify-center text-2xl font-bold mb-6">
                        📈
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-3">
                        {{ app()->getLocale() === 'ar' ? 'إدارة ونمو التسويق الرقمي' : 'Performance Growth Marketing' }}
                    </h3>
                    <p class="text-sm text-slate-600 dark:text-slate-400 leading-relaxed">
                        {{ app()->getLocale() === 'ar' ? 'إطلاق وإدارة الحملات الإعلانية المدفوعة على منصات Meta و Google و TikTok، وتتبع مؤشرات الأداء وحساب تكلفة العميل بدقة.' : 'Data-driven paid media campaigns across Meta, Google Ads, and TikTok with strict CPL and CAC monitoring for maximum client acquisition.' }}
                    </p>
                </div>

                <!-- Card 3: Enterprise ERP -->
                <div class="p-8 rounded-2xl bg-slate-50 dark:bg-slate-950 border border-slate-200/80 dark:border-slate-800 hover:shadow-xl transition-all">
                    <div class="w-12 h-12 rounded-xl bg-indigo-100 dark:bg-indigo-950/60 text-indigo-600 flex items-center justify-center text-2xl font-bold mb-6">
                        🏢
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-3">
                        {{ app()->getLocale() === 'ar' ? 'لوحة تحكم SaaS لإدارة الشركات' : 'SaaS Enterprise ERP Suite' }}
                    </h3>
                    <p class="text-sm text-slate-600 dark:text-slate-400 leading-relaxed">
                        {{ app()->getLocale() === 'ar' ? 'نظام متكامل يمنح كل شركة قاعدة بيانات مستقلة بالكامل لإدارة الموظفين، المرتبات، العملاء المحتملين، المشاريع، والفواتير التجارية.' : 'Isolated database tenancy offering complete operational control over HRM, payroll calculation, sales pipeline, sprints, and invoicing.' }}
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Case Study Spotlight (NilePay) -->
    <section class="py-24 bg-slate-50 dark:bg-slate-950">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-gradient-to-br from-indigo-900 to-slate-900 rounded-3xl p-8 sm:p-12 lg:p-16 text-white shadow-2xl relative overflow-hidden">
                <div class="max-w-2xl">
                    <span class="inline-block px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider bg-indigo-500/30 text-indigo-200 mb-4">
                        {{ app()->getLocale() === 'ar' ? 'مشروع مميز' : 'Featured Case Study' }}
                    </span>
                    <h3 class="text-3xl sm:text-4xl font-extrabold tracking-tight mb-4">
                        NilePay — Digital Payment & Wallet Platform
                    </h3>
                    <p class="text-indigo-200/90 text-sm sm:text-base leading-relaxed mb-8">
                        {{ app()->getLocale() === 'ar'
                            ? 'بناء بنية تحتية مالية لمعالجة آلاف عمليات الدفع الآمن وتحويلات المحافظ الإلكترونية يومياً بدقة زمنية فائقة وأعلى معايير الأمان.'
                            : 'Architecting a fault-tolerant payment infrastructure executing thousands of secure card transactions and wallet transfers daily with sub-second latency.' }}
                    </p>
                    <a href="{{ route('site.work') }}" class="inline-flex items-center gap-2 px-6 py-3 rounded-xl font-bold text-indigo-900 bg-white hover:bg-indigo-50 transition-colors">
                        {{ app()->getLocale() === 'ar' ? 'اقرأ قصة النجاح' : 'View Case Study Details' }} →
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-20 bg-indigo-600 text-white text-center">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-3xl sm:text-5xl font-extrabold tracking-tight">
                {{ app()->getLocale() === 'ar' ? 'جاهز لإطلاق وتوسيع شركتك؟' : 'Ready to Transform Your Company Operations?' }}
            </h2>
            <p class="mt-4 text-lg text-indigo-100 max-w-xl mx-auto">
                {{ app()->getLocale() === 'ar' ? 'سجل شركتك الآن واحصل على لوحة تحكم ERP كاملة مع فترة تجريبية مجانية.' : 'Create your company workspace now and experience physical database isolation with a 14-day free trial.' }}
            </p>
            <div class="mt-8">
                <a href="{{ route('site.pricing') }}" class="px-8 py-3.5 rounded-xl font-extrabold text-indigo-600 bg-white hover:bg-indigo-50 shadow-lg transition-transform hover:scale-105 inline-block">
                    {{ app()->getLocale() === 'ar' ? 'ابدأ تجربتك المجانية الآن' : 'Start Your Free Trial' }}
                </a>
            </div>
        </div>
    </section>
</x-layouts.site>
