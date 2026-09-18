@props([
    'name',
    'label' => null,
    'value' => null,
    'options' => [],
    'placeholder' => '-- Pilih --',
    'required' => false,
    'helper' => null,
])

@php
    $hasError = $errors->has($name);
    $current = old($name, $value);
@endphp

<div class="w-full">
    @if ($label)
        <label for="{{ $name }}" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">
            {{ $label }}
            @if ($required)
                <span class="text-rose-500">*</span>
            @endif
        </label>
    @endif

    <div class="relative">
        <select
            name="{{ $name }}"
            id="{{ $name }}"
            {{ $required ? 'required' : '' }}
            {{ $attributes->merge([
                'class' => 'block w-full rounded-lg border text-sm transition-colors duration-150 px-3.5 py-2.5 appearance-none bg-white ' .
                    ($hasError
                        ? 'border-rose-400 text-rose-900 focus:border-rose-500 focus:ring-2 focus:ring-rose-200 bg-rose-50/20'
                        : 'border-slate-300 text-slate-900 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200/60')
            ]) }}
        >
            @if ($placeholder)
                <option value="">{{ $placeholder }}</option>
            @endif

            @if (count($options) > 0)
                @foreach ($options as $key => $option)
                    @php
                        $optValue = is_numeric($key) ? $option : $key;
                        $optLabel = $option;
                    @endphp
                    <option value="{{ $optValue }}" @selected((string)$current === (string)$optValue)>
                        {{ $optLabel }}
                    </option>
                @endforeach
            @else
                {{ $slot }}
            @endif
        </select>

        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-slate-400">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
            </svg>
        </div>
    </div>

    @if ($helper && !$hasError)
        <p class="mt-1 text-xs text-slate-500">{{ $helper }}</p>
    @endif

    @error($name)
        <p class="mt-1 text-xs text-rose-600 font-medium flex items-center gap-1">
            <svg class="w-3.5 h-3.5 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
            </svg>
            {{ $message }}
        </p>
    @enderror
</div>
