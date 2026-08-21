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
                            <div class="card-title">Formulir Tambah User</div>
                        </div>

                        <form wire:submit.prevent="store">
                            @csrf
                            <div class="card-body">

                                <div class="mb-3">
                                    <label for="nama" class="form-label">Nama <span class="text-danger">*</span></label>
                                    <input
                                        wire:model.live="nama"
                                        type="text"
                                        id="nama"
                                        class="form-control @error('nama') is-invalid @enderror">
                                    @error('nama')
                                    <div class="form-text text-danger">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="row">
                                    <div class="col">
                                        <div class="mb-3">
                                            <label for="username" class="form-label">Username <span class="text-danger">*</span></label>
                                            <input
                                                wire:model="username"
                                                type="text"
                                                id="username"
                                                class="form-control @error('username') is-invalid @enderror">
                                            @error('username')
                                            <div class="form-text text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="mb-3">
                                            <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                                            <input
                                                wire:model.debounce.500ms="email"
                                                type="email"
                                                id="email"
                                                class="form-control @error('email') is-invalid @enderror">
                                            @error('email')
                                            <div class="form-text text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>


                                <div class="mb-3">
                                    <label for="role" class="form-label">Role <span class="text-danger">*</span></label>
                                    <select
                                        wire:model.debounce.500ms="role"
                                        id="role"
                                        class="form-select @error('role') is-invalid @enderror"
                                        aria-label="Pilih Role">
                                        <option value="">-- Pilih Role --</option>
                                        <option value="superadmin">Superadmin</option>
                                        <option value="admin">Admin</option>
                                        <option value="user">User</option>

                                    </select>

                                    @error('role')
                                    <div class="form-text text-danger">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="avatar" class="form-label">Avatar <span class="text-danger">*</span></label>
                                    <input type="file" id="avatar" wire:model="avatar"
                                        class="form-control @error('avatar') is-invalid @enderror">
                                    @error('avatar')
                                    <div class="form-text text-danger">{{ $message }}</div>
                                    @enderror
                                </div>


                                <div class="mb-3">
                                    <label for="password" class="form-label">Password <span class="text-danger">*</span></label>
                                    <input
                                        wire:model.debounce.500ms="password"
                                        type="password"
                                        id="password"
                                        class="form-control @error('password') is-invalid @enderror">
                                    @error('password')
                                    <div class="form-text text-danger">{{ $message }}</div>
                                    @enderror
                                </div>


                                <div class="mb-3">
                                    <label for="password_confirmation" class="form-label">Konfirmasi Password <span class="text-danger">*</span></label>
                                    <input
                                        wire:model.debounce.500ms="password_confirmation"
                                        type="password"
                                        id="password_confirmation"
                                        class="form-control @error('password_confirmation') is-invalid @enderror">
                                    @error('password_confirmation')
                                    <div class="form-text text-danger">{{ $message }}</div>
                                    @enderror
                                </div>


                            </div>


                            <div class="card-footer">
                                <div class="d-flex justify-content-between align-items-center">
                                    <a wire:navigate href="{{ route('superadmin.user') }}" class="btn btn-secondary">
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