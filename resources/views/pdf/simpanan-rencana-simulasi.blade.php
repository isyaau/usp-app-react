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
            font-size: 12px;
            text-align: center;
        }

        h3 {
            margin: 8px 0 3px 0;
            font-size: 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 2px;
            vertical-align: top;
        }

        th {
            background-color: #eee;
            text-align: center;
            font-weight: bold;
            font-size: 9px;
        }

        .params {
            margin: 5px 0;
            width: 100%;
        }

        .params td {
            border: 1px solid #000;
            padding: 3px;
        }

        .keterangan {
            font-size: 8px;
            color: #333;
            margin: 3px 0 8px 0;
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
        <h2>Simulasi Setoran Rencana</h2>

        <p class="keterangan">
            No. Bukti: {{ $rencana->no_bukti }}
            &nbsp;|&nbsp; Target: Rp {{ number_format($hasil['nominal'], 0, ',', '.') }}
            &nbsp;|&nbsp; Kantor: {{ $rencana->kantor?->nama_kantor ?? '-' }}
        </p>

        <table class="params">
            <tr>
                <th>Nominal Target</th>
                <th>Bagi Hasil / Tahun</th>
                <th>Jangka Waktu</th>
                <th>Periode Setoran</th>
                <th>Setoran Pokok / Periode</th>
                <th>Total Bagi Hasil</th>
                <th>Saldo Akhir</th>
            </tr>
            <tr>
                <td>Rp {{ number_format($hasil['nominal'], 0, ',', '.') }}</td>
                <td>{{ $hasil['bunga_tahun'] }}%</td>
                <td>{{ $rencana->jangka_waktu }} {{ $rencana->satuan }}</td>
                <td>{{ $hasil['jumlah_periode'] }} {{ $hasil['satuan_periode'] }}</td>
                <td>Rp {{ number_format($hasil['setoran_pokok'], 0, ',', '.') }}</td>
                <td>Rp {{ number_format($hasil['total_bunga'], 0, ',', '.') }}</td>
                <td>Rp {{ number_format($hasil['saldo_akhir'], 0, ',', '.') }}</td>
            </tr>
        </table>

        <h3>Tabel Jadwal Setoran</h3>
        <table>
            <thead>
                <tr>
                    <th width="12%">Periode</th>
                    <th width="22%">Setoran</th>
                    <th width="22%">Bagi Hasil</th>
                    <th width="22%">Total Setor</th>
                    <th width="22%">Saldo Akhir</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($hasil['jadwal'] as $baris)
                <tr>
                    <td style="text-align:center">{{ $baris['ke'] }}</td>
                    <td style="text-align:right">Rp {{ number_format($baris['setoran'], 0, ',', '.') }}</td>
                    <td style="text-align:right">Rp {{ number_format($baris['bunga'], 0, ',', '.') }}</td>
                    <td style="text-align:right">Rp {{ number_format($baris['total_setor'], 0, ',', '.') }}</td>
                    <td style="text-align:right">Rp {{ number_format($baris['saldo'], 0, ',', '.') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</body>

</html>
