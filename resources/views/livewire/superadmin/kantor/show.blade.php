<div class="app-main">

    <div class="app-content-header">
        <div class="container-fluid">
            <div class="row">

                <div class="col-sm-6">
                    <h3 class="mb-0">{{ $title }}</h3>
                </div>

                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-end">
                        <li class="breadcrumb-item"><a href="{{ route('superadmin.dashboard') }}">Dashboard</a></li>
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
                                    <td>{{ $kantor->id }}</td>
                                </tr>
                                <tr>
                                    <th>Kode</th>
                                    <td>{{ $kantor->kode }}</td>
                                </tr>
                                <tr>
                                    <th>Nama Kantor</th>
                                    <td>{{ $kantor->nama_kantor }}</td>
                                </tr>
                                <tr>
                                    <th>Provinsi</th>
                                    <td>{{ $kantor->provinsi?->name }}</td>
                                </tr>
                                <tr>
                                    <th>Kota</th>
                                    <td>{{ $kantor->kota?->name }}</td>
                                </tr>
                                <tr>
                                    <th>Kecamatan</th>
                                    <td>{{ $kantor->kecamatan?->name }}</td>
                                </tr>
                                <tr>
                                    <th>Kelurahan</th>
                                    <td>{{ $kantor->kelurahan?->name }}</td>
                                </tr>
                                <tr>
                                    <th>Alamat Kantor</th>
                                    <td>{{ $kantor->alamat_kantor }}</td>
                                </tr>
                                <tr>
                                    <th>Pejabat</th>
                                    <td>{{ $kantor->pejabat }}</td>
                                </tr>
                                <tr>
                                    <th>Jabatan</th>
                                    <td>{{ $kantor->jabatan }}</td>
                                </tr>
                                <tr>
                                    <th>Bendahara</th>
                                    <td>{{ $kantor->bendahara }}</td>
                                </tr>
                                <tr>
                                    <th>Dibuat</th>
                                    <td>{{ $kantor->created_at->format('d-m-Y H:i') }}</td>
                                </tr>

                                <tr>
                                    <th>Diubah</th>
                                    <td>{{ $kantor->updated_at->format('d-m-Y H:i') }}</td>
                                </tr>
                            </table>

                        </div>

                        <div class="card-footer">
                            <a wire:navigate href="{{ route('superadmin.kantor') }}" class="btn btn-secondary">
                                Kembali
                            </a>
                        </div>

                    </div>

                </div>
            </div>

        </div>
    </div>

</div>