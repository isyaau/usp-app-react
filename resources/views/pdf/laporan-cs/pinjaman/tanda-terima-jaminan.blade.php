<!DOCTYPE html>
<html>

<head>
    <style>
        @page {
            size: A4 portrait;
            margin: 50px 15px 70px 15px;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 11px;
            line-height: 1.6;
            margin: 0;
            padding: 0;
        }

        header {
            position: fixed;
            top: -40px;
            left: 0;
            right: 0;
            height: 40px;
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

        .title {
            text-align: center;
            font-size: 14px;
            font-weight: bold;
            text-decoration: underline;
            margin-bottom: 15px;
        }

        .text-justify {
            text-align: justify;
        }

        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            margin-bottom: 15px;
        }

        table.data-table th,
        table.data-table td {
            border: 1px solid #000;
            padding: 4px 6px;
            vertical-align: top;
        }

        table.data-table th {
            background-color: #eee;
            text-align: center;
            font-weight: bold;
            font-size: 10px;
        }

        .signature-area {
            margin-top: 40px;
            display: flex;
            justify-content: space-between;
        }

        .sig-block {
            width: 45%;
            text-align: center;
        }

        .sig-line {
            border-top: 1px solid #000;
            margin-top: 60px;
            padding-top: 5px;
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
        <div class="title">TANDA TERIMA JAMINAN</div>

        <p class="text-justify">
            Pihak PT. Koperasi Simpan Pinjam KOPINKA dengan ini mengakui telah menerima jaminan dari anggota
            berikut ini:
        </p>

        <table style="width: 90%; margin-left: 20px; border-collapse: collapse; margin-bottom: 15px;">
            <tr>
                <td style="width: 160px; padding: 2px 5px;">Nama Anggota</td>
                <td style="padding: 2px 5px;">: {{ $pinjaman->anggota->nama ?? '-' }}</td>
            </tr>
            <tr>
                <td style="padding: 2px 5px;">No Anggota</td>
                <td style="padding: 2px 5px;">: {{ $pinjaman->anggota->no_anggota ?? '-' }}</td>
            </tr>
            <tr>
                <td style="padding: 2px 5px;">Kelompok</td>
                <td style="padding: 2px 5px;">: {{ $pinjaman->anggota->kelompok->nama ?? '-' }}</td>
            </tr>
        </table>

        <p class="text-justify">
            Berkaitan dengan pinjaman dengan data sebagai berikut:
        </p>

        <table style="width: 90%; margin-left: 20px; border-collapse: collapse; margin-bottom: 15px;">
            <tr>
                <td style="width: 160px; padding: 2px 5px;">No Pinjaman</td>
                <td style="padding: 2px 5px;">: {{ $pinjaman->no_pinjaman }}</td>
            </tr>
            <tr>
                <td style="padding: 2px 5px;">Plafon Pinjaman</td>
                <td style="padding: 2px 5px;">: Rp {{ number_format($pinjaman->plafon, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td style="padding: 2px 5px;">Tanggal</td>
                <td style="padding: 2px 5px;">: {{ \Carbon\Carbon::parse($pinjaman->tanggal)->format('d-m-Y') }}</td>
            </tr>
        </table>

        <p class="text-justify">
            Berikut adalah jaminan yang telah diterima:
        </p>

        <table class="data-table" width="100%">
            <thead>
                <tr>
                    <th width="5%">No</th>
                    <th width="25%">Nama Jaminan</th>
                    <th width="40%">Keterangan</th>
                    <th width="30%">Nominal</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($jaminan as $j)
                <tr>
                    <td style="text-align: center;">{{ $loop->iteration }}</td>
                    <td>{{ $j->nama ?? '-' }}</td>
                    <td>{{ $j->keterangan ?? '-' }}</td>
                    <td style="text-align: right;">Rp {{ number_format($j->nominal, 0, ',', '.') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <p class="text-justify">
            Jaminan di atas diterima dalam kondisi baik dan akan dikembalikan kepada anggota setelah seluruh
            kewajiban pinjaman telah lunas.
        </p>

        <div class="signature-area">
            <div class="sig-block">
                <div>Yang Menerima Jaminan</div>
                <div class="sig-line">&nbsp;</div>
                <div>(___________________)</div>
            </div>
            <div class="sig-block">
                <div>Anggota (Pemberi Jaminan)</div>
                <div class="sig-line">&nbsp;</div>
                <div>({{ $pinjaman->anggota->nama ?? '___________________' }})</div>
            </div>
        </div>
    </div>
</body>

</html>
