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
                                <!-- ===================== IDENTITAS ANGGOTA ===================== -->
                                <div class="row">
                                    <div class="col">
                                        <div class="mb-3">
                                            <label for="tanggal" class="form-label">Tanggal Lahir <span class="text-danger">*</span></label>
                                            <input wire:model.live="tanggal" type="text" id="tanggal" placeholder="dd-mm-yyyy" class="form-control @error('tanggal') is-invalid @enderror">
                                            @error('tanggal')
                                            <div class="form-text text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>


                                    <div class="col">
                                        <div class="mb-3">
                                            <label for="kantor_id" class="form-label">
                                                Kantor <span class="text-danger">*</span>
                                            </label>

                                            <select wire:model="kantor_id"
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
                                    </div>

                                </div>
                                <div class="row">
                                    <div class="col-6">
                                        <div class="mb-3">
                                            <label for="no_deposito" class="form-label">Nomor Simpanan <span class="text-danger">*</span></label>
                                            <input wire:model.debounce.500ms="no_deposito" type="text" id="no_deposito" class="form-control @error('no_deposito') is-invalid @enderror" readonly>
                                            @error('no_deposito')
                                            <div class="form-text text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col">
                                        <div class="mb-3">
                                            <label class="form-label">
                                                Nomor Anggota <span class="text-danger">*</span>
                                            </label>
                                            <div class="input-group">
                                                <input
                                                    wire:model="no_anggota"
                                                    type="text"
                                                    class="form-control"
                                                    placeholder="Klik untuk pilih anggota"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#modalAnggota"
                                                    readonly>
                                                <button class="btn btn-outline-secondary" type="button" data-bs-toggle="modal" data-bs-target="#modalAnggota">
                                                    Pilih
                                                </button>
                                            </div>

                                            @error('anggota_id')
                                            <div class="form-text text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col">
                                        <div class="mb-3">
                                            <label class="form-label">
                                                Nama <span class="text-danger">*</span>
                                            </label>

                                            <input
                                                wire:model="nama"
                                                type="text"
                                                class="form-control"
                                                readonly>

                                            @error('nama')
                                            <div class="form-text text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>


                                <div class="row">
                                    <div class="col-6">
                                        <div class="mb-3">
                                            <label for="jenis_id" class="form-label">Produk Simpanan <span class="text-danger">*</span></label>
                                            <select wire:model="jenis_id"
                                                id="jenis_id"
                                                class="form-select @error('jenis_id') is-invalid @enderror">
                                                <option value="">-- Pilih Produk --</option>
                                                @foreach ($produks as $produk)
                                                <option value="{{ $produk->id }}">
                                                    [ {{ $produk->kode }} ] {{ $produk->nama }}
                                                </option>
                                                @endforeach
                                            </select>
                                            @error('jenis_id')
                                            <div class="form-text text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-6">
                                        <div class="mb-3">
                                            <label for="jangka_waktu" class="form-label">Jangka Waktu <span class="text-danger">*</span></label>
                                            <div class="input-group mb-3">
                                                <input wire:model.live="jangka_waktu" type="text" id="jangka_waktu" class="form-control @error('jangka_waktu') is-invalid @enderror">
                                                <div class="input-group-text">
                                                    <label class="mb-0">Bulan</label>
                                                </div>
                                            </div>
                                            @error('jangka_waktu')
                                            <div class="form-text text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="mb-3">
                                            <label for="jatuh_tempo" class="form-label">Jatuh Tempo <span class="text-danger">*</span></label>
                                            <input wire:model.live="jatuh_tempo" type="text" id="jatuh_tempo" class="form-control @error('jatuh_tempo') is-invalid @enderror" readonly>
                                            @error('jatuh_tempo')
                                            <div class="form-text text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-6">
                                        <div class="mb-3">
                                            <label for="bunga" class="form-label">Bagi Hasil <span class="text-danger">*</span></label>
                                            <div class="input-group mb-3">
                                                <input wire:model.live="bunga" type="text" id="bunga" class="form-control @error('bunga') is-invalid @enderror">
                                                <div class="input-group-text">
                                                    <label class="mb-0">%</label>
                                                </div>
                                            </div>
                                            @error('bunga')
                                            <div class="form-text text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="mb-3">
                                            <label for="nominal_bagihasil" class="form-label">Nominal Bagi Hasil <span class="text-danger">*</span></label>
                                            <input wire:model.live="nominal_bagihasil" type="text" id="nominal_bagihasil" class="form-control @error('nominal_bagihasil') is-invalid @enderror">
                                            @error('nominal_bagihasil')
                                            <div class="form-text text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-6">
                                        <div class="mb-3">
                                            <label for="mengendap" class="form-label">
                                                Bagi Hasil Accrual <span class="text-danger">*</span>
                                            </label>

                                            <div class="input-group mb-3">
                                                <div class="input-group-text">
                                                    <input type="checkbox"
                                                        class="form-check-input me-2 mb-1"
                                                        wire:model.live="bunga_accrual"
                                                        id="bunga_accrual_checkbox">
                                                    <label for="bunga_accrual_checkbox" class="mb-0">Aktif</label>
                                                </div>

                                                <select wire:model="account_bungaaccrual"
                                                    id="account_bungaaccrual"
                                                    class="form-select @error('account_bungaaccrual') is-invalid @enderror"
                                                    {{ $bunga_accrual ? '' : 'disabled' }}>
                                                    <option value="">-- Pilih Account Bunga Accrual --</option>
                                                    @foreach ($bungaaccruals as $accrual)
                                                    <option value="{{ $accrual->id }}">
                                                        [ {{ $accrual->no_account }} ] {{ $accrual->nama }}
                                                    </option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            @error('account_bungaaccrual')
                                            <div class="form-text text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>

                                    </div>
                                    <div class="col-6">
                                        <div class="mb-3">
                                            <label class="form-label">
                                                Perpanjangan <span class="text-danger">*</span>
                                            </label>

                                            <div class="form-check">
                                                <input
                                                    class="form-check-input"
                                                    type="checkbox"
                                                    id="otomatisCheck"
                                                    wire:model.debounce.500ms="otomatis" value="1">
                                                <label class="form-check-label" for="otomatisCheck">
                                                    Otomatis
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col">
                                        <div class="mb-3">
                                            <label for="nominal" class="form-label">Nominal <span class="text-danger">*</span></label>
                                            <input wire:model.live="nominal" type="text" id="nominal" class="form-control @error('nominal') is-invalid @enderror">
                                            @error('nominal')
                                            <div class="form-text text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="mb-3">
                                            <label for="qq" class="form-label">QQ <span class="text-danger">*</span></label>
                                            <input wire:model.debounce.500ms="qq" type="text" id="qq" class="form-control @error('qq') is-invalid @enderror">
                                            @error('qq')
                                            <div class="form-text text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                </div>
                                <div class="row">
                                    <div class="col">
                                        <div class="mb-3">
                                            <label for="marketing_id" class="form-label">Marketing <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <input type="text" class="form-control" readonly
                                                    value="{{ $selectedMarketingKode ? '['.$selectedMarketingKode.'] '.$selectedMarketingNama : '' }}"
                                                    placeholder="Pilih Marketing" data-bs-toggle="modal" data-bs-target="#modalMarketing">
                                                <button class="btn btn-outline-secondary" type="button" data-bs-toggle="modal" data-bs-target="#modalMarketing">
                                                    Pilih
                                                </button>
                                            </div>
                                            @error('marketing_id')
                                            <div class="form-text text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="mb-3">
                                            <label class="form-label">
                                                Status <span class="text-danger">*</span>
                                            </label>

                                            <div class="form-check">
                                                <input
                                                    class="form-check-input"
                                                    type="checkbox"
                                                    id="blokirCheck"
                                                    wire:model.debounce.500ms="blokir" value="1">
                                                <label class="form-check-label" for="blokirCheck">
                                                    Blokir
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- Pembayaran Bagi Hasil, Pembayaran Jatuh Tempo Tabs -->
                                <h5 class="mb-3 mt-5">Data Pembayaran</h5>
                                <hr class="border border-danger border-2 opacity-50 mb-3">
                                <ul class="nav nav-pills nav-fill mb-5">
                                    <li class="nav-item">
                                        <button type="button"
                                            class="nav-link w-100 {{ $activeTab === 'bagi_hasil' ? 'active bg-danger text-white' : 'text-danger' }}"
                                            wire:click="setTab('bagi_hasil')">
                                            Pembayaran Bagi Hasil
                                        </button>
                                    </li>
                                    <li class="nav-item">
                                        <button type="button"
                                            class="nav-link w-100 {{ $activeTab === 'jatuh_tempo' ? 'active bg-danger text-white' : 'text-danger' }}"
                                            wire:click="setTab('jatuh_tempo')">
                                            Pembayaran Jatuh Tempo
                                        </button>
                                    </li>
                                </ul>

                                <div class="tab-content">
                                    <!-- Bagi Hasil -->
                                    <div style="{{ $activeTab === 'bagi_hasil' ? 'display:block;' : 'display:none;' }}">
                                        <div class="row">
                                            <div class="col">
                                                <div class="mb-3">
                                                    <label class="form-label d-block">
                                                        Jenis Pembayaran <span class="text-danger">*</span>
                                                    </label>


                                                    <div class="form-check mb-2">
                                                        <input class="form-check-input"
                                                            type="radio"
                                                            wire:model.live="bayar_bunga"
                                                            value="1">
                                                        <label class="form-check-label">A.R.O.</label>
                                                    </div>

                                                    <div class="form-check">
                                                        <input class="form-check-input"
                                                            type="radio"
                                                            wire:model.live="bayar_bunga"
                                                            value="2">
                                                        <label class="form-check-label">Diambil Sendiri</label>
                                                    </div>

                                                    <div class="form-check">
                                                        <input class="form-check-input"
                                                            type="radio"
                                                            wire:model.live="bayar_bunga"
                                                            value="3"
                                                            id="bayar_bunga_transfer">
                                                        <label class="form-check-label" for="bayar_bunga_transfer">Transfer Ke No Simpanan</label>
                                                    </div>

                                                </div>
                                            </div>
                                            <div class="col">
                                                <div class="mb-3">
                                                    <label for="diawal" class="form-label">Pembayaran </label>
                                                    <select wire:model.defer="diawal"
                                                        id="diawal"
                                                        class="form-select @error('diawal') is-invalid @enderror">
                                                        <option value="">-- Pilih Cara Pembayaran --</option>
                                                        @foreach ($listPembayaran as $id => $nama)
                                                        <option value="{{ $id }}">{{ $nama }}</option>
                                                        @endforeach
                                                    </select>
                                                    @error('diawal')
                                                    <div class="form-text text-danger">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">No Simpanan <span class="text-danger">*</span></label>

                                                    <div class="input-group">
                                                        <input
                                                            wire:model="no_rekening"
                                                            type="text"
                                                            class="form-control"
                                                            placeholder="Klik untuk pilih simpanan"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#modalSimpanan"
                                                            readonly
                                                            {{ $bayar_bunga == 3 ? '' : 'disabled' }}>

                                                        <button class="btn btn-outline-secondary"
                                                            type="button"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#modalSimpanan"
                                                            {{ $bayar_bunga == 3 ? '' : 'disabled' }}>
                                                            Pilih
                                                        </button>
                                                    </div>

                                                    @error('tabunganbunga_id')
                                                    <div class="form-text text-danger">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>


                                <!-- Jatuh Tempo -->
                                <div style="{{ $activeTab === 'jatuh_tempo' ? 'display:block;' : 'display:none;' }}">
                                    <div class="row">
                                        <div class="col">
                                            <div class="mb-3">
                                                <label class="form-label d-block">
                                                    Jenis Pembayaran <span class="text-danger">*</span>
                                                </label>
                                                <div class="form-check">
                                                    <input class="form-check-input"
                                                        type="radio"
                                                        wire:model.live="bayar_jatuhtempo"
                                                        value="1">
                                                    <label class="form-check-label">Diambil Sendiri</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input"
                                                        type="radio"
                                                        wire:model.live="bayar_jatuhtempo"
                                                        value="2"
                                                        id="bayar_jatuhtempo_transfer">
                                                    <label class="form-check-label" for="bayar_jatuhtempo_transfer">Transfer ke No Simpanan</label>

                                                    @error('bayar_jatuhtempo')
                                                    <div class="text-danger">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col">
                                            <div class="mb-3">
                                                <label class="form-label">No Simpanan <span class="text-danger">*</span></label>
                                                <div class="input-group">
                                                    <input type="text"
                                                        class="form-control"
                                                        placeholder="Klik untuk pilih simpanan"
                                                        readonly
                                                        wire:model="selectedNoRekeningTempo"
                                                        {{ $bayar_jatuhtempo == 2 ? '' : 'disabled' }}>
                                                    <button type="button"
                                                        class="btn btn-outline-secondary"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#modalTabunganTempo"
                                                        {{ $bayar_jatuhtempo == 2 ? '' : 'disabled' }}>
                                                        Pilih
                                                    </button>
                                                </div>
                                                @error('tabungantempo_id')
                                                <div class="form-text text-danger">{{ $message }}</div>
                                                @enderror
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
        <!-- StartModal -->
        <div wire:ignore.self class="modal fade" id="modalAnggota">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">

                    <div class="modal-header">
                        <h5 class="modal-title">Pilih Anggota</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">
                        <!-- Search -->
                        <div class="mb-3">
                            <input type="text" class="form-control" placeholder="Cari anggota..." wire:model.live="searchAnggota">
                        </div>

                        <!-- Tabel -->
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>No Anggota</th>
                                    <th>Nama</th>
                                    <th>Alamat</th>
                                    <th>Kantor</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($anggotas ?? [] as $anggota)
                                <tr>
                                    <td>{{ $anggota->no_anggota }}</td>
                                    <td>{{ $anggota->nama }}</td>
                                    <td>{{ $anggota->alamat }}</td>
                                    <td>{{ $anggota->kantor->nama_kantor ?? '-' }}</td>
                                    <td>
                                        <button class="btn btn-sm btn-primary"
                                            wire:click="pilihAnggota('{{ $anggota->id }}','{{ $anggota->no_anggota }}','{{ $anggota->nama }}')"
                                            data-bs-dismiss="modal">
                                            Pilih
                                        </button>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center">Tidak ada data</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                        <!-- Pagination -->
                        <div class="mt-2">
                            {{ $anggotas?->links() }}
                        </div>
                    </div>

                </div>
            </div>
        </div>


        <!-- Modal Marketing -->
        <div class="modal fade" id="modalMarketing" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Pilih Marketing</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="text" class="form-control mb-2" placeholder="Cari Marketing..."
                            wire:model.debounce.500ms="searchMarketing">

                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Kode</th>
                                    <th>Nama</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($marketings as $marketing)
                                <tr>
                                    <td>{{ $marketing->kode }}</td>
                                    <td>{{ $marketing->nama }}</td>
                                    <td>
                                        <button class="btn btn-sm btn-primary"
                                            wire:click="pilihMarketing('{{ $marketing->id }}', '{{ $marketing->kode }}', '{{ $marketing->nama }}')"
                                            data-bs-dismiss="modal">
                                            Pilih
                                        </button>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="3" class="text-center">Tidak ada data</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Simpanan -->
        <div wire:ignore.self class="modal fade" id="modalSimpanan" tabindex="-1">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Pilih Simpanan</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">
                        <div class="row mb-3">

                            <!-- Search -->
                            <div class="col-md-12 mb-3">
                                <input type="text"
                                    wire:model.live="searchSimpanan"
                                    class="form-control"
                                    placeholder="Cari No Simpanan / Nama Anggota">
                            </div>

                            <!-- Filter Jenis Simpanan -->
                            <div class="col-md-12 mb-2">
                                <label class="form-label">Filter Jenis Simpanan:</label>
                                <div class="d-flex flex-wrap gap-2">
                                    @php
                                    $jenisOptions = [
                                    '' => 'Semua',
                                    '1' => 'Simpanan Pokok',
                                    '2' => 'Simpanan Wajib',
                                    '3' => 'Simpanan Sukarela',
                                    '4' => 'Simpanan Wajib Pinjaman',
                                    '5' => 'Saham',
                                    '6' => 'Simpanan Pokok Pinjaman',
                                    '7' => 'Simpanan Rencana',
                                    ];
                                    @endphp

                                    @foreach($jenisOptions as $key => $label)
                                    <div class="form-check">
                                        <input class="form-check-input"
                                            type="radio"
                                            wire:model.live="jenisFilter"
                                            name="jenisFilter"
                                            value="{{ $key }}"
                                            id="jenisFilter{{ $loop->index }}">
                                        <label class="form-check-label" for="jenisFilter{{ $loop->index }}">
                                            {{ $label }}
                                        </label>
                                    </div>
                                    @endforeach
                                </div>
                            </div>


                        </div>

                        <!-- Tabel Simpanan -->
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>No Simpanan</th>
                                    <th>Produk</th>
                                    <th>Jenis</th>
                                    <th>No Anggota</th>
                                    <th>Nama Anggota</th>
                                    <th>Alamat</th>
                                    <th width="100">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($simpananList as $simpanan)
                                <tr>
                                    <td>{{ $simpanan->no_rekening }}</td>
                                    <td>{{ $simpanan->produk->nama ?? '-' }}</td>
                                    <td>{{ $simpanan->jenis->nama ?? '-' }}</td>
                                    <td>{{ $simpanan->anggota->no_anggota }}</td>
                                    <td>{{ $simpanan->anggota->nama }}</td>
                                    <td>{{ $simpanan->anggota->alamat }}</td>
                                    <td>
                                        <button class="btn btn-sm btn-primary"
                                            wire:click="selectSimpanan({{ $simpanan->id }})"
                                            data-bs-dismiss="modal">
                                            Pilih
                                        </button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>

                        {{ $simpananList->links() }}

                    </div>
                </div>
            </div>
        </div>

        <!-- Akhir Moadal Simpanan -->
        <!-- Modal Tempo -->

        <div wire:ignore.self class="modal fade" id="modalTabunganTempo" tabindex="-1">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">

                    <div class="modal-header">
                        <h5 class="modal-title">Pilih Tabungan Tempo</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">

                        <!-- Filter Jenis Simpanan -->
                        <div class="row mb-3">
                            <!-- Search -->
                            <div class="col-md-12 mb-3">
                                <input type="text"
                                    wire:model.live="searchTempo"
                                    class="form-control"
                                    placeholder="Cari No Simpanan / Nama Anggota">
                            </div>
                            <div class="col-md-12 mb-2">
                                <label class="form-label">Filter Jenis Simpanan:</label>
                                <div class="d-flex flex-wrap gap-2">
                                    @php
                                    $jenisOptions = [
                                    '' => 'Semua',
                                    'Simpanan Pokok' => 'Simpanan Pokok',
                                    'Simpanan Wajib' => 'Simpanan Wajib',
                                    'Simpanan Sukarela' => 'Simpanan Sukarela',
                                    'Simpanan Wajib Pinjaman' => 'Simpanan Wajib Pinjaman',
                                    'Saham' => 'Saham',
                                    'Simpanan Pokok Pinjaman' => 'Simpanan Pokok Pinjaman',
                                    'Simpanan Rencana' => 'Simpanan Rencana',
                                    ];
                                    @endphp

                                    @foreach($jenisOptions as $key => $label)
                                    <div class="form-check">
                                        <input class="form-check-input"
                                            type="radio"
                                            wire:model.live="jenisTempoFilter"
                                            name="jenisTempoFilter"
                                            value="{{ $key }}"
                                            id="jenisTempoFilter{{ $loop->index }}">
                                        <label class="form-check-label" for="jenisTempoFilter{{ $loop->index }}">
                                            {{ $label }}
                                        </label>
                                    </div>
                                    @endforeach
                                </div>
                            </div>


                        </div>

                        <!-- Tabel data -->
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>No Simpanan</th>
                                    <th>Produk</th>
                                    <th>Jenis</th>
                                    <th>No Anggota</th>
                                    <th>Nama Anggota</th>
                                    <th>Alamat</th>
                                    <th width="100">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($tabunganTempoList as $tempo)
                                <tr>
                                    <td>{{ $tempo->no_rekening }}</td>
                                    <td>{{ $tempo->produk->nama ?? '-' }}</td>
                                    <td>{{ $tempo->jenis->nama ?? '-' }}</td>
                                    <td>{{ $tempo->anggota->no_anggota }}</td>
                                    <td>{{ $tempo->anggota->nama }}</td>
                                    <td>{{ $tempo->anggota->alamat }}</td>
                                    <td>
                                        <button class="btn btn-sm btn-primary"
                                            wire:click="selectTabunganTempo({{ $tempo->id }})"
                                            data-bs-dismiss="modal">
                                            Pilih
                                        </button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>

                        {{ $tabunganTempoList->links() }}

                    </div>
                </div>
            </div>
        </div>
        <!-- Akhir Modal Tempo -->
        <!-- EndModal -->

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
                    selector: "#tanggal",
                    event: "updateTanggal"
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