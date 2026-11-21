<div class="space-y-6 max-w-7xl mx-auto font-sans">
    <!-- Executive Header Card -->
    <div class="bg-gradient-to-r from-[#050C1A] via-[#08142C] to-[#0D1E3D] border border-white/10 rounded-2xl p-6 sm:p-8 text-white shadow-card flex flex-col lg:flex-row lg:items-center justify-between gap-4">
        <div>
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-accent-500/20 text-accent-500 border border-accent-500/30 text-[11px] font-mono font-bold uppercase tracking-wider mb-2">
                <span class="w-2 h-2 rounded-full bg-accent-500"></span>
                <span>Leadership & Multidisciplinary Talent</span>
            </div>
            <h2 class="text-2xl font-heading font-black tracking-tight">
                {{ app()->getLocale() === 'ar' ? 'المؤسسون وفريق العمل (Founders & Leadership Team)' : 'Founders & Leadership Team Directory' }}
            </h2>
            <p class="text-xs sm:text-sm text-slate-300 mt-1">
                {{ app()->getLocale() === 'ar' ? 'إدارة صفحات المؤسسين (مهند، أيمن، بيشوي) ودليل قادة الفرق المعروض في الرئيسية، عن الشركة، وصفحة الفريق (/team).' : 'Manage co-founder profiles, executive bios, and team leads displayed across Home, About, /team, and /founder/{slug}.' }}
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-3 shrink-0">
            <div class="inline-flex rounded-xl border border-white/15 bg-white/10 p-1 text-xs font-mono">
                <button wire:click="$set('filter', 'all')" class="px-3 py-1.5 rounded-lg cursor-pointer transition-all {{ $filter === 'all' ? 'bg-accent-500 text-white font-bold' : 'text-slate-300 hover:text-white' }}">All ({{ $totalCount }})</button>
                <button wire:click="$set('filter', 'founders')" class="px-3 py-1.5 rounded-lg cursor-pointer transition-all {{ $filter === 'founders' ? 'bg-accent-500 text-white font-bold' : 'text-slate-300 hover:text-white' }}">Founders ({{ $foundersCount }})</button>
                <button wire:click="$set('filter', 'team')" class="px-3 py-1.5 rounded-lg cursor-pointer transition-all {{ $filter === 'team' ? 'bg-accent-500 text-white font-bold' : 'text-slate-300 hover:text-white' }}">Team</button>
            </div>

            <button wire:click="create"
                    class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-accent-500 to-[#ff6b42] hover:from-accent-600 hover:to-accent-500 text-white font-bold text-xs uppercase tracking-wider rounded-xl shadow-glow transition-all cursor-pointer">
                <span>+ {{ app()->getLocale() === 'ar' ? 'إضافة مؤسس / عضو' : 'Add Member' }}</span>
            </button>
        </div>
    </div>

    <!-- Members Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($members as $m)
            <div class="bg-white border {{ $m->is_founder ? 'border-accent-500/60 ring-2 ring-accent-500/10' : 'border-slate-200/80' }} rounded-2xl p-6 flex flex-col justify-between shadow-subtle hover:shadow-card transition-all relative group">
                @if($m->is_founder)
                    <div class="absolute -top-3 end-5 px-3 py-0.5 bg-gradient-to-r from-accent-500 to-[#ff6b42] text-white font-mono text-[10px] font-bold uppercase tracking-wider rounded-full shadow-md">
                        ★ Co-Founder
                    </div>
                @endif

                <div>
                    <div class="flex items-center gap-4 mb-4">
                        <img src="{{ asset($m->avatar ?: 'assets/img/team/mohanad-shehata.jpg') }}"
                             alt="{{ $m->name }}"
                             class="w-16 h-16 rounded-2xl object-cover border border-slate-200 shrink-0 shadow-xs"
                             onerror="this.onerror=null; this.src='{{ asset('assets/img/favicon.svg') }}';">
                        <div class="overflow-hidden">
                            <h3 class="font-heading font-extrabold text-ink-900 text-base leading-snug truncate">{{ $m->name }}</h3>
                            <p class="text-xs text-accent-500 font-bold truncate mt-0.5">{{ $m->role_title }}</p>
                            <a href="{{ route('site.founder', $m->slug) }}" target="_blank" class="text-[11px] text-slate-400 hover:text-ink-900 font-mono inline-flex items-center gap-1 mt-0.5">
                                /founder/{{ $m->slug }} ↗
                            </a>
                        </div>
                    </div>

                    <p class="text-xs text-slate-600 line-clamp-3 mb-4 leading-relaxed">
                        {{ $m->bio }}
                    </p>

                    @if(!empty($m->social_links))
                        <div class="flex flex-wrap items-center gap-2 mb-4 pt-3 border-t border-slate-100 text-xs font-mono">
                            @foreach($m->social_links as $network => $url)
                                <a href="{{ $url }}" target="_blank" class="px-2.5 py-1 bg-slate-100 hover:bg-ink-900 hover:text-white text-slate-700 rounded-lg text-[10px] font-bold transition-colors">
                                    {{ ucfirst($network) }} ↗
                                </a>
                            @endforeach
                        </div>
                    @endif
                </div>

                <div class="pt-4 border-t border-slate-100 flex items-center justify-between text-xs">
                    <span class="text-[11px] text-slate-400 font-mono font-bold">Order #{{ $m->sort_order }}</span>
                    <div class="flex items-center gap-2">
                        <button wire:click="edit({{ $m->id }})" class="px-3.5 py-1.5 text-ink-900 hover:text-white hover:bg-ink-900 font-bold border border-slate-200 rounded-xl bg-white transition-all cursor-pointer">
                            {{ app()->getLocale() === 'ar' ? 'تعديل البيانات' : 'Edit Profile' }}
                        </button>
                        <button wire:click="delete({{ $m->id }})" onclick="return confirm('Delete this member?') || event.stopImmediatePropagation()" class="p-1.5 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-xl transition-colors cursor-pointer">
                            ✕
                        </button>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full py-14 text-center bg-white border border-slate-200 rounded-2xl text-slate-400">
                No team members found.
            </div>
        @endforelse
    </div>

    <!-- Create / Edit Modal -->
    @if($showModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-ink-950/75 backdrop-blur-xs">
            <div class="bg-white border border-slate-200 rounded-2xl w-full max-w-xl p-6 shadow-2xl space-y-5 max-h-[90vh] overflow-y-auto">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <h3 class="text-base font-heading font-extrabold text-ink-900">
                        {{ $editingId ? (app()->getLocale() === 'ar' ? 'تعديل بيانات العضو / المؤسس' : 'Edit Member Profile') : (app()->getLocale() === 'ar' ? 'إضافة عضو جديد' : 'Add Team Member') }}
                    </h3>
                    <button wire:click="$set('showModal', false)" class="text-slate-400 hover:text-slate-700">✕</button>
                </div>

                <div class="space-y-4 text-xs">
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Full Name</label>
                            <input type="text" wire:model="name" class="w-full px-3.5 py-2.5 border border-slate-200 rounded-xl focus:border-accent-500 outline-none" placeholder="Mohanad Shehata" />
                            @error('name') <span class="text-rose-600 text-[10px]">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Slug (URL)</label>
                            <input type="text" wire:model="slug" class="w-full px-3.5 py-2.5 border border-slate-200 rounded-xl font-mono" placeholder="mohanad" />
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Role / Executive Title</label>
                            <input type="text" wire:model="role_title" class="w-full px-3.5 py-2.5 border border-slate-200 rounded-xl" placeholder="Co-Founder & CTO" />
                            @error('role_title') <span class="text-rose-600 text-[10px]">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Sort Order</label>
                            <input type="number" wire:model="sort_order" class="w-full px-3.5 py-2.5 border border-slate-200 rounded-xl font-mono" />
                        </div>
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Avatar Image Path (relative or URL)</label>
                        <input type="text" wire:model="avatar" class="w-full px-3.5 py-2.5 border border-slate-200 rounded-xl font-mono" placeholder="assets/img/team/mohanad-shehata.jpg" />
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Executive Bio & Practice Focus</label>
                        <textarea rows="4" wire:model="bio" class="w-full px-3.5 py-2.5 border border-slate-200 rounded-xl" placeholder="Executive biography..."></textarea>
                    </div>

                    <div class="pt-2 border-t border-slate-100">
                        <label class="flex items-center gap-2.5 cursor-pointer font-bold text-ink-900">
                            <input type="checkbox" wire:model="is_founder" class="rounded border-slate-300 text-accent-500 focus:ring-accent-500">
                            <span>Mark as Co-Founder (Featured on Homepage, About & Team Founders Grid)</span>
                        </label>
                    </div>

                    <div class="grid grid-cols-3 gap-3 pt-2">
                        <div>
                            <label class="block text-[10px] font-mono font-bold text-slate-500 mb-1">LinkedIn URL</label>
                            <input type="text" wire:model="linkedin" class="w-full px-3 py-2 text-xs border border-slate-200 rounded-xl font-mono" placeholder="https://..." />
                        </div>
                        <div>
                            <label class="block text-[10px] font-mono font-bold text-slate-500 mb-1">GitHub URL</label>
                            <input type="text" wire:model="github" class="w-full px-3 py-2 text-xs border border-slate-200 rounded-xl font-mono" placeholder="https://..." />
                        </div>
                        <div>
                            <label class="block text-[10px] font-mono font-bold text-slate-500 mb-1">Twitter / X URL</label>
                            <input type="text" wire:model="twitter" class="w-full px-3 py-2 text-xs border border-slate-200 rounded-xl font-mono" placeholder="https://..." />
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-2 pt-4 border-t border-slate-100">
                    <button wire:click="$set('showModal', false)" class="px-4 py-2.5 border border-slate-200 rounded-xl text-xs font-bold text-slate-600 hover:bg-slate-50">
                        Cancel
                    </button>
                    <button wire:click="save" class="px-5 py-2.5 bg-gradient-to-r from-accent-500 to-[#ff6b42] text-white font-bold rounded-xl text-xs uppercase tracking-wider shadow-glow">
                        Save Member ✓
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
