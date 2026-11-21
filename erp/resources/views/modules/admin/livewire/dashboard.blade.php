<div class="space-y-8 max-w-7xl mx-auto font-sans">
    <!-- 1. Hero Executive Command Banner -->
    <div class="bg-gradient-to-br from-[#050C1A] via-[#08142C] to-[#0D1E3D] border border-white/10 rounded-3xl p-6 sm:p-10 text-white relative overflow-hidden shadow-2xl">
        <!-- Architectural Grid & Glow -->
        <div class="absolute inset-0 opacity-10 pointer-events-none" style="background-image: linear-gradient(#FF4D1C 1px, transparent 1px), linear-gradient(90deg, #FF4D1C 1px, transparent 1px); background-size: 36px 36px;"></div>
        <div class="absolute -top-24 -end-24 w-96 h-96 rounded-full bg-accent-500/20 blur-3xl pointer-events-none"></div>

        <div class="relative z-10 flex flex-col lg:flex-row lg:items-center justify-between gap-8">
            <div class="space-y-3">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-accent-500/20 text-accent-500 border border-accent-500/30 text-xs font-mono font-bold tracking-wider uppercase">
                    <span class="w-2 h-2 rounded-full bg-accent-500 animate-ping"></span>
                    {{ app()->getLocale() === 'ar' ? 'تحكم كامل 100% بالموقع العام ومنظومة الـ SaaS' : '100% Full Website & Multi-Tenant SaaS Control' }}
                </div>
                <h2 class="text-2xl sm:text-4xl font-heading font-black tracking-tight leading-tight">
                    {{ app()->getLocale() === 'ar' ? 'غرفة القيادة المركزية — Empetix Studio' : 'Empetix Executive Mission Control' }}
                </h2>
                <p class="text-slate-300 text-sm max-w-2xl leading-relaxed">
                    {{ app()->getLocale() === 'ar'
                        ? 'تحكم في كل صفحة وقسم على موقع الشركة (الهوية، الـ 4 شرائح في الهيرو، فيديو من نحن، الحلول الخمسة، الأعمال، المؤسسون، المدونة، الوظائف، الشركاء، الرسائل) بالإضافة إلى إدارة الشركات المشتركة.'
                        : 'Complete live administration over every website page and section (Brand, 4-Slide Hero, Film, Solutions Hub, Portfolio, Founders, Blog, Careers, Partners, Inquiries) and SaaS tenants.' }}
                </p>
            </div>

            <div class="flex flex-wrap items-center gap-3 shrink-0">
                <a href="{{ route('admin.cms.settings') }}"
                   class="inline-flex items-center gap-2.5 px-5 py-3 bg-gradient-to-r from-accent-500 to-[#ff6b42] hover:from-accent-600 hover:to-accent-500 text-white font-bold text-xs uppercase tracking-wider rounded-2xl shadow-glow transition-all">
                    <span>{{ app()->getLocale() === 'ar' ? 'تعديل صفحات وأقسام الموقع' : 'Edit All Website Pages' }}</span>
                    <span class="font-mono">✎</span>
                </a>
                <a href="{{ route('home') }}" target="_blank"
                   class="inline-flex items-center gap-2 px-5 py-3 bg-white/10 hover:bg-white/20 text-white border border-white/15 text-xs font-bold rounded-2xl transition-all">
                    <span>{{ app()->getLocale() === 'ar' ? 'فتح الموقع الحي' : 'Open Live Website' }}</span>
                    <span class="font-mono">↗</span>
                </a>
            </div>
        </div>
    </div>

    <!-- 2. High-Impact KPI Metrics Bar -->
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4">
        <a href="{{ route('admin.cms.portfolio') }}" class="bg-white border border-slate-200/80 hover:border-accent-500 p-5 rounded-2xl transition-all group shadow-subtle hover:shadow-card">
            <div class="flex items-center justify-between mb-3">
                <span class="w-9 h-9 rounded-xl bg-ink-900/5 text-ink-900 group-hover:bg-accent-500 group-hover:text-white flex items-center justify-center transition-colors">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                </span>
                <span class="text-[10px] font-mono font-bold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-full">LIVE</span>
            </div>
            <p class="text-2xl font-mono font-black text-ink-900">{{ $portfolioCount }}</p>
            <span class="text-xs font-bold text-slate-600 mt-0.5 block">{{ app()->getLocale() === 'ar' ? 'سابقة الأعمال' : 'Case Studies' }}</span>
        </a>

        <a href="{{ route('admin.cms.posts') }}" class="bg-white border border-slate-200/80 hover:border-accent-500 p-5 rounded-2xl transition-all group shadow-subtle hover:shadow-card">
            <div class="flex items-center justify-between mb-3">
                <span class="w-9 h-9 rounded-xl bg-ink-900/5 text-ink-900 group-hover:bg-accent-500 group-hover:text-white flex items-center justify-center transition-colors">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/></svg>
                </span>
                <span class="text-[10px] font-mono font-bold text-slate-400">BLOG</span>
            </div>
            <p class="text-2xl font-mono font-black text-ink-900">{{ $postsCount }}</p>
            <span class="text-xs font-bold text-slate-600 mt-0.5 block">{{ app()->getLocale() === 'ar' ? 'مقالات وفيديوهات' : 'Journal Posts' }}</span>
        </a>

        <a href="{{ route('admin.cms.team') }}" class="bg-white border border-slate-200/80 hover:border-accent-500 p-5 rounded-2xl transition-all group shadow-subtle hover:shadow-card">
            <div class="flex items-center justify-between mb-3">
                <span class="w-9 h-9 rounded-xl bg-ink-900/5 text-ink-900 group-hover:bg-accent-500 group-hover:text-white flex items-center justify-center transition-colors">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                </span>
                <span class="text-[10px] font-mono font-bold text-slate-400">TEAM</span>
            </div>
            <p class="text-2xl font-mono font-black text-ink-900">{{ $teamCount }}</p>
            <span class="text-xs font-bold text-slate-600 mt-0.5 block">{{ app()->getLocale() === 'ar' ? 'المؤسسون والفريق' : 'Founders & Team' }}</span>
        </a>

        <a href="{{ route('admin.cms.careers') }}" class="bg-white border border-slate-200/80 hover:border-accent-500 p-5 rounded-2xl transition-all group shadow-subtle hover:shadow-card">
            <div class="flex items-center justify-between mb-3">
                <span class="w-9 h-9 rounded-xl bg-accent-50 text-accent-500 group-hover:bg-accent-500 group-hover:text-white flex items-center justify-center transition-colors">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                </span>
                <span class="text-[10px] font-mono font-bold text-accent-500 bg-accent-50 px-2 py-0.5 rounded-full">HIRING</span>
            </div>
            <p class="text-2xl font-mono font-black text-accent-500">{{ $openJobsCount }}</p>
            <span class="text-xs font-bold text-slate-600 mt-0.5 block">{{ app()->getLocale() === 'ar' ? 'الوظائف المفتوحة' : 'Open Roles' }}</span>
        </a>

        <a href="{{ route('admin.cms.applications') }}" class="bg-white border border-slate-200/80 hover:border-accent-500 p-5 rounded-2xl transition-all group shadow-subtle hover:shadow-card">
            <div class="flex items-center justify-between mb-3">
                <span class="w-9 h-9 rounded-xl bg-ink-900/5 text-ink-900 group-hover:bg-accent-500 group-hover:text-white flex items-center justify-center transition-colors">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                </span>
                <span class="text-[10px] font-mono font-bold text-slate-400">CVS</span>
            </div>
            <p class="text-2xl font-mono font-black text-ink-900">{{ $applicationsCount }}</p>
            <span class="text-xs font-bold text-slate-600 mt-0.5 block">{{ app()->getLocale() === 'ar' ? 'طلبات التوظيف' : 'Job Applicants' }}</span>
        </a>

        <a href="{{ route('admin.cms.inquiries') }}" class="bg-white border {{ $unreadMessagesCount > 0 ? 'border-accent-500 ring-2 ring-accent-500/15' : 'border-slate-200/80' }} hover:border-accent-500 p-5 rounded-2xl transition-all group shadow-subtle hover:shadow-card">
            <div class="flex items-center justify-between mb-3">
                <span class="w-9 h-9 rounded-xl {{ $unreadMessagesCount > 0 ? 'bg-accent-500 text-white' : 'bg-ink-900/5 text-ink-900' }} group-hover:bg-accent-500 group-hover:text-white flex items-center justify-center transition-colors">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                </span>
                <span class="text-[10px] font-mono font-bold {{ $unreadMessagesCount > 0 ? 'text-accent-600 bg-accent-50 px-2 py-0.5 rounded-full animate-pulse' : 'text-slate-400' }}">
                    {{ $unreadMessagesCount > 0 ? 'NEW' : 'INBOX' }}
                </span>
            </div>
            <p class="text-2xl font-mono font-black {{ $unreadMessagesCount > 0 ? 'text-accent-500' : 'text-ink-900' }}">{{ $unreadMessagesCount }}</p>
            <span class="text-xs font-bold text-slate-600 mt-0.5 block">{{ app()->getLocale() === 'ar' ? 'رسائل غير مقروءة' : 'Unread Inquiries' }}</span>
        </a>
    </div>

    <!-- 3. Complete Website Control Matrix (Every Page & Section of the Website) -->
    <div class="bg-white border border-slate-200/80 rounded-3xl p-6 sm:p-8 shadow-subtle space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-slate-100">
            <div>
                <div class="flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-accent-500"></span>
                    <h3 class="text-lg font-heading font-extrabold text-ink-900">
                        {{ app()->getLocale() === 'ar' ? 'مصفوفة التحكم الشامل بكل صفحات وأقسام الموقع (Website Control Matrix)' : 'Full Website Page & Section Control Matrix' }}
                    </h3>
                </div>
                <p class="text-xs text-slate-500 mt-1">
                    {{ app()->getLocale() === 'ar' ? 'اختر أي صفحة أو قسم من الموقع لتعديل محتواه مباشرة أو معاينته على الموقع الحي.' : 'Click any website page or section below to edit its content directly or preview it live.' }}
                </p>
            </div>
            <a href="{{ route('admin.cms.settings') }}" class="text-xs font-mono font-bold text-accent-500 hover:underline">
                {{ app()->getLocale() === 'ar' ? 'فتح استوديو الصفحات الكامل (7 تبويبات) ←' : 'Open Full 7-Tab Pages Studio →' }}
            </a>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            @php
                $controlCards = [
                    [
                        'icon' => '🏛️',
                        'title_ar' => 'الهوية والهيدر والفوتر',
                        'title_en' => 'Brand, Header & Footer',
                        'desc_ar' => 'الشعار، شريط الإعلانات، القائمة العملاقة، الفوتر، والسوشيال ميديا.',
                        'desc_en' => 'Logos, top ticker, mega menu, footer banner & social links.',
                        'edit_url' => route('admin.cms.settings', ['activeTab' => 'brand']),
                        'live_url' => route('home'),
                        'badge' => 'Global',
                    ],
                    [
                        'icon' => '🎬',
                        'title_ar' => 'سلايدر الهيرو (4 شرائح)',
                        'title_en' => '4-Slide Hero Carousel',
                        'desc_ar' => 'عناوين وأوصاف وصور وأزرار الشرائح الأربع في أعلى الرئيسية.',
                        'desc_en' => 'Headlines, backgrounds, CTAs & dock tabs for all 4 hero slides.',
                        'edit_url' => route('admin.cms.settings', ['activeTab' => 'hero']),
                        'live_url' => route('home'),
                        'badge' => 'Homepage',
                    ],
                    [
                        'icon' => '📽️',
                        'title_ar' => 'فيديو من نحن والركائز',
                        'title_en' => 'Who We Are Film & Pillars',
                        'desc_ar' => 'الفيديو التعريفي، غلاف الفيديو، والركائز الثلاث للشركة.',
                        'desc_en' => 'Overview MP4 film, poster image, and 3 core studio pillars.',
                        'edit_url' => route('admin.cms.settings', ['activeTab' => 'overview']),
                        'live_url' => route('home') . '#who-we-are',
                        'badge' => 'Video',
                    ],
                    [
                        'icon' => '⚡',
                        'title_ar' => 'مركز الحلول (5 قدرات)',
                        'title_en' => 'Solutions Hub (5 Pillars)',
                        'desc_ar' => 'البرمجيات، الموبايل، التسويق، الإنتاج الفني، والذكاء الاصطناعي.',
                        'desc_en' => 'Interactive 5 solution tabs, deliverables, images & metrics.',
                        'edit_url' => route('admin.cms.settings', ['activeTab' => 'solutions']),
                        'live_url' => route('home') . '#solutions-hub',
                        'badge' => '5 Pillars',
                    ],
                    [
                        'icon' => '📊',
                        'title_ar' => 'إحصائيات الإنجاز والأرقام',
                        'title_en' => 'Impact Metrics & Counters',
                        'desc_ar' => 'عدادات المشاريع (140+)، العائد الإعلاني (3.8x)، الاستقرار، والتقييم.',
                        'desc_en' => 'Animated counters (140+, 3.8x, 99.96%, 4.9/5) & labels.',
                        'edit_url' => route('admin.cms.settings', ['activeTab' => 'stats']),
                        'live_url' => route('home'),
                        'badge' => 'Counters',
                    ],
                    [
                        'icon' => '🌍',
                        'title_ar' => 'صفحة عن الشركة والفروع',
                        'title_en' => 'About, Principles & Timeline',
                        'desc_ar' => 'قصة الشركة، المبادئ الأربعة، المسيرة الزمنية، وفروع القاهرة والرياض ولندن.',
                        'desc_en' => 'Studio story, 4 principles, timeline, and Cairo/Riyadh/London offices.',
                        'edit_url' => route('admin.cms.settings', ['activeTab' => 'about']),
                        'live_url' => route('site.about'),
                        'badge' => '/about',
                    ],
                    [
                        'icon' => '💼',
                        'title_ar' => 'المشاريع ودراسات الحالة',
                        'title_en' => 'Portfolio & Case Studies',
                        'desc_ar' => 'إضافة وتعديل المشاريع (مثل NilePay) والصور والنتائج والتصنيفات.',
                        'desc_en' => 'Add/edit case studies, client metrics, screenshots & categories.',
                        'edit_url' => route('admin.cms.portfolio'),
                        'live_url' => route('site.work'),
                        'badge' => $portfolioCount . ' Cases',
                    ],
                    [
                        'icon' => '👥',
                        'title_ar' => 'المؤسسون وفريق العمل',
                        'title_en' => 'Founders & Team Directory',
                        'desc_ar' => 'إدارة بروفايلات المؤسسين (مهند، أيمن، بيشوي) وأعضاء الفريق.',
                        'desc_en' => 'Manage founders (Mohanad, Ayman, Bishoy) and team leads.',
                        'edit_url' => route('admin.cms.team'),
                        'live_url' => route('site.team'),
                        'badge' => $teamCount . ' Members',
                    ],
                    [
                        'icon' => '📰',
                        'title_ar' => 'المدونة، الفيديو، والصور',
                        'title_en' => 'Blog, Video & Photo Essays',
                        'desc_ar' => 'نشر المقالات الهندسية، حلقات الفيديو الوثائقية، والمقالات المصورة.',
                        'desc_en' => 'Publish engineering articles, video episodes, and photo essays.',
                        'edit_url' => route('admin.cms.posts'),
                        'live_url' => route('site.blog'),
                        'badge' => $postsCount . ' Posts',
                    ],
                    [
                        'icon' => '🚀',
                        'title_ar' => 'بوابة الوظائف وطلبات الـ CV',
                        'title_en' => 'Careers Portal & Applicants',
                        'desc_ar' => 'إدارة الوظائف الشاغرة ومراجعة السير الذاتية للمتقدمين.',
                        'desc_en' => 'Manage open vacancies, perks, and candidate CV applications.',
                        'edit_url' => route('admin.cms.careers'),
                        'live_url' => route('site.careers'),
                        'badge' => $openJobsCount . ' Jobs',
                    ],
                    [
                        'icon' => '🤝',
                        'title_ar' => 'شركاء النجاح وآراء العملاء',
                        'title_en' => 'Partners & Testimonials',
                        'desc_ar' => 'إدارة شعارات الشركات الشريكة وتقييمات وشهادات العملاء.',
                        'desc_en' => 'Manage alliance partner logos and 5-star client reviews.',
                        'edit_url' => route('admin.cms.partners'),
                        'live_url' => route('site.partners'),
                        'badge' => $partnersCount . ' Brands',
                    ],
                    [
                        'icon' => '✉️',
                        'title_ar' => 'صفحة التواصل وصندوق الرسائل',
                        'title_en' => 'Contact Desks & Inquiries',
                        'desc_ar' => 'تخصيص مكاتب التواصل واستقبال رسائل وطلبات المشاريع.',
                        'desc_en' => 'Customize contact desks and manage inbound client project briefs.',
                        'edit_url' => route('admin.cms.inquiries'),
                        'live_url' => route('site.contact'),
                        'badge' => $unreadMessagesCount . ' Unread',
                    ],
                ];
            @endphp

            @foreach($controlCards as $card)
                <div class="p-5 rounded-2xl border border-slate-200/80 hover:border-ink-900 bg-slate-50/50 hover:bg-white transition-all flex flex-col justify-between group shadow-xs">
                    <div>
                        <div class="flex items-center justify-between mb-3">
                            <span class="text-2xl">{{ $card['icon'] }}</span>
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-mono font-bold bg-white border border-slate-200 text-ink-900">
                                {{ $card['badge'] }}
                            </span>
                        </div>
                        <h4 class="font-heading font-extrabold text-ink-900 text-sm mb-1 group-hover:text-accent-500 transition-colors">
                            {{ app()->getLocale() === 'ar' ? $card['title_ar'] : $card['title_en'] }}
                        </h4>
                        <p class="text-xs text-slate-500 leading-relaxed mb-4">
                            {{ app()->getLocale() === 'ar' ? $card['desc_ar'] : $card['desc_en'] }}
                        </p>
                    </div>

                    <div class="pt-3 border-t border-slate-200/60 flex items-center justify-between gap-2">
                        <a href="{{ $card['edit_url'] }}"
                           class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-ink-900 hover:bg-accent-500 text-white text-xs font-bold transition-colors">
                            <span>{{ app()->getLocale() === 'ar' ? 'إدارة وتعديل' : 'Manage' }}</span>
                            <span>→</span>
                        </a>
                        <a href="{{ $card['live_url'] }}" target="_blank"
                           class="px-2.5 py-1.5 rounded-xl border border-slate-200 hover:border-ink-900 text-slate-600 hover:text-ink-900 text-[11px] font-mono font-bold transition-colors"
                           title="Preview on Live Site">
                            {{ app()->getLocale() === 'ar' ? 'معاينة ↗' : 'Live ↗' }}
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- 4. Recent Activity Grid: Contact Inquiries & Candidate Applications -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Recent Contact Messages -->
        <div class="bg-white border border-slate-200/80 rounded-3xl shadow-subtle p-6">
            <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-4">
                <div>
                    <h3 class="text-base font-heading font-extrabold text-ink-900">
                        {{ app()->getLocale() === 'ar' ? 'أحدث رسائل واستفسارات العملاء' : 'Recent Client Project Inquiries' }}
                    </h3>
                    <p class="text-xs text-slate-400">{{ app()->getLocale() === 'ar' ? 'الواردة مباشرة من نموذج صفحة التواصل (/contact)' : 'Submitted via live /contact brief form' }}</p>
                </div>
                <a href="{{ route('admin.cms.inquiries') }}" class="px-3 py-1.5 rounded-xl bg-slate-100 hover:bg-ink-900 hover:text-white text-ink-900 text-xs font-mono font-bold transition-colors">
                    {{ app()->getLocale() === 'ar' ? 'كل الرسائل' : 'All Inquiries' }} →
                </a>
            </div>

            <div class="divide-y divide-slate-100">
                @forelse($recentInquiries as $msg)
                    <div class="py-3.5 flex items-start justify-between gap-3 text-xs">
                        <div class="space-y-1 min-w-0">
                            <div class="flex items-center gap-2">
                                <span class="font-bold text-ink-900 text-sm">{{ $msg->name }}</span>
                                @if(!$msg->is_read)
                                    <span class="px-2 py-0.5 rounded-full bg-accent-500 text-white font-mono text-[9px] font-bold">NEW</span>
                                @endif
                            </div>
                            <p class="text-xs text-slate-600 truncate">{{ $msg->subject ?: $msg->message }}</p>
                            <span class="text-[11px] text-slate-400 font-mono">{{ $msg->email }} • {{ $msg->created_at?->diffForHumans() }}</span>
                        </div>
                        <div class="shrink-0 flex items-center gap-2">
                            @if(!$msg->is_read)
                                <button wire:click="markMessageAsRead({{ $msg->id }})" class="text-[11px] font-mono font-bold text-slate-600 hover:text-ink-900 border border-slate-200 px-2.5 py-1 rounded-lg bg-slate-50 hover:bg-slate-100 cursor-pointer">
                                    {{ app()->getLocale() === 'ar' ? 'تحديد كمقروء' : 'Mark Read' }}
                                </button>
                            @endif
                        </div>
                    </div>
                @empty
                    <p class="py-8 text-center text-xs text-slate-400">{{ app()->getLocale() === 'ar' ? 'لا توجد رسائل جديدة حالياً' : 'No recent inquiries.' }}</p>
                @endforelse
            </div>
        </div>

        <!-- Recent Job Applications -->
        <div class="bg-white border border-slate-200/80 rounded-3xl shadow-subtle p-6">
            <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-4">
                <div>
                    <h3 class="text-base font-heading font-extrabold text-ink-900">
                        {{ app()->getLocale() === 'ar' ? 'أحدث المتقدمين للوظائف (CVs)' : 'Recent Candidate Job Applications' }}
                    </h3>
                    <p class="text-xs text-slate-400">{{ app()->getLocale() === 'ar' ? 'الواردة عبر بوابة التوظيف (/careers)' : 'Submitted via live /careers portal' }}</p>
                </div>
                <a href="{{ route('admin.cms.applications') }}" class="px-3 py-1.5 rounded-xl bg-slate-100 hover:bg-ink-900 hover:text-white text-ink-900 text-xs font-mono font-bold transition-colors">
                    {{ app()->getLocale() === 'ar' ? 'كل المتقدمين' : 'All Applicants' }} →
                </a>
            </div>

            <div class="divide-y divide-slate-100">
                @forelse($recentApplications as $app)
                    <div class="py-3.5 flex items-start justify-between gap-3 text-xs">
                        <div class="space-y-1">
                            <div class="flex items-center gap-2 flex-wrap">
                                <span class="font-bold text-ink-900 text-sm">{{ $app->candidate_name }}</span>
                                <span class="px-2 py-0.5 rounded-lg font-mono text-[10px] font-bold bg-slate-100 text-ink-900">
                                    {{ $app->jobPosting?->title ?? 'General' }}
                                </span>
                            </div>
                            <p class="text-[11px] text-slate-500 font-mono">{{ $app->candidate_email }} • {{ $app->created_at?->diffForHumans() }}</p>
                        </div>
                        <div class="shrink-0">
                            <span class="px-2.5 py-1 rounded-full font-mono text-[10px] font-bold
                                {{ $app->status === 'new' ? 'bg-accent-50 text-accent-600 border border-accent-200' : 'bg-slate-100 text-slate-700' }}">
                                {{ strtoupper($app->status) }}
                            </span>
                        </div>
                    </div>
                @empty
                    <p class="py-8 text-center text-xs text-slate-400">{{ app()->getLocale() === 'ar' ? 'لا توجد طلبات توظيف جديدة' : 'No job applications submitted yet.' }}</p>
                @endforelse
            </div>
        </div>
    </div>

    <!-- 5. Multi-Tenant SaaS Platform Overview -->
    <div class="bg-white border border-slate-200/80 rounded-3xl shadow-subtle p-6 sm:p-8">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-5 border-b border-slate-100 mb-6">
            <div>
                <div class="flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-ink-900"></span>
                    <h3 class="text-lg font-heading font-extrabold text-ink-900">
                        {{ app()->getLocale() === 'ar' ? 'الشركات المشتركة في المنظومة (Multi-Tenant SaaS Workspaces)' : 'Active SaaS Tenants & Isolated Databases' }}
                    </h3>
                </div>
                <p class="text-xs text-slate-500 mt-1">
                    {{ app()->getLocale() === 'ar' ? 'كل شركة تعمل بقاعدة بيانات مستقلة تماماً (Spatie Multi-Database Tenancy)' : 'Physically isolated databases per subscribed software/marketing agency' }}
                </p>
            </div>

            <div class="flex flex-wrap items-center gap-4 text-xs font-mono">
                <div class="px-3 py-1.5 rounded-xl bg-slate-100">
                    <span class="text-slate-500">{{ app()->getLocale() === 'ar' ? 'الشركات:' : 'Tenants:' }}</span>
                    <span class="font-bold text-ink-900 ms-1">{{ $totalTenants }}</span>
                </div>
                <div class="px-3 py-1.5 rounded-xl bg-emerald-50 text-emerald-700">
                    <span>{{ app()->getLocale() === 'ar' ? 'الاشتراكات الفعالة:' : 'Active Subscriptions:' }}</span>
                    <span class="font-bold ms-1">{{ $activeSubscriptions }}</span>
                </div>
                <a href="{{ route('admin.tenants') }}" class="px-4 py-2 bg-ink-900 hover:bg-accent-500 text-white font-bold rounded-xl transition-colors">
                    {{ app()->getLocale() === 'ar' ? 'إدارة الشركات' : 'Manage All Tenants' }} →
                </a>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-start text-xs font-sans">
                <thead>
                    <tr class="border-b border-slate-200 text-slate-400 font-mono uppercase text-[10px]">
                        <th class="py-3 px-4 text-start">{{ app()->getLocale() === 'ar' ? 'الشركة' : 'Tenant Company' }}</th>
                        <th class="py-3 px-4 text-start">{{ app()->getLocale() === 'ar' ? 'الرابط' : 'Workspace Path' }}</th>
                        <th class="py-3 px-4 text-start">{{ app()->getLocale() === 'ar' ? 'قاعدة البيانات المعزولة' : 'Isolated Database' }}</th>
                        <th class="py-3 px-4 text-start">{{ app()->getLocale() === 'ar' ? 'الباقة' : 'SaaS Plan' }}</th>
                        <th class="py-3 px-4 text-start">{{ app()->getLocale() === 'ar' ? 'الحالة' : 'Status' }}</th>
                        <th class="py-3 px-4 text-end">{{ app()->getLocale() === 'ar' ? 'فتح لوحة الشركة' : 'Open ERP' }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($recentTenants as $t)
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="py-3.5 px-4">
                                <div class="font-bold text-ink-900 text-sm">{{ $t->name }}</div>
                                <div class="text-[11px] text-slate-400 font-mono">{{ $t->email }}</div>
                            </td>
                            <td class="py-3.5 px-4 font-mono text-xs text-slate-600">
                                /t/{{ $t->slug }}
                            </td>
                            <td class="py-3.5 px-4 font-mono text-xs text-slate-500">
                                <span class="px-2 py-1 rounded-lg bg-slate-100 border border-slate-200">{{ $t->database_name }}</span>
                            </td>
                            <td class="py-3.5 px-4">
                                <span class="font-bold text-ink-900">{{ $t->currentSubscription?->plan?->name ?? 'Enterprise Trial' }}</span>
                            </td>
                            <td class="py-3.5 px-4">
                                @if($t->is_active)
                                    <span class="inline-flex items-center gap-1 text-[10px] font-mono font-bold text-emerald-700 bg-emerald-50 px-2.5 py-1 rounded-full border border-emerald-200">
                                        ● ACTIVE
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 text-[10px] font-mono font-bold text-rose-700 bg-rose-50 px-2.5 py-1 rounded-full border border-rose-200">
                                        ● SUSPENDED
                                    </span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 text-end">
                                <a href="{{ route('tenant.path.dashboard', ['tenant_slug' => $t->slug]) }}" target="_blank"
                                   class="inline-flex items-center gap-1 px-3 py-1.5 bg-slate-100 hover:bg-accent-500 hover:text-white text-ink-900 rounded-xl font-mono text-xs font-bold transition-colors">
                                    <span>{{ app()->getLocale() === 'ar' ? 'دخول النظام' : 'Launch ERP' }}</span>
                                    <span>↗</span>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-8 text-center text-slate-400">No tenants provisioned yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
