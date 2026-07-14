@props([
    'target' => null,
    'type' => 'button',
    'disabled' => false,
])
{{-- @php
    // مقدار wire:click را دریافت می‌کنیم
    $clickAction = $attributes->get('wire:click');

    // اگر wire:key دستی نبود → از wire:click بسازیم
    if (!$attributes->get('wire:key') && $clickAction) {
        $attributes = $attributes->merge([
            'wire:key' => $clickAction . '-btn-'.verta()->now()->format('YmdHis')
        ]);
    }
@endphp --}}
<button 
    type="{{  $type ?? 'button'  }}" 
    {{ $attributes->merge(['class' => 'rounded-lg  px-4 py-2 text-sm  disabled:opacity-40']) }}
    wire:loading.attr="disabled"
    @if($attributes->has('wire:click'))
        wire:key="{{ $attributes->get('wire:click') }}-btn-{{ verta()->now()->format('YmdHis') }}"
    @endif
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
