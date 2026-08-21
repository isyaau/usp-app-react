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

                        <form wire:submit.prevent="update" autocomplete="off">
                            @csrf

                            <div class="card-body">
                                <div class="row">
                                    <div class="col">
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
                                    </div>
                                    <div class="col">
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
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col">
                                        <div class="mb-3">
                                            <label for="account_debet" class="form-label">
                                                Account Debet <span class="text-danger">*</span>
                                            </label>

                                            <select
                                                wire:model.debounce.500ms="account_debet"
                                                class="form-select @error('account_debet') is-invalid @enderror">
                                                <option value="">-- Pilih Account Debet --</option>
                                                @foreach ($debet as $debet)
                                                <option value="{{ $debet->id }}">
                                                    {{ $debet->no_account }} - {{ $debet->nama }}
                                                </option>
                                                @endforeach
                                            </select>

                                            @error('account_debet')
                                            <div class="invalid-feedback d-block">
                                                {{ $message }}
                                            </div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="mb-3">
                                            <label for="account_kredit" class="form-label">
                                                Account Kredit <span class="text-danger">*</span>
                                            </label>

                                            <select
                                                wire:model.debounce.500ms="account_kredit"
                                                class="form-select @error('account_kredit') is-invalid @enderror">
                                                <option value="">-- Pilih Account Kredit --</option>
                                                @foreach ($kredit as $kredit)
                                                <option value="{{ $kredit->id }}">
                                                    {{ $kredit->no_account }} - {{ $kredit->nama }}
                                                </option>
                                                @endforeach
                                            </select>

                                            @error('account_kredit')
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
                                            <label class="form-label">
                                                Tipe <span class="text-danger">*</span>
                                            </label>

                                            <div class="form-check">
                                                <input
                                                    class="form-check-input"
                                                    type="checkbox"
                                                    id="setoranCheck"
                                                    wire:model.debounce.500ms="setoran">
                                                <label class="form-check-label" for="setoranCheck">
                                                    Setoran
                                                </label>
                                            </div>

                                            <div class="form-check">
                                                <input
                                                    class="form-check-input"
                                                    type="checkbox"
                                                    id="tarikanCheck"
                                                    wire:model.debounce.500ms="tarikan">
                                                <label class="form-check-label" for="tarikanCheck">
                                                    Tarikan
                                                </label>
                                            </div>

                                            <div class="form-check">
                                                <input
                                                    class="form-check-input"
                                                    type="checkbox"
                                                    id="transferCheck"
                                                    wire:model.debounce.500ms="transfer">
                                                <label class="form-check-label" for="transferCheck">
                                                    Pemindahbukuan
                                                </label>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col">
                                        <div class="mb-3">
                                            <label class="form-label">
                                                Simpanan <span class="text-danger">*</span>
                                            </label>

                                            <div class="form-check">
                                                <input
                                                    class="form-check-input"
                                                    type="checkbox"
                                                    id="pokokCheck"
                                                    wire:model.debounce.500ms="pokok">
                                                <label class="form-check-label" for="pokokCheck">
                                                    Pokok
                                                </label>
                                            </div>

                                            <div class="form-check">
                                                <input
                                                    class="form-check-input"
                                                    type="checkbox"
                                                    id="wajibCheck"
                                                    wire:model.debounce.500ms="wajib">
                                                <label class="form-check-label" for="wajibCheck">
                                                    Wajib
                                                </label>
                                            </div>

                                            <div class="form-check">
                                                <input
                                                    class="form-check-input"
                                                    type="checkbox"
                                                    id="sukarelaCheck"
                                                    wire:model.debounce.500mse="sukarela">
                                                <label class="form-check-label" for="sukarelaCheck">
                                                    Sukarela
                                                </label>
                                            </div>

                                            <div class="form-check">
                                                <input
                                                    class="form-check-input"
                                                    type="checkbox"
                                                    id="pinjamanCheck"
                                                    wire:model.debounce.500ms="pinjaman">
                                                <label class="form-check-label" for="pinjamanCheck">
                                                    Wajib Pinjaman
                                                </label>
                                            </div>

                                            <div class="form-check">
                                                <input
                                                    class="form-check-input"
                                                    type="checkbox"
                                                    id="sahamCheck"
                                                    wire:model.debounce.500ms="saham">
                                                <label class="form-check-label" for="sahamCheck">
                                                    Saham
                                                </label>
                                            </div>

                                            <div class="form-check">
                                                <input
                                                    class="form-check-input"
                                                    type="checkbox"
                                                    id="pokokPinjamanCheck"
                                                    wire:model.debounce.500ms="pokok_pinjaman">
                                                <label class="form-check-label" for="pokokPinjamanCheck">
                                                    Pokok Pinjaman
                                                </label>
                                            </div>

                                            <div class="form-check">
                                                <input
                                                    class="form-check-input"
                                                    type="checkbox"
                                                    id="rencanaCheck"
                                                    wire:model.debounce.500ms="rencana">
                                                <label class="form-check-label" for="rencanaCheck">
                                                    Rencana
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="keterangan" class="form-label">Keterangan <span class="text-danger">*</span></label>
                                    <textarea wire:model="keterangan" id="keterangan" class="form-control @error('keterangan') is-invalid @enderror" rows="3"></textarea>
                                    @error('keterangan') <small class="text-danger">{{ $message }}</small> @enderror
                                </div>




                            </div> {{-- /card-body --}}

                            <div class="card-footer">
                                <div class="d-flex justify-content-between align-items-center">
                                    <a
                                        wire:navigate
                                        href="{{ route('superadmin.simpanan.kode-transaksi') }}"
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