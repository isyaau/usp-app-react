<script data-navigate-once
    src="https://cdn.jsdelivr.net/npm/overlayscrollbars@2.11.0/browser/overlayscrollbars.browser.es6.min.js"
    crossorigin="anonymous"></script>


<script data-navigate-once
    src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"
    crossorigin="anonymous"></script>


<script data-navigate-once
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.min.js"
    crossorigin="anonymous"></script>


<script src="{{ asset('AdminLTE/dist/js/adminlte.js') }}"></script>


<script data-navigate-once>
    const SELECTOR_SIDEBAR_WRAPPER = '.sidebar-wrapper';
    const Default = {
        scrollbarTheme: 'os-theme-light',
        scrollbarAutoHide: 'leave',
        scrollbarClickScroll: true,
    };
    document.addEventListener('DOMContentLoaded', function() {
        const sidebarWrapper = document.querySelector(SELECTOR_SIDEBAR_WRAPPER);
        if (sidebarWrapper && OverlayScrollbarsGlobal?.OverlayScrollbars !== undefined) {
            OverlayScrollbarsGlobal.OverlayScrollbars(sidebarWrapper, {
                scrollbars: {
                    theme: Default.scrollbarTheme,
                    autoHide: Default.scrollbarAutoHide,
                    clickScroll: Default.scrollbarClickScroll,
                },
            });
        }
    });
</script>
<script data-navigate-once src="{{ asset('sweetalert2/dist/sweetalert2.all.min.js') }}"></script>
<script src="https://unpkg.com/mathlive"></script>


<script>
    document.addEventListener('livewire:init', () => {

        let mathField = null;

        function initMath() {
            mathField = document.getElementById('math-editor');
            if (!mathField) return;

            // kirim ke livewire saat user ketik
            mathField.addEventListener('input', sync);
        }

        window.insertToken = function(value) {
            if (!mathField) initMath();

            mathField.insert(value);
            mathField.focus();
            sync();
        }

        function sync() {
            Livewire.dispatch('update-rumus', {
                value: mathField.getValue('ascii-math')
            });
        }

        // saat modal dibuka
        Livewire.on('set-rumus', (rumus) => {
            initMath();
            mathField.setValue(rumus || '');
        });

    });
</script>