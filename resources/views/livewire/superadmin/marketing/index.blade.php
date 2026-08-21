<div class="app-main">
    <div class="app-content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-6">
                    <h3 class="mb-0">@yield('title')</h3>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-end">
                        <li class="breadcrumb-item"><a href="dashboard">Dashboard</a></li>
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
                                <a wire:navigate href="/superadmin/marketing/create" class="col-xl-2 col-md-2 btn btn-sm btn-primary mb-2"><i class="fa-solid fa-plus"></i> Tambah {{ $title }}</a>

                                <div class="col-5xl-7 col-md-7">




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
                                            <th style="width: 100px">Kode</th>
                                            <th style="width: 200px">Nama</th>
                                            <th style="width: 300px">Alamat</th>
                                            <th style="width: 100px">Telepon</th>
                                            <th style="width: 100px">No HP</th>
                                            <th style="width: 100px">Kantor</th>
                                            <th style="width: 100px">Status</th>
                                            <th style="width: 100px;"><i class="fas fa-cog"></i></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($marketing as $item)
                                        <tr class="align-middle">
                                            <td class="text-center">{{ $marketing->firstItem() + $loop->index }}</td>
                                            <td>{{ $item->kode }}</td>
                                            <td>{{ $item->nama }}</td>
                                            <td>{{ $item->alamat }}</td>
                                            <td>{{ $item->telepon }}</td>
                                            <td>{{ $item->no_hp }}</td>
                                            <td>{{ $item->kantor->nama_kantor }}</td>

                                            <td class="text-center">
                                                @if ($item->aktif == 1)
                                                <i class="fa-solid fa-square-check text-success"></i>
                                                @else
                                                <i class="fa-solid fa-square-xmark text-danger"></i>
                                                @endif
                                            </td>



                                            <td class="text-center">
                                                <a wire:navigate href="/superadmin/marketing/{{ $item->id }}/edit" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i></a>
                                                <a wire:navigate href="/superadmin/marketing/{{ $item->id }}" class="btn btn-sm btn-primary"><i class="fas fa-eye"></i></a>
                                                <button type="button"
                                                    class="btn btn-sm btn-danger btn-delete-marketing"
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
                            {{ $marketing->links() }}
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
            document.querySelectorAll('.btn-delete-marketing').forEach(btn => {
                btn.onclick = function() {
                    let marketingId = this.dataset.id;

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
                            Livewire.dispatch('deleteMarketing', {
                                id: marketingId
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