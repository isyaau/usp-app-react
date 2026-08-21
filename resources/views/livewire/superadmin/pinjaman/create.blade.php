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
                                <h5 class="mb-3">Data Status</h5>
                                <hr class="border border-danger border-2 opacity-50 mb-3">
                                <div class="row">
                                    <div class="col">
                                        <div class="mb-3">
                                            <label class="form-label">Pencairan ke Simpanan <span class="text-danger">*</span></label>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="cairSimpananCheck" wire:model.live="cair_simpanan" checked>
                                                <label class="form-check-label" for="cairSimpananCheck">Aktif</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="mb-3">
                                            <label class="form-label">SMS <span class="text-danger">*</span></label>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="smsCheck" wire:model.live="sms" checked>
                                                <label class="form-check-label" for="smsCheck">Aktif</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col">
                                        <div class="mb-3">
                                            <label class="form-label">Rekening Koran <span class="text-danger">*</span></label>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="rekeningKoranCheck" wire:model.live="rekening_koran" checked>
                                                <label class="form-check-label" for="rekeningKoranCheck">Aktif</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="mb-3">
                                            <label class="form-label">Status <span class="text-danger">*</span></label>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="statusCheck" wire:model.live="aktif" checked>
                                                <label class="form-check-label" for="statusCheck">Aktif</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <h5 class="mb-3">Data Pinjaman</h5>
                                <hr class="border border-danger border-2 opacity-50 mb-3">
                                <ul class="nav nav-pills nav-fill mb-5">
                                    <li class="nav-item">
                                        <button type="button"
                                            class="nav-link w-100 {{ $activeTab === 'pinjaman' ? 'active bg-danger text-white' : 'text-danger' }}"
                                            wire:click="setTab('pinjaman')">
                                            Pinjaman
                                        </button>
                                    </li>
                                    <li class="nav-item">
                                        <button type="button"
                                            class="nav-link w-100 {{ $activeTab === 'biaya' ? 'active bg-danger text-white' : 'text-danger' }}"
                                            wire:click="setTab('biaya')">
                                            Biaya
                                        </button>
                                    </li>
                                    <li class="nav-item">
                                        <button type="button"
                                            class="nav-link w-100 {{ $activeTab === 'jaminan' ? 'active bg-danger text-white' : 'text-danger' }}"
                                            wire:click="setTab('jaminan')">
                                            Jaminan
                                        </button>
                                    </li>
                                    <li class="nav-item">
                                        <button type="button"
                                            class="nav-link w-100 {{ $activeTab === 'saksi' ? 'active bg-danger text-white' : 'text-danger' }}"
                                            wire:click="setTab('saksi')">
                                            Saksi
                                        </button>
                                    </li>
                                    <li class="nav-item">
                                        <button type="button"
                                            class="nav-link w-100 {{ $activeTab === 'surat' ? 'active bg-danger text-white' : 'text-danger' }}"
                                            wire:click="setTab('surat')">
                                            Surat
                                        </button>
                                    </li>
                                    <li class="nav-item">
                                        <button type="button"
                                            class="nav-link w-100 {{ $activeTab === 'penjamin' ? 'active bg-danger text-white' : 'text-danger' }}"
                                            wire:click="setTab('penjamin')">
                                            Penjamin
                                        </button>
                                    </li>
                                </ul>

                                <div class="tab-content">
                                    <!-- Pinjaman -->
                                    <div style="{{ $activeTab === 'pinjaman' ? 'display:block;' : 'display:none;' }}">
                                        <div class="row">
                                            <div class="col">
                                                <div class="mb-3">
                                                    <label for="tanggal" class="form-label">Tanggal </label>
                                                    <input wire:model.live="tanggal"
                                                        type="text"
                                                        id="tanggal"
                                                        class="form-control">
                                                </div>
                                            </div>
                                            <div class="col">
                                                <div class="mb-3">
                                                    <label for="kantor_id" class="form-label">Kantor </label>
                                                    <input wire:model.live="kantor_id"
                                                        type="text"
                                                        id="kantor_id"
                                                        class="form-control">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col">
                                                <div class="mb-3">
                                                    <label for="no_pinjaman" class="form-label">Nomor Pinjaman </label>
                                                    <input wire:model.live="no_pinjaman"
                                                        type="text"
                                                        id="tanggal"
                                                        class="form-control">
                                                </div>
                                            </div>
                                            <div class="col">
                                                <div class="mb-3">
                                                    <label for="proposal_id" class="form-label">Nomor Proposal </label>
                                                    <input wire:model.live="proposal_id"
                                                        type="text"
                                                        id="proposal_id"
                                                        class="form-control">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col">
                                                <div class="mb-3">
                                                    <label for="anggota_id" class="form-label">Nomor Anggota </label>
                                                    <input wire:model.live="anggota_id"
                                                        type="text"
                                                        id="anggota_id"
                                                        class="form-control">
                                                </div>
                                            </div>
                                            <div class="col">
                                                <div class="mb-3">
                                                    <label for="nama_anggota" class="form-label">Nama Anggota </label>
                                                    <input wire:model.live="nama_anggota"
                                                        type="text"
                                                        id="nama_anggota"
                                                        class="form-control">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col">
                                                <div class="mb-3">
                                                    <label for="jenis_id" class="form-label">Produk Pinjaman </label>
                                                    <input wire:model.live="jenis_id"
                                                        type="text"
                                                        id="jenis_id"
                                                        class="form-control">
                                                </div>
                                            </div>
                                            <div class="col">
                                                <div class="mb-3">
                                                    <label for="jaminan_id" class="form-label">Jaminan </label>
                                                    <input wire:model.live="jaminan_id"
                                                        type="text"
                                                        id="jaminan_id"
                                                        class="form-control">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col">
                                                <div class="mb-3">
                                                    <label for="angsuran" class="form-label">Jenis Angsuran </label>
                                                    <input wire:model.live="angsuran"
                                                        type="text"
                                                        id="angsuran"
                                                        class="form-control">
                                                </div>
                                            </div>
                                            <div class="col">
                                                <div class="mb-3">
                                                    <label for="swp_id" class="form-label">SWP </label>
                                                    <input wire:model.live="swp_id"
                                                        type="text"
                                                        id="swp_id"
                                                        class="form-control">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col">
                                                <div class="mb-3">
                                                    <label for="plafon" class="form-label">Plafon </label>
                                                    <input wire:model.live="plafon"
                                                        type="text"
                                                        id="plafon"
                                                        class="form-control">
                                                </div>
                                            </div>
                                            <div class="col">
                                                <div class="mb-3">
                                                    <label for="spp_id" class="form-label">SPP </label>
                                                    <input wire:model.live="spp_id"
                                                        type="text"
                                                        id="spp_id"
                                                        class="form-control">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col">
                                                <div class="mb-3">
                                                    <label for="bunga" class="form-label">Bagi Hasil/Tahun </label>
                                                    <input wire:model.live="bunga"
                                                        type="text"
                                                        id="bunga"
                                                        class="form-control">
                                                </div>
                                            </div>
                                            <div class="col">
                                                <div class="mb-3">
                                                    <label for="jangka_waktu" class="form-label">Jangka Waktu </label>
                                                    <input wire:model.live="jangka_waktu"
                                                        type="text"
                                                        id="jangka_waktu"
                                                        class="form-control">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col">
                                                <div class="mb-3">
                                                    <label for="periode" class="form-label">Biaya Pokok Per </label>
                                                    <input wire:model.live="periode"
                                                        type="text"
                                                        id="periode"
                                                        class="form-control">
                                                </div>
                                            </div>
                                            <div class="col">
                                                <div class="mb-3">
                                                    <label for="pembayaran" class="form-label">Jenis Pembayaran </label>
                                                    <input wire:model.live="pembayaran"
                                                        type="text"
                                                        id="pembayaran"
                                                        class="form-control">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col">
                                                <div class="mb-3">
                                                    <label for="angsuran" class="form-label">Angsuran </label>
                                                    <input wire:model.live="angsuran"
                                                        type="text"
                                                        id="angsuran"
                                                        class="form-control">
                                                </div>
                                            </div>
                                            <div class="col">
                                                <div class="mb-3">
                                                    <label for="marketing_id" class="form-label">Marketing </label>
                                                    <input wire:model.live="marketing_id"
                                                        type="text"
                                                        id="marketing_id"
                                                        class="form-control">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col">
                                                <div class="mb-3">
                                                    <label for="sektor_id" class="form-label">Sektor </label>
                                                    <input wire:model.live="sektor_id"
                                                        type="text"
                                                        id="sektor_id"
                                                        class="form-control">
                                                </div>
                                            </div>
                                            <div class="col">

                                            </div>
                                        </div>

                                        <h5 class="mb-3">Pembayaran Angsuran</h5>
                                        <hr class="border border-danger border-2 opacity-50 mb-3">
                                        <div class="row">
                                            <div class="col">
                                                <div class="mb-3">
                                                    <label class="form-label d-block">Jenis Angsuran <span class="text-danger">*</span></label>
                                                    <div class="form-check form-check-inline">
                                                        <input class="form-check-input" type="radio" id="manual" name="manual" wire:model="manual" value="1">
                                                        <label class="form-check-label" for="manual">Manual</label>
                                                    </div>
                                                    <div class="form-check form-check-inline">
                                                        <input class="form-check-input" type="radio" id="otomatis" name="manual" wire:model="manual" value="0">
                                                        <label class="form-check-label" for="otomatis">Otomatis</label>
                                                    </div>
                                                    @error('manual')
                                                    <div class="form-text text-danger">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col">
                                                <div class="mb-3">
                                                    <label for="tabungan_id" class="form-label">No Simpanan </label>
                                                    <input wire:model.live="tabungan_id"
                                                        type="text"
                                                        id="tabungan_id"
                                                        class="form-control">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col">
                                                <div class="mb-3">
                                                    <label for="kode_id" class="form-label">Kode Tarikan </label>
                                                    <input wire:model.live="kode_id"
                                                        type="text"
                                                        id="kode_id"
                                                        class="form-control">
                                                </div>
                                            </div>
                                            <div class="col">
                                                <div class="mb-3">
                                                    <label for="kode_koreksi" class="form-label">Kode Debet </label>
                                                    <input wire:model.live="kode_koreksi"
                                                        type="text"
                                                        id="kode_koreksi"
                                                        class="form-control">
                                                </div>
                                            </div>
                                        </div>

                                    </div>

                                    <!-- Biaya -->
                                    <div style="{{ $activeTab === 'biaya' ? 'display:block;' : 'display:none;' }}">
                                        <div class="table-responsive">
                                            <table class="table table-hover table-bordered align-middle">
                                                <thead class="table-danger text-center">
                                                    <tr>
                                                        <th style="width: 50px;"></th>
                                                        <th>Komponen Pinjaman</th>
                                                        <th style="width: 200px">Nominal</th>
                                                        <th style="width: 50px;">%</th>
                                                        <th style="width: 200px">No. Account</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
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
                                    </div>

                                    <!-- Jaminan -->
                                    <div style="{{ $activeTab === 'jaminan' ? 'display:block;' : 'display:none;' }}">
                                        <div class="col-2">
                                            <div class="mb-3">
                                                <label for="bunga" class="form-label">Total </label>
                                                <input wire:model.live="total"
                                                    type="text"
                                                    id="total"
                                                    class="form-control">
                                            </div>
                                        </div>
                                        <div class="table-responsive">
                                            <table class="table table-hover table-bordered align-middle">
                                                <thead class="table-danger text-center">
                                                    <tr>
                                                        <th style="width: 50px;"></th>
                                                        <th>Detail Jaminan</th>
                                                        <th>Keterangan</th>
                                                        <th style="width: 300px;">Nilai Jaminan</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td class="text-center">
                                                        </td>
                                                        <td>
                                                            <input wire:model.live="nama"
                                                                type="text"
                                                                id="nama"
                                                                class="form-control">
                                                        </td>
                                                        <td>
                                                            <input wire:model.live="keterangan"
                                                                type="text"
                                                                id="keterangan"
                                                                class="form-control">
                                                        </td>
                                                        <td class="text-center">
                                                            <input wire:model.live="nominal"
                                                                type="text"
                                                                id="nominal"
                                                                class="form-control">
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>

                                    <!-- Saksi -->
                                    <div style="{{ $activeTab === 'saksi' ? 'display:block;' : 'display:none;' }}">
                                        <div class="table-responsive">
                                            <table class="table table-hover table-bordered align-middle">
                                                <thead class="table-danger text-center">
                                                    <tr>
                                                        <th style="width: 50px;">Urutan</th>
                                                        <th>Nama</th>
                                                        <th>Pekerjaan</th>
                                                        <th style="width: 180px;">
                                                            <button type="button" class="btn btn-sm btn-primary" wire:click="createSaksi">
                                                                <i class="fas fa-plus"></i> Tambah Saksi
                                                            </button>
                                                        </th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @forelse($listSaksiTemp as $index => $saksi)
                                                    <tr>
                                                        <td class="text-center">
                                                            <div class="btn-group-vertical shadow-sm">
                                                                <button type="button" wire:click="moveUpSaksi({{ $index }})" class="btn btn-sm btn-light border py-0"><i class="fas fa-chevron-up"></i></button>
                                                                <button type="button" wire:click="moveDownSaksi({{ $index }})" class="btn btn-sm btn-light border py-0"><i class="fas fa-chevron-down"></i></button>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <strong>{{ $saksi['nama'] }}</strong><br>
                                                            <small class="text-muted">{{ $saksi['nik'] }}</small>
                                                        </td>
                                                        <td>{{ $saksi['pekerjaan'] }}</td>
                                                        <td class="text-center">
                                                            <button type="button" class="btn btn-sm btn-warning" wire:click="editSaksi({{ $index }})">
                                                                <i class="fas fa-edit"></i>
                                                            </button>

                                                            <button type="button" class="btn btn-sm btn-danger" wire:click="removeSaksi({{ $index }})">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </td>
                                                    </tr>
                                                    @empty
                                                    <tr>
                                                        <td colspan="4" class="text-center py-3 text-muted">Belum ada saksi ditambahkan.</td>
                                                    </tr>
                                                    @endforelse
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    <!-- Surat -->
                                    <div style="{{ $activeTab === 'surat' ? 'display:block;' : 'display:none;' }}">
                                        <div class="table-responsive">
                                            <table class="table table-hover table-bordered align-middle">
                                                <thead class="table-danger text-center">
                                                    <tr>
                                                        <th style="width: 50px;"></th>
                                                        <th style="width: 200px">Surat Pinjaman</th>
                                                        <th style="width: 50px">
                                                            <button type="button" class="btn btn-sm btn-primary" wire:click="createSuratPinjaman">
                                                                <i class="fas fa-plus"></i> Tambah Surat
                                                            </button>
                                                        </th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td class="text-center">
                                                        </td>
                                                        <td>
                                                        </td>
                                                        <td>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>

                                    <!-- Penjamin -->
                                    <div style="{{ $activeTab === 'penjamin' ? 'display:block;' : 'display:none;' }}">
                                        <div class="table-responsive">
                                            <table class="table table-hover table-bordered align-middle">
                                                <thead class="table-danger text-center">
                                                    <tr>
                                                        <th style="width: 50px;"></th>
                                                        <th style="width: 200px">Nama</th>
                                                        <th style="width: 200px">Hubungan</th>
                                                        <th style="width: 200px">Telepon</th>
                                                        <th style="width: 200px">Tampil</th>
                                                         <th style="width: 50px">
                                                            <button type="button" class="btn btn-sm btn-primary" wire:click="createPenjamin">
                                                                <i class="fas fa-plus"></i> Tambah
                                                            </button>
                                                        </th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td class="text-center">
                                                        </td>
                                                        <td>
                                                        </td>
                                                        <td>
                                                        </td>
                                                        <td>
                                                        </td>
                                                        <td>
                                                        </td>
                                                        <td>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
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