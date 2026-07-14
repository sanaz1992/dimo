@props([
    'model',        // نام پراپ Livewire برای entangle، مثال: 'form.price'
    'class' => 'form-input',
])


    <div
        x-data="{
            raw: @entangle($model),
            formatted: ''
        }"
        x-init="
            formatted = raw ? new Intl.NumberFormat().format(raw) : '';
        "
    >
        <input
            type="text"
            x-model="formatted"
            @input="
                raw = formatted.replace(/\D/g, '');
                formatted = raw ? new Intl.NumberFormat().format(raw) : '';
            "
            {{ $attributes->merge(['class' => $class]) }}
        >
    </div>

    
