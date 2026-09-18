@props([
    'name',
    'label',
    'description' => null,
    'checked' => false,
    'value' => '1',
])

@php
    $isChecked = old($name, $checked) ? true : false;
@endphp

<div class="relative flex items-start">
    <div class="flex h-6 items-center">
        <input
            type="checkbox"
            name="{{ $name }}"
            id="{{ $name }}"
            value="{{ $value }}"
            @checked($isChecked)
            {{ $attributes->merge([
                'class' => 'h-4.5 w-4.5 rounded-md border-slate-300 text-emerald-600 focus:ring-emerald-500 focus:ring-offset-0 transition cursor-pointer'
            ]) }}
        />
    </div>
    <div class="ml-3 text-sm leading-6">
        <label for="{{ $name }}" class="font-medium text-slate-800 cursor-pointer select-none">
            {{ $label }}
        </label>
        @if ($description)
            <p class="text-xs text-slate-500">{{ $description }}</p>
        @endif
        @error($name)
            <p class="text-xs text-rose-600 mt-0.5">{{ $message }}</p>
        @enderror
    </div>
</div>
