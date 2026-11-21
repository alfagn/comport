@props([
    'title' => 'No records found',
    'description' => 'Try adjusting your search criteria or create a new entry.',
    'action' => null,
])

<div class="py-12 px-4 text-center">
    <div class="mx-auto w-12 h-12 rounded-full bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-slate-400">
        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
        </svg>
    </div>
    <h3 class="mt-3 text-sm font-semibold text-slate-900 dark:text-white">{{ $title }}</h3>
    <p class="mt-1 text-xs text-slate-500 dark:text-slate-400 max-w-sm mx-auto">{{ $description }}</p>

    @if($action)
        <div class="mt-4">
            {{ $action }}
        </div>
    @endif
</div>
