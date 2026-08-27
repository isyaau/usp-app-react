<!DOCTYPE html>
<html>

<head>
    <style>
        @page {
            size: A4 landscape;
            margin: 100px 15px 70px 15px;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 9px;
            margin: 0;
            padding: 0;
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

        .content {
            margin-bottom: 10px;
        }

        h2 {
            margin: 0 0 5px 0;
            font-size: 11px;
            text-align: center;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }

        th,
        td {
            border: 1px solid #d1d5db;
            padding: 4px;
            vertical-align: top;
            word-wrap: break-word;
        }

        th {
            background-color: #f3f4f6;
            text-align: center;
            font-weight: bold;
            font-size: 9px;
        }

        .group-header {
            background-color: #e5e7eb;
            font-weight: bold;
            font-size: 9px;
            padding: 6px;
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
        <h2>{{ $variantTitle ?? 'Rekening Koran Simpanan' }}</h2>
        <p style="text-align:center;font-size:10px;color:#666;">{{ $generatedAt }}</p>

        @forelse($items as $noRek => $transaksi)
        <div style="margin-bottom:15px;">
            <div class="group-header">
                No. Rekening: {{ $noRek }} | Anggota: {{ $transaksi->first()->anggota->nama ?? '—' }} | No. Anggota: {{ $transaksi->first()->anggota->no_anggota ?? '—' }}
            </div>
            <table>
                <thead>
                    <tr>
                        <th style="width:4%;">No</th>
                        <th style="width:12%;">No Transaksi</th>
                        <th style="width:10%;">Tanggal</th>
                        <th style="width:10%;">Kode Transaksi</th>
                        <th style="width:24%;">Keterangan</th>
                        <th style="width:14%;">Setoran</th>
                        <th style="width:14%;">Penarikan</th>
                        <th style="width:12%;">Saldo</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transaksi as $i => $t)
                    <tr>
                        <td style="text-align:center;">{{ $i + 1 }}</td>
                        <td>{{ $t->no_transaksi ?? '—' }}</td>
                        <td style="text-align:center;">{{ \Carbon\Carbon::parse($t->tanggal)->format('d/m/Y') ?? '—' }}</td>
                        <td style="text-align:center;">{{ $t->kode_transaksi ?? '—' }}</td>
                        <td>{{ $t->keterangan ?? '—' }}</td>
                        <td style="text-align:right;">{{ $t->setoran ? 'Rp ' . number_format($t->setoran, 0, ',', '.') : '—' }}</td>
                        <td style="text-align:right;">{{ $t->penarikan ? 'Rp ' . number_format($t->penarikan, 0, ',', '.') : '—' }}</td>
                        <td style="text-align:right;">Rp {{ number_format($t->saldo ?? 0, 0, ',', '.') }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" style="text-align:center;">Tidak ada data.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @empty
        <p style="text-align:center;">Tidak ada data.</p>
        @endforelse
    </div>
</body>

</html>
