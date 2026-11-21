<div class="max-w-5xl mx-auto space-y-8">
    <!-- Active Plan Overview Card -->
    <x-ui.card :title="app()->getLocale() === 'ar' ? 'الخطة الحالية والاشتراك' : 'Current Active Plan'">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-6">
            <div>
                <div class="flex items-center gap-3">
                    <h3 class="text-2xl font-extrabold text-slate-900 dark:text-white">
                        {{ $tenant?->isInternal() ? 'Holding Company (Internal HQ)' : ($plan?->name ?? 'Free Trialing') }}
                    </h3>
                    <x-ui.badge :variant="$tenant?->isInternal() ? 'primary' : 'success'">
                        {{ $tenant?->isInternal() ? 'Unlimited Access' : ucfirst($subscription?->status ?? 'Active') }}
                    </x-ui.badge>
                </div>
                <p class="mt-1 text-sm text-slate-500">
                    {{ $tenant?->isInternal() ? 'Permanent full holding company access with all modules unlocked.' : ($plan?->description ?? '14-Day Free Evaluation Period') }}
                </p>

                @if(! $tenant?->isInternal() && $tenant?->trial_ends_at)
                    <p class="mt-3 text-xs font-semibold text-amber-600 dark:text-amber-400">
                        ⏳ {{ app()->getLocale() === 'ar' ? 'تنتهي الفترة التجريبية في: ' : 'Trial period expires on: ' }}
                        {{ $tenant->trial_ends_at->format('d M Y') }} ({{ $tenant->trial_ends_at->diffForHumans() }})
                    </p>
                @endif
            </div>

            @if(! $tenant?->isInternal() && $plan)
                <div class="text-end">
                    <p class="text-3xl font-extrabold text-indigo-600 dark:text-indigo-400">
                        {{ $plan->monthly_price->format() }}
                    </p>
                    <p class="text-xs text-slate-400">/{{ app()->getLocale() === 'ar' ? 'شهرياً' : 'monthly' }}</p>
                </div>
            @endif
        </div>

        <!-- Unlocked Modules Badges -->
        <div class="mt-6 pt-6 border-t border-slate-100 dark:border-slate-800">
            <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-3">
                {{ app()->getLocale() === 'ar' ? 'الوحدات المفعلة في خطتك' : 'Included Enterprise Modules' }}
            </h4>
            <div class="flex flex-wrap gap-2">
                @foreach(['hr' => 'HRM & Payroll', 'crm' => 'CRM & Leads', 'marketing' => 'Marketing & Campaigns', 'projects' => 'Software Projects', 'finance' => 'Finance & Invoices'] as $key => $label)
                    <div class="flex items-center gap-1.5 px-3 py-1.5 rounded-xl border text-xs font-semibold {{ $tenant?->hasFeature($key) ? 'bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-300 border-emerald-200 dark:border-emerald-800' : 'bg-slate-50 dark:bg-slate-800 text-slate-400 border-slate-200 dark:border-slate-700 opacity-60' }}">
                        <span>{{ $tenant?->hasFeature($key) ? '✓' : '✕' }}</span>
                        <span>{{ $label }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    </x-ui.card>

    <!-- Available Plans for Upgrade -->
    @if(! $tenant?->isInternal())
        <div>
            <h3 class="text-xl font-extrabold text-slate-900 dark:text-white mb-4">
                {{ app()->getLocale() === 'ar' ? 'ترقية الخطة أو التجديد' : 'Available Plans for Upgrade' }}
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @foreach($allPlans as $p)
                    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-6 shadow-xs flex flex-col justify-between">
                        <div>
                            <h4 class="font-bold text-lg text-slate-900 dark:text-white">{{ $p->name }}</h4>
                            <p class="text-xs text-slate-500 mt-1 mb-4">{{ $p->description }}</p>
                            <p class="text-2xl font-extrabold text-indigo-600 dark:text-indigo-400">
                                {{ $p->monthly_price->format() }}
                                <span class="text-xs text-slate-400 font-normal">/mo</span>
                            </p>
                        </div>

                        <div class="mt-6 pt-4 border-t border-slate-100 dark:border-slate-800">
                            @if($plan?->id === $p->id)
                                <button disabled class="w-full py-2 text-xs font-bold rounded-lg bg-slate-100 text-slate-500 cursor-not-allowed">
                                    {{ app()->getLocale() === 'ar' ? 'الخطة الحالية' : 'Current Plan' }}
                                </button>
                            @else
                                <button
                                    wire:click="upgradeToPlan({{ $p->id }})"
                                    class="w-full py-2 text-xs font-bold rounded-lg bg-indigo-600 hover:bg-indigo-700 text-white transition-colors"
                                >
                                    {{ app()->getLocale() === 'ar' ? 'الترقية لهذه الخطة' : 'Upgrade Plan' }} →
                                </button>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>
