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
                                <h5 class="mb-3">Produk</h5>
                                <hr class="border border-danger border-2 opacity-50 mb-5">
                                <div class="row">
                                    <div class="col">
                                        <div class="mb-3">
                                            <label for="kode" class="form-label">Kode <span class="text-danger">*</span></label>
                                            <input wire:model.debounce.500ms="kode" type="text" id="kode" class="form-control @error('kode') is-invalid @enderror">
                                            @error('kode')
                                            <div class="form-text text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col">
                                        <div class="mb-3">
                                            <label for="nama" class="form-label">Nama <span class="text-danger">*</span></label>
                                            <input wire:model.debounce.500ms="nama" type="text" id="nama" class="form-control @error('nama') is-invalid @enderror">
                                            @error('nama')
                                            <div class="form-text text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                </div>

                                <div class="row">
                                    <div class="col-6">
                                        <div class="mb-3">
                                            <label for="account_id" class="form-label">
                                                No Account <span class="text-danger">*</span>
                                            </label>

                                            <select wire:model="account_id"
                                                id="account_id"
                                                class="form-select @error('account_id') is-invalid @enderror">
                                                <option value="">-- Pilih Account --</option>
                                                @foreach ($accounts as $account)
                                                <option value="{{ $account->id }}">
                                                    [ {{ $account->no_account }} ] {{ $account->nama }}
                                                </option>
                                                @endforeach
                                            </select>

                                            @error('account_id')
                                            <div class="form-text text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                </div>

                                <div class="row">
                                    <div class="col">
                                        <div class="mb-3">
                                            <label for="minimum" class="form-label">Saldo Minimum <span class="text-danger">*</span></label>
                                            <input wire:model.debounce.500ms="minimum" type="text" id="minimum" class="form-control @error('minimum') is-invalid @enderror">
                                            @error('minimum')
                                            <div class="form-text text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="mb-3">
                                            <label for="mengendap" class="form-label">Mengendap <span class="text-danger">*</span></label>
                                            <div class="input-group mb-3">
                                                <input wire:model.debounce.500ms="mengendap" type="text" class="form-control @error('mengendap') is-invalid @enderror" id="mengendap" aria-label="Recipient’s username" aria-describedby="basic-addon2">
                                                <span class="input-group-text" id="basic-addon2">Bulan</span>
                                            </div>
                                            @error('mengendap')
                                            <div class="form-text text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <!-- ===================== BAGI HASIL ===================== -->
                                <h5 class="mb-3 mt-5">Bagi Hasil</h5>
                                <hr class="border border-danger border-2 opacity-50 mb-5">
                                <div>

                                    {{-- KODE --}}
                                    <div class="row">
                                        <div class="col-6">
                                            <div class="mb-3">
                                                <label class="form-label">
                                                    Kode <span class="text-danger">*</span>
                                                </label>

                                                <select wire:model="bunga_id"
                                                    class="form-select @error('bunga_id') is-invalid @enderror">
                                                    <option value="">-- Pilih Kode --</option>
                                                    @foreach ($bungas as $bunga)
                                                    <option value="{{ $bunga->id }}">
                                                        [{{ $bunga->kode }}] {{ $bunga->nama }}
                                                    </option>
                                                    @endforeach
                                                </select>

                                                @error('bunga_id')
                                                <div class="text-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    {{-- JENIS BAGI HASIL --}}
                                    <div class="row">
                                        <div class="col-6">
                                            <div class="mb-3">
                                                <label class="form-label d-block">
                                                    Bagi Hasil / Tahun <span class="text-danger">*</span>
                                                </label>

                                                {{-- FLAT --}}
                                                <div class="form-check mb-2">
                                                    <input class="form-check-input"
                                                        type="radio"
                                                        wire:model.live="jenis_bunga"
                                                        value="1">
                                                    <label class="form-check-label">Flat</label>

                                                    <div class="input-group mt-2">
                                                        <input type="text"
                                                            wire:model.debounce.500ms="bungaJenis"
                                                            class="form-control"
                                                            @if($jenis_bunga !=1) readonly style="background:#e9ecef; pointer-events:none;" @endif>

                                                        <span class="input-group-text">%</span>
                                                    </div>

                                                    @error('bunga')
                                                    <div class="text-danger">{{ $message }}</div>
                                                    @enderror
                                                </div>

                                                {{-- BERTINGKAT --}}
                                                <div class="form-check">
                                                    <input class="form-check-input"
                                                        type="radio"
                                                        wire:model.live="jenis_bunga"
                                                        value="2">
                                                    <label class="form-check-label">Bertingkat</label>
                                                </div>

                                                @error('jenis_bunga')
                                                <div class="text-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    {{-- TABEL BERTINGKAT --}}
                                    <table class="table table-bordered table-hover">
                                        <thead class="table-danger text-center">
                                            <tr>
                                                <th style="width:50px">No</th>
                                                <th>Minimal</th>
                                                <th>Maksimal</th>
                                                <th style="width:150px">Bagi Hasil (%)</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($tingkat as $i => $row)
                                            <tr>
                                                <td class="text-center">{{ $i + 1 }}</td>

                                                <td>
                                                    <input type="text"
                                                        class="form-control"
                                                        wire:model.live="tingkat.{{ $i }}.minimal"
                                                        @if($jenis_bunga !=2) readonly style="background:#e9ecef; pointer-events:none;" @endif>

                                                </td>

                                                <td>
                                                    <input type="text"
                                                        class="form-control"
                                                        wire:model.live="tingkat.{{ $i }}.maksimal"
                                                        @disabled($jenis_bunga !=2)>
                                                </td>

                                                <td>
                                                    <input type="text"
                                                        class="form-control"
                                                        wire:model.live="tingkat.{{ $i }}.bunga"
                                                        @disabled($jenis_bunga !=2)>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>

                                </div>

                                <div class="row">
                                    <div class="col-6">
                                        <div class="mb-3">
                                            <label for="account_bunga" class="form-label">
                                                No Account <span class="text-danger">*</span>
                                            </label>

                                            <select wire:model="account_bunga"
                                                id="account_bunga"
                                                class="form-select @error('account_bunga') is-invalid @enderror">
                                                <option value="">-- Pilih Account --</option>
                                                @foreach ($accounts as $account)
                                                <option value="{{ $account->id }}">
                                                    [ {{ $account->no_account }} ] {{ $account->nama }}
                                                </option>
                                                @endforeach
                                            </select>

                                            @error('account_bunga')
                                            <div class="form-text text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                </div>

                                <div class="row">
                                    <div class="col">
                                        <div class="mb-3">
                                            <label class="form-label d-block">
                                                Rumus <span class="text-danger">*</span>
                                            </label>

                                            {{-- SALDO TERENDAH --}}
                                            <div class="form-check">
                                                <input class="form-check-input"
                                                    type="radio"
                                                    name="rumus_bunga"
                                                    id="rumus_terendah"
                                                    wire:model.live="rumus_bunga"
                                                    value="1">

                                                <label class="form-check-label" for="rumus_terendah">
                                                    Saldo Terendah
                                                </label>
                                            </div>

                                            {{-- Checkbox hanya aktif jika saldo terendah --}}
                                            <div class="form-check ms-4 mt-1">
                                                <input class="form-check-input"
                                                    type="checkbox"
                                                    id="rumus_1_bulan"
                                                    wire:model="rumus_satu_bulan"
                                                    @disabled($rumus_bunga !=1)>
                                                <label class="form-check-label" for="rumus_1_bulan">
                                                    1 Bulan
                                                </label>
                                            </div>



                                            {{-- SALDO HARIAN --}}
                                            <div class="form-check mt-2">
                                                <input class="form-check-input"
                                                    type="radio"
                                                    name="rumus_bunga"
                                                    id="rumus_harian"
                                                    wire:model.live="rumus_bunga"
                                                    value="2">

                                                <label class="form-check-label" for="rumus_harian">
                                                    Saldo Harian
                                                </label>
                                            </div>

                                            {{-- SALDO RATA-RATA --}}
                                            <div class="form-check">
                                                <input class="form-check-input"
                                                    type="radio"
                                                    name="rumus_bunga"
                                                    id="rumus_rata"
                                                    wire:model.live="rumus_bunga"
                                                    value="3">

                                                <label class="form-check-label" for="rumus_rata">
                                                    Saldo Rata-rata
                                                </label>
                                            </div>

                                            @error('rumus_bunga')
                                            <div class="form-text text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <!-- ===================== ADMINISTRASI BIAYA ===================== -->
                                <h5 class="mb-3 mt-5">Administrasi Biaya</h5>
                                <hr class="border border-danger border-2 opacity-50 mb-5">
                                <div class="row">
                                    <div class="col-6">
                                        <div class="mb-3">
                                            <label class="form-label">
                                                Kode <span class="text-danger">*</span>
                                            </label>

                                            <select wire:model="biaya_id"
                                                class="form-select @error('biaya_id') is-invalid @enderror">
                                                <option value="">-- Pilih Kode --</option>
                                                @foreach ($biayas as $biaya)
                                                <option value="{{ $biaya->id }}">
                                                    [{{ $biaya->kode }}] {{ $biaya->nama }}
                                                </option>
                                                @endforeach
                                            </select>

                                            @error('biaya_id')
                                            <div class="text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col">
                                        <div class="mb-3">
                                            <label for="biaya" class="form-label">Biaya Administrasi <span class="text-danger">*</span></label>
                                            <input wire:model.debounce.500ms="biaya" type="text" id="biaya" class="form-control @error('biaya') is-invalid @enderror">
                                            @error('biaya')
                                            <div class="form-text text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col">
                                        <div class="mb-3">
                                            <label for="account_biaya" class="form-label">
                                                No Account <span class="text-danger">*</span>
                                            </label>

                                            <select wire:model="account_biaya"
                                                id="account_biaya"
                                                class="form-select @error('account_biaya') is-invalid @enderror">
                                                <option value="">-- Pilih Account --</option>
                                                @foreach ($accounts as $account)
                                                <option value="{{ $account->id }}">
                                                    [ {{ $account->no_account }} ] {{ $account->nama }}
                                                </option>
                                                @endforeach
                                            </select>

                                            @error('account_biaya')
                                            <div class="form-text text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                </div>


                                <!-- ===================== PAJAK ===================== -->
                                <h5 class="mb-3 mt-5">Pajak</h5>
                                <hr class="border border-danger border-2 opacity-50 mb-5">
                                <div class="row">
                                    <div class="col-6">
                                        <div class="mb-3">
                                            <label class="form-label">
                                                Kode <span class="text-danger">*</span>
                                            </label>

                                            <select wire:model="pajak_id"
                                                class="form-select @error('pajak_id') is-invalid @enderror">
                                                <option value="">-- Pilih Kode --</option>
                                                @foreach ($pajaks as $pajak)
                                                <option value="{{ $pajak->id }}">
                                                    [{{ $pajak->kode }}] {{ $pajak->nama }}
                                                </option>
                                                @endforeach
                                            </select>

                                            @error('pajak_id')
                                            <div class="text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="mb-3">
                                            <label for="pajak" class="form-label">Pajak <span class="text-danger">*</span></label>
                                            <div class="input-group mb-3">
                                                <input wire:model.debounce.500ms="pajak" type="text" class="form-control @error('pajak') is-invalid @enderror" id="pajak" aria-label="Recipient’s username" aria-describedby="basic-addon2">
                                                <span class="input-group-text" id="basic-addon2">%</span>
                                            </div>
                                            @error('pajak')
                                            <div class="form-text text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col">
                                        <div class="mb-3">
                                            <label for="account_pajak" class="form-label">
                                                No Account <span class="text-danger">*</span>
                                            </label>

                                            <select wire:model="account_pajak"
                                                id="account_pajak"
                                                class="form-select @error('account_pajak') is-invalid @enderror">
                                                <option value="">-- Pilih Account --</option>
                                                @foreach ($accounts as $account)
                                                <option value="{{ $account->id }}">
                                                    [ {{ $account->no_account }} ] {{ $account->nama }}
                                                </option>
                                                @endforeach
                                            </select>

                                            @error('account_pajak')
                                            <div class="form-text text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col">
                                        <div class="mb-3">
                                            <label for="saldo_pajak" class="form-label">Saldo Minimum <span class="text-danger">*</span></label>
                                            <input wire:model.debounce.500ms="saldo_pajak" type="text" id="saldo_pajak" class="form-control @error('saldo_pajak') is-invalid @enderror">
                                            @error('saldo_pajak')
                                            <div class="form-text text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                </div>


                                <!-- ===================== JENIS ===================== -->
                                <h5 class="mb-3 mt-5">Jenis</h5>
                                <hr class="border border-danger border-2 opacity-50 mb-5">
                                <div>
                                    {{-- Simpanan --}}
                                    <div class="row">
                                        <div class="col-6">
                                            <div class="mb-3">
                                                <label class="form-label d-block">
                                                    Simpanan <span class="text-danger">*</span>
                                                </label>


                                                <div class="form-check mb-2">
                                                    <input class="form-check-input"
                                                        type="radio"
                                                        wire:model.live="jenis"
                                                        value="1">
                                                    <label class="form-check-label">Pokok</label>
                                                </div>

                                                <div class="form-check">
                                                    <input class="form-check-input"
                                                        type="radio"
                                                        wire:model.live="jenis"
                                                        value="2">
                                                    <label class="form-check-label">Wajib</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input"
                                                        type="radio"
                                                        wire:model.live="jenis"
                                                        value="3">
                                                    <label class="form-check-label">Sukarela</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input"
                                                        type="radio"
                                                        wire:model.live="jenis"
                                                        value="4">
                                                    <label class="form-check-label">Wajib Pinjaman</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input"
                                                        type="radio"
                                                        wire:model.live="jenis"
                                                        value="5">
                                                    <label class="form-check-label">Saham</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input"
                                                        type="radio"
                                                        wire:model.live="jenis"
                                                        value="6">
                                                    <label class="form-check-label">Pokok Pinjaman</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input"
                                                        type="radio"
                                                        wire:model.live="jenis"
                                                        value="7">
                                                    <label class="form-check-label">Rencana</label>
                                                </div>

                                                @error('jenis')
                                                <div class="text-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-6">

                                            <div class="mb-3">
                                                <label for="saham" class="form-label">Harga Saham <span class="text-danger">*</span></label>
                                                <div class="input-group mb-3">
                                                    <input wire:model.debounce.500ms="saham" type="text" class="form-control @error('saham') is-invalid @enderror" id="saham" aria-label="Recipient’s username" aria-describedby="basic-addon2">
                                                    <span class="input-group-text" id="basic-addon2">%</span>
                                                </div>
                                                @error('saham')
                                                <div class="form-text text-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="mb-3">
                                                <label for="setor_id" class="form-label">Kode Setoran <span class="text-danger">*</span></label>

                                                <select wire:model="setor_id"
                                                    class="form-select @error('setor_id') is-invalid @enderror">
                                                    <option value="">-- Pilih Kode --</option>
                                                    @foreach ($setors as $setor)
                                                    <option value="{{ $setor->id }}">
                                                        [{{ $setor->kode }}] {{ $setor->nama }}
                                                    </option>
                                                    @endforeach
                                                </select>

                                                @error('setor_id')
                                                <div class="form-text text-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="mb-3">
                                                <label for="tarik_id" class="form-label">Kode Tarikan <span class="text-danger">*</span></label>
                                                <select wire:model="tarik_id"
                                                    class="form-select @error('tarik_id') is-invalid @enderror">
                                                    <option value="">-- Pilih Kode --</option>
                                                    @foreach ($tariks as $tarik)
                                                    <option value="{{ $tarik->id }}">
                                                        [{{ $tarik->kode }}] {{ $tarik->nama }}
                                                    </option>
                                                    @endforeach
                                                </select>
                                                @error('tarik_id')
                                                <div class="form-text text-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="mb-3">
                                                <label for="nominal" class="form-label">Nominal Setoran <span class="text-danger">*</span></label>
                                                <div class="input-group mb-3">
                                                    <input wire:model.debounce.500ms="nominal" type="text" class="form-control @error('nominal') is-invalid @enderror" id="nominal" aria-label="Recipient’s username" aria-describedby="basic-addon2">
                                                    <span class="input-group-text" id="basic-addon2">%</span>
                                                </div>
                                                @error('nominal')
                                                <div class="form-text text-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="mb-3">
                                                <label for="insentif" class="form-label">Insentif Marketing <span class="text-danger">*</span></label>
                                                <div class="input-group mb-3">
                                                    <input wire:model.debounce.500ms="insentif" type="text" class="form-control @error('insentif') is-invalid @enderror" id="insentif" aria-label="Recipient’s username" aria-describedby="basic-addon2">
                                                    <span class="input-group-text" id="basic-addon2">%</span>
                                                </div>
                                                @error('insentif')
                                                <div class="form-text text-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    {{-- TABEL BERTINGKAT --}}
                                    <div>
                                        {{-- Tabel utama --}}
                                        <table class="table table-bordered table-hover">
                                            <thead class="table-danger text-center">
                                                <tr>
                                                    <th style="width:50px">No</th>
                                                    <th>Kode</th>
                                                    <th>Nama Transaksi</th>
                                                    <th>Account Debet</th>
                                                    <th>Account Kredit</th>
                                                    <th style="width:150px">
                                                        <button
                                                            type="button"
                                                            class="btn btn-success btn-sm"
                                                            wire:click="$set('showKodeModal', true)">
                                                            +
                                                        </button>

                                                    </th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($kodeRows as $i => $row)
                                                <tr>
                                                    <td class="text-center">{{ $i + 1 }}</td>
                                                    <td><input class="form-control" value="{{ $row['kode'] }}" readonly></td>
                                                    <td><input class="form-control" value="{{ $row['nama'] }}" readonly></td>
                                                    <td><input class="form-control" value="{{ $row['account_debet'] }}" readonly></td>
                                                    <td><input class="form-control" value="{{ $row['account_kredit'] }}" readonly></td>
                                                    <td class="text-center">
                                                        <button
                                                            type="button"
                                                            class="btn btn-danger btn-sm"
                                                            wire:click="removeKodeRow({{ $i }})">
                                                            Hapus
                                                        </button>

                                                    </td>
                                                </tr>
                                                @endforeach

                                            </tbody>
                                        </table>

                                        {{-- Modal --}}
                                        <div class="modal fade @if($showKodeModal) show d-block @endif" tabindex="-1" style="@if($showKodeModal) background: rgba(0,0,0,0.5); @endif">
                                            <div class="modal-dialog modal-lg">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Pilih Kode Simpanan</h5>
                                                        <button type="button" class="btn-close" wire:click="$set('showKodeModal', false)"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <table class="table table-striped">
                                                            <thead>
                                                                <tr>
                                                                    <th style="width:40px"><input type="checkbox" wire:model="selectAllKodes"></th>
                                                                    <th>Kode</th>
                                                                    <th>Nama Transaksi</th>
                                                                    <th>Account Debet</th>
                                                                    <th>Account Kredit</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                            <tbody>
                                                                @foreach($allKodes as $data)
                                                                <tr>
                                                                    <td>
                                                                        <input type="checkbox"
                                                                            wire:model="selectedKodes"
                                                                            value="{{ $data->id }}">
                                                                    </td>
                                                                    <td>{{ $data->kode }}</td>
                                                                    <td>{{ $data->nama }}</td>
                                                                    <td>{{ $data->debetAccount?->no_account ?? '-' }}</td>
                                                                    <td>{{ $data->kreditAccount?->no_account ?? '-' }}</td>
                                                                </tr>
                                                                @endforeach
                                                            </tbody>

                                                            </tbody>
                                                        </table>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button
                                                            type="button"
                                                            class="btn btn-secondary"
                                                            wire:click="$set('showKodeModal', false)">
                                                            Tutup
                                                        </button>

                                                        <button
                                                            type="button"
                                                            class="btn btn-primary"
                                                            wire:click="addSelectedKodes">
                                                            Simpan
                                                        </button>

                                                    </div>
                                                </div>
                                            </div>
                                        </div>


                                    </div>

                                    <!-- ===================== ANDROID ===================== -->
                                    <h5 class="mb-3 mt-5">Android</h5>
                                    <hr class="border border-danger border-2 opacity-50 mb-5">
                                    <div class="row">
                                        <div class="col-6">
                                            <div class="mb-3">
                                                <label class="form-label">
                                                    Kode <span class="text-danger">*</span>
                                                </label>

                                                <select wire:model="android"
                                                    class="form-select @error('android') is-invalid @enderror">
                                                    <option value="">-- Pilih Kode --</option>
                                                    @foreach ($pajaks as $pajak)
                                                    <option value="{{ $pajak->id }}">
                                                        [{{ $pajak->kode }}] {{ $pajak->nama }}
                                                    </option>
                                                    @endforeach
                                                </select>

                                                @error('android')
                                                <div class="text-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="mb-3">
                                                <label for="nominal_android" class="form-label">Biaya Android <span class="text-danger">*</span></label>
                                                <div class="input-group mb-3">
                                                    <input wire:model.debounce.500ms="nominal_android" type="text" class="form-control @error('nominal_android') is-invalid @enderror" id="nominal_android" aria-label="Recipient’s username" aria-describedby="basic-addon2">
                                                    <span class="input-group-text" id="basic-addon2">%</span>
                                                </div>
                                                @error('nominal_android')
                                                <div class="form-text text-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col">
                                            <div class="mb-3">
                                                <label for="account_android" class="form-label">
                                                    No Account <span class="text-danger">*</span>
                                                </label>

                                                <select wire:model="account_android"
                                                    id="account_android"
                                                    class="form-select @error('account_android') is-invalid @enderror">
                                                    <option value="">-- Pilih Account --</option>
                                                    @foreach ($accounts as $account)
                                                    <option value="{{ $account->id }}">
                                                        [ {{ $account->no_account }} ] {{ $account->nama }}
                                                    </option>
                                                    @endforeach
                                                </select>

                                                @error('account_android')
                                                <div class="form-text text-danger">{{ $message }}</div>
                                                @enderror
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