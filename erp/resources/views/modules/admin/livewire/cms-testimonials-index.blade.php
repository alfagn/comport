<div class="space-y-6 max-w-7xl mx-auto font-sans">
    <!-- Executive Header Card -->
    <div class="bg-gradient-to-r from-[#050C1A] via-[#08142C] to-[#0D1E3D] border border-white/10 rounded-2xl p-6 sm:p-8 text-white shadow-card flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-accent-500/20 text-accent-500 border border-accent-500/30 text-[11px] font-mono font-bold uppercase tracking-wider mb-2">
                <span class="w-2 h-2 rounded-full bg-accent-500"></span>
                <span>Executive Social Proof & Reviews</span>
            </div>
            <h2 class="text-2xl font-heading font-black tracking-tight">
                {{ app()->getLocale() === 'ar' ? 'آراء وتقييمات العملاء (Client Testimonials & Reviews)' : 'Client Testimonials & Reviews' }}
            </h2>
            <p class="text-xs sm:text-sm text-slate-300 mt-1">
                {{ app()->getLocale() === 'ar' ? 'إدارة شهادات وتقييمات الرؤساء التنفيذيين والعملاء المعروضة في الصفحة الرئيسية للموقع.' : 'Manage verified executive quotes and 5-star reviews featured on the Homepage.' }}
            </p>
        </div>

        <div>
            <button wire:click="create"
                    class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-accent-500 to-[#ff6b42] hover:from-accent-600 hover:to-accent-500 text-white font-bold text-xs uppercase tracking-wider rounded-xl shadow-glow transition-all cursor-pointer">
                <span>+ {{ app()->getLocale() === 'ar' ? 'إضافة تقييم جديد' : 'New Client Review' }}</span>
            </button>
        </div>
    </div>

    <!-- Testimonials Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($testimonials as $t)
            <div class="bg-white border border-slate-200/80 rounded-2xl p-6 flex flex-col justify-between shadow-subtle hover:border-accent-500 hover:shadow-card transition-all">
                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-1 text-accent-500 text-sm">
                            @for($i = 0; $i < $t->rating; $i++)
                                ★
                            @endfor
                        </div>
                        <button wire:click="togglePublished({{ $t->id }})" class="font-mono text-[10px] font-bold px-2.5 py-0.5 rounded-full border cursor-pointer {{ $t->is_published ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-slate-100 text-slate-500 border-slate-200' }}">
                            {{ $t->is_published ? '● LIVE' : '○ HIDDEN' }}
                        </button>
                    </div>

                    <p class="text-xs text-slate-700 italic leading-relaxed line-clamp-4">
                        "{{ $t->content }}"
                    </p>

                    <div class="pt-4 border-t border-slate-100 flex items-center gap-3">
                        <img src="{{ asset($t->avatar ?: 'assets/img/team/farida-zaki.jpg') }}" alt="{{ $t->client_name }}" class="w-11 h-11 rounded-xl object-cover border border-slate-200 shrink-0" onerror="this.onerror=null; this.src='{{ asset('assets/img/favicon.svg') }}';">
                        <div class="overflow-hidden">
                            <h4 class="font-heading font-extrabold text-ink-900 text-xs truncate">{{ $t->client_name }}</h4>
                            <p class="text-[11px] text-slate-500 truncate">{{ $t->client_position }} • <strong class="text-ink-900">{{ $t->client_company }}</strong></p>
                        </div>
                    </div>
                </div>

                <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-end gap-2">
                    <button wire:click="edit({{ $t->id }})" class="px-3.5 py-1.5 text-ink-900 hover:text-white hover:bg-ink-900 font-bold border border-slate-200 rounded-xl bg-white text-xs transition-all cursor-pointer">
                        Edit
                    </button>
                    <button wire:click="delete({{ $t->id }})" onclick="return confirm('Delete review?') || event.stopImmediatePropagation()" class="p-1.5 text-slate-400 hover:text-rose-600 rounded-xl cursor-pointer">
                        ✕
                    </button>
                </div>
            </div>
        @empty
            <div class="col-span-full py-14 text-center bg-white border border-slate-200 rounded-2xl text-slate-400">
                No testimonials added yet.
            </div>
        @endforelse
    </div>

    <!-- Create / Edit Modal -->
    @if($showModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-ink-950/75 backdrop-blur-xs">
            <div class="bg-white border border-slate-200 rounded-2xl w-full max-w-lg p-6 shadow-2xl space-y-5">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <h3 class="text-base font-heading font-extrabold text-ink-900">
                        {{ $editingId ? 'Edit Client Review' : 'Add Client Testimonial' }}
                    </h3>
                    <button wire:click="$set('showModal', false)" class="text-slate-400 hover:text-slate-700">✕</button>
                </div>

                <div class="space-y-4 text-xs">
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Client Full Name</label>
                        <input type="text" wire:model="client_name" class="w-full px-3.5 py-2.5 border border-slate-200 rounded-xl" placeholder="Karim Mansour" />
                        @error('client_name') <span class="text-rose-600 text-[10px]">{{ $message }}</span> @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Position / Title</label>
                            <input type="text" wire:model="client_position" class="w-full px-3.5 py-2.5 border border-slate-200 rounded-xl" placeholder="VP of Product" />
                        </div>
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Company / Brand</label>
                            <input type="text" wire:model="client_company" class="w-full px-3.5 py-2.5 border border-slate-200 rounded-xl" placeholder="NilePay Inc." />
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Avatar Image Path</label>
                            <input type="text" wire:model="avatar" class="w-full px-3.5 py-2.5 border border-slate-200 rounded-xl font-mono" placeholder="assets/img/team/farida-zaki.jpg" />
                        </div>
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Rating</label>
                            <select wire:model="rating" class="w-full px-3.5 py-2.5 border border-slate-200 rounded-xl font-mono">
                                <option value="5">★★★★★ (5 Stars)</option>
                                <option value="4">★★★★☆ (4 Stars)</option>
                                <option value="3">★★★☆☆ (3 Stars)</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Review Quote / Content</label>
                        <textarea rows="4" wire:model="content" class="w-full px-3.5 py-2.5 border border-slate-200 rounded-xl" placeholder="What the client said about Empetix..."></textarea>
                        @error('content') <span class="text-rose-600 text-[10px]">{{ $message }}</span> @enderror
                    </div>

                    <div class="pt-2 border-t border-slate-100">
                        <label class="flex items-center gap-2.5 cursor-pointer font-bold text-ink-900">
                            <input type="checkbox" wire:model="is_published" class="rounded border-slate-300 text-accent-500 focus:ring-accent-500">
                            <span>Publish on Live Website</span>
                        </label>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-2 pt-4 border-t border-slate-100">
                    <button wire:click="$set('showModal', false)" class="px-4 py-2.5 border border-slate-200 rounded-xl text-xs font-bold text-slate-600 hover:bg-slate-50">Cancel</button>
                    <button wire:click="save" class="px-5 py-2.5 bg-gradient-to-r from-accent-500 to-[#ff6b42] text-white font-bold rounded-xl text-xs uppercase tracking-wider shadow-glow">Save Review ✓</button>
                </div>
            </div>
        </div>
    @endif
</div>
