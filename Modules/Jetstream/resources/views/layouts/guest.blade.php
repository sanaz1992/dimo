<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="rtl">

<head>
    <?php
use Modules\Core\Helpers\SettingHelper;

$settingHelper = app(SettingHelper::class); ?>
    <meta charset="UTF-8" />
    <title>
        {{$settingHelper->setting('site_title') ? $settingHelper->setting('site_title')?->value : __('core::attributes.company_title')}}
        | {{ $title ?? '' }}
    </title>
    <link rel="icon" type="image/x-icon"
        href="{{$settingHelper->setting('favicon')?->main_image?->getThumbnailUrl('small') ?? asset('build/images/fav2.jpg')}}">

    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @vite(['Modules/Jetstream/resources/assets/css/login.css'])

    <!-- Styles -->
    @livewireStyles
    {{--
    <script src="{{ asset('assets/scripts/alpine.min.js') }}" defer></script> --}}
</head>

<body>


        {{ $slot }}


    @livewireScripts
    @vite(['Modules/Jetstream/resources/assets/js/login.js'])
</body>

</html>
