@props([
    'tag' => 'button',
    'type' => 'button',
    'href' => null,
    'disabled' => false,
    'size' => 'md', // sm | md | lg | full
])

@php
    $isLink = $tag === 'a' || $href;

    $sizes = [
        'sm' => 'px-3 py-1.5 text-xs rounded-lg',
        'md' => 'px-4 py-2 text-sm rounded-xl',
        'lg' => 'px-5 py-3 text-sm rounded-xl',
        'full'=>'w-full sm:w-auto',
    ];

    $classes = 'inline-flex items-center justify-center ' . ($sizes[$size] ?? $sizes['md']);
    $disabledClasses = $disabled ? ' pointer-events-none opacity-50' : '';
@endphp

@if ($isLink)
    <a
        href="{{ $disabled ? 'javascript:void(0)' : $href }}"
        {{ $attributes->merge(['class' => $classes . $disabledClasses]) }}
        @if($disabled) aria-disabled="true" @endif
    >
        @isset($icon)
            <span>
                {{ $icon }}
            </span>
        @endisset

        {{ $slot }}
    </a>
@else
    <button
        type="{{ $type }}"
        {{ $disabled ? 'disabled' : '' }}
        {{ $attributes->merge(['class' => $classes . $disabledClasses]) }}
    >
        @isset($icon)
            <span>
                {{ $icon }}
            </span>
        @endisset

        {{ $slot }}
    </button>
@endif
