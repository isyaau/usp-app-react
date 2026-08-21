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
                                <div class="row">
                                    <div class="col">
                                        <h5 class="mb-3">Produk</h5>
                                        <hr class="border border-danger border-2 opacity-50 mb-3">
                                        <div class="mb-3">
                                            <label for="kode" class="form-label">
                                                Kode <span class="text-danger">*</span>
                                            </label>

                                            <input
                                                wire:model.debounce.500ms="kode"
                                                type="text"
                                                id="kode"
                                                class="form-control @error('kode') is-invalid @enderror">

                                            @error('kode')
                                            <div class="invalid-feedback d-block">
                                                {{ $message }}
                                            </div>
                                            @enderror
                                        </div>
                                        <div class="mb-3">
                                            <label for="nama" class="form-label">
                                                Nama <span class="text-danger">*</span>
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
                                            <label for="account_id" class="form-label">
                                                No Account <span class="text-danger">*</span>
                                            </label>

                                            <select
                                                wire:model.debounce.500ms="account_id"
                                                class="form-select @error('account_id') is-invalid @enderror">
                                                <option value="">-- Pilih Account --</option>
                                                @foreach ($accounts as $account)
                                                <option value="{{ $account->id }}">
                                                    {{ $account->no_account }} - {{ $account->nama }}
                                                </option>
                                                @endforeach
                                            </select>

                                            @error('account_id')
                                            <div class="invalid-feedback d-block">
                                                {{ $message }}
                                            </div>
                                            @enderror
                                        </div>
                                        <div class="col">
                                            <div class="mb-3">
                                                <label for="jangka_waktu" class="form-label">
                                                    Jangka Waktu <span class="text-danger">*</span>
                                                </label>
                                                <div class="input-group mb-3">
                                                    <input wire:model.debounce.500ms="jangka_waktu" type="text" class="form-control @error('jangka_waktu') is-invalid @enderror" id="jangka_waktu" aria-label="Recipient’s username" aria-describedby="basic-addon2">
                                                    <span class="input-group-text" id="basic-addon2">Bulan</span>
                                                </div>
                                                @error('jangka_waktu')
                                                <div class="invalid-feedback d-block">
                                                    {{ $message }}
                                                </div>
                                                @enderror
                                            </div>
                                        </div>

                                        <h5 class="mb-3">Bagi Hasil</h5>
                                        <hr class="border border-danger border-2 opacity-50 mb-3">

                                        <div class="mb-3">
                                            <label for="bunga" class="form-label">
                                                Bagi Hasil <span class="text-danger">*</span>
                                            </label>
                                            <div class="input-group mb-3">
                                                <input wire:model.debounce.500ms="bunga" type="text" class="form-control @error('bunga') is-invalid @enderror" id="bunga" aria-label="Recipient’s username" aria-describedby="basic-addon2">
                                                <span class="input-group-text" id="basic-addon2">%</span>
                                            </div>

                                            @error('bunga')
                                            <div class="invalid-feedback d-block">
                                                {{ $message }}
                                            </div>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label for="account_bunga" class="form-label">
                                                No Account <span class="text-danger">*</span>
                                            </label>

                                            <select
                                                wire:model.debounce.500ms="account_bunga"
                                                class="form-select @error('account_bunga') is-invalid @enderror">
                                                <option value="">-- Pilih Account Bunga --</option>
                                                @foreach ($bungas as $bunga)
                                                <option value="{{ $bunga->id }}">
                                                    {{ $bunga->no_account }} - {{ $bunga->nama }}
                                                </option>
                                                @endforeach
                                            </select>

                                            @error('account_bunga')
                                            <div class="invalid-feedback d-block">
                                                {{ $message }}
                                            </div>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label d-block">Rumus <span class="text-danger">*</span></label>
                                            <div class="form-check form-check">
                                                <input class="form-check-input" type="radio" id="Harian" name="rumus_bunga" wire:model="rumus_bunga" value="harian">
                                                <label class="form-check-label" for="harian">Harian</label>
                                            </div>
                                            <div class="form-check form-check">
                                                <input class="form-check-input" type="radio" id="Bulanan" name="rumus_bunga" wire:model="rumus_bunga" value="bulanan">
                                                <label class="form-check-label" for="bulanan">Bulanan</label>
                                            </div>

                                            @error('rumus')
                                            <div class="form-text text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col">
                                        <h5 class="mb-3">Pajak</h5>
                                        <hr class="border border-danger border-2 opacity-50 mb-3">
                                        <div class="mb-3">
                                            <label for="pajak" class="form-label">
                                                Pajak <span class="text-danger">*</span>
                                            </label>
                                            <div class="input-group mb-3">
                                                <input wire:model.debounce.500ms="pajak" type="text" class="form-control @error('pajak') is-invalid @enderror" id="pajak" aria-label="Recipient’s username" aria-describedby="basic-addon2">
                                                <span class="input-group-text" id="basic-addon2">%</span>
                                            </div>

                                            @error('pajak')
                                            <div class="invalid-feedback d-block">
                                                {{ $message }}
                                            </div>
                                            @enderror
                                        </div>
                                        <div class="mb-3">
                                            <label for="account_pajak" class="form-label">
                                                Account Pajak <span class="text-danger">*</span>
                                            </label>

                                            <select
                                                wire:model.debounce.500ms="account_pajak"
                                                class="form-select @error('account_pajak') is-invalid @enderror">
                                                <option value="">-- Pilih Account Pajak --</option>
                                                @foreach ($pajaks as $pajak)
                                                <option value="{{ $pajak->id }}">
                                                    {{ $pajak->no_account }} - {{ $pajak->nama }}
                                                </option>
                                                @endforeach
                                            </select>

                                            @error('account_pajak')
                                            <div class="invalid-feedback d-block">
                                                {{ $message }}
                                            </div>
                                            @enderror
                                        </div>
                                        <div class="mb-3">
                                            <label for="saldo_pajak" class="form-label">
                                                Saldo Minimum <span class="text-danger">*</span>
                                            </label>

                                            <input
                                                wire:model.debounce.500ms="saldo_pajak"
                                                type="text"
                                                id="saldo_pajak"
                                                class="form-control @error('saldo_pajak') is-invalid @enderror">

                                            @error('saldo_pajak')
                                            <div class="invalid-feedback d-block">
                                                {{ $message }}
                                            </div>
                                            @enderror
                                        </div>
                                        <h5 class="mb-3">Penalti</h5>
                                        <hr class="border border-danger border-2 opacity-50 mb-3">
                                        <div class="mb-3">
                                            <label for="penalti" class="form-label">
                                                Penalti <span class="text-danger">*</span>
                                            </label>
                                            <div class="input-group mb-3">
                                                <input wire:model.debounce.500ms="penalti" type="text" class="form-control @error('penalti') is-invalid @enderror" id="penalti" aria-label="Recipient’s username" aria-describedby="basic-addon2">
                                                <span class="input-group-text" id="basic-addon2">%</span>
                                            </div>

                                            @error('penalti')
                                            <div class="invalid-feedback d-block">
                                                {{ $message }}
                                            </div>
                                            @enderror
                                        </div>
                                        <div class="mb-3">
                                            <label for="account_penalti" class="form-label">
                                                Account Penalti <span class="text-danger">*</span>
                                            </label>

                                            <select
                                                wire:model.debounce.500ms="account_penalti"
                                                class="form-select @error('account_penalti') is-invalid @enderror">
                                                <option value="">-- Pilih Account Penalti --</option>
                                                @foreach ($penalties as $penalti)
                                                <option value="{{ $penalti->id }}">
                                                    {{ $penalti->no_account }} - {{ $penalti->nama }}
                                                </option>
                                                @endforeach
                                            </select>

                                            @error('account_penalti')
                                            <div class="invalid-feedback d-block">
                                                {{ $message }}
                                            </div>
                                            @enderror
                                        </div>
                                        <div class="mb-3">
                                            <label for="insentif" class="form-label">
                                                Insentif Marketing <span class="text-danger">*</span>
                                            </label>
                                            <div class="input-group mb-3">
                                                <input wire:model.debounce.500ms="insentif" type="text" class="form-control @error('insentif') is-invalid @enderror" id="insentif" aria-label="Recipient’s username" aria-describedby="basic-addon2">
                                                <span class="input-group-text" id="basic-addon2">%</span>
                                            </div>

                                            @error('insentif')
                                            <div class="invalid-feedback d-block">
                                                {{ $message }}
                                            </div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                            </div> {{-- /card-body --}}

                            <div class="card-footer">
                                <div class="d-flex justify-content-between align-items-center">
                                    <a
                                        wire:navigate
                                        href="{{ route('superadmin.simpanan-berjangka.produk') }}"
                                        class="btn btn-secondary">
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

</div>