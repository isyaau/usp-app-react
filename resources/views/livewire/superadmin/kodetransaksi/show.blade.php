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
                        <li class="breadcrumb-item active">{{ $title }}</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="app-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-6">
                    <div class="card card-info card-outline mb-4">
                        <div class="card-header">
                            <div class="card-title">Detail {{ $title }}</div>
                        </div>

                        <div class="card-body">
                            <table class="table table-bordered">
                                <tr>
                                    <th width="35%">Kode</th>
                                    <td>{{ $data->kode }}</td>
                                </tr>
                                <tr>
                                    <th>Nama</th>
                                    <td>{{ $data->nama }}</td>
                                </tr>
                                <tr>
                                    <th>Account Debet</th>
                                    <td>
                                        {{ $data->accountDebet->no_account ?? '-' }} -
                                        {{ $data->accountDebet->nama ?? '-' }}
                                    </td>
                                </tr>
                                <tr>
                                    <th>Account Kredit</th>
                                    <td>
                                        {{ $data->accountKredit->no_account ?? '-' }} -
                                        {{ $data->accountKredit->nama ?? '-' }}
                                    </td>
                                </tr>
                                <tr>
                                    <th>Tipe Transaksi</th>
                                    <td>
                                        <ul class="mb-0">
                                            @if($data->setoran) <li>Setoran</li> @endif
                                            @if($data->tarikan) <li>Tarikan</li> @endif
                                            @if($data->transfer) <li>Pemindahbukuan</li> @endif
                                        </ul>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Jenis Simpanan</th>
                                    <td>
                                        <ul class="mb-0">
                                            @if($data->pokok) <li>Pokok</li> @endif
                                            @if($data->wajib) <li>Wajib</li> @endif
                                            @if($data->sukarela) <li>Sukarela</li> @endif
                                            @if($data->pinjaman) <li>Wajib Pinjaman</li> @endif
                                            @if($data->saham) <li>Saham</li> @endif
                                            @if($data->pokok_pinjaman) <li>Pokok Pinjaman</li> @endif
                                            @if($data->rencana) <li>Rencana</li> @endif
                                        </ul>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Keterangan</th>
                                    <td>{{ $data->keterangan }}</td>
                                </tr>
                                <tr>
                                    <th>Dibuat</th>
                                    <td>{{ $data->created_at->format('d-m-Y H:i') }}</td>
                                </tr>
                                <tr>
                                    <th>Diubah</th>
                                    <td>{{ $data->updated_at->format('d-m-Y H:i') }}</td>
                                </tr>
                            </table>
                        </div>

                        <div class="card-footer">
                            <a wire:navigate href="{{ route('superadmin.simpanan.kode-transaksi') }}" class="btn btn-secondary">
                                Kembali
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>