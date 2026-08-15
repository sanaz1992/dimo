document.addEventListener('DOMContentLoaded', () => {

            const sidebar = document.getElementById('sidebar');
            const menuBtn = document.getElementById('menu-btn');
            const closeBtn = document.getElementById('close-btn');
            const mainColumn = document.querySelector('.main-column');

            const mobileBreakpoint = 1023;

            function isMobile() {
                return window.matchMedia(`(max-width: ${mobileBreakpoint}px)`).matches;
            }

            function openSidebar() {
                if (!sidebar || !isMobile()) {
                    return;
                }

                sidebar.classList.add('open');
                document.body.classList.add('drawer-open');

                menuBtn?.setAttribute('aria-expanded', 'true');
            }

            function closeSidebar() {
                if (!sidebar) {
                    return;
                }

                sidebar.classList.remove('open');
                document.body.classList.remove('drawer-open');

                menuBtn?.setAttribute('aria-expanded', 'false');
            }

            function toggleSidebar() {
                if (!sidebar || !isMobile()) {
                    return;
                }

                const isOpen = sidebar.classList.contains('open');

                if (isOpen) {
                    closeSidebar();
                } else {
                    openSidebar();
                }
            }

            // باز / بسته کردن با دکمه منو
            menuBtn?.addEventListener('click', (event) => {
                event.stopPropagation();
                toggleSidebar();
            });

            // بستن با دکمه X
            closeBtn?.addEventListener('click', closeSidebar);

            // بستن با کلیک روی محتوای اصلی
            mainColumn?.addEventListener('click', () => {
                if (sidebar?.classList.contains('open')) {
                    closeSidebar();
                }
            });

            // اگر از حالت موبایل خارج شدیم، Sidebar بسته شود
            const mediaQuery = window.matchMedia(
                `(max-width: ${mobileBreakpoint}px)`
            );

            mediaQuery.addEventListener('change', () => {
                if (!mediaQuery.matches) {
                    closeSidebar();
                }
            });

            // Escape برای بستن
            document.addEventListener('keydown', (event) => {
                if (
                    event.key === 'Escape' &&
                    sidebar?.classList.contains('open')
                ) {
                    closeSidebar();
                }
            });

            // وضعیت اولیه
            closeSidebar();

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
