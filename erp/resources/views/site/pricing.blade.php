@php
$plans = \App\Modules\Billing\Models\Plan::on('landlord')->where('is_active', true)->orderBy('sort_order')->get();
@endphp

<x-layouts.site :title="'Plans & Pricing — Empetix Enterprise SaaS'">
    <section class="py-20 lg:py-28 bg-slate-50 dark:bg-slate-950">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-16">
                <span class="text-xs font-bold text-indigo-600 uppercase tracking-widest">{{ app()->getLocale() === 'ar' ? 'الخطط والأسعار' : 'Transparent Pricing' }}</span>
                <h1 class="mt-2 text-4xl sm:text-5xl font-extrabold text-slate-900 dark:text-white tracking-tight">
                    {{ app()->getLocale() === 'ar' ? 'اختر الخطة المناسبة لحجم ونمو شركتك' : 'Scalable Plans Designed for Agencies & Software Teams' }}
                </h1>
                <p class="mt-4 text-slate-600 dark:text-slate-400">
                    {{ app()->getLocale() === 'ar' ? 'جميع الخطط تشمل قاعدة بيانات مخصصة ومعزولة تماماً مع فترة تجربة مجانية 14 يوماً.' : 'All tiers feature dedicated database isolation with full support for Arabic/English & RTL.' }}
                </p>
            </div>

            <!-- Registration Livewire Component -->
            <livewire:tenancy.register-company />
        </div>
    </section>
</x-layouts.site>
