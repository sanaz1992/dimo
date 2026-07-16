@props([
    'tag' => 'button',
    'type' => 'button',
    'href' => null,
    'disabled' => false,
])

@php
    $isLink = $tag === 'a' || $href;
    $classes = 'btn-fill w-full justify-center sm:w-auto';
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
