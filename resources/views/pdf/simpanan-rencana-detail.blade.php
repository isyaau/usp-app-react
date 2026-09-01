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

        h3 {
            margin: 12px 0 4px 0;
            font-size: 11px;
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
        <h2>Data Simpanan Rencana</h2>

        <table>
            <tr>
                <th>No. Bukti</th>
                <td>{{ $rencana->no_bukti }}</td>
                <th>Tanggal Mulai</th>
                <td>{{ $rencana->tanggal_mulai ? \Carbon\Carbon::parse($rencana->tanggal_mulai)->format('d-m-Y') : '-' }}</td>
            </tr>
            <tr>
                <th>Jatuh Tempo</th>
                <td>{{ $rencana->tanggal_jatuhtempo ? \Carbon\Carbon::parse($rencana->tanggal_jatuhtempo)->format('d-m-Y') : '-' }}</td>
                <th>Jangka Waktu</th>
                <td>{{ $rencana->jangka_waktu }} {{ $rencana->satuan }}</td>
            </tr>
            <tr>
                <th>Nominal Target</th>
                <td>Rp {{ number_format((float) $rencana->nominal, 0, ',', '.') }}</td>
                <th>Bagi Hasil / Tahun</th>
                <td>{{ $rencana->bunga }}%</td>
            </tr>
            <tr>
                <th>Kantor</th>
                <td>{{ $rencana->kantor?->nama_kantor ?? '-' }}</td>
                <th>Jumlah Rekening</th>
                <td>{{ count($rekeningList) }}</td>
            </tr>
            <tr>
                <th>Keterangan</th>
                <td colspan="3">{{ $rencana->keterangan ?? '-' }}</td>
            </tr>
        </table>

        <h3>Rekening Terlibat</h3>
        <table>
            <thead>
                <tr>
                    <th width="25%">No. Rekening</th>
                    <th width="45%">Anggota</th>
                    <th width="30%">Produk</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($rekeningList as $rek)
                <tr>
                    <td style="text-align:center">{{ $rek->no_rekening }}</td>
                    <td>{{ $rek->anggota?->nama ?? '-' }} ({{ $rek->anggota?->no_anggota ?? '-' }})</td>
                    <td>{{ $rek->jenis_simpanan?->nama ?? '-' }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="3" style="text-align:center">Tidak ada rekening yang terlibat.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</body>

</html>
