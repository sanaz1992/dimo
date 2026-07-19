@props([
    'label' => null,
    'name' => null,
    'options' => [],
    'orientation' => 'horizontal', // horizontal | vertical
])

<div class="block">
    @if($label)
        <span class="mb-1 block text-[12px] text-ink-faint">
            @lang($label)
        </span>
    @endif

    <div class="{{ $orientation === 'horizontal' ? 'flex flex-row items-center gap-4 mt-3' : 'flex flex-col gap-3 mt-3' }}">
        @foreach($options as $value => $text)
            <label class="{{ $orientation === 'horizontal' ? 'flex items-center gap-2' : 'flex items-start gap-2' }}">
                <input
                    type="radio"
                    value="{{ $value }}"
                    {{ $attributes->merge(['class' => 'h-4 w-4 border-gray-300 text-primary focus:ring-primary']) }}
                >

                <span class="text-[12px] text-ink-faint">
                    @lang($text)
                </span>
            </label>
        @endforeach
    </div>

    @if($name)
        @error($name)
            <span class="text-red-500 text-xs">{{ $message }}</span>
        @enderror
    @endif
</div>


{{-- <x-dashboard::forms.radio
    label="product::attributes.status"
    name="form.status"
    wire:model.defer="form.status"
    orientation="horizontal"
    :options="[
        'draft' => 'product::attributes.draft',
        'active' => 'product::attributes.active',
        'inactive' => 'product::attributes.inactive',
    ]"
/> --}}
