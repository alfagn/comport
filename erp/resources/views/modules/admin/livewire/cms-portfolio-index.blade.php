<div class="space-y-6 max-w-7xl mx-auto font-sans">
    <!-- Executive Header Card -->
    <div class="bg-gradient-to-r from-[#050C1A] via-[#08142C] to-[#0D1E3D] border border-white/10 rounded-2xl p-6 sm:p-8 text-white shadow-card flex flex-col lg:flex-row lg:items-center justify-between gap-4">
        <div>
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-accent-500/20 text-accent-500 border border-accent-500/30 text-[11px] font-mono font-bold uppercase tracking-wider mb-2">
                <span class="w-2 h-2 rounded-full bg-accent-500"></span>
                <span>Portfolio & Case Studies Studio</span>
            </div>
            <h2 class="text-2xl font-heading font-black tracking-tight">
                {{ app()->getLocale() === 'ar' ? 'سابقة الأعمال ودراسات الحالة (Work & Case Studies)' : 'Portfolio & Case Studies Management' }}
            </h2>
            <p class="text-xs sm:text-sm text-slate-300 mt-1">
                {{ app()->getLocale() === 'ar' ? 'إدارة المشاريع ودراسات الجدوى والنتائج المعروضة في الصفحة الرئيسية وصفحة الأعمال (/work).' : 'Manage software & growth client case studies, metrics, and categories displayed on Home and /work.' }}
            </p>
        </div>

        <div class="flex items-center gap-3 shrink-0">
            <a href="{{ route('site.work') }}" target="_blank"
               class="px-4 py-2.5 bg-white/10 hover:bg-white/20 text-white border border-white/15 rounded-xl text-xs font-bold transition-all">
                {{ app()->getLocale() === 'ar' ? 'معاينة صفحة الأعمال ↗' : 'Preview /work ↗' }}
            </a>
            <button wire:click="create"
                    class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-accent-500 to-[#ff6b42] hover:from-accent-600 hover:to-accent-500 text-white font-bold text-xs uppercase tracking-wider rounded-xl shadow-glow transition-all cursor-pointer">
                <span>+ {{ app()->getLocale() === 'ar' ? 'إضافة مشروع جديد' : 'New Case Study' }}</span>
            </button>
        </div>
    </div>

    <!-- Category Filter & Quick Category Creator Bar -->
    <div class="bg-white border border-slate-200/80 rounded-2xl p-4 shadow-subtle flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div class="flex items-center gap-2 flex-wrap text-xs font-mono">
            <button wire:click="$set('selectedCategory', 'all')"
                    class="px-3.5 py-2 rounded-xl transition-all cursor-pointer {{ $selectedCategory === 'all' ? 'bg-ink-900 text-white font-bold shadow-xs' : 'bg-slate-100 text-slate-600 hover:text-ink-900' }}">
                All Projects ({{ $totalProjects }})
            </button>
            @foreach($categories as $cat)
                <button wire:click="$set('selectedCategory', '{{ $cat->id }}')"
                        class="px-3.5 py-2 rounded-xl transition-all cursor-pointer {{ $selectedCategory == $cat->id ? 'bg-ink-900 text-white font-bold shadow-xs' : 'bg-slate-100 text-slate-600 hover:text-ink-900' }}">
                    {{ $cat->name }}
                </button>
            @endforeach
        </div>

        <form wire:submit.prevent="createCategory" class="flex items-center gap-2">
            <input type="text" wire:model="newCategoryName" placeholder="{{ app()->getLocale() === 'ar' ? 'اسم تصنيف جديد...' : 'New category name...' }}"
                   class="px-3 py-2 text-xs border border-slate-200 rounded-xl focus:border-accent-500 outline-none" />
            <button type="submit" class="px-3.5 py-2 bg-slate-900 hover:bg-accent-500 text-white text-xs font-bold rounded-xl transition-colors cursor-pointer">
                + {{ app()->getLocale() === 'ar' ? 'إضافة تصنيف' : 'Add Category' }}
            </button>
        </form>
    </div>

    <!-- Projects Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($projects as $p)
            <div class="bg-white border border-slate-200/80 rounded-2xl overflow-hidden flex flex-col justify-between shadow-subtle group hover:border-accent-500 hover:shadow-card transition-all">
                <div>
                    <div class="aspect-16/10 bg-slate-100 overflow-hidden relative border-b border-slate-100">
                        <img src="{{ asset($p->featured_image ?: 'assets/img/work/nilepay.jpg') }}"
                             alt="{{ $p->title }}"
                             class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                             onerror="this.onerror=null; this.src='{{ asset('assets/img/og-empetix.jpg') }}';">
                        <div class="absolute top-3 start-3">
                            <span class="px-2.5 py-1 rounded-lg bg-ink-900/90 backdrop-blur-xs text-white text-[10px] font-mono font-bold uppercase tracking-wider">
                                {{ $p->category?->name ?? 'Case Study' }}
                            </span>
                        </div>
                        @if($p->is_featured)
                            <div class="absolute top-3 end-3">
                                <span class="px-2.5 py-1 rounded-full bg-accent-500 text-white text-[10px] font-mono font-bold uppercase tracking-wider shadow-md">
                                    ★ Featured
                                </span>
                            </div>
                        @endif
                    </div>

                    <div class="p-6 space-y-2.5">
                        <div class="text-[11px] font-mono text-slate-400 flex items-center justify-between">
                            <span class="font-bold text-accent-500">{{ $p->client_name ?: 'Enterprise Client' }}</span>
                            <span>{{ $p->completion_date ? $p->completion_date->format('Y') : date('Y') }}</span>
                        </div>
                        <h3 class="font-heading font-extrabold text-ink-900 text-base leading-snug line-clamp-2">{{ $p->title }}</h3>
                        <p class="text-xs text-slate-600 line-clamp-2 leading-relaxed">
                            {{ $p->excerpt ?: Str::limit($p->description, 100) }}
                        </p>
                    </div>
                </div>

                <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/50 flex items-center justify-between text-xs">
                    <button wire:click="togglePublished({{ $p->id }})"
                            class="font-mono text-[10px] font-bold px-2.5 py-1 rounded-full border cursor-pointer {{ $p->is_published ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-slate-100 text-slate-500 border-slate-200' }}">
                        {{ $p->is_published ? '● PUBLISHED' : '○ DRAFT' }}
                    </button>

                    <div class="flex items-center gap-2">
                        <a href="{{ route('site.work.detail', $p->slug) }}" target="_blank" class="px-2.5 py-1 text-slate-500 hover:text-ink-900 font-mono text-xs" title="Preview Case Study">↗</a>
                        <button wire:click="edit({{ $p->id }})" class="px-3.5 py-1.5 text-ink-900 hover:text-white hover:bg-ink-900 font-bold border border-slate-200 rounded-xl bg-white transition-all cursor-pointer">
                            {{ app()->getLocale() === 'ar' ? 'تعديل' : 'Edit' }}
                        </button>
                        <button wire:click="delete({{ $p->id }})" onclick="return confirm('Remove project?') || event.stopImmediatePropagation()" class="p-1.5 text-slate-400 hover:text-rose-600 rounded-xl cursor-pointer">
                            ✕
                        </button>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full py-14 text-center bg-white border border-slate-200 rounded-2xl text-slate-400">
                No projects found in this category.
            </div>
        @endforelse
    </div>

    <div class="pt-2">
        {{ $projects->links() }}
    </div>

    <!-- Create / Edit Modal -->
    @if($showModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-ink-950/75 backdrop-blur-xs">
            <div class="bg-white border border-slate-200 rounded-2xl w-full max-w-2xl p-6 shadow-2xl space-y-5 max-h-[90vh] overflow-y-auto">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <h3 class="text-base font-heading font-extrabold text-ink-900">
                        {{ $editingId ? 'Edit Project / Case Study' : 'Create Project / Case Study' }}
                    </h3>
                    <button wire:click="$set('showModal', false)" class="text-slate-400 hover:text-slate-700">✕</button>
                </div>

                <div class="space-y-4 text-xs">
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Project Title</label>
                            <input type="text" wire:model="title" class="w-full px-3.5 py-2.5 border border-slate-200 rounded-xl focus:border-accent-500 outline-none" placeholder="NilePay — Digital Payment Platform" />
                            @error('title') <span class="text-rose-600 text-[10px]">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Slug (URL)</label>
                            <input type="text" wire:model="slug" class="w-full px-3.5 py-2.5 border border-slate-200 rounded-xl font-mono" placeholder="nilepay" />
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Client Name / Brand</label>
                            <input type="text" wire:model="client_name" class="w-full px-3.5 py-2.5 border border-slate-200 rounded-xl" placeholder="NilePay Financial Inc." />
                        </div>
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Category</label>
                            <select wire:model="category_id" class="w-full px-3.5 py-2.5 border border-slate-200 rounded-xl">
                                <option value="">Select Category...</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Featured Image Path</label>
                            <input type="text" wire:model="featured_image" class="w-full px-3.5 py-2.5 border border-slate-200 rounded-xl font-mono" placeholder="assets/img/work/nilepay.jpg" />
                        </div>
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Live Project Link (URL)</label>
                            <input type="url" wire:model="project_url" class="w-full px-3.5 py-2.5 border border-slate-200 rounded-xl font-mono" placeholder="https://..." />
                        </div>
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Short Summary (Excerpt)</label>
                        <input type="text" wire:model="excerpt" class="w-full px-3.5 py-2.5 border border-slate-200 rounded-xl" placeholder="Brief 1-2 sentence highlight for cards..." />
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Full Case Study & Metrics</label>
                        <textarea rows="5" wire:model="description" class="w-full px-3.5 py-2.5 border border-slate-200 rounded-xl" placeholder="Describe the challenge, tech stack, architecture, marketing funnels, and measured ROI results..."></textarea>
                    </div>

                    <div class="flex items-center gap-6 pt-2 border-t border-slate-100">
                        <label class="flex items-center gap-2 cursor-pointer font-bold text-ink-900">
                            <input type="checkbox" wire:model="is_featured" class="rounded border-slate-300 text-accent-500 focus:ring-accent-500">
                            <span>Feature on Homepage</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer font-bold text-ink-900">
                            <input type="checkbox" wire:model="is_published" class="rounded border-slate-300 text-accent-500 focus:ring-accent-500">
                            <span>Published (Live)</span>
                        </label>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-2 pt-4 border-t border-slate-100">
                    <button wire:click="$set('showModal', false)" class="px-4 py-2.5 border border-slate-200 rounded-xl text-xs font-bold text-slate-600 hover:bg-slate-50">
                        Cancel
                    </button>
                    <button wire:click="save" class="px-5 py-2.5 bg-gradient-to-r from-accent-500 to-[#ff6b42] text-white font-bold rounded-xl text-xs uppercase tracking-wider shadow-glow">
                        Save Project ✓
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
