@props([
    'label' => null,
    'name' => null,
    'text' => null,
    'value' => 1,
])

<label class="flex items-center gap-2">
    <input
        type="checkbox"
        value="{{ $value }}"
        {{ $attributes->merge(['class' => 'h-4 w-4 rounded border-gray-300 text-primary focus:ring-primary']) }}
    >

    @if($text || $label)
        <span class="text-[12px] text-ink-faint">
            @lang($text ?? $label)
        </span>
    @endif
</label>

@if($name)
    @error($name)
        <span class="text-red-500 text-xs">{{ $message }}</span>
    @enderror
@endif

{{-- <x-dashboard::forms.checkbox
    text="product::attributes.is_active"
    name="form.is_active"
    wire:model.defer="form.is_active"
/> --}}

