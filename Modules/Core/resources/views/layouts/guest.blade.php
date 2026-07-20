<!DOCTYPE html>
<html dir="rtl" lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <?php
use Modules\Core\Helpers\SettingHelper;

$settingHelper = app(SettingHelper::class); ?>
    <meta charset="UTF-8" />
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>
        {{$settingHelper->setting('site_title') ? $settingHelper->setting('site_title')?->value : __('core::attributes.venus_company_title')}}
        | {{ $title ?? '' }}
    </title>

    <link rel="icon" type="image/x-icon"
        href="{{$settingHelper->setting('favicon')?->main_image?->getThumbnailUrl('small') ?? asset('build/images/fav2.jpg')}}">

    <meta name="viewport" content="width=device-width, initial-scale=1.0" />


    @vite([
        'resources/css/app.css',
        'resources/js/app.js',
        'Modules/Shop/resources/assets/css/style.css'
    ])
    @livewireStyles

    @stack('styles')
</head>

<body>
    <x-Core::guest.header />

    {{ $slot }}

    <x-Core::guest.footer />

    @stack('modals')

    @livewireScripts
    @stack('scripts')

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const dropdownButtons = document.querySelectorAll('.dropdown-wrap [aria-controls]');

            function closeAllDropdowns(exceptPanel = null, exceptButton = null) {
                dropdownButtons.forEach((button) => {
                    const panelId = button.getAttribute('aria-controls');
                    const panel = panelId ? document.getElementById(panelId) : null;

                    if (!panel) return;

                    const isExceptPanel = exceptPanel && panel === exceptPanel;
                    const isExceptButton = exceptButton && button === exceptButton;

                    if (!isExceptPanel && !isExceptButton) {
                        panel.classList.add('hidden');
                        button.setAttribute('aria-expanded', 'false');
                        button.classList.remove('btn-ghost--active', 'profile-btn--active');
                    }
                });
            }

            dropdownButtons.forEach((button) => {
                const panelId = button.getAttribute('aria-controls');
                const panel = panelId ? document.getElementById(panelId) : null;

                if (!panel) return;

                button.addEventListener('click', function (event) {
                    event.stopPropagation();

                    const isHidden = panel.classList.contains('hidden');

                    closeAllDropdowns(panel, button);

                    panel.classList.toggle('hidden', !isHidden);
                    button.setAttribute('aria-expanded', String(isHidden));

                    if (isHidden) {
                        button.classList.add('btn-ghost--active');

                        const input = panel.querySelector('input, textarea, select');
                        if (input) input.focus();
                    } else {
                        button.classList.remove('btn-ghost--active', 'profile-btn--active');
                    }
                });

                panel.addEventListener('click', function (event) {
                    event.stopPropagation();
                });
            });

            document.addEventListener('click', function () {
                closeAllDropdowns();
            });

            document.addEventListener('keydown', function (event) {
                if (event.key === 'Escape') {
                    closeAllDropdowns();
                }
            });
        });


    </script>

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
