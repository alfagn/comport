@php
$posts = \App\Modules\CMS\Models\Post::with('category')->where('is_published', true)->orderBy('id', 'desc')->get();
@endphp

<x-layouts.site :title="'Blog & Articles — Empetix'">
    <section class="py-20 lg:py-28 bg-white dark:bg-slate-900">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="max-w-3xl mb-16">
                <span class="text-xs font-bold text-indigo-600 uppercase tracking-widest">{{ app()->getLocale() === 'ar' ? 'المدونة والأفكار' : 'Insights & Engineering' }}</span>
                <h1 class="mt-2 text-4xl sm:text-5xl font-extrabold text-slate-900 dark:text-white tracking-tight">
                    {{ app()->getLocale() === 'ar' ? 'مقالات في هندسة البرمجيات والتسويق الرقمي' : 'Articles on Architecture, Growth & Engineering' }}
                </h1>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                @forelse($posts as $post)
                    <article class="bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-2xl p-6 hover:shadow-lg transition-all flex flex-col justify-between">
                        <div>
                            <div class="flex items-center gap-2 mb-3">
                                <span class="text-xs font-semibold text-indigo-600 dark:text-indigo-400">
                                    {{ $post->category?->name ?? 'Article' }}
                                </span>
                                <span class="text-slate-400 text-xs">•</span>
                                <span class="text-xs text-slate-500">{{ $post->published_at?->format('d M Y') ?? 'Recent' }}</span>
                            </div>

                            <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-2">{{ $post->title }}</h3>
                            <p class="text-sm text-slate-600 dark:text-slate-400 leading-relaxed mb-4">{{ $post->excerpt }}</p>
                        </div>

                        <div class="pt-4 border-t border-slate-200 dark:border-slate-800 flex items-center justify-between text-xs text-slate-500">
                            <span>{{ $post->views_count }} {{ app()->getLocale() === 'ar' ? 'مشاهدة' : 'views' }}</span>
                            <span class="font-bold text-indigo-600">{{ app()->getLocale() === 'ar' ? 'اقرأ المزيد' : 'Read Article' }} →</span>
                        </div>
                    </article>
                @empty
                    <p class="text-slate-500">No blog posts available.</p>
                @endforelse
            </div>
        </div>
    </section>
</x-layouts.site>
