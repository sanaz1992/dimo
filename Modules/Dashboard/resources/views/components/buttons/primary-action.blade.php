@props([
    'target' => null,
    'tag' => 'button',
    'type' => 'button',
    'href' => null,
    'disabled' => false,
    'size' => 'md', // sm | md | lg | full
    'color' => 'gray', // gray | blue | green | red | yellow | purple | orange
])

@php
    $isLink = $tag === 'a' || $href;

    $sizes = [
        'sm' => 'p-1 text-xs rounded-lg',
        'md' => 'p-1 text-sm rounded-xl',
        'lg' => 'p-1.5 text-sm rounded-xl',
        'full' => 'w-full sm:w-auto',
    ];

    $colors = [
        'gray' => 'bg-gray-50 text-gray-600 hover:bg-gray-100',
        'blue' => 'bg-blue-50 text-blue-600 hover:bg-blue-100',
        'green' => 'bg-green-50 text-green-600 hover:bg-green-100',
        'red' => 'bg-red-50 text-red-600 hover:bg-red-100',
        'yellow' => 'bg-yellow-50 text-yellow-600 hover:bg-yellow-100',
        'purple' => 'bg-purple-50 text-purple-600 hover:bg-purple-100',
        'orange' => 'bg-orange-50 text-orange-600 hover:bg-orange-100',
    ];

    $classes =
        'inline-flex items-center justify-center transition-colors duration-150 ' .
        ($sizes[$size] ?? $sizes['md']) . ' ' .
        ($colors[$color] ?? $colors['gray']);

    $disabledClasses = $disabled
        ? ' pointer-events-none opacity-50'
        : '';
@endphp

@if ($isLink)

    <a
        href="{{ $disabled ? 'javascript:void(0)' : $href }}"
        {{ $attributes->merge(['class' => $classes . $disabledClasses]) }}
        target="{{ $target ?? null }}"
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
        wire:loading.attr="disabled"
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
        <span
            wire:loading.remove
            @if($target) wire:target="{{ $target }}" @endif
        >
            {{ $slot }}
        </span>

        <!-- لودینگ -->
        <span
            wire:loading
            @if($target) wire:target="{{ $target }}" @endif
            class="inline-flex items-center gap-2"
        >
            <svg
                class="animate-spin h-5 w-5"
                xmlns="http://www.w3.org/2000/svg"
                fill="none"
                viewBox="0 0 24 24"
            >
                <circle
                    class="opacity-25"
                    cx="12"
                    cy="12"
                    r="10"
                    stroke="currentColor"
                    stroke-width="4"
                ></circle>

                <path
                    class="opacity-75"
                    fill="currentColor"
                    d="M4 12a8 8 0 018-8v8H4z"
                ></path>
            </svg>
        </span>
    </button>

@endif
