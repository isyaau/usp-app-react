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
                                    <div class="col">
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
                                    <div class="col">
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
                                <div class="col-6">
                                    <div class="mb-3">
                                        <label for="kode" class="form-label">
                                            Jangka Waktu <span class="text-danger">*</span>
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
                                <div class="col-6">
                                    <div class="mb-3">
                                        <label for="kode" class="form-label">
                                            Angsur <span class="text-danger">*</span>
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

                            </div> {{-- /card-body --}}

                            <div class="card-footer">
                                <div class="d-flex justify-content-between align-items-center">
                                    <a
                                        wire:navigate
                                        href="{{ route('superadmin.transaksi-pinjaman.penalti-pinjaman') }}"
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