@props([
    'variant' => 'neutral',
])

@php
$variants = [
    'accent' => 'bg-accent-500/10 text-accent-600 border-accent-500/30',
    'ink' => 'bg-ink-900 text-white border-ink-900',
    'success' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
    'danger' => 'bg-rose-50 text-rose-700 border-rose-200',
    'warning' => 'bg-amber-50 text-amber-700 border-amber-200',
    'info' => 'bg-sky-50 text-sky-700 border-sky-200',
    'primary' => 'bg-ink-800 text-white border-ink-800',
    'neutral' => 'bg-neutral-100 text-neutral-700 border-neutral-200',
];

$classes = 'inline-flex items-center px-2 py-0.5 rounded-sm text-[11px] font-mono uppercase tracking-wider font-semibold border ' . ($variants[$variant] ?? $variants['neutral']);
@endphp

<span {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</span>
