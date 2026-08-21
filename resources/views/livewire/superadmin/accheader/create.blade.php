<div class="app-main">


    <div class="app-content-header">
        <div class="container-fluid">
            <div class="row">

                <div class="col-sm-6">
                    <h3 class="mb-0">{{ $title }}</h3>
                </div>

                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-end">
                        <li class="breadcrumb-item"><a href="/superadmin/dashboard">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">{{ $title }}</li>
                    </ol>
                </div>

            </div>
        </div>
    </div>


    <div class="app-content">
        <div class="container-fluid">

            <div class="row">
                <div class="col-md-6 flex-fill">

                    <div class="card card-danger card-outline mb-4">

                        <div class="card-header">
                            <div class="card-title">Formulir {{ $title }}</div>
                        </div>

                        <form wire:submit.prevent="store" autocomplete="off">
                            @csrf
                            <div class="card-body">



                                <div class="mb-3">
                                    <label for="group_id" class="form-label">
                                        Group <span class="text-danger">*</span>
                                    </label>

                                    <select wire:model.live="group_id"
                                        id="group_id"
                                        class="form-select @error('group_id') is-invalid @enderror">
                                        <option value="">-- Pilih Group --</option>
                                        @foreach ($group as $item)
                                        <option value="{{ $item->id }}">
                                            {{ $item->nama }}
                                        </option>
                                        @endforeach
                                    </select>

                                    @error('group_id')
                                    <div class="form-text text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="mb-3">
                                    <label for="no_header" class="form-label">Nomor Header <span class="text-danger">*</span></label>
                                    <input wire:model.debounce.500ms="no_header" type="text" id="no_header" class="form-control @error('no_header') is-invalid @enderror">
                                    @error('no_header')
                                    <div class="form-text text-danger">{{ $message }}</div>
                                    @enderror
                                </div>



                                <div class="row">
                                    <div class="col">
                                        <div class="mb-3">
                                            <label for="nama" class="form-label">Nama <span class="text-danger">*</span></label>
                                            <input wire:model.debounce.500ms="nama" type="text" id="nama" class="form-control @error('nama') is-invalid @enderror">
                                            @error('nama')
                                            <div class="form-text text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col">

                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="keterangan" class="form-label">Keterangan <span class="text-danger">*</span></label>
                                    <textarea wire:model="keterangan" id="keterangan" class="form-control @error('keterangan') is-invalid @enderror" rows="3"></textarea>
                                    @error('keterangan') <small class="text-danger">{{ $message }}</small> @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="jenis" class="form-label d-flex">Jenis <span class="text-danger">*</span></label>

                                    {{-- Tampilkan pesan sebelum grup dipilih --}}
                                    @if(empty($group_name))
                                    <div class="text-danger mt-2">
                                        Harap pilih grup terlebih dahulu
                                    </div>
                                    @endif

                                    {{-- Loop radio jika ada --}}
                                    @if (!empty($radioItems))
                                    @foreach ($radioItems as $groupLabel => $radios)
                                    <label class="form-label"><span class="badge text-bg-danger"><strong>{{ $groupLabel }}</strong></span></label>

                                    @foreach ($radios as $i => $radio)
                                    <div class="form-check">
                                        <input
                                            class="form-check-input"
                                            type="radio"
                                            wire:model="jenis"
                                            value="{{ $radio['value'] }}"
                                            id="posisi{{ $groupLabel }}{{ $i }}"
                                            wire:key="jenis-{{ $groupLabel }}-{{ $i }}">
                                        <label class="form-check-label" for="posisi{{ $groupLabel }}{{ $i }}">
                                            {{ $radio['label'] }}
                                        </label>
                                    </div>
                                    @endforeach
                                    @endforeach
                                    @endif
                                </div>

                                <div class="card-footer">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <a wire:navigate href="{{ route('superadmin.account-header') }}" class="btn btn-secondary">
                                            Kembali
                                        </a>
                                        <button type="submit" class="btn btn-primary">
                                            Submit
                                        </button>
                                    </div>
                                </div>

                        </form>

                    </div>

                </div>
            </div>

        </div>
    </div>
    @script
    <script data-navigate-once>
        document.addEventListener('livewire:navigated', function() {

            // ============ FUNGSI INIT FLATPICKR ============
            function initFlatpickr(selector, livewireEvent) {
                const el = document.querySelector(selector);
                if (!el) return;

                // Hancurkan instance sebelumnya bila ada
                if (el._flatpickr) {
                    el._flatpickr.destroy();
                }

                flatpickr(el, {
                    dateFormat: "d-m-Y",
                    locale: "id",
                    allowInput: true,
                    clickOpens: true,
                    onChange: (selectedDates, dateStr) => {
                        Livewire.dispatch(livewireEvent, {
                            date: dateStr
                        });
                    }
                });
            }


            // ============ DAFTAR FIELD TANGGAL ============
            const dateFields = [{
                    selector: "#tgl_lahir",
                    event: "updateTglLahir"
                },
                {
                    selector: "#tgl_anggota_berhenti",
                    event: "updateTgl_anggota_berhenti"
                },
                {
                    selector: "#tgl_pengurus_diangkat",
                    event: "updateTgl_pengurus_diangkat"
                },
                {
                    selector: "#tgl_pengurus_berhenti",
                    event: "updateTgl_pengurus_berhenti"
                },
                {
                    selector: "#tgl_pengawas_diangkat",
                    event: "updateTgl_pengawas_diangkat"
                },
                {
                    selector: "#tgl_pengawas_berhenti",
                    event: "updateTgl_pengawas_berhenti"
                }
            ];

            // Init semua Flatpickr
            dateFields.forEach(field => {
                initFlatpickr(field.selector, field.event);
            });


            // ============ EVENT UNTUK RESET DARI LIVEWIRE ============
            Livewire.on('resetDateField', ({
                field
            }) => {
                const el = document.querySelector(`#${field}`);
                if (el?._flatpickr) {
                    el._flatpickr.clear(); // reset tampilan input
                }
            });
        });
    </script>
    @endscript





</div>