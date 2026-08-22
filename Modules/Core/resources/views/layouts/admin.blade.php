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

    <link rel="stylesheet" href="{{ asset('plugins/persian-datepicker/persian-datepicker.min.css') }}">

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
        <x-Core::admin.sidebar />

        <div class="main-column flex min-w-0 flex-1 flex-col gap-3 p-3 sm:gap-4 sm:p-4 lg:gap-5 lg:p-6">
            <x-Core::admin.navbar />

            <x-dashboard::errors />

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

    <script src="{{ asset('plugins/persian-datepicker/jquery.min.js') }}"></script>
    <script src="{{ asset('plugins/persian-datepicker/persian-date.min.js') }}"></script>
    <script src="{{ asset('plugins/persian-datepicker/persian-datepicker.min.js') }}"></script>

    <script>
        function adjustDatePickerPosition(datepicker) {
            const input = $(datepicker.inputElement);
            const container = datepicker.view.$container;

            setTimeout(() => {
                const offset = input.offset();
                const inputHeight = input.outerHeight();
                const containerHeight = container.outerHeight() || 280;
                const containerWidth = container.outerWidth() || 260;

                const windowHeight = $(window).height();
                const windowWidth = $(window).width();
                const scrollTop = $(window).scrollTop();

                const spaceBelow = windowHeight - (offset.top - scrollTop) - inputHeight;
                const spaceAbove = offset.top - scrollTop;

                let topVal = offset.top + inputHeight;
                // If not enough space below, and enough space above, show above
                if (spaceBelow < containerHeight && spaceAbove > containerHeight) {
                    topVal = offset.top - containerHeight;
                }

                let leftVal = offset.left;
                // Keep within screen boundaries
                if (leftVal + containerWidth > windowWidth) {
                    leftVal = windowWidth - containerWidth - 16;
                }
                if (leftVal < 16) {
                    leftVal = 16;
                }

                container.css({
                    top: topVal + 'px',
                    left: leftVal + 'px'
                });
            }, 10);
        }

        function initPersianDatePickers() {

            $('.persianDateTime').each(function () {

                const input = $(this);

                if (input.data('datepicker-initialized')) return;
                input.data('datepicker-initialized', true);

                input.persianDatepicker({
                    format: 'YYYY/MM/DD HH:mm',
                    timePicker: { enabled: true },
                    initialValue: false,
                    onShow: adjustDatePickerPosition,
                    onSelect: function (unix) {
                        // Dispatch standard events for Alpine/Vanilla JS compatibility
                        if (input[0]) {
                            input[0].dispatchEvent(new Event('input', { bubbles: true }));
                            input[0].dispatchEvent(new Event('change', { bubbles: true }));
                        }

                        const model =
                            input.attr('wire:model') ??
                            input.attr('wire:model.defer');
                        if (!model) return;

                        /*
                           const componentId = input.closest('[wire\\:id]').attr('wire:id');
                           const component = Livewire.find(componentId);
                           if (!component) return;
     */
                        // مقدار واقعی ورودی (فرمت شده)
                        const value = input.val(); // مثلا: 1403/10/05 11:38

                        /*      component.$wire.set(model, value);*/
                        const root = input.get(0).closest('[wire\\:id]');
                        if (!root || !root.__livewire) return;
                        root.__livewire.$wire.set(model, value);

                    }
                });
            });

            $('.persianDate').each(function () {
                const input = $(this);

                // جلوگیری از دوباره initialize شدن
                if (input.data('datepicker-initialized')) return;
                input.data('datepicker-initialized', true);

                input.persianDatepicker({
                    format: 'YYYY/MM/DD',
                    timePicker: {
                        enabled: false
                    },
                    initialValue: false,
                    autoClose: false,
                    observer: true,
                    toolbox: {
                        enabled: true
                    },
                    onShow: adjustDatePickerPosition,
                    onSelect: function () {
                        // Dispatch standard events for Alpine/Vanilla JS compatibility
                        if (input[0]) {
                            input[0].dispatchEvent(new Event('input', { bubbles: true }));
                            input[0].dispatchEvent(new Event('change', { bubbles: true }));
                        }

                        const value = input.val();
                        const model = input.data('model');

                        if (!model) return;

                        const root = input.get(0).closest('[wire\\:id]');
                        if (!root || !root.__livewire) return;

                        root.__livewire.$wire.set(model, value);
                    }
                });
            });
        }

        document.addEventListener('livewire:init', () => {

            initPersianDatePickers();

            Livewire.on('reinit-datepickers', () => {

                setTimeout(() => {
                    initPersianDatePickers();
                }, 50);
            });

            Livewire.hook('commit', () => {
                setTimeout(() => {
                    initPersianDatePickers();
                }, 50);
            });
        });
    </script>

</body>

</html>
