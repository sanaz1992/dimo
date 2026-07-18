@props([
    'label' => null,
    'name' => null,
    'options' => [],
])

<div class="block">
    @if($label)
        <span class="mb-1 block text-[12px] text-ink-faint">
            @lang($label)
        </span>
    @endif

    <div class="space-y-2">
        @foreach($options as $value => $text)
            <label class="flex items-center gap-2">
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
    :options="[
        'draft' => 'product::attributes.draft',
        'active' => 'product::attributes.active',
        'inactive' => 'product::attributes.inactive',
    ]"
/> --}}
