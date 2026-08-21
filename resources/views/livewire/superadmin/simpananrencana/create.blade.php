<div class="app-main">


    <div class="app-content-header">
        <div class="container-fluid">
            <div class="row">

                <div class="col-sm-6">
                    <h3 class="mb-0">{{ $title }}</h3>
                </div>

                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-end">
                        <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
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

                        <form wire:submit.prevent="store" autocomplete="false">
                            @csrf
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-6">
                                        <div class="mb-3">
                                            <label for="tanggal_mulai" class="form-label">Tanggal<span class="text-danger">*</span></label>
                                            <input wire:model.defer="tanggal_mulai" type="text" id="tanggal_mulai" placeholder="dd-mm-yyyy" class="form-control @error('tanggal_mulai') is-invalid @enderror">
                                            @error('tanggal_mulai')
                                            <div class="form-text text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>


                                        <div class="mb-3">
                                            <label for="no_bukti" class="form-label">No Bukti <span class="text-danger">*</span></label>
                                            <input
                                                wire:model.defer="no_bukti"
                                                type="text"
                                                id="no_bukti"
                                                class="form-control @error('no_bukti') is-invalid @enderror">
                                            @error('no_bukti')
                                            <div class="form-text text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label for="jangka_waktu" class="form-label">Jangka Waktu <span class="text-danger">*</span></label>
                                            <div class="input-group mb-3">
                                                <input
                                                    wire:model.defer="jangka_waktu"
                                                    type="number"
                                                    min="1"
                                                    id="jangka_waktu"
                                                    class="form-control @error('jangka_waktu') is-invalid @enderror"
                                                    placeholder="Jangka waktu">

                                                <select
                                                    wire:model.defer="satuan"
                                                    class="form-select @error('satuan') is-invalid @enderror"
                                                    aria-label="Pilih satuan waktu">
                                                    <option value="" disabled selected>Pilih satuan</option>
                                                    <option value="hari">Hari</option>
                                                    <option value="bulan">Bulan</option>
                                                    <option value="tahun">Tahun</option>
                                                </select>
                                            </div>

                                            @error('jangka_waktu')
                                            <div class="invalid-feedback d-block">
                                                {{ $message }}
                                            </div>
                                            @enderror

                                            @error('satuan')
                                            <div class="invalid-feedback d-block">
                                                {{ $message }}
                                            </div>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label for="tanggal_jatuhtempo" class="form-label">Jatuh Tempo <span class="text-danger">*</span></label>
                                            <input wire:model.defer="tanggal_jatuhtempo" type="text" id="tanggal_jatuhtempo" placeholder="dd-mm-yyyy" class="form-control @error('tanggal_jatuhtempo') is-invalid @enderror">
                                            @error('tanggal_jatuhtempo')
                                            <div class="form-text text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="mb-3">
                                            <label for="nominal" class="form-label">Nominal <span class="text-danger">*</span></label>
                                            <input
                                                wire:model.defer="nominal"
                                                type="text"
                                                id="nominal"
                                                class="form-control @error('nominal') is-invalid @enderror">
                                            @error('nominal')
                                            <div class="form-text text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="mb-3">
                                            <label for="bagi_hasil" class="form-label">Bagi Hasil <span class="text-danger">*</span></label>
                                            <div class="input-group mb-3">
                                                <input wire:model.defer="bagi_hasil" type="text" class="form-control @error('bagi_hasil') is-invalid @enderror" id="bagi_hasil" aria-label="Recipient’s username" aria-describedby="basic-addon2">
                                                <span class="input-group-text" id="basic-addon2">%</span>
                                            </div>
                                            @error('bagi_hasil')
                                            <div class="form-text text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="mb-3">
                                            <label for="kantor_id" class="form-label">
                                                Kantor <span class="text-danger">*</span>
                                            </label>

                                            <select wire:model.defer="kantor_id"
                                                id="kantor_id"
                                                class="form-select @error('kantor_id') is-invalid @enderror">
                                                <option value="">-- Pilih Kantor --</option>
                                                @foreach ($kantors as $kantor)
                                                <option value="{{ $kantor->id }}">
                                                    [ {{ $kantor->kode }} ] {{ $kantor->nama_kantor }}
                                                </option>
                                                @endforeach
                                            </select>

                                            @error('kantor_id')
                                            <div class="form-text text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="mb-3">
                                            <label for="keterangan" class="form-label">Keterangan <span class="text-danger">*</span></label>
                                            <textarea wire:model.defer="keterangan" class="form-control @error('keterangan') is-invalid @enderror" id="exampleFormControlTextarea1" rows="3"></textarea>
                                            @error('keterangan')
                                            <div class="form-text text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <h5 class="mb-3">Nomor Simpanan</h5>
                                        <hr class="border border-danger border-2 opacity-50 mb-5">
                                        <div class="table-responsive">
                                            <table class="table table-hover table-bordered">
                                                <thead class="table-danger text-center">
                                                    <tr>
                                                        <th style="width: 10px">#</th>
                                                        <th style="width: 100px">No Simpanan</th>
                                                        <th style="width: 100px">Anggota</th>
                                                        <th style="width: 100px;"> <button
                                                                type="button"
                                                                class="btn btn-sm btn-success"
                                                                data-bs-toggle="modal"
                                                                data-bs-target="#modalPilihSimpanan">
                                                                <i class="fas fa-plus"></i> Tambah
                                                            </button></th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($detailSementara as $index => $simpananId)
                                                    @php
                                                    $simpanan = \App\Models\Simpanan::find($simpananId);
                                                    @endphp
                                                    <tr class="align-middle">
                                                        <td class="text-center">{{ $index + 1 }}</td>
                                                        <td>{{ $simpanan->no_rekening ?? '-' }}</td>
                                                        <td>{{ $simpanan->anggota->nama ?? '-' }}</td>
                                                        <td class="text-center">
                                                            <button type="button"
                                                                class="btn btn-sm btn-danger"
                                                                wire:click="removeDetail({{ $index }})">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </td>
                                                    </tr>
                                                    @endforeach
                                                </tbody>


                                            </table>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal fade" id="modalPilihSimpanan" tabindex="-1" aria-hidden="true" wire:ignore.self>
                                    <div class="modal-dialog modal-lg modal-dialog-scrollable">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Pilih Simpanan</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <input type="text" class="form-control mb-3"
                                                    placeholder="Cari No Rekening / Nama / Alamat"
                                                    wire:model.live.debounce.500ms="search">

                                                <table class="table table-bordered table-hover">
                                                    <thead class="table-secondary text-center">
                                                        <tr>
                                                            <th width="5%">
                                                                <input type="checkbox" wire:model.defer="selectAll">
                                                            </th>
                                                            <th>No</th>
                                                            <th>No Rekening</th>
                                                            <th>Produk</th>
                                                            <th>Nama Anggota</th>
                                                            <th>Alamat</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($simpananList as $item)
                                                        <tr>
                                                            <td class="text-center">
                                                                <input type="checkbox"
                                                                    value="{{ $item->id }}"
                                                                    wire:model.defer="selectedSimpanan">
                                                            </td>
                                                            <td class="text-center">{{ $loop->iteration }}</td>
                                                            <td>{{ $item->no_rekening }}</td>
                                                            <td>{{ $item->jenis_simpanan->nama ?? '-' }}</td>
                                                            <td>{{ $item->anggota->nama ?? '-' }}</td>
                                                            <td>{{ $item->anggota->alamat ?? '-' }}</td>
                                                        </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                            <div class="modal-footer">
                                                <button class="btn btn-primary" wire:click.prevent="addDetail">Pilih</button>


                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" wire:click.prevent="closeModal">Tutup</button>


                                            </div>
                                        </div>
                                    </div>
                                </div>











                            </div>


                            <div class="card-footer">
                                <div class="d-flex justify-content-between align-items-center">
                                    <a wire:navigate href="{{ route('superadmin.simpanan.rencana') }}" class="btn btn-secondary">
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
                    selector: "#tanggal_mulai",
                    event: "updateTanggal_mulai"
                },
                {
                    selector: "#tanggal_jatuhtempo",
                    event: "updateTanggal_jatuhtempo"
                },

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