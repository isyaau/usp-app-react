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
                                <a wire:navigate href="/superadmin/simpanan/rencana/create" class="col-xl-2 col-md-2 btn btn-sm btn-primary mb-2"><i class="fa-solid fa-plus"></i> Tambah {{ $title }}</a>

                                <div class="col-5xl-7 col-md-7">
                                    <div class="row">
                                        <label for="role" class="form-label">Import Data <span class="text-danger"><a wire:click="downloadTemplate">Download Template Import Data</a></span></label>
                                        <div class="col-xl-12 col-md-12 col-sm-7">
                                            <!-- Import + Export Group -->
                                            <div class="input-group">
                                                <!-- Import File -->
                                                <input type="file" id="file" wire:model="file" class="form-control" />
                                                <button wire:click="import" class="btn btn-danger bg-gradient">
                                                    Import
                                                    <span wire:loading wire:target="import"
                                                        class="spinner-border spinner-border-sm ms-2 text-light"
                                                        role="status"></span>
                                                </button>

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
                                                <input autocomplete="off" type="text" id="tglMulai" wire:model="tglMulai" class="form-control" placeholder="dd-mm-yyyy" />
                                                <!-- Tanggal Sampai -->
                                                <input autocomplete="off" type="text" id="tglSampai" wire:model="tglSampai" class="form-control" placeholder="dd-mm-yyyy" />
                                                <!-- Dropdown Export -->
                                                <!-- Tombol Export dengan Spinner -->
                                                <button class="btn btn-warning dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false" id="export-btn">
                                                    <i class="fa-solid fa-file-export" id="export-icon"></i> Export
                                                    <!-- Spinner (Awalnya disembunyikan) -->
                                                    <span wire:loading wire:target="exportPdf, exportXls" id="loading-spinner"
                                                        class="spinner-border spinner-border-sm ms-2 text-light"
                                                        role="status"></span>

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
                                            <th style="width: 100px">Tanggal</th>
                                            <th style="width: 100px">Jatuh Tempo</th>
                                            <th style="width: 300px">No Bukti</th>
                                            <th style="width: 300px">Nominal</th>
                                            <th style="width: 100px">Bagi Hasil</th>
                                            <th style="width: 300px">Keterangan</th>
                                            <th style="width: 200px"><i class="fas fa-cog"></i></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($rencana as $item)
                                        <tr class="align-middle text-center">
                                            <td>{{ $rencana->firstItem() + $loop->index }}</td>
                                            <td>{{ \Carbon\Carbon::parse($item->tanggal_mulai)->format('d-m-Y') }}</td>
                                            <td>{{ \Carbon\Carbon::parse($item->tanggal_jatuhtempo)->format('d-m-Y') }}</td>
                                            <td>{{ $item->no_bukti }}</td>
                                            <td>{{ number_format($item->nominal, 0, ',', '.') }}</td>
                                            <td>{{ $item->bunga }} %</td>
                                            <td class="text-start">{{ $item->keterangan }}</td>
                                            <td>
                                                <a wire:navigate href="/superadmin/rencana/{{ $item->id }}/edit" class="btn btn-sm btn-warning">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <a wire:navigate href="/superadmin/rencana/{{ $item->id }}" class="btn btn-sm btn-primary">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <button type="button" class="btn btn-sm btn-danger btn-delete-rencana" data-id="{{ $item->id }}">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>

                                <!-- Pagination -->
                                <div class="d-flex justify-content-end">
                                    {{ $rencana->links() }}
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>

        @script
        <script>
            document.addEventListener('livewire:navigated', () => {
                const component = $wire; // aman, tidak duplikasi listener

                component.on('deleteSwal', () => {
                    Swal.fire({
                        title: 'Berhasil!',
                        text: 'Data berhasil dihapus!',
                        icon: 'success'
                    });
                });

                component.on('kosongSwal', () => {
                    Swal.fire({
                        title: 'Error!',
                        text: 'Data tidak ditemukan',
                        icon: 'error'
                    });
                });

                component.on('storeSwal', () => {
                    Swal.fire({
                        title: 'Berhasil!',
                        text: 'Data berhasil disimpan!',
                        icon: 'success'
                    });
                });

                component.on('importSwal', () => {
                    Swal.fire({
                        title: 'Berhasil!',
                        text: 'Data berhasil diimport!',
                        icon: 'success'
                    });
                });

                component.on('exportSwal', () => {
                    Swal.fire({
                        title: 'Berhasil!',
                        text: 'Data berhasil diexport!',
                        icon: 'success'
                    });
                });

                component.on('importErrorSwal', (data) => {
                    Swal.fire({
                        title: 'Gagal!',
                        text: data.message ?? 'Terjadi kesalahan saat import.',
                        icon: 'error'
                    });
                });

            });

            function initDeleteButtons() {
                document.querySelectorAll('.btn-delete-rencana').forEach(btn => {
                    btn.onclick = function() {
                        let rencanaId = this.dataset.id;

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
                                Livewire.dispatch('deleteRencana', {
                                    id: rencanaId
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