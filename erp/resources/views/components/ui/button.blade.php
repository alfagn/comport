@props([
    'variant' => 'primary',
    'size' => 'md',
    'type' => 'button',
    'disabled' => false,
])

@php
$baseClasses = 'inline-flex items-center justify-center font-medium rounded-sm transition-all duration-150 focus:outline-none focus:ring-2 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed';

$variants = [
    'primary' => 'bg-ink-900 hover:bg-accent-600 text-white focus:ring-ink-900 border border-transparent shadow-xs',
    'accent' => 'bg-accent-500 hover:bg-accent-600 text-white font-bold focus:ring-accent-500 border border-transparent shadow-md shadow-accent-500/20',
    'secondary' => 'bg-ink-800 hover:bg-ink-700 text-white focus:ring-ink-800 border border-transparent',
    'outline' => 'border border-neutral-200 bg-white hover:bg-neutral-100 hover:border-neutral-300 text-ink-900 focus:ring-ink-900',
    'danger' => 'bg-rose-600 hover:bg-rose-700 text-white focus:ring-rose-500 border border-transparent',
    'success' => 'bg-emerald-600 hover:bg-emerald-700 text-white focus:ring-emerald-500 border border-transparent',
    'ghost' => 'text-neutral-700 hover:text-ink-900 hover:bg-neutral-100 border border-transparent shadow-none',
];

$sizes = [
    'sm' => 'text-xs px-2.5 py-1.5 gap-1.5',
    'md' => 'text-sm px-4 py-2 gap-2',
    'lg' => 'text-base px-6 py-2.5 gap-2.5 font-bold',
];

$classes = $baseClasses . ' ' . ($variants[$variant] ?? $variants['primary']) . ' ' . ($sizes[$size] ?? $sizes['md']);
@endphp

<button type="{{ $type }}" {{ $disabled ? 'disabled' : '' }} {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</button>
