<div class="app-main">
    <div class="app-content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-6">
                    <h3 class="mb-0">@yield('title')</h3>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-end">
                        <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
                        <li class="breadcrumb-item" aria-current="page">{{ $title }}</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <div class="app-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-3 flex-fill ">
                    <div class="card mb-4">
                        <div class="card-header">
                            <div class="d-flex flex-column flex-md-row justify-content-between align-items-start r gap-2">
                                <a wire:navigate href="/superadmin/user/create" class="col-xl-2 col-md-2 btn btn-sm btn-primary mb-2"><i class="fa-solid fa-plus"></i> Tambah User</a>

                                <div class="col-5xl-7 col-md-7">
                                    <div class="row">
                                        <label for="role" class="form-label">Import Data <span class="text-danger"><a wire:click="downloadTemplate">Download Template Import Data</a></span></label>
                                        <div class="col-xl-12 col-md-12 col-sm-7">
                                            <!-- Import + Export Group -->
                                            <div class="input-group">
                                                <!-- Import File -->
                                                <input type="file" wire:model="file" class="form-control" />
                                                <button wire:click="import" class="btn btn-danger bg-gradient">Import</button>
                                            </div>

                                            <!-- Error Message -->
                                            @error('file')
                                            <span class="text-danger ms-2">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="row my-3">
                                        <label for="tglMulai" class="form-label">Export Data (Mulai - Sampai)</label>
                                        <div class="col-12">
                                            <div class="input-group" wire:ignore>
                                                <!-- Tanggal Mulai -->
                                                <input type="text" id="tglMulai" wire:model="tglMulai" class="form-control" placeholder="dd-mm-yyyy" />
                                                <!-- Tanggal Sampai -->
                                                <input type="text" id="tglSampai" wire:model="tglSampai" class="form-control" placeholder="dd-mm-yyyy" />
                                                <!-- Dropdown Export -->
                                                <!-- Tombol Export dengan Spinner -->
                                                <button class="btn btn-warning dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false" id="export-btn">
                                                    <i class="fa-solid fa-file-export" id="export-icon"></i> Export
                                                    <!-- Spinner (Awalnya disembunyikan) -->
                                                    <span id="loading-spinner" class="d-none spinner-border spinner-border-sm text-light" role="status" aria-hidden="true"></span>
                                                </button>
                                                <ul class="dropdown-menu">
                                                    <li>
                                                        <a wire:click="exportPdf" class="dropdown-item text-danger" role="button" id="export-pdf">
                                                            <i class="fa-solid fa-file-pdf"></i> PDF
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a wire:click="exportXls" class="dropdown-item text-success" role="button" id="export-excel">
                                                            <i class="fa-solid fa-file-excel"></i> EXCEL
                                                        </a>
                                                    </li>
                                                </ul>
                                            </div>

                                            @error('file')
                                            <span class="text-danger mt-1 d-block">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>



                                </div>
                            </div>

                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="d-flex align-items-center">
                                        <span class="me-2">Halaman</span>
                                        <select wire:model.live="paginate" class="form-select" style="width: 80px;">
                                            <option value="10">10</option>
                                            <option value="25">25</option>
                                            <option value="50">50</option>
                                            <option value="100">100</option>
                                        </select>
                                    </div>
                                    <div class="d-flex align-items-center col-5">
                                        <div class="input-group mb-3 mt-3">
                                            <span class="input-group-text text-danger" id="basic-addon1"><i class="fa-brands fa-searchengin"></i></span>
                                            <input wire:model.live="search"
                                                type="text"
                                                class="form-control"
                                                placeholder="Search..."
                                                aria-label="Username"
                                                aria-describedby="basic-addon1" />
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="table-responsive">
                                <table class="table table-hover table-bordered">
                                    <thead class="table-danger text-center">
                                        <tr>
                                            <th style="width: 10px">#</th>
                                            <th>Nama</th>
                                            <th style="width: 300px">Email</th>
                                            <th style="width: 100px">Avatar</th>
                                            <th style="width: 100px">Role</th>
                                            <th style="width: 150px;"><i class="fas fa-cog"></i></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($user as $item)
                                        <tr class="align-middle">
                                            <td class="text-center">{{ $user->firstItem() + $loop->index }}</td>
                                            <td>{{ $item->nama }}</td>
                                            <td>{{ $item->email }}</td>

                                            @php
                                            $avatarPath = $item->avatar && Storage::disk('public')->exists($item->avatar)
                                            ? $item->avatar
                                            : 'avatar/avatar-default.jpg';
                                            @endphp

                                            <td class="text-center">
                                                <img src="{{ asset('storage/' . $avatarPath) }}"
                                                    width="50"
                                                    height="50"
                                                    class="rounded-circle"
                                                    style="object-fit: cover;">
                                            </td>



                                            <td class="text-center">
                                                @if ($item->role == 'superadmin')
                                                <span class="badge rounded-pill text-bg-info">
                                                    {{ $item->role }}
                                                </span>
                                                @elseif ($item->role == 'admin')
                                                <span class="badge rounded-pill text-bg-primary">
                                                    {{ $item->role }}
                                                </span>
                                                @elseif ($item->role == 'user')
                                                <span class="badge rounded-pill text-bg-dark">
                                                    {{ $item->role }}
                                                </span>
                                                @endif
                                            <td class="text-center">
                                                <a wire:navigate href="/superadmin/user/{{ $item->id }}/edit" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i></a>
                                                <a wire:navigate href="/superadmin/user/{{ $item->id }}" class="btn btn-sm btn-primary"><i class="fas fa-eye"></i></a>
                                                <button type="button"
                                                    class="btn btn-sm btn-danger btn-delete-users"
                                                    data-id="{{ $item->id }}">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="card-footer clearfix">
                            {{ $user->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @script
    <script>
        $wire.on('deleteSwal', () => {
            Swal.fire({
                title: 'Berhasil!',
                text: 'Data berhasil dihapus!',
                icon: 'success'
            })
        });

        function initDeleteButtons() {
            document.querySelectorAll('.btn-delete-users').forEach(btn => {
                btn.onclick = function() {
                    let userId = this.dataset.id;

                    Swal.fire({
                        title: "Yakin ingin menghapus?",
                        text: "Data yang dihapus tidak dapat dikembalikan!",
                        icon: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#d33",
                        cancelButtonColor: "#3085d6",
                        confirmButtonText: "Ya, hapus!",
                        cancelButtonText: "Batal"
                    }).then((result) => {
                        if (result.isConfirmed) {
                            Livewire.dispatch('deleteUser', {
                                id: userId
                            });
                        }
                    });
                };
            });
        }

        document.addEventListener('DOMContentLoaded', initDeleteButtons);


        document.addEventListener('livewire:navigated', initDeleteButtons);
    </script>
    @endscript


</div>