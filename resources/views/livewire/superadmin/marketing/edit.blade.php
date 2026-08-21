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
                                            <label for="alamat" class="form-label">Alamat <span class="text-danger">*</span></label>
                                            <textarea wire:model="alamat" id="alamat" class="form-control @error('alamat') is-invalid @enderror" rows="3"></textarea>
                                            @error('alamat') <small class="text-danger">{{ $message }}</small> @enderror
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="mb-3">
                                            <label for="no_ktp" class="form-label">
                                                No KTP <span class="text-danger">*</span>
                                            </label>

                                            <input
                                                wire:model.debounce.500ms="no_ktp"
                                                type="text"
                                                id="no_ktp"
                                                class="form-control @error('no_ktp') is-invalid @enderror">

                                            @error('no_ktp')
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
                                            <label for="no_hp" class="form-label">
                                                No HP <span class="text-danger">*</span>
                                            </label>

                                            <input
                                                wire:model.debounce.500ms="no_hp"
                                                type="text"
                                                id="no_hp"
                                                class="form-control @error('no_hp') is-invalid @enderror">

                                            @error('no_hp')
                                            <div class="invalid-feedback d-block">
                                                {{ $message }}
                                            </div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="mb-3">
                                            <label for="telepon" class="form-label">
                                                Telepon <span class="text-danger">*</span>
                                            </label>

                                            <input
                                                wire:model.debounce.500ms="telepon"
                                                type="text"
                                                id="telepon"
                                                class="form-control @error('telepon') is-invalid @enderror">

                                            @error('telepon')
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

                                    <div class="col">
                                        <div class="mb-3">
                                            <label class="form-label">
                                                Status <span class="text-danger">*</span>
                                            </label>

                                            <div class="form-check">
                                                <input
                                                    class="form-check-input"
                                                    type="checkbox"
                                                    id="aktifCheck"
                                                    wire:model.debounce.500ms="aktif" value="1">
                                                <label class="form-check-label" for="aktifCheck">
                                                    Aktif
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div> {{-- /card-body --}}

                            <div class="card-footer">
                                <div class="d-flex justify-content-between align-items-center">
                                    <a
                                        wire:navigate
                                        href="{{ route('superadmin.marketing') }}"
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