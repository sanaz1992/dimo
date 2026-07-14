@if ($errors->any())
    <div {{ $attributes }}>
        {{-- <div class="font-medium text-red-600">{{ __('Whoops! Something went wrong.') }}</div> --}}

        <div class="">
            @foreach ($errors->all() as $error)
                <span class="error-text">{{ $error }}</span>
            @endforeach
        </div>
    </div>
@endif
