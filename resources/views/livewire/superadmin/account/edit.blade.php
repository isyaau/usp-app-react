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

                                {{-- HEADER --}}
                                <div class="mb-3">
                                    <label class="form-label">
                                        Header <span class="text-danger">*</span>
                                    </label>

                                    <select
                                        wire:model="header_id"
                                        class="form-select @error('header_id') is-invalid @enderror">
                                        <option value="">-- Pilih Header --</option>
                                        @foreach ($headers as $item)
                                        <option value="{{ $item->id }}">
                                            {{ $item->no_header }} - {{ $item->nama }}
                                        </option>
                                        @endforeach
                                    </select>

                                    @error('header_id')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- NOMOR ACCOUNT --}}
                                <div class="mb-3">
                                    <label class="form-label">
                                        Nomor Account <span class="text-danger">*</span>
                                    </label>

                                    <div class="input-group">
                                        {{-- PREFIX (TIDAK BISA DIEDIT) --}}
                                        <span class="input-group-text">
                                            {{ $no_header_prefix ?? '---' }}-
                                        </span>

                                        {{-- YANG BISA DIEDIT HANYA INI --}}
                                        <input
                                            wire:model.debounce.500ms="no_account"
                                            type="text"
                                            class="form-control @error('no_account') is-invalid @enderror"
                                            placeholder="001"
                                            inputmode="numeric"
                                            pattern="[0-9]*"
                                            {{ !$header_id ? 'disabled' : '' }}>
                                    </div>

                                    @error('no_account')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>


                                {{-- NAMA --}}
                                <div class="mb-3">
                                    <label class="form-label">
                                        Nama <span class="text-danger">*</span>
                                    </label>

                                    <input
                                        wire:model.debounce.500ms="nama"
                                        type="text"
                                        class="form-control @error('nama') is-invalid @enderror">

                                    @error('nama')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- TIPE --}}
                                <div class="mb-3">
                                    <label class="form-label">
                                        Tipe <span class="text-danger">*</span>
                                    </label>

                                    <select
                                        wire:model.defer="tipe"
                                        class="form-select @error('tipe') is-invalid @enderror">
                                        <option value="">-- Pilih Tipe --</option>
                                        <option value="Debet">Debet</option>
                                        <option value="Kredit">Kredit</option>
                                    </select>

                                    @error('tipe')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>

                            </div>

                            <div class="card-footer d-flex justify-content-between">
                                <a wire:navigate href="{{ route('superadmin.account-header') }}" class="btn btn-secondary">
                                    Kembali
                                </a>

                                <button type="submit" class="btn btn-primary">
                                    Submit
                                </button>
                            </div>
                        </form>


                    </div>

                </div>
            </div>

        </div>
    </div>

</div>