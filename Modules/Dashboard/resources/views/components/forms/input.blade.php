@props([
    'label' => null,
    'name' => null,
    'type' => 'text',
    'placeholder' => null,
    'suffix'=>null
])

<label class="block">
    @if($label)
        <span class="mb-1 block text-[12px] text-ink-faint">
            @lang($label)
             @if(isset($suffix))
                <span class="text-muted small">({{$suffix}})</span>
            @endif
        </span>
    @endif

    <input
        type="{{ $type }}"
        {{ $attributes->merge(['class' => 'input-field']) }}
        @if($placeholder)
            placeholder="{{ __($placeholder) }}"
        @endif
    >

    @if($name)
        @error($name)
            <span class="text-red-500 text-xs">{{ $message }}</span>
        @enderror
    @endif
</label>
