@props([
    'label' => null,
    'name' => null,
    'error' => null,
    'required' => false,
])

<div class="w-full">
    @if($label)
        <label for="{{ $name }}" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">
            {{ $label }}
            @if($required) <span class="text-rose-500">*</span> @endif
        </label>
    @endif

    <select
        name="{{ $name }}"
        id="{{ $name }}"
        {{ $attributes->merge([
            'class' => 'block w-full rounded-lg border-slate-300 dark:border-slate-700 dark:bg-slate-800 dark:text-white px-3.5 py-2.5 text-sm shadow-xs transition duration-150 focus:border-indigo-500 focus:ring-indigo-500 ' . ($error ? 'border-rose-400 focus:border-rose-500 focus:ring-rose-500' : '')
        ]) }}
    >
        {{ $slot }}
    </select>

    @if($error)
        <p class="mt-1.5 text-xs text-rose-600 dark:text-rose-400">{{ $error }}</p>
    @endif
</div>
