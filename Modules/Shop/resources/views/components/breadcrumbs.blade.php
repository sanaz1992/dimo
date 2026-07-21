@props([
    'items' => [],
])

<div class="breadcrumbs">
    @foreach ($items as $item)
        @if (!$loop->first &&  $item['label'])
            <span>/</span>
        @endif

        @if (!empty($item['url']))
            <a href="{{ $item['url'] }}">{{ $item['label'] }}</a>
        @else
            <span>{{ $item['label'] }}</span>
        @endif

    @endforeach
</div>
