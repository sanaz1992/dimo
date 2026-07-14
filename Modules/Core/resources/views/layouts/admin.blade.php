<!DOCTYPE html>
<html dir="rtl" lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <?php $settingHelper = app(\Modules\Core\Helpers\SettingHelper::class); ?>
    <meta charset="UTF-8" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>
        {{$settingHelper->setting('site_title') ? $settingHelper->setting('site_title')?->value : __('core::attributes.venus_company_title')}}
        | {{ $title ?? '' }}
    </title>
    <link rel="icon" type="image/x-icon"
        href="{{$settingHelper->setting('favicon')?->main_image?->getThumbnailUrl('small') ?? asset('build/images/fav2.jpg')}}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    @livewireStyles
    @vite(['Modules/Core/resources/assets/css/tailwind.css', 'resources/js/app.js'])



    <style>
        [x-cloak] {
            display: none !important;
        }

        .bg-darkGray {
            background-color: #3E3E3B;
        }
    </style>
    @stack('styles')
</head>

<body class="!bg-[#F7F8F8] text-gray-800 overflow-x-hidden">

    <div class="flex flex-col gap-8 p-8 relative min-h-screen">
        <!-- نوار ناوبری -->
        {{-- <div x-init="fetch('/src/components/navbar.html').then(r=>r.text()).then(html=>$el.innerHTML=html)"></div>
        --}}
        <x-Core::admin.navbar />

        <div class="flex gap-8">
            <!-- نوار کناری -->
            <x-Core::admin.sidebar />


            <!-- محتوای اصلی -->
            <main class="flex-1 overflow-x-hidden max-w-full">
                {{-- <div x-html="content"></div> --}}
                {{ $slot }}
            </main>
        </div>

        @stack('modals')

        {{-- <script src="//unpkg.com/alpinejs" defer></script> --}}
        @livewireScripts

        <script src="{{ asset('build/plugins/sweetalert2/sweetalert2@11.js') }}"></script>

        {{-- sidebar scripts --}}
        <script>
            (function () {
                // Wait for DOM to be ready
                if (document.readyState === 'loading') {
                    document.addEventListener('DOMContentLoaded', initSidebar);
                } else {
                    initSidebar();
                }

                function initSidebar() {
                    // Constants
                    const LG_BREAKPOINT = 1024;

                    // Sidebar elements
                    const sidebar = document.getElementById("sidebar");
                    const overlay = document.getElementById("sidebar-overlay");
                    const closeBtn = document.getElementById("sidebar-close-btn");
                    const closeLinks = document.querySelectorAll(".sidebar-close-link");
                    const mobileMenuButton = document.getElementById("mobile-menu-button");

                    // Early return if essential elements don't exist
                    if (!sidebar) {
                        console.warn("Sidebar element not found");
                        return;
                    }

                    let isOpen = false;

                    function openSidebar() {
                        if (!sidebar || !overlay) return;

                        isOpen = true;
                        sidebar.classList.remove("translate-x-full");
                        sidebar.classList.add("translate-x-0");
                        overlay.classList.remove("opacity-0", "pointer-events-none");
                        overlay.classList.add("opacity-100");
                        document.body.style.overflow = "hidden";
                    }

                    function closeSidebar() {
                        if (!sidebar || !overlay) return;

                        isOpen = false;
                        sidebar.classList.remove("translate-x-0");
                        sidebar.classList.add("translate-x-full");
                        overlay.classList.remove("opacity-100");
                        overlay.classList.add("opacity-0", "pointer-events-none");
                        document.body.style.overflow = "";
                    }

                    function toggleSidebar() {
                        if (isOpen) {
                            closeSidebar();
                        } else {
                            openSidebar();
                        }
                    }

                    // Mobile menu button click
                    if (mobileMenuButton) {
                        mobileMenuButton.addEventListener("click", toggleSidebar);
                    }

                    // Close button click
                    if (closeBtn) {
                        closeBtn.addEventListener("click", closeSidebar);
                    }

                    // Overlay click
                    if (overlay) {
                        overlay.addEventListener("click", closeSidebar);
                    }

                    // Close links click
                    closeLinks.forEach((link) => {
                        link.addEventListener("click", closeSidebar);
                    });

                    // Close on escape key
                    document.addEventListener("keydown", function (e) {
                        if (e.key === "Escape" && isOpen) {
                            closeSidebar();
                        }
                    });

                    // Close on window resize if desktop
                    window.addEventListener("resize", function () {
                        if (window.innerWidth >= LG_BREAKPOINT && isOpen) {
                            closeSidebar();
                        }
                    });
                }
            })();
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

        @stack('scripts')

        @livewireScripts

</body>

</html>
