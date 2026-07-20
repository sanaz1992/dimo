@props([
    'target' => null,
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
    <button wire:loading.attr="disabled"
        type="{{ $type }}"
        {{ $disabled ? 'disabled' : '' }}
        {{ $attributes->merge(['class' => $classes . $disabledClasses]) }}
    >
        @isset($icon)
            <span>
                {{ $icon }}
            </span>
        @endisset

        <!-- متن اصلی دکمه -->
    <span wire:loading.remove @if($target) wire:target="{{ $target }}" @endif>
        {{ $slot }}
    </span>

    <!-- لودینگ -->
    <span wire:loading @if($target) wire:target="{{ $target }}" @endif class="inline-flex items-center gap-2">
        <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
        </svg>
        {{-- Loading... --}}
    </span>
    </button>
@endif
