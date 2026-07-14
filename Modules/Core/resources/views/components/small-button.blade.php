@props([
    'target' => null,
    'type' => 'button',
    'disabled' => false,
])

<button 
    type="{{  $type ?? 'button'  }}" 
    {{ $attributes->merge(['class' => 'px-3 py-1.5 border border-transparent text-xs font-medium  rounded-full focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500']) }}
    wire:loading.attr="disabled"
    @if($target) wire:target="{{ $target }}" @endif
    @if($disabled) disabled @endif
>
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
