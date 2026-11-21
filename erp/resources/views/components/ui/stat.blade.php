@props([
    'title',
    'value',
    'change' => null,
    'changeType' => 'positive', // positive, negative, neutral
    'icon' => null,
])

<div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl p-5 shadow-xs flex items-center justify-between">
    <div>
        <p class="text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">{{ $title }}</p>
        <p class="mt-2 text-2xl font-bold tracking-tight text-slate-900 dark:text-white">{{ $value }}</p>

        @if($change !== null)
            <div class="mt-2 flex items-center text-xs">
                @if($changeType === 'positive')
                    <span class="text-emerald-600 dark:text-emerald-400 font-semibold flex items-center gap-0.5">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"/></svg>
                        {{ $change }}
                    </span>
                @elseif($changeType === 'negative')
                    <span class="text-rose-600 dark:text-rose-400 font-semibold flex items-center gap-0.5">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/></svg>
                        {{ $change }}
                    </span>
                @else
                    <span class="text-slate-500 dark:text-slate-400 font-medium">{{ $change }}</span>
                @endif
            </div>
        @endif
    </div>

    @if($icon)
        <div class="p-3 bg-indigo-50 dark:bg-indigo-950/40 text-indigo-600 dark:text-indigo-400 rounded-xl">
            {{ $icon }}
        </div>
    @endif
</div>
