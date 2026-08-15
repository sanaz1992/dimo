<td {{ $attributes->merge(['class' => 'data-cell px-4 py-3.5']) }} data-label="{{ $label??'' }}">
    {{ $slot }}
</td>
