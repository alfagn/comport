<div class="space-y-6 max-w-7xl mx-auto font-sans">
    <!-- Executive Header Card -->
    <div class="bg-gradient-to-r from-[#050C1A] via-[#08142C] to-[#0D1E3D] border border-white/10 rounded-2xl p-6 sm:p-8 text-white shadow-card flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-accent-500/20 text-accent-500 border border-accent-500/30 text-[11px] font-mono font-bold uppercase tracking-wider mb-2">
                <span class="w-2 h-2 rounded-full bg-accent-500"></span>
                <span>Ecosystem & Corporate Alliances</span>
            </div>
            <h2 class="text-2xl font-heading font-black tracking-tight">
                {{ app()->getLocale() === 'ar' ? 'إدارة شركاء النجاح والعملاء (Partners & Client Brands)' : 'Partners & Client Brands Management' }}
            </h2>
            <p class="text-xs sm:text-sm text-slate-300 mt-1">
                {{ app()->getLocale() === 'ar' ? 'تحكم في شعارات الشركات الشريكة المعروضة في الصفحة الرئيسية وصفحة الشركاء (/partners).' : 'Manage strategic partner logos and client brands displayed on the Homepage and /partners directory.' }}
            </p>
        </div>

        <div class="flex items-center gap-3 shrink-0">
            <a href="{{ route('site.partners') }}" target="_blank"
               class="px-4 py-2.5 bg-white/10 hover:bg-white/20 text-white border border-white/15 rounded-xl text-xs font-bold transition-all">
                {{ app()->getLocale() === 'ar' ? 'معاينة الصفحة ↗' : 'Preview Page ↗' }}
            </a>
            <button wire:click="create"
                    class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-accent-500 to-[#ff6b42] hover:from-accent-600 hover:to-accent-500 text-white font-bold text-xs uppercase tracking-wider rounded-xl shadow-glow transition-all cursor-pointer">
                <span>+ {{ app()->getLocale() === 'ar' ? 'إضافة شريك جديد' : 'Add Partner Brand' }}</span>
            </button>
        </div>
    </div>

    <!-- Partners Grid -->
    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-5">
        @forelse($partners as $p)
            <div class="bg-white border border-slate-200/80 rounded-2xl p-5 flex flex-col items-center justify-between text-center shadow-subtle group hover:border-accent-500 hover:shadow-card transition-all">
                <div class="w-full flex items-center justify-between mb-3">
                    <span class="text-[10px] text-slate-400 font-mono font-bold">#{{ $p->sort_order }}</span>
                    <button wire:click="toggleActive({{ $p->id }})"
                            class="px-2 py-0.5 rounded-full text-[9px] font-mono font-bold cursor-pointer {{ $p->is_active ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-slate-100 text-slate-500' }}">
                        {{ $p->is_active ? 'ACTIVE' : 'HIDDEN' }}
                    </button>
                </div>

                <div class="h-16 flex items-center justify-center p-3 mb-3 w-full bg-slate-50 rounded-xl border border-slate-100">
                    <img src="{{ asset($p->logo ?: 'assets/img/partners/aws-partner.svg') }}"
                         alt="{{ $p->name }}"
                         class="max-h-11 max-w-full object-contain group-hover:scale-105 transition-transform"
                         onerror="this.onerror=null; this.src='{{ asset('assets/img/favicon.svg') }}';">
                </div>

                <div class="w-full">
                    <h4 class="font-heading font-extrabold text-ink-900 text-sm truncate">{{ $p->name }}</h4>
                    @if($p->website_url)
                        <a href="{{ $p->website_url }}" target="_blank" class="text-[10px] font-mono text-accent-500 hover:underline truncate block mt-0.5">
                            {{ $p->website_url }} ↗
                        </a>
                    @endif
                </div>

                <div class="mt-4 pt-3 border-t border-slate-100 w-full flex items-center justify-between gap-2">
                    <button wire:click="edit({{ $p->id }})" class="flex-1 py-1.5 text-xs font-bold text-ink-900 hover:text-white hover:bg-ink-900 border border-slate-200 rounded-xl transition-colors cursor-pointer">
                        {{ app()->getLocale() === 'ar' ? 'تعديل' : 'Edit' }}
                    </button>
                    <button wire:click="delete({{ $p->id }})" onclick="return confirm('Remove partner?') || event.stopImmediatePropagation()" class="p-1.5 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-xl transition-colors cursor-pointer">
                        ✕
                    </button>
                </div>
            </div>
        @empty
            <div class="col-span-full py-14 text-center bg-white border border-slate-200 rounded-2xl text-slate-400">
                No partner brands configured yet.
            </div>
        @endforelse
    </div>

    <!-- Create / Edit Modal -->
    @if($showModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-ink-950/75 backdrop-blur-xs">
            <div class="bg-white border border-slate-200 rounded-2xl w-full max-w-md p-6 shadow-2xl space-y-5">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <h3 class="text-base font-heading font-extrabold text-ink-900">
                        {{ $editingId ? (app()->getLocale() === 'ar' ? 'تعديل بيانات الشريك' : 'Edit Partner Brand') : (app()->getLocale() === 'ar' ? 'إضافة شريك جديد' : 'Add Partner Brand') }}
                    </h3>
                    <button wire:click="$set('showModal', false)" class="text-slate-400 hover:text-slate-700">✕</button>
                </div>

                <div class="space-y-4 text-xs">
                    <div>
                        <label class="block font-bold text-slate-700 mb-1.5">Brand / Company Name</label>
                        <input type="text" wire:model="name" class="w-full px-3.5 py-2.5 border border-slate-200 rounded-xl focus:border-accent-500 outline-none" placeholder="e.g. Stripe, AWS, Meta" />
                        @error('name') <span class="text-rose-600 text-[10px]">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 mb-1.5">Logo Image Path / URL</label>
                        <input type="text" wire:model="logo" class="w-full px-3.5 py-2.5 border border-slate-200 rounded-xl focus:border-accent-500 outline-none font-mono" placeholder="assets/img/partners/aws-partner.svg" />
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 mb-1.5">Partner Website URL</label>
                        <input type="url" wire:model="website_url" class="w-full px-3.5 py-2.5 border border-slate-200 rounded-xl focus:border-accent-500 outline-none font-mono" placeholder="https://..." />
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 mb-1.5">Display Order</label>
                        <input type="number" wire:model="sort_order" class="w-full px-3.5 py-2.5 border border-slate-200 rounded-xl font-mono" />
                    </div>

                    <div class="pt-2 border-t border-slate-100">
                        <label class="flex items-center gap-2.5 cursor-pointer font-bold text-ink-900">
                            <input type="checkbox" wire:model="is_active" class="rounded border-slate-300 text-accent-500 focus:ring-accent-500">
                            <span>Display on Live Website (Active)</span>
                        </label>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-2 pt-4 border-t border-slate-100">
                    <button wire:click="$set('showModal', false)" class="px-4 py-2.5 border border-slate-200 rounded-xl text-xs font-bold text-slate-600 hover:bg-slate-50">Cancel</button>
                    <button wire:click="save" class="px-5 py-2.5 bg-gradient-to-r from-accent-500 to-[#ff6b42] text-white font-bold rounded-xl text-xs uppercase tracking-wider shadow-glow">Save Partner ✓</button>
                </div>
            </div>
        </div>
    @endif
</div>
