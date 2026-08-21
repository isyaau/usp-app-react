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
                                                No Bukti <span class="text-danger">*</span>
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
                                                Nama Debitur <span class="text-danger">*</span>
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
                                        <div class="mb-3">
                                            <label for="kode" class="form-label">
                                                Bagi Hasil/Tahun <span class="text-danger">*</span>
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
                                            <div class="col">
                                                <div class="mb-3">
                                                    <label for="kode" class="form-label">
                                                        Bulan <span class="text-danger">*</span>
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
                                                        Pembayaran Pokok Per <span class="text-danger">*</span>
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
                                                        Jenis Angsuran <span class="text-danger">*</span>
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
                                                        Setiap Saat <span class="text-danger">*</span>
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
                                                Produk Pinjaman <span class="text-danger">*</span>
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
                                                Penggunaan Kredit <span class="text-danger">*</span>
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
                                                Jaminan <span class="text-danger">*</span>
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
                                                No HP <span class="text-danger">*</span>
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
                                                Marketing <span class="text-danger">*</span>
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
                                            <label class="form-label">Biaya <span class="text-danger">*</span></label>

                                            <table class="table table-bordered">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th>Komponen Pinjaman</th>
                                                        <th>Nominal</th>
                                                        <th>%</th>
                                                        <th>No Account</th>
                                                        <th width="50px">Hapus</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($jaminan as $index => $item)
                                                    <tr>
                                                        <td>
                                                            <input
                                                                wire:model.live="jaminan.{{ $index }}"
                                                                type="text"
                                                                class="form-control @error('jaminan.'.$index) is-invalid @enderror"
                                                                placeholder="Masukkan detail...">

                                                            @error('jaminan.'.$index)
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                            @enderror
                                                        </td>
                                                        <td class="text-center">
                                                            @if(count($jaminan) > 1)
                                                            <button type="button" wire:click.prevent="removeField({{ $index }})" class="btn btn-sm btn-outline-danger">
                                                                &times;
                                                            </button>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                        <div class="mb-3">
                                            <label for="kode" class="form-label">
                                                Total Jaminan <span class="text-danger">*</span>
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
                                        href="{{ route('superadmin.pinjaman.jaminan') }}"
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