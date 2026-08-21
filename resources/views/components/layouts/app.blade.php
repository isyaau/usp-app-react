<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>{{ $title ?? 'KSP KOPINKA' }}</title>

    @include('components.layouts.style')


    @livewireStyles

</head>

<body class="layout-fixed sidebar-expand-lg sidebar-open bg-body-tertiary">

    <div class="app-wrapper">

        @include('components.layouts.navbar')

        @include('components.layouts.sidebar')

        {{ $slot }}

        @include('components.layouts.footer')

    </div>
    @livewireScripts
    @filepondScripts

    @include('components.layouts.script')





    @if (session('swal'))
    <script>
        document.addEventListener('livewire:navigated', () => {
            Swal.fire(@json(session('swal')));
        }, {
            once: true
        });
    </script>

    <div
        x-data="{}"
        x-init="$nextTick(() => { Swal.fire(@json(session('swal'))) })"></div>
    @endif



</body>



























<script data-navigate-once src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script data-navigate-once src="https://npmcdn.com/flatpickr/dist/l10n/id.js"></script>
<script data-navigate-once>
    document.addEventListener('livewire:navigated', function() {
        flatpickr("#tglMulai", {
            dateFormat: "d-m-Y",
            locale: "id",
            allowInput: true,
            clickOpens: true,
            onChange: function(selectedDates, dateStr) {
                Livewire.dispatch('updateTglMulai', {
                    date: dateStr
                });
            }
        });

        flatpickr("#tglSampai", {
            dateFormat: "d-m-Y",
            locale: "id",
            allowInput: true,
            clickOpens: true,
            onChange: function(selectedDates, dateStr) {
                Livewire.dispatch('updateTglSampai', {
                    date: dateStr
                });
            }
        });
    });
</script>

<script>
    document.addEventListener('livewire:load', function() {
        // Inisialisasi Select2 untuk dropdown yang ada
        $('#provinsi').select2({
            placeholder: '-- Pilih Provinsi --',
            allowClear: true
        });

        $('#kota').select2({
            placeholder: '-- Pilih Kota/Kab --',
            allowClear: true
        });

        $('#kecamatan').select2({
            placeholder: '-- Pilih Kecamatan --',
            allowClear: true
        });

        $('#kelurahan').select2({
            placeholder: '-- Pilih Kelurahan --',
            allowClear: true
        });

        // Memperbarui select2 setelah data dropdown berubah
        Livewire.hook('message.processed', (message, component) => {
            $('#provinsi').select2({
                placeholder: '-- Pilih Provinsi --',
                allowClear: true
            });
            $('#kota').select2({
                placeholder: '-- Pilih Kota/Kab --',
                allowClear: true
            });
            $('#kecamatan').select2({
                placeholder: '-- Pilih Kecamatan --',
                allowClear: true
            });
            $('#kelurahan').select2({
                placeholder: '-- Pilih Kelurahan --',
                allowClear: true
            });
        });
    });
</script>





























</body>

</html>