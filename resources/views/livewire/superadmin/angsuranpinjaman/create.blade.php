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

                        <form wire:submit.prevent="store" autocomplete="off">
                            @csrf
                            <div class="card-body">
                                <!-- Keanggotaan, Pengurus, Pengawas, Ahli Waris Tabs -->

                                <h5 class="mb-3">Data Angsuran Pinjaman</h5>
                                <hr class="border border-danger border-2 opacity-50 mb-3">
                                <ul class="nav nav-pills nav-fill mb-5">
                                    <li class="nav-item">
                                        <button type="button"
                                            class="nav-link w-100 {{ $activeTab === 'angsuran' ? 'active bg-danger text-white' : 'text-danger' }}"
                                            wire:click="setTab('angsuran')">
                                            Angsuran
                                        </button>
                                    </li>
                                    <li class="nav-item">
                                        <button type="button"
                                            class="nav-link w-100 {{ $activeTab === 'transaksi' ? 'active bg-danger text-white' : 'text-danger' }}"
                                            wire:click="setTab('transaksi')">
                                            Transaksi
                                        </button>
                                    </li>

                                </ul>

                                <div class="tab-content">
                                    <!-- Angsuran -->
                                    <div style="{{ $activeTab === 'angsuran' ? 'display:block;' : 'display:none;' }}">
                                        <div class="row">
                                            <div class="col">
                                                <div class="mb-3">
                                                    <label for="kode" class="form-label">
                                                        Tanggal <span class="text-danger">*</span>
                                                    </label>

                                                    <input
                                                        wire:model.debounce.500ms="nama"
                                                        type="text"
                                                        id="nama"
                                                        class="form-control @error('nama') is-invalid @enderror">

                                                    @error('nama')
                                                    <div class="invalid-feedback d-block">
                                                        {{ $message }}
                                                    </div>
                                                    @enderror
                                                </div>
                                                <div class="mb-3">
                                                    <label for="kode" class="form-label">
                                                        Nomor Bukti <span class="text-danger">*</span>
                                                    </label>

                                                    <input
                                                        wire:model.debounce.500ms="nama"
                                                        type="text"
                                                        id="nama"
                                                        class="form-control @error('nama') is-invalid @enderror">

                                                    @error('nama')
                                                    <div class="invalid-feedback d-block">
                                                        {{ $message }}
                                                    </div>
                                                    @enderror
                                                </div>
                                                <div class="mb-3">
                                                    <label for="kode" class="form-label">
                                                        Nomor Pinjaman <span class="text-danger">*</span>
                                                    </label>

                                                    <input
                                                        wire:model.debounce.500ms="nama"
                                                        type="text"
                                                        id="nama"
                                                        class="form-control @error('nama') is-invalid @enderror">

                                                    @error('nama')
                                                    <div class="invalid-feedback d-block">
                                                        {{ $message }}
                                                    </div>
                                                    @enderror
                                                </div>

                                            </div>
                                            <div class="col">
                                                <div class="mb-3">
                                                    <label for="kode" class="form-label">
                                                        Terima Dana <span class="text-danger">*</span>
                                                    </label>

                                                    <input
                                                        wire:model.debounce.500ms="nama"
                                                        type="text"
                                                        id="nama"
                                                        class="form-control @error('nama') is-invalid @enderror">

                                                    @error('nama')
                                                    <div class="invalid-feedback d-block">
                                                        {{ $message }}
                                                    </div>
                                                    @enderror
                                                </div>
                                                <div class="mb-3">
                                                    <label for="kode" class="form-label">
                                                        No Account <span class="text-danger">*</span>
                                                    </label>

                                                    <input
                                                        wire:model.debounce.500ms="nama"
                                                        type="text"
                                                        id="nama"
                                                        class="form-control @error('nama') is-invalid @enderror">

                                                    @error('nama')
                                                    <div class="invalid-feedback d-block">
                                                        {{ $message }}
                                                    </div>
                                                    @enderror
                                                </div>
                                                <div class="mb-3">
                                                    <label for="kode" class="form-label">
                                                        Jatuh Tempo Angsuran <span class="text-danger">*</span>
                                                    </label>

                                                    <input
                                                        wire:model.debounce.500ms="nama"
                                                        type="text"
                                                        id="nama"
                                                        class="form-control @error('nama') is-invalid @enderror">

                                                    @error('nama')
                                                    <div class="invalid-feedback d-block">
                                                        {{ $message }}
                                                    </div>
                                                    @enderror
                                                </div>
                                                <div class="mb-3">
                                                    <label for="kode" class="form-label">
                                                        Terlambat <span class="text-danger">*</span>
                                                    </label>

                                                    <input
                                                        wire:model.debounce.500ms="nama"
                                                        type="text"
                                                        id="nama"
                                                        class="form-control @error('nama') is-invalid @enderror">

                                                    @error('nama')
                                                    <div class="invalid-feedback d-block">
                                                        {{ $message }}
                                                    </div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <label for="kode" class="form-label">
                                                Nama Anggota <span class="text-danger">*</span>
                                            </label>

                                            <input
                                                wire:model.debounce.500ms="nama"
                                                type="text"
                                                id="nama"
                                                class="form-control @error('nama') is-invalid @enderror">

                                            @error('nama')
                                            <div class="invalid-feedback d-block">
                                                {{ $message }}
                                            </div>
                                            @enderror
                                        </div>
                                        <div class="mb-3">
                                            <label for="kode" class="form-label">
                                                Alamat Anggota <span class="text-danger">*</span>
                                            </label>

                                            <input
                                                wire:model.debounce.500ms="nama"
                                                type="text"
                                                id="nama"
                                                class="form-control @error('nama') is-invalid @enderror">

                                            @error('nama')
                                            <div class="invalid-feedback d-block">
                                                {{ $message }}
                                            </div>
                                            @enderror
                                        </div>
                                        <div class="row">
                                            <div class="col-6">
                                                <div class="mb-3">
                                                    <label for="kode" class="form-label">
                                                        Plafon <span class="text-danger">*</span>
                                                    </label>

                                                    <input
                                                        wire:model.debounce.500ms="nama"
                                                        type="text"
                                                        id="nama"
                                                        class="form-control @error('nama') is-invalid @enderror">

                                                    @error('nama')
                                                    <div class="invalid-feedback d-block">
                                                        {{ $message }}
                                                    </div>
                                                    @enderror
                                                </div>
                                            </div>

                                        </div>
                                        <div class="row">
                                            <div class="col">
                                                <div class="mb-3">
                                                    <label for="kode" class="form-label">
                                                        Sisa Pinjaman <span class="text-danger">*</span>
                                                    </label>

                                                    <input
                                                        wire:model.debounce.500ms="nama"
                                                        type="text"
                                                        id="nama"
                                                        class="form-control @error('nama') is-invalid @enderror">

                                                    @error('nama')
                                                    <div class="invalid-feedback d-block">
                                                        {{ $message }}
                                                    </div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col">
                                                <div class="mb-3">
                                                    <label for="kode" class="form-label">
                                                        Tunggakan <span class="text-danger">*</span>
                                                    </label>

                                                    <input
                                                        wire:model.debounce.500ms="nama"
                                                        type="text"
                                                        id="nama"
                                                        class="form-control @error('nama') is-invalid @enderror">

                                                    @error('nama')
                                                    <div class="invalid-feedback d-block">
                                                        {{ $message }}
                                                    </div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col">
                                                <div class="mb-3">
                                                    <label for="kode" class="form-label">
                                                        Sisa Pokok <span class="text-danger">*</span>
                                                    </label>

                                                    <input
                                                        wire:model.debounce.500ms="nama"
                                                        type="text"
                                                        id="nama"
                                                        class="form-control @error('nama') is-invalid @enderror">

                                                    @error('nama')
                                                    <div class="invalid-feedback d-block">
                                                        {{ $message }}
                                                    </div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col">
                                                <div class="mb-3">
                                                    <label for="kode" class="form-label">
                                                        Total Pokok <span class="text-danger">*</span>
                                                    </label>

                                                    <input
                                                        wire:model.debounce.500ms="nama"
                                                        type="text"
                                                        id="nama"
                                                        class="form-control @error('nama') is-invalid @enderror">

                                                    @error('nama')
                                                    <div class="invalid-feedback d-block">
                                                        {{ $message }}
                                                    </div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col">
                                                <div class="mb-3">
                                                    <label for="kode" class="form-label">
                                                        Sisa Bagi Hasil <span class="text-danger">*</span>
                                                    </label>

                                                    <input
                                                        wire:model.debounce.500ms="nama"
                                                        type="text"
                                                        id="nama"
                                                        class="form-control @error('nama') is-invalid @enderror">

                                                    @error('nama')
                                                    <div class="invalid-feedback d-block">
                                                        {{ $message }}
                                                    </div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col">
                                                <div class="mb-3">
                                                    <label for="kode" class="form-label">
                                                        Total Bagi Hasil <span class="text-danger">*</span>
                                                    </label>

                                                    <input
                                                        wire:model.debounce.500ms="nama"
                                                        type="text"
                                                        id="nama"
                                                        class="form-control @error('nama') is-invalid @enderror">

                                                    @error('nama')
                                                    <div class="invalid-feedback d-block">
                                                        {{ $message }}
                                                    </div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col">
                                                <div class="mb-3">
                                                    <label class="form-label">Detail Komponen <span class="text-danger">*</span></label>

                                                    <table class="table table-bordered">
                                                        <thead class="table-light">
                                                            <tr>
                                                                <th class="text-center">Biaya Pinjaman</th>
                                                                <th class="text-center">Nominal</th>
                                                                <th class="text-center">%</th>
                                                                <th class="text-center">No Account</th>
                                                                <th class="text-center">Jumlah</th>

                                                            </tr>
                                                        </thead>
                                                        <tbody>

                                                            <tr>
                                                                <td>
                                                                </td>
                                                                <td>
                                                                </td>
                                                                <td class="text-center">
                                                                    <input class="form-check-input" type="checkbox" value="" id="checkDefault">
                                                                </td>
                                                                <td>
                                                                </td>
                                                                <td class="text-center">
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-6">
                                                <div class="mb-3">
                                                    <label for="kode" class="form-label">
                                                        Angsuran <span class="text-danger">*</span>
                                                    </label>

                                                    <input
                                                        wire:model.debounce.500ms="nama"
                                                        type="text"
                                                        id="nama"
                                                        class="form-control @error('nama') is-invalid @enderror">

                                                    @error('nama')
                                                    <div class="invalid-feedback d-block">
                                                        {{ $message }}
                                                    </div>
                                                    @enderror
                                                </div>
                                                <div class="mb-3">
                                                    <label for="kode" class="form-label">
                                                        Pembulatan <span class="text-danger">*</span>
                                                    </label>

                                                    <input
                                                        wire:model.debounce.500ms="nama"
                                                        type="text"
                                                        id="nama"
                                                        class="form-control @error('nama') is-invalid @enderror">

                                                    @error('nama')
                                                    <div class="invalid-feedback d-block">
                                                        {{ $message }}
                                                    </div>
                                                    @enderror
                                                </div>
                                                <div class="mb-3">
                                                    <label for="kode" class="form-label">
                                                        Total <span class="text-danger">*</span>
                                                    </label>

                                                    <input
                                                        wire:model.debounce.500ms="nama"
                                                        type="text"
                                                        id="nama"
                                                        class="form-control @error('nama') is-invalid @enderror">

                                                    @error('nama')
                                                    <div class="invalid-feedback d-block">
                                                        {{ $message }}
                                                    </div>
                                                    @enderror
                                                </div>
                                                <div class="mb-3">
                                                    <label for="kode" class="form-label">
                                                        Keterangan <span class="text-danger">*</span>
                                                    </label>

                                                    <input
                                                        wire:model.debounce.500ms="nama"
                                                        type="text"
                                                        id="nama"
                                                        class="form-control @error('nama') is-invalid @enderror">

                                                    @error('nama')
                                                    <div class="invalid-feedback d-block">
                                                        {{ $message }}
                                                    </div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>

                                    </div>

                                    <!-- Transaksi -->
                                    <div style="{{ $activeTab === 'transaksi' ? 'display:block;' : 'display:none;' }}">
                                        <div class="table-responsive">
                                            <table class="table table-hover table-bordered align-middle">
                                                <thead class="table-danger text-center">
                                                    <tr>
                                                        <th style="width: 50px;">No</th>
                                                        <th>Tanggal</th>
                                                        <th style="width: 200px">Nomor Bukti</th>
                                                        <th style="width: 50px;">Pencairan</th>
                                                        <th style="width: 200px">Angsuran Pokok</th>
                                                        <th style="width: 200px">Angsuran Bagi Hasil</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td class="text-center">

                                                        </td>
                                                        <td class="text-center">

                                                        </td>

                                                        <td>
                                                            <input wire:model.live="nama"
                                                                type="text"
                                                                id="nama"
                                                                class="form-control">
                                                        </td>
                                                        <td>
                                                            <input wire:model.live="nominal"
                                                                type="text"
                                                                id="nominal"
                                                                class="form-control">
                                                        </td>
                                                        <td class="text-center">
                                                            <input class="form-check-input" type="checkbox" id="persenCheck" wire:model.live="persen" disabled>
                                                        </td>
                                                        <td class="text-center">
                                                            <input wire:model.live="account_id"
                                                                type="text"
                                                                id="account_id"
                                                                class="form-control">
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                        <div class="row">
                                            <div class="col">
                                                <div class="mb-3">
                                                    <label for="kode" class="form-label">
                                                        Total Pokok <span class="text-danger">*</span>
                                                    </label>

                                                    <input
                                                        wire:model.debounce.500ms="nama"
                                                        type="text"
                                                        id="nama"
                                                        class="form-control @error('nama') is-invalid @enderror">

                                                    @error('nama')
                                                    <div class="invalid-feedback d-block">
                                                        {{ $message }}
                                                    </div>
                                                    @enderror
                                                </div>
                                                <div class="mb-3">
                                                    <label for="kode" class="form-label">
                                                        Total Bagi Hasil <span class="text-danger">*</span>
                                                    </label>

                                                    <input
                                                        wire:model.debounce.500ms="nama"
                                                        type="text"
                                                        id="nama"
                                                        class="form-control @error('nama') is-invalid @enderror">

                                                    @error('nama')
                                                    <div class="invalid-feedback d-block">
                                                        {{ $message }}
                                                    </div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col">
                                                <div class="mb-3">
                                                    <label for="kode" class="form-label">
                                                        Total Biaya <span class="text-danger">*</span>
                                                    </label>

                                                    <input
                                                        wire:model.debounce.500ms="nama"
                                                        type="text"
                                                        id="nama"
                                                        class="form-control @error('nama') is-invalid @enderror">

                                                    @error('nama')
                                                    <div class="invalid-feedback d-block">
                                                        {{ $message }}
                                                    </div>
                                                    @enderror
                                                </div>
                                                <div class="mb-3">
                                                    <label for="kode" class="form-label">
                                                        Total Pembulatan <span class="text-danger">*</span>
                                                    </label>

                                                    <input
                                                        wire:model.debounce.500ms="nama"
                                                        type="text"
                                                        id="nama"
                                                        class="form-control @error('nama') is-invalid @enderror">

                                                    @error('nama')
                                                    <div class="invalid-feedback d-block">
                                                        {{ $message }}
                                                    </div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>


                            <div class="card-footer">
                                <div class="d-flex justify-content-between align-items-center">
                                    <a wire:navigate href="{{ route('superadmin.anggota') }}" class="btn btn-secondary">
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

        <!-- MODAL SAKSI -->

        <div wire:ignore.self class="modal fade" id="modalTambahSaksi" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header {{ $editSaksiIndex !== null ? 'bg-warning' : 'bg-primary' }} text-white">
                        <h5 class="modal-title">
                            {{ $editSaksiIndex !== null ? 'Edit Data Saksi' : 'Tambah Data Saksi' }}
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <form wire:submit.prevent="saveSaksi">
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label">Nama Lengkap</label>
                                <input type="text" class="form-control" wire:model="saksi_nama" placeholder="Masukkan nama...">
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Tempat Lahir</label>
                                    <input type="text" class="form-control" wire:model="saksi_tempat_lahir">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Tanggal Lahir</label>
                                    <input type="date" class="form-control" wire:model="saksi_tanggal_lahir">
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">No. KTP / SIM</label>
                                <input type="text" class="form-control" wire:model="saksi_nik">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Pekerjaan</label>
                                <input type="text" class="form-control" wire:model="saksi_pekerjaan">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Alamat</label>
                                <textarea class="form-control" wire:model="saksi_alamat" rows="3"></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" wire:click="resetSaksiForm">Batal</button>
                            <button type="submit" class="btn {{ $editSaksiIndex !== null ? 'btn-warning' : 'btn-primary' }}">
                                {{ $editSaksiIndex !== null ? 'Update List' : 'Tambahkan ke List' }}
                            </button>
                        </div>
                    </form>
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


        document.addEventListener('livewire:initialized', () => {

            // Listener untuk MEMBUKA modal
            Livewire.on('show-modal', () => {
                let modalEl = document.getElementById('modalTambahSaksi');
                // Gunakan getOrCreateInstance untuk menghindari dobel instance
                let modal = bootstrap.Modal.getOrCreateInstance(modalEl);
                modal.show();
            });

            // Listener untuk MENUTUP modal
            Livewire.on('close-modal', () => {
                let modalEl = document.getElementById('modalTambahSaksi');
                let modal = bootstrap.Modal.getInstance(modalEl);
                if (modal) {
                    modal.hide();
                }

                // Opsional: Hapus backdrop abu-abu yang kadang nyangkut di Bootstrap
                let backdrop = document.querySelector('.modal-backdrop');
                if (backdrop) {
                    backdrop.remove();
                }
                document.body.classList.remove('modal-open');
                document.body.style.removeProperty('padding-right');
                document.body.style.removeProperty('overflow');
            });

        });
    </script>
    @endscript





</div>