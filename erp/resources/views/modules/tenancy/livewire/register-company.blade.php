<div class="max-w-4xl mx-auto bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-8 sm:p-12 shadow-2xl">
    <div class="text-center max-w-lg mx-auto mb-10">
        <h2 class="text-3xl font-extrabold text-slate-900 dark:text-white">
            {{ app()->getLocale() === 'ar' ? 'سجّل شركتك وابدأ التجربة المجانية' : 'Register Your Company Workspace' }}
        </h2>
        <p class="mt-2 text-sm text-slate-600 dark:text-slate-400">
            {{ app()->getLocale() === 'ar' ? 'فترة تجريبية 14 يوماً تشمل قاعدة بيانات مستقلة بالكامل وبدون بطاقة ائتمانية.' : '14-day free trial with fully isolated multi-database tenancy.' }}
        </p>
    </div>

    <!-- Step 1: Choose Plan -->
    <div class="mb-10">
        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-4 text-center">
            {{ app()->getLocale() === 'ar' ? 'اختر خطة الاشتراك المناسبة' : 'Select Your Desired Subscription Plan' }}
        </label>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            @foreach($plans as $plan)
                <div
                    wire:click="selectPlan({{ $plan->id }})"
                    class="cursor-pointer p-5 rounded-2xl border-2 transition-all flex flex-col justify-between {{ $selectedPlanId === $plan->id ? 'border-indigo-600 bg-indigo-50/50 dark:bg-indigo-950/40 shadow-md' : 'border-slate-200 dark:border-slate-800 hover:border-slate-300' }}"
                >
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <h4 class="font-bold text-slate-900 dark:text-white text-base">{{ $plan->name }}</h4>
                            @if($selectedPlanId === $plan->id)
                                <span class="w-5 h-5 rounded-full bg-indigo-600 text-white text-xs flex items-center justify-center">✓</span>
                            @endif
                        </div>
                        <p class="text-xs text-slate-500 mb-3">{{ $plan->description }}</p>
                        <p class="text-2xl font-extrabold text-indigo-600 dark:text-indigo-400">
                            {{ $plan->monthly_price->format(app()->getLocale()) }}
                            <span class="text-xs font-normal text-slate-500">/{{ app()->getLocale() === 'ar' ? 'شهر' : 'mo' }}</span>
                        </p>
                    </div>

                    <div class="mt-4 pt-3 border-t border-slate-100 dark:border-slate-800 flex flex-wrap gap-1">
                        @foreach((array) $plan->features as $f)
                            <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 uppercase">
                                {{ $f }}
                            </span>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Step 2: Company & Owner Details -->
    <form wire:submit.prevent="register" class="space-y-6">
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
            <x-ui.input
                wire:model.live="companyName"
                :label="app()->getLocale() === 'ar' ? 'اسم الشركة' : 'Company Name'"
                name="companyName"
                placeholder="Acme Technologies"
                :error="$errors->first('companyName')"
                required
            />

            <div>
                <x-ui.input
                    wire:model="slug"
                    :label="app()->getLocale() === 'ar' ? 'رابط النطاق الفرعي' : 'Workspace Subdomain'"
                    name="slug"
                    placeholder="acme"
                    :error="$errors->first('slug')"
                    required
                />
                <p class="mt-1 text-[11px] text-slate-500">
                    http://<span class="font-bold text-indigo-600">{{ $slug ?: 'subdomain' }}</span>.empetix.test
                </p>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
            <x-ui.input
                wire:model="ownerName"
                :label="app()->getLocale() === 'ar' ? 'اسم المسؤول' : 'Your Full Name'"
                name="ownerName"
                placeholder="Mohamed Ahmed"
                :error="$errors->first('ownerName')"
                required
            />

            <x-ui.input
                wire:model="ownerEmail"
                type="email"
                :label="app()->getLocale() === 'ar' ? 'البريد الإلكتروني' : 'Business Email'"
                name="ownerEmail"
                placeholder="admin@company.com"
                :error="$errors->first('ownerEmail')"
                required
            />
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
            <x-ui.input
                wire:model="ownerPassword"
                type="password"
                :label="app()->getLocale() === 'ar' ? 'كلمة المرور' : 'Password'"
                name="ownerPassword"
                placeholder="••••••••"
                :error="$errors->first('ownerPassword')"
                required
            />

            <x-ui.input
                wire:model="phone"
                type="tel"
                :label="app()->getLocale() === 'ar' ? 'رقم الهاتف (اختياري)' : 'Phone Number (Optional)'"
                name="phone"
                placeholder="+20 100 000 0000"
                :error="$errors->first('phone')"
            />
        </div>

        <div class="pt-4 text-center">
            <x-ui.button type="submit" size="lg" class="w-full sm:w-auto px-10 text-base font-extrabold shadow-lg shadow-indigo-600/30">
                <span wire:loading.remove wire:target="register">
                    {{ app()->getLocale() === 'ar' ? 'إنشاء مساحة العمل والبدء فوراً' : 'Create Company Workspace Now' }} →
                </span>
                <span wire:loading wire:target="register">
                    {{ app()->getLocale() === 'ar' ? 'جاري تجهيز قاعدة البيانات وتثبيت المنظومة...' : 'Provisioning Isolated Database & Workspace...' }}
                </span>
            </x-ui.button>
        </div>
    </form>
</div>
