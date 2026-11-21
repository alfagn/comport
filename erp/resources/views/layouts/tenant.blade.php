@php
/** @var \App\Modules\Tenancy\Models\Tenant|null $currentTenant */
$currentTenant = \App\Modules\Tenancy\Models\Tenant::current();
$currentPlan = $currentTenant?->currentSubscription?->plan;
@endphp

<x-layouts.base :title="($title ?? 'Workspace') . ' — ' . ($currentTenant?->name ?? 'Empetix ERP')">
    <div x-data="{ sidebarOpen: false }" class="min-h-screen flex bg-slate-100 dark:bg-slate-950">
        <!-- Sidebar Navigation -->
        <aside class="w-68 bg-slate-900 text-slate-300 flex flex-col shrink-0 border-e border-slate-800">
            <!-- Sidebar Header / Company Profile -->
            <div class="h-18 px-5 flex items-center justify-between border-b border-slate-800">
                <div class="flex items-center gap-3 overflow-hidden">
                    <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-indigo-500 to-indigo-700 flex items-center justify-center text-white font-extrabold text-base shrink-0">
                        {{ mb_substr($currentTenant?->name ?? 'E', 0, 1) }}
                    </div>
                    <div class="truncate">
                        <span class="block text-sm font-bold text-white tracking-tight truncate leading-tight">
                            {{ $currentTenant?->name ?? 'Workspace' }}
                        </span>
                        <div class="flex items-center gap-1.5 mt-0.5">
                            @if($currentTenant?->isInternal())
                                <span class="px-1.5 py-0.2 rounded text-[10px] font-bold bg-amber-500/20 text-amber-400">HQ Unlimited</span>
                            @else
                                <span class="px-1.5 py-0.2 rounded text-[10px] font-bold bg-indigo-500/20 text-indigo-300">
                                    {{ $currentPlan?->name ?? 'Trialing' }}
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Nav Links -->
            <div class="flex-1 py-5 px-3 space-y-5 overflow-y-auto text-sm">
                <!-- Overview -->
                <div>
                    <a href="{{ route('tenant.dashboard') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg font-medium transition-colors {{ request()->routeIs('tenant.dashboard') ? 'bg-indigo-600 text-white' : 'hover:bg-slate-800 text-slate-300' }}">
                        <svg class="w-5 h-5 opacity-80" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                        {{ app()->getLocale() === 'ar' ? 'الرئيسية' : 'Dashboard' }}
                    </a>
                </div>

                <!-- HRM Module -->
                @if($currentTenant?->hasFeature('hr'))
                    <div>
                        <p class="px-3 text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-1.5">
                            {{ app()->getLocale() === 'ar' ? 'الموارد البشرية (HR)' : 'Human Resources' }}
                        </p>
                        <div class="space-y-0.5">
                            <a href="{{ route('tenant.hrm.employees') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg font-medium transition-colors {{ request()->routeIs('tenant.hrm.employees*') ? 'bg-indigo-600 text-white' : 'hover:bg-slate-800 text-slate-300' }}">
                                <svg class="w-4.5 h-4.5 opacity-80" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                {{ app()->getLocale() === 'ar' ? 'الموظفين' : 'Employees' }}
                            </a>
                            <a href="{{ route('tenant.hrm.payroll') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg font-medium transition-colors {{ request()->routeIs('tenant.hrm.payroll*') ? 'bg-indigo-600 text-white' : 'hover:bg-slate-800 text-slate-300' }}">
                                <svg class="w-4.5 h-4.5 opacity-80" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                {{ app()->getLocale() === 'ar' ? 'المرتبات (Payroll)' : 'Payroll & Slips' }}
                            </a>
                        </div>
                    </div>
                @endif

                <!-- CRM Module -->
                @if($currentTenant?->hasFeature('crm'))
                    <div>
                        <p class="px-3 text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-1.5">
                            {{ app()->getLocale() === 'ar' ? 'إدارة العملاء (CRM)' : 'CRM & Pipeline' }}
                        </p>
                        <div class="space-y-0.5">
                            <a href="{{ route('tenant.crm.leads') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg font-medium transition-colors {{ request()->routeIs('tenant.crm.leads*') ? 'bg-indigo-600 text-white' : 'hover:bg-slate-800 text-slate-300' }}">
                                <svg class="w-4.5 h-4.5 opacity-80" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                                {{ app()->getLocale() === 'ar' ? 'العملاء المحتملين (Leads)' : 'Leads & Pipeline' }}
                            </a>
                        </div>
                    </div>
                @endif

                <!-- Marketing Module -->
                @if($currentTenant?->hasFeature('marketing'))
                    <div>
                        <p class="px-3 text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-1.5">
                            {{ app()->getLocale() === 'ar' ? 'إدارة التسويق' : 'Marketing & Ads' }}
                        </p>
                        <div class="space-y-0.5">
                            <a href="{{ route('tenant.marketing.campaigns') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg font-medium transition-colors {{ request()->routeIs('tenant.marketing.campaigns*') ? 'bg-indigo-600 text-white' : 'hover:bg-slate-800 text-slate-300' }}">
                                <svg class="w-4.5 h-4.5 opacity-80" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/></svg>
                                {{ app()->getLocale() === 'ar' ? 'الحملات الإعلانية' : 'Ad Campaigns' }}
                            </a>
                        </div>
                    </div>
                @endif

                <!-- Projects Module -->
                @if($currentTenant?->hasFeature('projects'))
                    <div>
                        <p class="px-3 text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-1.5">
                            {{ app()->getLocale() === 'ar' ? 'المشاريع والبرمجيات' : 'Projects & Tasks' }}
                        </p>
                        <div class="space-y-0.5">
                            <a href="{{ route('tenant.projects.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg font-medium transition-colors {{ request()->routeIs('tenant.projects*') ? 'bg-indigo-600 text-white' : 'hover:bg-slate-800 text-slate-300' }}">
                                <svg class="w-4.5 h-4.5 opacity-80" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                                {{ app()->getLocale() === 'ar' ? 'المشاريع والمهام' : 'Projects & Tasks' }}
                            </a>
                        </div>
                    </div>
                @endif

                <!-- Finance Module -->
                @if($currentTenant?->hasFeature('finance'))
                    <div>
                        <p class="px-3 text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-1.5">
                            {{ app()->getLocale() === 'ar' ? 'المالية والفواتير' : 'Finance & Billing' }}
                        </p>
                        <div class="space-y-0.5">
                            <a href="{{ route('tenant.finance.invoices') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg font-medium transition-colors {{ request()->routeIs('tenant.finance.invoices*') ? 'bg-indigo-600 text-white' : 'hover:bg-slate-800 text-slate-300' }}">
                                <svg class="w-4.5 h-4.5 opacity-80" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                {{ app()->getLocale() === 'ar' ? 'فواتير العملاء' : 'Invoices' }}
                            </a>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Footer / Tenant Settings -->
            <div class="p-3 border-t border-slate-800 text-xs flex items-center justify-between">
                <a href="{{ route('tenant.subscription') }}" class="text-slate-400 hover:text-white flex items-center gap-1.5">
                    <span>💳</span>
                    <span>{{ app()->getLocale() === 'ar' ? 'الاشتراك' : 'Plan' }}</span>
                </a>
                <span class="text-slate-600">|</span>
                <a href="{{ route('tenant.settings') }}" class="text-slate-400 hover:text-white flex items-center gap-1.5">
                    <span>⚙</span>
                    <span>{{ app()->getLocale() === 'ar' ? 'إعدادات الشركة' : 'Settings' }}</span>
                </a>
            </div>
        </aside>

        <!-- Main Body -->
        <div class="flex-1 flex flex-col min-w-0">
            <!-- Topbar -->
            <header class="h-18 bg-white dark:bg-slate-900 border-b border-slate-200 dark:border-slate-800 px-6 sm:px-8 flex items-center justify-between">
                <div>
                    <h1 class="text-lg font-bold text-slate-900 dark:text-white">{{ $header ?? 'ERP Workspace' }}</h1>
                </div>

                <div class="flex items-center gap-4">
                    <a href="{{ route('locale.switch', app()->getLocale() === 'ar' ? 'en' : 'ar') }}" class="px-2.5 py-1 text-xs font-semibold rounded-lg border border-slate-300 dark:border-slate-700 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors">
                        {{ app()->getLocale() === 'ar' ? 'English' : 'العربية' }}
                    </a>
                    <span class="text-xs font-semibold text-slate-700 dark:text-slate-300">
                        {{ auth()->user()?->name ?? 'Admin' }}
                    </span>
                </div>
            </header>

            <main class="flex-1 p-6 sm:p-8 overflow-y-auto">
                {{ $slot }}
            </main>
        </div>
    </div>
</x-layouts.base>
