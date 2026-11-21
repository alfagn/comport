@php
$jobs = \App\Modules\CMS\Models\JobPosting::where('is_active', true)->get();
@endphp

<x-layouts.site :title="'Careers & Open Positions — Empetix'">
    <section class="py-20 lg:py-28 bg-white dark:bg-slate-900">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="max-w-3xl mb-16">
                <span class="text-xs font-bold text-indigo-600 uppercase tracking-widest">{{ app()->getLocale() === 'ar' ? 'انضم إلى فريقنا' : 'Join Our Team' }}</span>
                <h1 class="mt-2 text-4xl sm:text-5xl font-extrabold text-slate-900 dark:text-white tracking-tight">
                    {{ app()->getLocale() === 'ar' ? 'اصنع مستقبلك المهني مع مهندسي ومبدعي إمبتكس' : 'Build High-Impact Digital Products With Us' }}
                </h1>
                <p class="mt-4 text-slate-600 dark:text-slate-400">
                    {{ app()->getLocale() === 'ar' ? 'نحن نبحث دائماً عن كفاءات استثنائية في هندسة البرمجيات، تصميم واجهات المستخدم، والتسويق الرقمي.' : 'We are always looking for passionate engineers, designers, and marketers.' }}
                </p>
            </div>

            <div class="space-y-6 max-w-4xl">
                @forelse($jobs as $job)
                    <div class="bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-2xl p-6 hover:shadow-lg transition-all flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                        <div>
                            <div class="flex items-center gap-2.5 mb-2">
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-indigo-50 dark:bg-indigo-950 text-indigo-600 border border-indigo-200 dark:border-indigo-800">
                                    {{ $job->department }}
                                </span>
                                <span class="text-xs text-slate-500">{{ $job->location }}</span>
                                <span class="text-xs text-slate-500">• {{ str_replace('_', ' ', ucfirst($job->employment_type)) }}</span>
                            </div>
                            <h3 class="text-xl font-bold text-slate-900 dark:text-white">{{ $job->title }}</h3>
                            <p class="mt-1 text-sm text-slate-600 dark:text-slate-400 max-w-2xl">{{ $job->description }}</p>
                        </div>

                        <div class="shrink-0">
                            <a href="mailto:careers@empetix.com?subject=Application: {{ urlencode($job->title) }}" class="inline-flex items-center justify-center px-4 py-2.5 rounded-xl font-bold text-sm text-white bg-indigo-600 hover:bg-indigo-700 transition-colors shadow-sm">
                                {{ app()->getLocale() === 'ar' ? 'قدّم للوظيفة' : 'Apply Now' }} →
                            </a>
                        </div>
                    </div>
                @empty
                    <p class="text-slate-500">No open vacancies at this moment.</p>
                @endforelse
            </div>
        </div>
    </section>
</x-layouts.site>
