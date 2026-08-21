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
                <div class="col-md-3 flex-fill">
                    <div class="card mb-4">
                        <div class="card-header">
                            <div class="d-flex flex-column flex-md-row align-items-start r ">
                                <div class="btn-group" role="group" aria-label="Basic mixed styles example">
                                    <button type="button" class="btn btn-danger">Proses</button>
                                    <button type="button" class="btn btn-warning">Cetak 1</button>
                                    <button type="button" class="btn btn-success">Cetak 2</button>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="row">
                                    <div class="col">
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
                                        <div class="row">
                                            <div class="col">
                                                <div class="mb-3">
                                                    <label for="kode" class="form-label">
                                                        Jatuh Tempo Angsuran Mulai <span class="text-danger">*</span>
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
                                                        Sampai <span class="text-danger">*</span>
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
                                            </div>
                                            <div class="col">
                                                <div class="mb-3">
                                                    <label for="kode" class="form-label">
                                                        Excel <span class="text-danger">*</span>
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

                                    </div>
                                    <div class="col">
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
                                        <div class="mb-3">
                                            <label for="kode" class="form-label">
                                                Sektor <span class="text-danger">*</span>
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
                                                Jatuh Tempo Angsuran Masih <span class="text-danger">*</span>
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
                                                Kantor <span class="text-danger">*</span>
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
                                <hr>
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
                                            <th style="width: 100px">Nama</th>
                                            <th style="width: 200px">Detail</th>

                                            <th style="width: 100px;"><i class="fas fa-cog"></i></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($jaminan as $item)
                                        <tr class="align-middle">
                                            <td class="text-center">{{ $jaminan->firstItem() + $loop->index }}</td>
                                            <td>{{ $item->nama }}</td>
                                            <td>
                                                @foreach ($item->details as $detail)
                                                <span class="badge bg-danger">{{ $detail->detail }}</span>
                                                @endforeach
                                            </td>
                                            <td class="text-center">
                                                <a wire:navigate href="/superadmin/pinjaman/jaminan/{{ $item->id }}/edit" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i></a>
                                                <a wire:navigate href="/superadmin/pinjaman/jaminan/{{ $item->id }}" class="btn btn-sm btn-primary"><i class="fas fa-eye"></i></a>
                                                <button type="button"
                                                    class="btn btn-sm btn-danger btn-delete-jaminan"
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
                            {{ $jaminan->links() }}
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
            document.querySelectorAll('.btn-delete-jaminan').forEach(btn => {
                btn.onclick = function() {
                    let jaminanId = this.dataset.id;

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
                            Livewire.dispatch('deleteJaminan', {
                                id: jaminanId
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