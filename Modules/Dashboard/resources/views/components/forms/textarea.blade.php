@props([
    'label' => null,
    'name' => null,
    'rows' => 4,
])

<label class="block">
    @if($label)
        <span class="mb-1 block text-[12px] text-ink-faint">
            @lang($label)
        </span>
    @endif

    <textarea
        rows="{{ $rows }}"
        {{ $attributes->merge(['class' => 'input-field']) }}
    ></textarea>

    @if($name)
        @error($name)
            <span class="text-red-500 text-xs">{{ $message }}</span>
        @enderror
    @endif
</label>

{{-- <x-dashboard::forms.textarea
    label="product::attributes.description"
    name="form.description"
    wire:model.defer="form.description"
/> --}}

