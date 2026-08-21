<div class="app-main">

    <div class="app-content-header">
        <div class="container-fluid">
            <div class="row">

                <div class="col-sm-6">
                    <h3 class="mb-0">{{ $title }}</h3>
                </div>

                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-end">
                        <li class="breadcrumb-item"><a href="{{ route('superadmin.kelompok') }}">Dashboard</a></li>
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

                    <div class="card card-info card-outline mb-4">

                        <div class="card-header">
                            <div class="card-title">Detail Kelompok</div>
                        </div>

                        <div class="card-body">
                            <table class="table table-bordered">
                                <tr>
                                    <th>ID</th>
                                    <td>{{ $kelompok->id }}</td>
                                </tr>
                                <tr>
                                    <th>Kode</th>
                                    <td>{{ $kelompok->kode }}</td>
                                </tr>
                                <tr>
                                    <th>Nama</th>
                                    <td>{{ $kelompok->nama }}</td>
                                </tr>
                                <tr>
                                    <th>Ketua</th>
                                    <td>
                                        {{ $kelompok->ketua_id }} - {{ $kelompok->ketua ? $kelompok->ketua->nama : '-' }}
                                    </td>
                                </tr>
                                <tr>
                                    <th>Dibuat</th>
                                    <td>{{ $kelompok->created_at->format('d-m-Y H:i') }}</td>
                                </tr>
                                <tr>
                                    <th>Diubah</th>
                                    <td>{{ $kelompok->updated_at->format('d-m-Y H:i') }}</td>
                                </tr>
                            </table>
                        </div>

                        <div class="card-footer">
                            <a wire:navigate href="{{ route('superadmin.kelompok') }}" class="btn btn-secondary">
                                Kembali
                            </a>
                        </div>

                    </div>

                </div>
            </div>

        </div>
    </div>

</div>