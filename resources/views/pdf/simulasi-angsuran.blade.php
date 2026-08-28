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
        <h2>{{ $judul ?? 'Simulasi Angsuran Pinjaman' }}</h2>

        @if (! empty($pinjaman))
        <p class="keterangan">
            No. Pinjaman: {{ $pinjaman->no_pinjaman }}
            &nbsp;|&nbsp; Anggota: {{ $pinjaman->anggota->no_anggota ?? '-' }} - {{ $pinjaman->anggota->nama ?? '-' }}
            &nbsp;|&nbsp; Produk: {{ $pinjaman->jenisPinjaman->nama ?? '-' }}
        </p>
        @endif

        <table class="params">
            <tr>
                <th>Plafon</th>
                <th>Bunga / Tahun</th>
                <th>Jangka Waktu</th>
                <th>Metode</th>
                <th>Nominal Angsuran</th>
                <th>Total Bunga</th>
                <th>Total Pembayaran</th>
            </tr>
            <tr>
                <td>Rp {{ number_format($plafon, 0, ',', '.') }}</td>
                <td>{{ $bunga }}%</td>
                <td>{{ $jangka_waktu }} {{ $satuan }}</td>
                <td>{{ $hasil['metode'] }}</td>
                <td>Rp {{ number_format($hasil['nominal_angsuran'], 2, ',', '.') }}</td>
                <td>Rp {{ number_format($hasil['total_bunga'], 2, ',', '.') }}</td>
                <td>Rp {{ number_format($hasil['nominal_angsuran'] * $hasil['jumlah_periode'], 2, ',', '.') }}</td>
            </tr>
        </table>

        <h3>Tabel Jadwal Angsuran</h3>
        <table>
            <thead>
                <tr>
                    <th width="8%">Angsuran ke</th>
                    <th width="23%">Pokok</th>
                    <th width="23%">Bunga</th>
                    <th width="23%">Angsuran</th>
                    <th width="23%">Sisa Pokok</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($hasil['jadwal'] as $baris)
                <tr>
                    <td style="text-align:center">{{ $baris['ke'] }}</td>
                    <td style="text-align:right">Rp {{ number_format($baris['pokok'], 0, ',', '.') }}</td>
                    <td style="text-align:right">Rp {{ number_format($baris['bunga'], 0, ',', '.') }}</td>
                    <td style="text-align:right">Rp {{ number_format($baris['angsuran'], 0, ',', '.') }}</td>
                    <td style="text-align:right">Rp {{ number_format($baris['sisa'], 0, ',', '.') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</body>

</html>