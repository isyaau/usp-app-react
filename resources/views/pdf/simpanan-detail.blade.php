<!DOCTYPE html>
<html>

<head>
    <style>
        @page {
            size: A4 portrait;
            margin: 100px 20px 70px 20px;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 9px;
        }

        header {
            position: fixed;
            top: -90px;
            left: 0;
            right: 0;
            height: 80px;
        }

        footer {
            position: fixed;
            bottom: -60px;
            left: 0;
            right: 0;
            height: 50px;
        }

        h2 {
            margin: 0 0 8px 0;
            font-size: 13px;
            text-align: center;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 4px;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 3px;
            vertical-align: top;
        }

        th {
            background-color: #eee;
            text-align: left;
            font-weight: bold;
            font-size: 9px;
            width: 25%;
        }

        .status-aktif {
            font-weight: bold;
            color: #006600;
        }

        .status-nonaktif {
            font-weight: bold;
            color: #990000;
        }

        .ttd {
            margin-top: 20px;
        }

        .ttd img {
            width: 150px;
            border: 1px solid #000;
            padding: 4px;
        }
    </style>
</head>

<body>
    <header>
        @include('pdf.partials.header')
    </header>

    <footer>
        @include('pdf.partials.footer')
    </footer>

    <div class="content">
        <h2>Data Simpanan</h2>

        <table>
            <tr>
                <th>No. Rekening</th>
                <td>{{ $simpanan->no_rekening }}</td>
                <th>Tanggal</th>
                <td>{{ $simpanan->tanggal ? \Carbon\Carbon::parse($simpanan->tanggal)->format('d-m-Y') : '-' }}</td>
            </tr>
            <tr>
                <th>Produk Simpanan</th>
                <td>{{ $simpanan->jenis_simpanan?->nama ?? '-' }}</td>
                <th>Status</th>
                <td>
                    <span class="{{ $simpanan->aktif === '1' ? 'status-aktif' : 'status-nonaktif' }}">
                        {{ $simpanan->aktif === '1' ? 'Aktif' : 'Nonaktif' }}
                    </span>
                </td>
            </tr>
            <tr>
                <th>Bagi Hasil / Tahun</th>
                <td>{{ ($simpanan->jenis_simpanan?->bunga ?? $simpanan->bunga) }}%</td>
                <th>QQ</th>
                <td>{{ $simpanan->qq ?? '-' }}</td>
            </tr>
            <tr>
                <th>Nominal Setoran Awal</th>
                <td>Rp {{ number_format((float) $simpanan->nominal_setor, 0, ',', '.') }}</td>
                <th>Notifikasi SMS</th>
                <td>{{ $simpanan->sms === '1' ? 'Aktif' : 'Nonaktif' }}</td>
            </tr>
            <tr>
                <th>Marketing</th>
                <td>{{ $simpanan->marketing?->nama ?? '-' }}</td>
                <th>Kantor</th>
                <td>{{ $simpanan->kantor?->nama_kantor ?? '-' }}</td>
            </tr>
        </table>

        <h2 style="font-size:11px;margin-top:12px">Data Anggota</h2>
        <table>
            <tr>
                <th>Nama</th>
                <td>{{ $simpanan->anggota?->nama ?? '-' }}</td>
                <th>No. Anggota</th>
                <td>{{ $simpanan->anggota?->no_anggota ?? '-' }}</td>
            </tr>
            <tr>
                <th>Alamat</th>
                <td colspan="3">{{ $simpanan->anggota?->alamat ?? '-' }}</td>
            </tr>
            <tr>
                <th>Telepon</th>
                <td>{{ $simpanan->anggota?->telepon ?? '-' }}</td>
                <th>No. HP</th>
                <td>{{ $simpanan->anggota?->no_hp ?? '-' }}</td>
            </tr>
        </table>

        <h2 style="font-size:11px;margin-top:12px">Blokir</h2>
        <table>
            <tr>
                <th>Blokir Simpanan</th>
                <td>{{ $simpanan->blokir_simpanan === '1' ? 'Diblokir' : 'Tidak' }}</td>
                <th>Blokir Nominal</th>
                <td>{{ $simpanan->blokir_nominal === '1' ? 'Rp '.number_format((float) $simpanan->nominal_blokir, 0, ',', '.') : '-' }}</td>
            </tr>
            <tr>
                <th>Blokir s/d Tanggal</th>
                <td colspan="3">{{ ($simpanan->blokir_tgl === '1' && $simpanan->tgl_blokir) ? \Carbon\Carbon::parse($simpanan->tgl_blokir)->format('d-m-Y') : '-' }}</td>
            </tr>
        </table>

        @if ($simpanan->ttd)
        <div class="ttd">
            <strong>Tanda Tangan</strong><br><br>
            <img src="{{ public_path('storage/'.$simpanan->ttd) }}" alt="Tanda tangan">
        </div>
        @endif
    </div>
</body>

</html>
