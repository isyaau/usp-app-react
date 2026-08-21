<div class="app-main">
    {{-- ================= HEADER ================= --}}
    <div class="app-content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-6">
                    <h3 class="mb-0">{{ $title }}</h3>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-end">
                        <li class="breadcrumb-item">
                            <a href="{{ route('superadmin.dashboard') }}">Dashboard</a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="{{ route('superadmin.simpanan') }}">Simpanan</a>
                        </li>
                        <li class="breadcrumb-item active">{{ $title }}</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    {{-- ================= CONTENT ================= --}}
    <div class="app-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-8">
                    <div class="card card-info card-outline mb-4">
                        <div class="card-header">
                            <div class="card-title">Detail Data</div>
                        </div>

                        <div class="card-body">

                            {{-- ================= FIELD UTAMA ================= --}}
                            <h5 class="mb-3 text-primary">Informasi Dasar</h5>
                            <table class="table table-bordered table-striped mb-4">
                                <tr>
                                    <th width="35%">No Rekening</th>
                                    <td class="fw-bold">{{ $no_rekening ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>Tanggal Registrasi</th>
                                    {{-- Menggunakan $created_at yang sudah diformat di component (d-m-Y) --}}
                                    <td>{{ $created_at ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>Anggota</th>
                                    <td>
                                        @if($nama_anggota)
                                        {{ $no_anggota }} - {{ $nama_anggota }}
                                        @else
                                        <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Jenis Simpanan</th>
                                    <td>{{ $jenis_nama ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>Marketing</th>
                                    <td>{{ $marketing_nama ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>Kantor Cabang</th>
                                    <td>{{ $kantor_nama ?? '-' }}</td>
                                </tr>
                            </table>

                            {{-- ================= DETAIL KEUANGAN ================= --}}
                            <h5 class="mb-3 text-primary">Detail Keuangan</h5>
                            <table class="table table-bordered mb-4">
                                <tr>
                                    <th width="35%">Atas Nama (QQ)</th>
                                    <td>{{ $qq ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>Suku Bunga</th>
                                    <td>{{ $bunga ? $bunga . '%' : '0%' }}</td>
                                </tr>
                                <tr>
                                    <th>Nominal Setoran</th>
                                    {{-- Disarankan menggunakan format number_format jika nilai berupa angka --}}
                                    <td>Rp {{ number_format((float)$nominal_setor, 0, ',', '.') }}</td>
                                </tr>
                                <tr>
                                    <th>Nominal Blokir</th>
                                    <td>Rp {{ number_format((float)$nominal_blokir, 0, ',', '.') }}</td>
                                </tr>
                                <tr>
                                    <th>Tanggal Blokir</th>
                                    <td>{{ $tgl_blokir ?? '-' }}</td>
                                </tr>
                            </table>

                            {{-- ================= STATUS ================= --}}
                            <h5 class="mb-3 text-primary">Status Akun</h5>
                            <table class="table table-bordered mb-4">
                                <tr>
                                    <th width="35%">Status Aktif</th>
                                    <td>
                                        <span class="badge {{ $aktif ? 'bg-success' : 'bg-danger' }}">
                                            {{ $aktif ? 'AKTIF' : 'TIDAK AKTIF' }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Blokir Simpanan</th>
                                    <td>
                                        <span class="badge {{ $blokir_simpanan ? 'bg-warning text-dark' : 'bg-secondary' }}">
                                            {{ $blokir_simpanan ? 'YA' : 'TIDAK' }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Blokir Nominal</th>
                                    <td>
                                        <span class="badge {{ $blokir_nominal ? 'bg-warning text-dark' : 'bg-secondary' }}">
                                            {{ $blokir_nominal ? 'YA' : 'TIDAK' }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Blokir Tanggal</th>
                                    <td>
                                        <span class="badge {{ $blokir_tgl ? 'bg-warning text-dark' : 'bg-secondary' }}">
                                            {{ $blokir_tgl ? 'YA' : 'TIDAK' }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Notifikasi SMS</th>
                                    <td>
                                        <span class="badge {{ $sms ? 'bg-info' : 'bg-secondary' }}">
                                            {{ $sms ? 'ON' : 'OFF' }}
                                        </span>
                                    </td>
                                </tr>
                            </table>

                            {{-- ================= TANDA TANGAN ================= --}}
                            <h5 class="mb-3 text-primary">Validasi</h5>
                            <table class="table table-bordered mb-4">
                                <tr>
                                    <th width="35%">Tanda Tangan</th>
                                    <td>
                                        @if($signature)
                                        <img src="{{ $signature }}"
                                            alt="Tanda Tangan"
                                            class="img-fluid border rounded p-1"
                                            style="max-height: 120px; background-color: #f8f9fa;">
                                        @else
                                        <span class="fst-italic text-muted small">Tidak ada tanda tangan</span>
                                        @endif
                                    </td>
                                </tr>
                            </table>

                            {{-- ================= LOG SYSTEM ================= --}}
                            <div class="callout callout-info small text-muted mt-4">
                                <div class="row">
                                    <div class="col-md-6">
                                        <strong>Dibuat pada:</strong><br>
                                        {{ $simpanan->created_at->format('d F Y H:i') }}
                                    </div>
                                    <div class="col-md-6 text-md-end">
                                        <strong>Terakhir diupdate:</strong><br>
                                        {{ $simpanan->updated_at->format('d F Y H:i') }}
                                    </div>
                                </div>
                            </div>

                        </div> {{-- End Card Body --}}

                        <div class="card-footer">
                            <a href="{{ route('superadmin.simpanan') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-1"></i> Kembali
                            </a>

                            <a href="{{ route('superadmin.simpanan.edit', $simpanan->id) }}"
                                class="btn btn-warning float-end">
                                <i class="fas fa-edit me-1"></i> Edit Data
                            </a>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>