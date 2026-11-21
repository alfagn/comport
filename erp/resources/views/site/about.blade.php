@php
$team = \App\Modules\CMS\Models\TeamMember::orderBy('sort_order')->get();
@endphp

<x-layouts.site :title="'About Us — Empetix'">
    <section class="py-20 lg:py-28 bg-white dark:bg-slate-900">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="max-w-3xl">
                <span class="text-xs font-bold text-indigo-600 uppercase tracking-widest">{{ app()->getLocale() === 'ar' ? 'من نحن' : 'Who We Are' }}</span>
                <h1 class="mt-2 text-4xl sm:text-5xl font-extrabold text-slate-900 dark:text-white tracking-tight">
                    {{ app()->getLocale() === 'ar' ? 'نحن نبني المستقبل الرقمي لشركات البرمجيات والتسويق' : 'Building the Digital Infrastructure for Tomorrow' }}
                </h1>
                <p class="mt-6 text-lg text-slate-600 dark:text-slate-300 leading-relaxed">
                    {{ app()->getLocale() === 'ar'
                        ? 'تأسست إمبتكس لتجمع بين القوة الهندسية البرمجية واستراتيجيات النمو التسويقي المدروسة، مع تقديم منصة SaaS متكاملة تعزز إنتاجية الشركات وتمنحها استقلالية تقنية تامة.'
                        : 'Empetix was founded to bridge high-performance software engineering with data-driven growth marketing, delivering an enterprise SaaS platform with physical database isolation.' }}
                </p>
            </div>

            <!-- Founders & Leadership -->
            <div class="mt-24">
                <div class="mb-12">
                    <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900 dark:text-white">
                        {{ app()->getLocale() === 'ar' ? 'المؤسسون وفريق القيادة' : 'Founders & Leadership' }}
                    </h2>
                    <p class="mt-2 text-sm text-slate-500">
                        {{ app()->getLocale() === 'ar' ? 'العقول التي تقود الرؤية والتميز التقني في إمبتكس.' : 'The visionaries steering technological excellence at Empetix.' }}
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    @forelse($team as $member)
                        <div class="bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-2xl p-6 hover:shadow-lg transition-all">
                            <div class="w-20 h-20 rounded-2xl bg-gradient-to-br from-indigo-500 to-indigo-700 text-white font-extrabold text-2xl flex items-center justify-center mb-6 shadow-md">
                                {{ mb_substr($member->name, 0, 1) }}
                            </div>
                            <h3 class="text-xl font-bold text-slate-900 dark:text-white">{{ $member->name }}</h3>
                            <p class="text-xs font-semibold text-indigo-600 dark:text-indigo-400 mt-0.5">{{ $member->role_title }}</p>
                            <p class="mt-4 text-sm text-slate-600 dark:text-slate-400 leading-relaxed">{{ $member->bio }}</p>
                            
                            @if($member->social_links)
                                <div class="mt-5 pt-4 border-t border-slate-200 dark:border-slate-800 flex gap-3 text-xs text-indigo-600 dark:text-indigo-400 font-semibold">
                                    @foreach($member->social_links as $network => $url)
                                        <a href="{{ $url }}" target="_blank" class="hover:underline capitalize">{{ $network }}</a>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    @empty
                        <p class="text-slate-500">Leadership details loading...</p>
                    @endforelse
                </div>
            </div>
        </div>
    </section>
</x-layouts.site>
