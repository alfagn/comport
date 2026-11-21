<div class="space-y-6 max-w-7xl mx-auto font-sans">
    <!-- Executive Header Card -->
    <div class="bg-gradient-to-r from-[#050C1A] via-[#08142C] to-[#0D1E3D] border border-white/10 rounded-2xl p-6 sm:p-8 text-white shadow-card flex flex-col lg:flex-row lg:items-center justify-between gap-4">
        <div>
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-accent-500/20 text-accent-500 border border-accent-500/30 text-[11px] font-mono font-bold uppercase tracking-wider mb-2">
                <span class="w-2 h-2 rounded-full bg-accent-500"></span>
                <span>Empetix Journal & Media Hub</span>
            </div>
            <h2 class="text-2xl font-heading font-black tracking-tight">
                {{ app()->getLocale() === 'ar' ? 'المدونة والمقالات والفيديوهات (Blog, Video & Photo Essays)' : 'Journal, Articles & Media CMS' }}
            </h2>
            <p class="text-xs sm:text-sm text-slate-300 mt-1">
                {{ app()->getLocale() === 'ar' ? 'نشر المقالات الهندسية، حلقات الفيديو الوثائقية، والمقالات المصورة المعروضة في الرئيسية وصفحة المدونة (/blog).' : 'Publish engineering monographs, video episodes, and photo essays displayed on Home and /blog.' }}
            </p>
        </div>

        <div class="flex items-center gap-3 shrink-0">
            <a href="{{ route('site.blog') }}" target="_blank"
               class="px-4 py-2.5 bg-white/10 hover:bg-white/20 text-white border border-white/15 rounded-xl text-xs font-bold transition-all">
                {{ app()->getLocale() === 'ar' ? 'معاينة المدونة ↗' : 'Preview /blog ↗' }}
            </a>
            <button wire:click="create"
                    class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-accent-500 to-[#ff6b42] hover:from-accent-600 hover:to-accent-500 text-white font-bold text-xs uppercase tracking-wider rounded-xl shadow-glow transition-all cursor-pointer">
                <span>+ {{ app()->getLocale() === 'ar' ? 'كتابة مقال / فيديو جديد' : 'New Article / Post' }}</span>
            </button>
        </div>
    </div>

    <!-- Filter, Search & Quick Category Bar -->
    <div class="bg-white border border-slate-200/80 rounded-2xl p-4 shadow-subtle flex flex-col lg:flex-row lg:items-center justify-between gap-4 text-xs">
        <div class="flex flex-wrap items-center gap-2 font-mono">
            <button wire:click="$set('typeFilter', 'all')" class="px-3.5 py-2 rounded-xl cursor-pointer transition-all {{ $typeFilter === 'all' ? 'bg-ink-900 text-white font-bold' : 'bg-slate-100 text-slate-600 hover:text-ink-900' }}">All ({{ $totalPosts }})</button>
            <button wire:click="$set('typeFilter', 'article')" class="px-3.5 py-2 rounded-xl cursor-pointer transition-all {{ $typeFilter === 'article' ? 'bg-ink-900 text-white font-bold' : 'bg-slate-100 text-slate-600 hover:text-ink-900' }}">Articles</button>
            <button wire:click="$set('typeFilter', 'video')" class="px-3.5 py-2 rounded-xl cursor-pointer transition-all {{ $typeFilter === 'video' ? 'bg-ink-900 text-white font-bold' : 'bg-slate-100 text-slate-600 hover:text-ink-900' }}">Video Episodes</button>
            <button wire:click="$set('typeFilter', 'photo')" class="px-3.5 py-2 rounded-xl cursor-pointer transition-all {{ $typeFilter === 'photo' ? 'bg-ink-900 text-white font-bold' : 'bg-slate-100 text-slate-600 hover:text-ink-900' }}">Photo Essays</button>
        </div>

        <div class="flex flex-wrap items-center gap-2">
            <input type="text" wire:model.live.debounce.300ms="search" placeholder="{{ app()->getLocale() === 'ar' ? 'بحث في المقالات...' : 'Search articles...' }}" class="px-3.5 py-2 text-xs border border-slate-200 rounded-xl focus:border-accent-500 outline-none w-52" />

            <form wire:submit.prevent="createCategory" class="flex items-center gap-1.5">
                <input type="text" wire:model="newCategoryName" placeholder="{{ app()->getLocale() === 'ar' ? 'تصنيف مدونة جديد...' : 'New category...' }}" class="px-3 py-2 text-xs border border-slate-200 rounded-xl outline-none w-40" />
                <button type="submit" class="px-3 py-2 bg-slate-900 hover:bg-accent-500 text-white text-xs font-bold rounded-xl transition-colors cursor-pointer">+</button>
            </form>
        </div>
    </div>

    <!-- Posts Table -->
    <div class="bg-white border border-slate-200/80 rounded-2xl shadow-subtle overflow-hidden">
        <table class="w-full text-start text-xs font-sans">
            <thead>
                <tr class="border-b border-slate-200 bg-slate-50/70 text-slate-400 font-mono uppercase text-[10px]">
                    <th class="py-3.5 px-5 text-start">Post Title</th>
                    <th class="py-3.5 px-4 text-start">Category</th>
                    <th class="py-3.5 px-4 text-start">Format</th>
                    <th class="py-3.5 px-4 text-start">Views</th>
                    <th class="py-3.5 px-4 text-start">Status</th>
                    <th class="py-3.5 px-5 text-end">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($posts as $post)
                    <tr class="hover:bg-slate-50/80 transition-colors">
                        <td class="py-4 px-5">
                            <div class="flex items-center gap-3">
                                <img src="{{ asset($post->featured_image ?: 'assets/img/blog/arabic-first.jpg') }}" alt="" class="w-12 h-9 rounded-lg object-cover border border-slate-200 shrink-0" onerror="this.style.display='none'">
                                <div>
                                    <div class="font-bold text-ink-900 text-sm hover:text-accent-500 cursor-pointer" wire:click="edit({{ $post->id }})">
                                        {{ $post->title }}
                                    </div>
                                    <a href="{{ route('site.blog.detail', $post->slug) }}" target="_blank" class="text-[11px] text-slate-400 hover:text-ink-900 font-mono">
                                        /blog/{{ $post->slug }} ↗
                                    </a>
                                </div>
                            </div>
                        </td>
                        <td class="py-4 px-4 font-mono text-xs text-slate-600">
                            {{ $post->category?->name ?? 'Engineering' }}
                        </td>
                        <td class="py-4 px-4">
                            <span class="px-2.5 py-1 rounded-full font-mono text-[10px] font-bold uppercase
                                {{ $post->type === 'video' ? 'bg-purple-50 text-purple-700 border border-purple-200' : ($post->type === 'photo' ? 'bg-amber-50 text-amber-700 border border-amber-200' : 'bg-slate-100 text-slate-700') }}">
                                {{ $post->type }}
                            </span>
                        </td>
                        <td class="py-4 px-4 font-mono text-xs font-bold text-slate-600">
                            {{ number_format($post->views_count) }}
                        </td>
                        <td class="py-4 px-4">
                            <button wire:click="togglePublish({{ $post->id }})" class="font-mono text-[10px] font-bold px-2.5 py-1 rounded-full border cursor-pointer {{ $post->is_published ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-slate-100 text-slate-500 border-slate-200' }}">
                                {{ $post->is_published ? '● LIVE' : '○ DRAFT' }}
                            </button>
                        </td>
                        <td class="py-4 px-5 text-end">
                            <div class="inline-flex items-center gap-2">
                                <button wire:click="edit({{ $post->id }})" class="px-3.5 py-1.5 text-ink-900 hover:text-white hover:bg-ink-900 font-bold border border-slate-200 rounded-xl bg-white transition-all cursor-pointer">
                                    Edit
                                </button>
                                <button wire:click="delete({{ $post->id }})" onclick="return confirm('Delete article?') || event.stopImmediatePropagation()" class="p-1.5 text-slate-400 hover:text-rose-600 rounded-xl cursor-pointer">
                                    ✕
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="py-12 text-center text-slate-400">No posts found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="p-4 border-t border-slate-100">
            {{ $posts->links() }}
        </div>
    </div>

    <!-- Create / Edit Modal -->
    @if($showModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-ink-950/75 backdrop-blur-xs">
            <div class="bg-white border border-slate-200 rounded-2xl w-full max-w-3xl p-6 shadow-2xl space-y-5 max-h-[90vh] overflow-y-auto">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <h3 class="text-base font-heading font-extrabold text-ink-900">
                        {{ $editingId ? 'Edit Article / Media Post' : 'Compose New Article / Post' }}
                    </h3>
                    <button wire:click="$set('showModal', false)" class="text-slate-400 hover:text-slate-700">✕</button>
                </div>

                <div class="space-y-4 text-xs">
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Article Headline</label>
                        <input type="text" wire:model="title" class="w-full px-3.5 py-2.5 border border-slate-200 rounded-xl text-sm font-bold" placeholder="Engineering Arabic-First Design Systems..." />
                        @error('title') <span class="text-rose-600 text-[10px]">{{ $message }}</span> @enderror
                    </div>

                    <div class="grid grid-cols-3 gap-3">
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">URL Slug</label>
                            <input type="text" wire:model="slug" class="w-full px-3.5 py-2.5 border border-slate-200 rounded-xl font-mono" placeholder="arabic-first" />
                        </div>
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Media Format</label>
                            <select wire:model="type" class="w-full px-3.5 py-2.5 border border-slate-200 rounded-xl font-mono">
                                <option value="article">Standard Article</option>
                                <option value="photo">Photo Essay</option>
                                <option value="video">Video Episode</option>
                            </select>
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
                            <label class="block font-bold text-slate-700 mb-1">Cover Image Path</label>
                            <input type="text" wire:model="featured_image" class="w-full px-3.5 py-2.5 border border-slate-200 rounded-xl font-mono" placeholder="assets/img/blog/arabic-first.jpg" />
                        </div>
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Video Embed URL (if Video Episode)</label>
                            <input type="text" wire:model="video_url" class="w-full px-3.5 py-2.5 border border-slate-200 rounded-xl font-mono" placeholder="https://www.youtube.com/embed/..." />
                        </div>
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Summary / Excerpt</label>
                        <input type="text" wire:model="excerpt" class="w-full px-3.5 py-2.5 border border-slate-200 rounded-xl" placeholder="1-2 sentences summarizing the key takeaway..." />
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Full Article Content</label>
                        <textarea rows="8" wire:model="body" class="w-full px-3.5 py-2.5 border border-slate-200 rounded-xl font-mono text-xs leading-relaxed" placeholder="Write full article content here..."></textarea>
                        @error('body') <span class="text-rose-600 text-[10px]">{{ $message }}</span> @enderror
                    </div>

                    <div class="pt-2 border-t border-slate-100">
                        <label class="flex items-center gap-2.5 cursor-pointer font-bold text-ink-900">
                            <input type="checkbox" wire:model="is_published" class="rounded border-slate-300 text-accent-500 focus:ring-accent-500">
                            <span>Publish to Live Journal Immediately</span>
                        </label>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-2 pt-4 border-t border-slate-100">
                    <button wire:click="$set('showModal', false)" class="px-4 py-2.5 border border-slate-200 rounded-xl text-xs font-bold text-slate-600 hover:bg-slate-50">
                        Cancel
                    </button>
                    <button wire:click="save" class="px-5 py-2.5 bg-gradient-to-r from-accent-500 to-[#ff6b42] text-white font-bold rounded-xl text-xs uppercase tracking-wider shadow-glow">
                        Publish / Update Article ✓
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
