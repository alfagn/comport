<div class="space-y-6 max-w-7xl mx-auto font-sans">
    <!-- Executive Header Card -->
    <div class="bg-gradient-to-r from-[#050C1A] via-[#08142C] to-[#0D1E3D] border border-white/10 rounded-2xl p-6 sm:p-8 text-white shadow-card flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-accent-500/20 text-accent-500 border border-accent-500/30 text-[11px] font-mono font-bold uppercase tracking-wider mb-2">
                <span class="w-2 h-2 rounded-full bg-accent-500"></span>
                <span>Multi-Database SaaS Architecture</span>
            </div>
            <h2 class="text-2xl font-heading font-black tracking-tight">
                {{ app()->getLocale() === 'ar' ? 'إدارة الشركات المشتركة وقواعد البيانات (SaaS Tenants)' : 'Registered SaaS Tenants & Isolated Databases' }}
            </h2>
            <p class="text-xs sm:text-sm text-slate-300 mt-1">
                {{ app()->getLocale() === 'ar' ? 'إدارة الشركات المشتركة في نظام الـ ERP، والتحقق من النطاقات، وتفعيل أو إيقاف مساحات العمل المعزولة.' : 'Manage subscribed software & marketing companies, custom domains, and isolated tenant databases.' }}
            </p>
        </div>

        <a href="{{ route('site.pricing') }}" target="_blank"
           class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-accent-500 to-[#ff6b42] text-white font-bold text-xs uppercase tracking-wider rounded-xl shadow-glow transition-all shrink-0">
            <span>+ {{ app()->getLocale() === 'ar' ? 'تسجيل شركة جديدة' : 'Provision New Tenant' }}</span>
        </a>
    </div>

    <!-- Search & Filter bar -->
    <div class="bg-white border border-slate-200/80 rounded-2xl p-4 shadow-subtle flex flex-col sm:flex-row items-center justify-between gap-4 text-xs">
        <div class="w-full sm:w-80">
            <input type="text" wire:model.live.debounce.300ms="search"
                   placeholder="{{ app()->getLocale() === 'ar' ? 'بحث بالاسم، النطاق، أو البريد...' : 'Search company, slug, or email...' }}"
                   class="w-full px-3.5 py-2.5 border border-slate-200 rounded-xl focus:border-accent-500 outline-none" />
        </div>

        <div class="w-full sm:w-52">
            <select wire:model.live="statusFilter" class="w-full px-3.5 py-2.5 border border-slate-200 rounded-xl font-mono bg-white">
                <option value="">{{ app()->getLocale() === 'ar' ? 'كل الحالات' : 'All Statuses' }}</option>
                <option value="active">{{ app()->getLocale() === 'ar' ? 'نشط فقط' : 'Active Only' }}</option>
                <option value="suspended">{{ app()->getLocale() === 'ar' ? 'موقوف فقط' : 'Suspended Only' }}</option>
            </select>
        </div>
    </div>

    <!-- Tenants Table -->
    <div class="bg-white border border-slate-200/80 rounded-2xl shadow-subtle overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-start text-xs font-sans">
                <thead>
                    <tr class="border-b border-slate-200 bg-slate-50/70 text-slate-400 font-mono uppercase text-[10px]">
                        <th class="py-3.5 px-5 text-start">{{ app()->getLocale() === 'ar' ? 'الشركة' : 'Company' }}</th>
                        <th class="py-3.5 px-4 text-start">{{ app()->getLocale() === 'ar' ? 'قاعدة البيانات المعزولة' : 'Isolated Database' }}</th>
                        <th class="py-3.5 px-4 text-start">{{ app()->getLocale() === 'ar' ? 'الخطة' : 'Plan' }}</th>
                        <th class="py-3.5 px-4 text-start">{{ app()->getLocale() === 'ar' ? 'النطاقات / المسار' : 'Domains / Workspace' }}</th>
                        <th class="py-3.5 px-4 text-start">{{ app()->getLocale() === 'ar' ? 'الحالة' : 'Status' }}</th>
                        <th class="py-3.5 px-5 text-end">{{ app()->getLocale() === 'ar' ? 'الإجراءات' : 'Actions' }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($tenants as $t)
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="py-4 px-5">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-xl bg-ink-900 text-white flex items-center justify-center font-heading font-black text-sm">
                                        {{ mb_substr($t->name, 0, 1) }}
                                    </div>
                                    <div>
                                        <p class="font-bold text-ink-900 text-sm">{{ $t->name }}</p>
                                        <p class="text-[11px] text-slate-400 font-mono">{{ $t->email }} • {{ $t->created_at->format('d M Y') }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="py-4 px-4 font-mono text-xs text-slate-600">
                                <span class="px-2.5 py-1 rounded-lg bg-slate-100 border border-slate-200">{{ $t->getDatabaseName() }}</span>
                            </td>
                            <td class="py-4 px-4">
                                @if($t->isInternal())
                                    <span class="px-2.5 py-1 rounded-full bg-accent-50 text-accent-600 border border-accent-200 font-mono text-[10px] font-bold">Empetix Internal HQ</span>
                                @else
                                    <span class="px-2.5 py-1 rounded-full bg-slate-100 text-ink-900 font-mono text-[10px] font-bold">
                                        {{ $t->currentSubscription?->plan?->name ?? 'Free Trial' }}
                                    </span>
                                @endif
                            </td>
                            <td class="py-4 px-4">
                                <div class="space-y-1 font-mono text-xs">
                                    <a href="{{ route('tenant.path.dashboard', ['tenant_slug' => $t->slug]) }}" target="_blank" class="text-accent-500 hover:underline font-bold block">
                                        /t/{{ $t->slug }} ↗
                                    </a>
                                    @foreach($t->domains as $d)
                                        <div class="flex items-center gap-1.5 text-[11px] text-slate-500">
                                            <span>{{ $d->domain }}</span>
                                            @if($d->is_custom)
                                                @if($d->is_verified)
                                                    <span class="text-[10px] text-emerald-600 font-bold">✓</span>
                                                @else
                                                    <button wire:click="verifyDomain({{ $d->id }})" class="text-[10px] text-amber-600 underline font-bold">Verify</button>
                                                @endif
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            </td>
                            <td class="py-4 px-4">
                                <span class="px-2.5 py-1 rounded-full font-mono text-[10px] font-bold {{ $t->is_active ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-rose-50 text-rose-700 border border-rose-200' }}">
                                    {{ $t->is_active ? '● ACTIVE' : '● SUSPENDED' }}
                                </span>
                            </td>
                            <td class="py-4 px-5 text-end">
                                <div class="inline-flex items-center gap-2">
                                    <a href="{{ route('tenant.path.dashboard', ['tenant_slug' => $t->slug]) }}" target="_blank"
                                       class="px-3 py-1.5 bg-ink-900 hover:bg-accent-500 text-white rounded-xl text-xs font-bold transition-colors">
                                        ERP ↗
                                    </a>
                                    <button wire:click="toggleStatus({{ $t->id }})"
                                            class="text-xs font-bold px-3 py-1.5 rounded-xl border cursor-pointer {{ $t->is_active ? 'border-rose-200 text-rose-600 hover:bg-rose-50' : 'border-emerald-200 text-emerald-600 hover:bg-emerald-50' }}">
                                        {{ $t->is_active ? (app()->getLocale() === 'ar' ? 'إيقاف' : 'Suspend') : (app()->getLocale() === 'ar' ? 'تفعيل' : 'Activate') }}
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center text-slate-400">No tenants matching criteria.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-slate-100">
            {{ $tenants->links() }}
        </div>
    </div>
</div>
