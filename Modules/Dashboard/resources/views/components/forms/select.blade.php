@props([
    'label' => null,
    'name' => null,
    'options' => [],
    'placeholder' => null,
    'optionValue' => 'id',
    'optionLabel' => 'name',
])

<label class="block">
    @if($label)
        <span class="mb-1 block text-[12px] text-ink-faint">
            @lang($label)
        </span>
    @endif

    <select {{ $attributes->merge(['class' => 'input-field left-3']) }}>
        @if($placeholder)
            <option value="">
                @lang($placeholder)
            </option>
        @endif

        @foreach($options as $key => $option)
            @if(is_array($option))
                <option value="{{ $option[$optionValue] ?? $key }}">
                    {{ $option[$optionLabel] ?? $option[$optionValue] ?? $key }}
                </option>
            @elseif(is_object($option))
                <option value="{{ $option->{$optionValue} }}">
                    {{ $option->{$optionLabel} }}
                </option>
            @else
                <option value="{{ $key }}">
                    {{ $option }}
                </option>
            @endif
        @endforeach
    </select>

    @if($name)
        @error($name)
            <span class="text-red-500 text-xs">{{ $message }}</span>
        @enderror
    @endif
</label>


{{--****************************** collection ***************************--}}
{{-- <x-dashboard::forms.select
    label="product::attributes.category"
    name="form.category_id"
    wire:model.defer="form.category_id"
    placeholder="product::attributes.select_category"
    :options="$categories"
    option-value="id"
    option-label="name"
/> --}}

{{--********************************* array *******************************--}}
{{-- <x-dashboard::forms.select
    label="product::attributes.status"
    name="form.status"
    wire:model.defer="form.status"
    placeholder="core::attributes.select"
    :options="[
        'draft' => 'پیش‌نویس',
        'active' => 'فعال',
        'inactive' => 'غیرفعال',
    ]"
/> --}}

