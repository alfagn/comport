@php
    try {
        $navCounts = [
            'portfolio' => \App\Modules\CMS\Models\PortfolioProject::on('landlord')->count(),
            'posts' => \App\Modules\CMS\Models\Post::on('landlord')->count(),
            'team' => \App\Modules\CMS\Models\TeamMember::on('landlord')->count(),
            'careers' => \App\Modules\CMS\Models\JobPosting::on('landlord')->where('is_active', true)->count(),
            'new_apps' => \App\Modules\CMS\Models\JobApplication::on('landlord')->where('status', 'new')->count(),
            'total_apps' => \App\Modules\CMS\Models\JobApplication::on('landlord')->count(),
            'unread_msgs' => \App\Modules\CMS\Models\ContactMessage::on('landlord')->where('is_read', false)->count(),
            'total_msgs' => \App\Modules\CMS\Models\ContactMessage::on('landlord')->count(),
            'partners' => \App\Modules\CMS\Models\Partner::on('landlord')->count(),
            'testimonials' => \App\Modules\CMS\Models\Testimonial::on('landlord')->count(),
            'tenants' => \App\Modules\Tenancy\Models\Tenant::on('landlord')->count(),
        ];
    } catch (\Throwable) {
        $navCounts = [
            'portfolio' => 0, 'posts' => 0, 'team' => 0, 'careers' => 0,
            'new_apps' => 0, 'total_apps' => 0, 'unread_msgs' => 0, 'total_msgs' => 0,
            'partners' => 0, 'testimonials' => 0, 'tenants' => 0,
        ];
    }
@endphp

<x-layouts.base :title="$title ?? 'Empetix Studio — Super Admin & CMS'">
    <div x-data="{ sidebarOpen: false, previewMenuOpen: false }" class="min-h-screen flex bg-[#F4F6F9] text-ink-900 font-sans">

        <!-- Mobile Backdrop -->
        <div x-show="sidebarOpen" x-cloak @click="sidebarOpen = false"
             class="fixed inset-0 z-40 bg-ink-950/70 backdrop-blur-xs lg:hidden"></div>

        <!-- Executive Sidebar Navigation (Deep Obsidian Navy #050C1A -> #08142C) -->
        <aside :class="sidebarOpen ? 'translate-x-0' : (document.dir === 'rtl' ? 'translate-x-full lg:translate-x-0' : '-translate-x-full lg:translate-x-0')"
               class="fixed inset-y-0 start-0 z-50 lg:static w-76 bg-gradient-to-b from-[#050C1A] via-[#08142C] to-[#050C1A] text-slate-200 flex flex-col shrink-0 border-e border-white/10 shadow-2xl transition-transform duration-300 ease-in-out">

            <!-- Subtle Top Radial Glow -->
            <div class="absolute top-0 inset-x-0 h-44 bg-gradient-to-b from-accent-500/10 to-transparent pointer-events-none"></div>

            <!-- Sidebar Brand Header -->
            <div class="relative h-20 px-6 flex items-center justify-between border-b border-white/10 bg-[#040914]/60">
                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3.5 group">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-accent-500 to-accent-600 flex items-center justify-center shadow-glow group-hover:scale-105 transition-transform">
                        <img src="{{ asset('assets/img/logo/empetix-logo.png') }}" alt="E" class="h-5 w-auto object-contain brightness-0 invert" onerror="this.style.display='none'">
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="font-heading font-black text-white text-base tracking-tight">EMPETIX</span>
                            <span class="px-1.5 py-0.5 text-[9px] font-mono font-bold uppercase bg-accent-500/20 text-accent-500 border border-accent-500/30 rounded-md">CMS 4.0</span>
                        </div>
                        <span class="block text-[11px] text-slate-400 font-medium">
                            {{ app()->getLocale() === 'ar' ? 'نظام إدارة الموقع والـ SaaS' : 'Website & SaaS Control Studio' }}
                        </span>
                    </div>
                </a>
                <button @click="sidebarOpen = false" class="lg:hidden text-slate-400 hover:text-white p-1">✕</button>
            </div>

            <!-- Navigation Scroll Container -->
            <div class="relative flex-1 py-6 px-3.5 space-y-7 overflow-y-auto text-xs">
                <!-- 1. Mission Control -->
                <div>
                    <div class="px-3 mb-2.5 flex items-center justify-between">
                        <span class="text-[10px] font-mono font-bold uppercase tracking-widest text-slate-400">
                            {{ app()->getLocale() === 'ar' ? 'غرفة القيادة المركزية' : 'Command Center' }}
                        </span>
                    </div>
                    <nav class="space-y-1">
                        <a href="{{ route('admin.dashboard') }}"
                           class="flex items-center justify-between px-3.5 py-2.5 rounded-xl font-semibold transition-all duration-200 {{ request()->routeIs('admin.dashboard') ? 'bg-gradient-to-r from-accent-500 to-[#ff6b42] text-white shadow-glow' : 'text-slate-300 hover:bg-white/8 hover:text-white' }}">
                            <div class="flex items-center gap-3">
                                <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                                <span>{{ app()->getLocale() === 'ar' ? 'لوحة التحكم الشاملة' : 'Mission Control' }}</span>
                            </div>
                            <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                        </a>
                    </nav>
                </div>

                <!-- 2. Full Website Content Management (CMS) -->
                <div>
                    <div class="flex items-center justify-between px-3 mb-2.5">
                        <span class="text-[10px] font-mono font-bold uppercase tracking-widest text-slate-400">
                            {{ app()->getLocale() === 'ar' ? 'التحكم الكامل بالموقع (CMS)' : 'Full Website CMS' }}
                        </span>
                        <span class="inline-flex items-center gap-1 text-[9px] font-mono bg-emerald-500/15 text-emerald-400 border border-emerald-500/30 px-2 py-0.5 rounded-full font-bold">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span> Live Sync
                        </span>
                    </div>

                    <nav class="space-y-1">
                        <!-- Master Website Pages & Sections Editor -->
                        <a href="{{ route('admin.cms.settings') }}"
                           class="flex items-center justify-between px-3.5 py-2.5 rounded-xl font-medium transition-all duration-200 {{ request()->routeIs('admin.cms.settings*') ? 'bg-gradient-to-r from-accent-500 to-[#ff6b42] text-white font-bold shadow-glow' : 'text-slate-300 hover:bg-white/8 hover:text-white' }}">
                            <div class="flex items-center gap-3">
                                <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                <span>{{ app()->getLocale() === 'ar' ? 'محتوى الصفحات والواجهة (كل الأقسام)' : 'Pages, Hero & Sections Studio' }}</span>
                            </div>
                            <span class="text-[9px] font-mono px-1.5 py-0.5 rounded bg-white/10 text-white">7 Tabs</span>
                        </a>

                        <!-- Founders & Team -->
                        <a href="{{ route('admin.cms.team') }}"
                           class="flex items-center justify-between px-3.5 py-2.5 rounded-xl font-medium transition-all duration-200 {{ request()->routeIs('admin.cms.team*') ? 'bg-gradient-to-r from-accent-500 to-[#ff6b42] text-white font-bold shadow-glow' : 'text-slate-300 hover:bg-white/8 hover:text-white' }}">
                            <div class="flex items-center gap-3">
                                <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                <span>{{ app()->getLocale() === 'ar' ? 'المؤسسون وفريق العمل' : 'Founders & Team Directory' }}</span>
                            </div>
                            <span class="text-[10px] font-mono px-2 py-0.5 rounded-full bg-white/10 text-slate-300">{{ $navCounts['team'] }}</span>
                        </a>

                        <!-- Portfolio & Case Studies -->
                        <a href="{{ route('admin.cms.portfolio') }}"
                           class="flex items-center justify-between px-3.5 py-2.5 rounded-xl font-medium transition-all duration-200 {{ request()->routeIs('admin.cms.portfolio*') ? 'bg-gradient-to-r from-accent-500 to-[#ff6b42] text-white font-bold shadow-glow' : 'text-slate-300 hover:bg-white/8 hover:text-white' }}">
                            <div class="flex items-center gap-3">
                                <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                                <span>{{ app()->getLocale() === 'ar' ? 'سابقة الأعمال ودراسات الحالة' : 'Portfolio & Case Studies' }}</span>
                            </div>
                            <span class="text-[10px] font-mono px-2 py-0.5 rounded-full bg-white/10 text-slate-300">{{ $navCounts['portfolio'] }}</span>
                        </a>

                        <!-- Blog, Videos & Photo Essays -->
                        <a href="{{ route('admin.cms.posts') }}"
                           class="flex items-center justify-between px-3.5 py-2.5 rounded-xl font-medium transition-all duration-200 {{ request()->routeIs('admin.cms.posts*') ? 'bg-gradient-to-r from-accent-500 to-[#ff6b42] text-white font-bold shadow-glow' : 'text-slate-300 hover:bg-white/8 hover:text-white' }}">
                            <div class="flex items-center gap-3">
                                <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/></svg>
                                <span>{{ app()->getLocale() === 'ar' ? 'المدونة والمقالات والفيديو' : 'Journal, Video & Photo Essays' }}</span>
                            </div>
                            <span class="text-[10px] font-mono px-2 py-0.5 rounded-full bg-white/10 text-slate-300">{{ $navCounts['posts'] }}</span>
                        </a>

                        <!-- Open Careers & Jobs -->
                        <a href="{{ route('admin.cms.careers') }}"
                           class="flex items-center justify-between px-3.5 py-2.5 rounded-xl font-medium transition-all duration-200 {{ request()->routeIs('admin.cms.careers*') ? 'bg-gradient-to-r from-accent-500 to-[#ff6b42] text-white font-bold shadow-glow' : 'text-slate-300 hover:bg-white/8 hover:text-white' }}">
                            <div class="flex items-center gap-3">
                                <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                <span>{{ app()->getLocale() === 'ar' ? 'بوابة الوظائف والشواغر' : 'Careers & Job Postings' }}</span>
                            </div>
                            <span class="text-[10px] font-mono px-2 py-0.5 rounded-full bg-white/10 text-slate-300">{{ $navCounts['careers'] }}</span>
                        </a>

                        <!-- Job Applications & CVs -->
                        <a href="{{ route('admin.cms.applications') }}"
                           class="flex items-center justify-between px-3.5 py-2.5 rounded-xl font-medium transition-all duration-200 {{ request()->routeIs('admin.cms.applications*') ? 'bg-gradient-to-r from-accent-500 to-[#ff6b42] text-white font-bold shadow-glow' : 'text-slate-300 hover:bg-white/8 hover:text-white' }}">
                            <div class="flex items-center gap-3">
                                <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                <span>{{ app()->getLocale() === 'ar' ? 'طلبات التوظيف والـ CVs' : 'Job Applications & CVs' }}</span>
                            </div>
                            @if($navCounts['new_apps'] > 0)
                                <span class="text-[10px] font-mono font-bold px-2 py-0.5 rounded-full bg-emerald-500 text-white">{{ $navCounts['new_apps'] }} new</span>
                            @else
                                <span class="text-[10px] font-mono px-2 py-0.5 rounded-full bg-white/10 text-slate-300">{{ $navCounts['total_apps'] }}</span>
                            @endif
                        </a>

                        <!-- Partners & Client Logos -->
                        <a href="{{ route('admin.cms.partners') }}"
                           class="flex items-center justify-between px-3.5 py-2.5 rounded-xl font-medium transition-all duration-200 {{ request()->routeIs('admin.cms.partners*') ? 'bg-gradient-to-r from-accent-500 to-[#ff6b42] text-white font-bold shadow-glow' : 'text-slate-300 hover:bg-white/8 hover:text-white' }}">
                            <div class="flex items-center gap-3">
                                <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/></svg>
                                <span>{{ app()->getLocale() === 'ar' ? 'شركاء النجاح والشعارات' : 'Partners & Client Logos' }}</span>
                            </div>
                            <span class="text-[10px] font-mono px-2 py-0.5 rounded-full bg-white/10 text-slate-300">{{ $navCounts['partners'] }}</span>
                        </a>

                        <!-- Client Testimonials -->
                        <a href="{{ route('admin.cms.testimonials') }}"
                           class="flex items-center justify-between px-3.5 py-2.5 rounded-xl font-medium transition-all duration-200 {{ request()->routeIs('admin.cms.testimonials*') ? 'bg-gradient-to-r from-accent-500 to-[#ff6b42] text-white font-bold shadow-glow' : 'text-slate-300 hover:bg-white/8 hover:text-white' }}">
                            <div class="flex items-center gap-3">
                                <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>
                                <span>{{ app()->getLocale() === 'ar' ? 'آراء وتقييمات العملاء' : 'Testimonials & Reviews' }}</span>
                            </div>
                            <span class="text-[10px] font-mono px-2 py-0.5 rounded-full bg-white/10 text-slate-300">{{ $navCounts['testimonials'] }}</span>
                        </a>

                        <!-- Contact Inquiries Inbox -->
                        <a href="{{ route('admin.cms.inquiries') }}"
                           class="flex items-center justify-between px-3.5 py-2.5 rounded-xl font-medium transition-all duration-200 {{ request()->routeIs('admin.cms.inquiries*') ? 'bg-gradient-to-r from-accent-500 to-[#ff6b42] text-white font-bold shadow-glow' : 'text-slate-300 hover:bg-white/8 hover:text-white' }}">
                            <div class="flex items-center gap-3">
                                <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                <span>{{ app()->getLocale() === 'ar' ? 'صندوق رسائل وطلبات العملاء' : 'Client Inquiries Inbox' }}</span>
                            </div>
                            @if($navCounts['unread_msgs'] > 0)
                                <span class="text-[10px] font-mono font-bold px-2 py-0.5 rounded-full bg-accent-500 text-white animate-pulse">{{ $navCounts['unread_msgs'] }} new</span>
                            @else
                                <span class="text-[10px] font-mono px-2 py-0.5 rounded-full bg-white/10 text-slate-300">{{ $navCounts['total_msgs'] }}</span>
                            @endif
                        </a>
                    </nav>
                </div>

                <!-- 3. SaaS Multi-Tenancy & Subscriptions -->
                <div>
                    <div class="px-3 mb-2.5 flex items-center justify-between">
                        <span class="text-[10px] font-mono font-bold uppercase tracking-widest text-slate-400">
                            {{ app()->getLocale() === 'ar' ? 'منظومة الـ SaaS والشركات' : 'Multi-Tenant SaaS Engine' }}
                        </span>
                    </div>
                    <nav class="space-y-1">
                        <a href="{{ route('admin.tenants') }}"
                           class="flex items-center justify-between px-3.5 py-2.5 rounded-xl font-medium transition-all duration-200 {{ request()->routeIs('admin.tenants*') ? 'bg-gradient-to-r from-accent-500 to-[#ff6b42] text-white font-bold shadow-glow' : 'text-slate-300 hover:bg-white/8 hover:text-white' }}">
                            <div class="flex items-center gap-3">
                                <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                <span>{{ app()->getLocale() === 'ar' ? 'الشركات وقواعد البيانات' : 'Tenants & Databases' }}</span>
                            </div>
                            <span class="text-[10px] font-mono px-2 py-0.5 rounded-full bg-white/10 text-slate-300">{{ $navCounts['tenants'] }}</span>
                        </a>
                        <a href="{{ route('admin.subscriptions') }}"
                           class="flex items-center justify-between px-3.5 py-2.5 rounded-xl font-medium transition-all duration-200 {{ request()->routeIs('admin.subscriptions*') || request()->routeIs('admin.invoices*') ? 'bg-gradient-to-r from-accent-500 to-[#ff6b42] text-white font-bold shadow-glow' : 'text-slate-300 hover:bg-white/8 hover:text-white' }}">
                            <div class="flex items-center gap-3">
                                <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                <span>{{ app()->getLocale() === 'ar' ? 'الخطط وفواتير الاشتراكات' : 'Plans & Billing Invoices' }}</span>
                            </div>
                        </a>
                    </nav>
                </div>
            </div>

            <!-- Sidebar Footer / Admin Profile -->
            <div class="relative p-4 border-t border-white/10 bg-[#040914]/80">
                <div class="flex items-center justify-between gap-2">
                    <div class="flex items-center gap-3 min-w-0">
                        <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-accent-500 to-accent-600 text-white font-bold flex items-center justify-center font-mono text-xs shadow-md shrink-0">
                            MS
                        </div>
                        <div class="overflow-hidden">
                            <p class="font-bold text-white truncate text-xs">Mohanad Shehata</p>
                            <p class="text-[10px] text-slate-400 font-mono truncate">Co-Founder & Super Admin</p>
                        </div>
                    </div>
                    <a href="{{ route('home') }}" target="_blank"
                       class="px-2.5 py-1.5 bg-white/10 hover:bg-accent-500 text-white rounded-lg font-mono text-[10px] font-bold transition-all shrink-0"
                       title="Open Live Website">
                        Site ↗
                    </a>
                </div>
            </div>
        </aside>

        <!-- Main Workspace Content Area -->
        <div class="flex-1 flex flex-col min-w-0">
            <!-- Executive Glassmorphic Topbar -->
            <header class="h-20 bg-white/90 backdrop-blur-md border-b border-slate-200/80 px-6 sm:px-8 flex items-center justify-between sticky top-0 z-30 shadow-xs">
                <div class="flex items-center gap-4">
                    <button @click="sidebarOpen = !sidebarOpen" class="lg:hidden p-2 rounded-lg text-slate-600 hover:bg-slate-100 hover:text-ink-900">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    </button>
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-accent-500"></span>
                            <span class="text-[11px] font-mono uppercase tracking-wider text-slate-400 font-bold">
                                {{ app()->getLocale() === 'ar' ? 'مركز التحكم الكامل بالموقع' : 'Empetix Executive Control Center' }}
                            </span>
                        </div>
                        <h1 class="text-lg sm:text-xl font-heading font-extrabold text-ink-900 tracking-tight">{{ $header ?? 'Mission Control' }}</h1>
                    </div>
                </div>

                <!-- Right Toolbar: Quick Page Previewer, Locale, Live Site Button -->
                <div class="flex items-center gap-2.5">
                    <!-- Quick Live Page Preview Dropdown -->
                    <div class="relative hidden md:block" @click.away="previewMenuOpen = false">
                        <button @click="previewMenuOpen = !previewMenuOpen"
                                class="inline-flex items-center gap-2 px-3.5 py-2 text-xs font-semibold rounded-xl border border-slate-200 hover:border-ink-900 bg-slate-50/70 hover:bg-white text-ink-900 transition-all">
                            <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                            <span>{{ app()->getLocale() === 'ar' ? 'معاينة صفحات الموقع' : 'Preview Site Pages' }}</span>
                            <svg class="w-3.5 h-3.5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>

                        <div x-show="previewMenuOpen" x-cloak
                             class="absolute end-0 mt-2 w-60 bg-white border border-slate-200 rounded-2xl shadow-xl py-2 z-50 text-xs">
                            <div class="px-3.5 py-1.5 border-b border-slate-100 text-[10px] font-mono uppercase text-slate-400 font-bold">
                                {{ app()->getLocale() === 'ar' ? 'فتح الصفحة الحية في تبويب جديد' : 'Open Live Page in New Tab' }}
                            </div>
                            <a href="{{ route('home') }}" target="_blank" class="flex items-center justify-between px-3.5 py-2 hover:bg-slate-50 text-ink-900 font-medium">
                                <span>{{ app()->getLocale() === 'ar' ? '🏠 الصفحة الرئيسية (Home)' : '🏠 Homepage (/)' }}</span>
                                <span class="font-mono text-[10px] text-slate-400">↗</span>
                            </a>
                            <a href="{{ route('site.about') }}" target="_blank" class="flex items-center justify-between px-3.5 py-2 hover:bg-slate-50 text-ink-900 font-medium">
                                <span>{{ app()->getLocale() === 'ar' ? '🏛️ عن الشركة والمؤسسين (About)' : '🏛️ About & Founders (/about)' }}</span>
                                <span class="font-mono text-[10px] text-slate-400">↗</span>
                            </a>
                            <a href="{{ route('site.work') }}" target="_blank" class="flex items-center justify-between px-3.5 py-2 hover:bg-slate-50 text-ink-900 font-medium">
                                <span>{{ app()->getLocale() === 'ar' ? '💼 سابقة الأعمال (Projects)' : '💼 Projects & Cases (/work)' }}</span>
                                <span class="font-mono text-[10px] text-slate-400">↗</span>
                            </a>
                            <a href="{{ route('site.blog') }}" target="_blank" class="flex items-center justify-between px-3.5 py-2 hover:bg-slate-50 text-ink-900 font-medium">
                                <span>{{ app()->getLocale() === 'ar' ? '📰 المدونة والفيديو (Blog)' : '📰 Blog & Media (/blog)' }}</span>
                                <span class="font-mono text-[10px] text-slate-400">↗</span>
                            </a>
                            <a href="{{ route('site.team') }}" target="_blank" class="flex items-center justify-between px-3.5 py-2 hover:bg-slate-50 text-ink-900 font-medium">
                                <span>{{ app()->getLocale() === 'ar' ? '👥 فريق العمل (Team)' : '👥 Our Team (/team)' }}</span>
                                <span class="font-mono text-[10px] text-slate-400">↗</span>
                            </a>
                            <a href="{{ route('site.careers') }}" target="_blank" class="flex items-center justify-between px-3.5 py-2 hover:bg-slate-50 text-ink-900 font-medium">
                                <span>{{ app()->getLocale() === 'ar' ? '🚀 بوابة الوظائف (Careers)' : '🚀 Careers Portal (/careers)' }}</span>
                                <span class="font-mono text-[10px] text-slate-400">↗</span>
                            </a>
                            <a href="{{ route('site.partners') }}" target="_blank" class="flex items-center justify-between px-3.5 py-2 hover:bg-slate-50 text-ink-900 font-medium">
                                <span>{{ app()->getLocale() === 'ar' ? '🤝 الشركاء (Partners)' : '🤝 Partners (/partners)' }}</span>
                                <span class="font-mono text-[10px] text-slate-400">↗</span>
                            </a>
                            <a href="{{ route('site.contact') }}" target="_blank" class="flex items-center justify-between px-3.5 py-2 hover:bg-slate-50 text-ink-900 font-medium">
                                <span>{{ app()->getLocale() === 'ar' ? '✉️ تواصل معنا (Contact)' : '✉️ Contact Us (/contact)' }}</span>
                                <span class="font-mono text-[10px] text-slate-400">↗</span>
                            </a>
                        </div>
                    </div>

                    <a href="{{ route('home') }}" target="_blank"
                       class="inline-flex items-center gap-2 px-4 py-2 text-xs font-bold bg-ink-900 hover:bg-accent-500 text-white rounded-xl shadow-xs transition-all">
                        <span>{{ app()->getLocale() === 'ar' ? 'زيارة الموقع الحي' : 'Visit Live Site' }}</span>
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                    </a>

                    <a href="{{ route('locale.switch', app()->getLocale() === 'ar' ? 'en' : 'ar') }}"
                       class="px-3 py-2 text-xs font-mono font-bold uppercase tracking-wider rounded-xl border border-slate-200 hover:border-accent-500 hover:text-accent-500 bg-white transition-colors">
                        {{ app()->getLocale() === 'ar' ? 'EN' : 'عربي' }}
                    </a>

                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit"
                                class="px-3 py-2 text-xs font-semibold text-slate-500 hover:text-rose-600 hover:bg-rose-50 rounded-xl transition-colors">
                            {{ app()->getLocale() === 'ar' ? 'خروج' : 'Sign out' }}
                        </button>
                    </form>
                </div>
            </header>

            <!-- Main Page Body -->
            <main class="flex-1 p-6 sm:p-8 overflow-y-auto">
                {{ $slot }}
            </main>
        </div>
    </div>
</x-layouts.base>
