@php
$projects = \App\Modules\CMS\Models\PortfolioProject::with('category')->where('is_published', true)->get();
@endphp

<x-layouts.site :title="'Our Work & Case Studies — Empetix'">
    <section class="py-20 lg:py-28 bg-white dark:bg-slate-900">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="max-w-3xl mb-16">
                <span class="text-xs font-bold text-indigo-600 uppercase tracking-widest">{{ app()->getLocale() === 'ar' ? 'أعمالنا ومشاريعنا' : 'Portfolio & Case Studies' }}</span>
                <h1 class="mt-2 text-4xl sm:text-5xl font-extrabold text-slate-900 dark:text-white tracking-tight">
                    {{ app()->getLocale() === 'ar' ? 'حلول هندسية أحدثت فارقاً حقيقياً لعملائنا' : 'Engineered Solutions Driving Real Business Impact' }}
                </h1>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                @forelse($projects as $p)
                    <div class="bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-3xl p-8 hover:shadow-xl transition-all flex flex-col justify-between">
                        <div>
                            <div class="flex items-center justify-between mb-4">
                                <span class="px-3 py-1 rounded-full text-xs font-semibold bg-indigo-50 dark:bg-indigo-950/60 text-indigo-600 dark:text-indigo-400 border border-indigo-200 dark:border-indigo-800">
                                    {{ $p->category?->name ?? 'Software' }}
                                </span>
                                @if($p->client_name)
                                    <span class="text-xs font-medium text-slate-500">{{ $p->client_name }}</span>
                                @endif
                            </div>

                            <h3 class="text-2xl font-bold text-slate-900 dark:text-white mb-3">{{ $p->title }}</h3>
                            <p class="text-sm text-slate-600 dark:text-slate-400 leading-relaxed mb-6">{{ $p->description ?? $p->excerpt }}</p>
                        </div>

                        <div class="pt-6 border-t border-slate-200 dark:border-slate-800 flex items-center justify-between text-xs font-semibold text-indigo-600">
                            <span>{{ $p->completion_date?->format('M Y') ?? 'Recent' }}</span>
                            <span>{{ app()->getLocale() === 'ar' ? 'قصة نجاح معتمدة' : 'Verified Case Study' }} ✓</span>
                        </div>
                    </div>
                @empty
                    <p class="text-slate-500">Case studies loading...</p>
                @endforelse
            </div>
        </div>
    </section>
</x-layouts.site>
