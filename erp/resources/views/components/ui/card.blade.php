@props([
    'title' => null,
    'description' => null,
    'actions' => null,
    'footer' => null,
])

<div {{ $attributes->merge(['class' => 'bg-white border border-neutral-200 rounded-sm shadow-subtle overflow-hidden transition-all duration-200']) }}>
    @if($title || $actions)
        <div class="px-6 py-4 border-b border-neutral-200 flex items-center justify-between gap-4 bg-white">
            <div>
                @if($title)
                    <h3 class="text-base font-heading font-extrabold text-ink-900 tracking-tight">{{ $title }}</h3>
                @endif
                @if($description)
                    <p class="mt-0.5 text-xs text-neutral-500 font-sans">{{ $description }}</p>
                @endif
            </div>

            @if($actions)
                <div class="flex items-center gap-2">
                    {{ $actions }}
                </div>
            @endif
        </div>
    @endif

    <div class="p-6">
        {{ $slot }}
    </div>

    @if($footer)
        <div class="px-6 py-3.5 bg-neutral-100/60 border-t border-neutral-200 flex items-center justify-between text-xs text-neutral-500 font-sans">
            {{ $footer }}
        </div>
    @endif
</div>
