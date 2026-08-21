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
                <div class="col-md-8">
                    <div class="card card-info card-outline mb-4">
                        <div class="card-header">
                            <div class="card-title">Detail {{ $title }}</div>
                        </div>

                        <div class="card-body">
                            {{-- ======================= --}}
                            {{-- FIELD UTAMA --}}
                            {{-- ======================= --}}
                            <h5 class="mb-3">Field Utama</h5>
                            <table class="table table-bordered mb-4">
                                <tr>
                                    <th width="35%">Kode Produk</th>
                                    <td>{{ $kode }}</td>
                                </tr>
                                <tr>
                                    <th>Nama Produk</th>
                                    <td>{{ $nama }}</td>
                                </tr>
                                <tr>
                                    <th>Account Utama</th>
                                    <td>
                                        {{ $account_id ? \App\Models\Account::find($account_id)->no_account : '-' }} -
                                        {{ $account_id ? \App\Models\Account::find($account_id)->nama : '-' }}
                                    </td>
                                </tr>
                                <tr>
                                    <th>Minimum</th>
                                    <td>{{ $minimum ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>Mengendap</th>
                                    <td>{{ $mengendap ?? '-' }}</td>
                                </tr>
                            </table>

                            {{-- ======================= --}}
                            {{-- BUNGA --}}
                            {{-- ======================= --}}
                            <h5 class="mb-3">Bunga</h5>
                            <table class="table table-bordered mb-4">
                                <tr>
                                    <th>Jenis Bunga</th>
                                    <td>{{ $jenis_bunga == 1 ? 'Flat' : 'Bertingkat' }}</td>
                                </tr>
                                @if($jenis_bunga == 1)
                                <tr>
                                    <th>Bunga Flat (%)</th>
                                    <td>{{ $bungaJenis ?? '-' }}</td>
                                </tr>
                                @else
                                <tr>
                                    <th>Bunga Bertingkat</th>
                                    <td>
                                        <table class="table table-sm table-bordered mb-0">
                                            <thead>
                                                <tr>
                                                    <th>Minimal</th>
                                                    <th>Maksimal</th>
                                                    <th>Bunga (%)</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($tingkat as $row)
                                                <tr>
                                                    <td>{{ $row['minimal'] ?? '-' }}</td>
                                                    <td>{{ $row['maksimal'] ?? '-' }}</td>
                                                    <td>{{ $row['bunga'] ?? '-' }}</td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </td>
                                </tr>
                                @endif
                            </table>

                            {{-- ======================= --}}
                            {{-- RUMUS --}}
                            {{-- ======================= --}}
                            <h5 class="mb-3">Rumus</h5>
                            <table class="table table-bordered mb-4">
                                <tr>
                                    <th>Rumus Bunga</th>
                                    <td>{{ $rumus_bunga ?? '-' }} {{ $rumus_satu_bulan ? '(Satu Bulan)' : '' }}</td>
                                </tr>
                            </table>

                            {{-- ======================= --}}
                            {{-- AKUN LAIN: BIAYA / PAJAK / ANDROID --}}
                            {{-- ======================= --}}
                            <h5 class="mb-3">Akun Lain</h5>
                            <table class="table table-bordered mb-4">
                                <tr>
                                    <th>Account Bunga</th>
                                    <td>
                                        {{ $account_bunga ? \App\Models\Account::find($account_bunga)->no_account : '-' }} -
                                        {{ $account_bunga ? \App\Models\Account::find($account_bunga)->nama : '-' }}
                                    </td>
                                </tr>
                                <tr>
                                    <th>Biaya</th>
                                    <td>
                                        {{ $biaya ?? '-' }} -
                                        {{ $account_biaya ? \App\Models\Account::find($account_biaya)->no_account : '-' }}
                                    </td>
                                </tr>
                                <tr>
                                    <th>Pajak</th>
                                    <td>
                                        {{ $pajak ?? '-' }} - Saldo Pajak: {{ $saldo_pajak ?? '-' }} -
                                        {{ $account_pajak ? \App\Models\Account::find($account_pajak)->no_account : '-' }}
                                    </td>
                                </tr>
                                <tr>
                                    <th>Android</th>
                                    <td>
                                        {{ $android ?? '-' }} - Nominal: {{ $nominal_android ?? '-' }} -
                                        {{ $account_android ? \App\Models\Account::find($account_android)->no_account : '-' }}
                                    </td>
                                </tr>
                            </table>

                            {{-- ======================= --}}
                            {{-- JENIS / SIMPANAN --}}
                            {{-- ======================= --}}
                            <h5 class="mb-3">Jenis / Simpanan</h5>
                            <table class="table table-bordered mb-4">
                                <tr>
                                    <th>Jenis Simpanan</th>
                                    <td>
                                        <ul class="mb-0">
                                            @if($jenis) <li>{{ $jenis }}</li> @endif
                                            @if($saham) <li>Saham</li> @endif
                                            @if($setor_id) <li>Setor</li> @endif
                                            @if($tarik_id) <li>Tarik</li> @endif
                                            @if($nominal) <li>Nominal</li> @endif
                                            @if($insentif) <li>Insentif</li> @endif
                                        </ul>
                                    </td>
                                </tr>
                            </table>

                            {{-- ======================= --}}
                            {{-- SIMPANAN KODE --}}
                            {{-- ======================= --}}
                            <h5 class="mb-3">Kode Transaksi</h5>
                            <table class="table table-sm table-bordered mb-4">
                                <thead>
                                    <tr>
                                        <th>Kode</th>
                                        <th>Nama</th>
                                        <th>Account Debet</th>
                                        <th>Account Kredit</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($kodeRows as $row)
                                    <tr>
                                        <td>{{ $row['kode'] }}</td>
                                        <td>{{ $row['nama'] }}</td>
                                        <td>{{ $row['account_debet'] ?? '-' }}</td>
                                        <td>{{ $row['account_kredit'] ?? '-' }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>

                            {{-- ======================= --}}
                            {{-- INFO WAKTU --}}
                            {{-- ======================= --}}
                            <h5 class="mb-3">Info Waktu</h5>
                            <table class="table table-bordered mb-4">
                                <tr>
                                    <th>Dibuat</th>
                                    <td>{{ now()->format('d-m-Y H:i') }}</td>
                                </tr>
                                <tr>
                                    <th>Diubah</th>
                                    <td>{{ now()->format('d-m-Y H:i') }}</td>
                                </tr>
                            </table>
                        </div>

                        <div class="card-footer">
                            <a href="{{ route('superadmin.simpanan.produk-simpanan') }}" class="btn btn-secondary">
                                Kembali
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>