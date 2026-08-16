<!DOCTYPE html>
<html dir="rtl" lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <?php
use Modules\Core\Helpers\SettingHelper;

    $settingHelper = app(SettingHelper::class); ?>
    <meta charset="UTF-8" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>
        {{$settingHelper->setting('site_title') ? $settingHelper->setting('site_title')?->value : __('core::attributes.company_title')}}
        | {{ $title ?? '' }}
    </title>
    <link rel="icon" type="image/x-icon"
        href="{{$settingHelper->setting('favicon')?->main_image?->getThumbnailUrl('small') ?? asset('build/images/fav2.jpg')}}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />


    @vite([
        'resources/css/app.css',
        'resources/js/app.js',
        'Modules/Dashboard/resources/assets/css/index.css'
    ])
    @livewireStyles

    @stack('styles')
</head>

<body class="min-h-screen overflow-x-hidden pb-safe">

    <div class="ambient" aria-hidden="true">
        <div class="orb orb-1"></div>
        <div class="orb orb-2"></div>
        <div class="orb orb-3"></div>
    </div>

    <div
        class="app-layout relative z-10 mx-auto min-h-screen w-full max-w-[1600px] pt-4 sm:pt-6 lg:px-6 lg:pb-6 lg:pt-12">
        <x-Core::user.sidebar />

        <div class="main-column flex min-w-0 flex-1 flex-col gap-3 p-3 sm:gap-4 sm:p-4 lg:gap-5 lg:p-6">
            <x-Core::user.navbar />

            {{ $slot }}
        </div>
    </div>

    @stack('modals')

    @vite(['Modules/Dashboard/resources/assets/js/index.js'])
    @livewireScripts
    @stack('scripts')

    <script>

        Livewire.on('notify', (data) => {
            Swal.fire({
                toast: true,
                position: 'bottom-start',
                icon: data.type,
                title: data.message,
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                customClass: {
                    popup: 'swal-toast'
                }
            });
        });

    </script>


</body>

</html>
