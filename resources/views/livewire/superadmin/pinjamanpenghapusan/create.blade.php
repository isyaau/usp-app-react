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
                                        No. Bukti <span class="text-danger">*</span>
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
                                        No. Pinjaman <span class="text-danger">*</span>
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
                                <div class="mb-3">
                                    <label for="kode" class="form-label">
                                        Angsuran Pokok <span class="text-danger">*</span>
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
                                        Angsuran Bagi Hasil <span class="text-danger">*</span>
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
                            </div> {{-- /card-body --}}

                            <div class="card-footer">
                                <div class="d-flex justify-content-between align-items-center">
                                    <a
                                        wire:navigate
                                        href="{{ route('superadmin.pinjaman.penghapusan') }}"
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