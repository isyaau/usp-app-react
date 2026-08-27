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
        <div class="title">SURAT PERNYATAAN</div>

        <p class="text-justify">
            Yang bertanda tangan di bawah ini:
        </p>

        <table style="width: 90%; margin-left: 20px; border-collapse: collapse; margin-bottom: 15px;">
            <tr>
                <td style="width: 160px; padding: 2px 5px;">Nama</td>
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
            <tr>
                <td style="padding: 2px 5px;">Kantor</td>
                <td style="padding: 2px 5px;">: {{ $pinjaman->kantor->nama_kantor ?? '-' }}</td>
            </tr>
        </table>

        <p class="text-justify">
            Dengan ini menyatakan dengan sebenar-benarnya bahwa saya telah mengajukan pinjaman kepada
            PT. Koperasi Simpan Pinjam KOPINKA dengan data sebagai berikut:
        </p>

        <table style="width: 90%; margin-left: 20px; border-collapse: collapse; margin-bottom: 15px;">
            <tr>
                <td style="width: 160px; padding: 2px 5px;">No Pinjaman</td>
                <td style="padding: 2px 5px;">: {{ $pinjaman->no_pinjaman }}</td>
            </tr>
            <tr>
                <td style="padding: 2px 5px;">Tanggal Pinjaman</td>
                <td style="padding: 2px 5px;">: {{ \Carbon\Carbon::parse($pinjaman->tanggal)->format('d-m-Y') }}</td>
            </tr>
            <tr>
                <td style="padding: 2px 5px;">Jenis Pinjaman</td>
                <td style="padding: 2px 5px;">: {{ $pinjaman->jenisPinjaman->nama ?? '-' }}</td>
            </tr>
            <tr>
                <td style="padding: 2px 5px;">Plafon Pinjaman</td>
                <td style="padding: 2px 5px;">: Rp {{ number_format($pinjaman->plafon, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td style="padding: 2px 5px;">Suku Bunga</td>
                <td style="padding: 2px 5px;">: {{ $pinjaman->bunga }}% per bulan</td>
            </tr>
            <tr>
                <td style="padding: 2px 5px;">Jangka Waktu</td>
                <td style="padding: 2px 5px;">: {{ $pinjaman->jangka_waktu }} bulan</td>
            </tr>
        </table>

        <p class="text-justify">
            Dengan menyatakan hal ini, saya menegaskan bahwa:
        </p>

        <ol class="text-justify">
            <li>Data-data yang saya berikan kepada KSP KOPINKA adalah benar dan dapat dipertanggungjawabkan.</li>
            <li>Saya bersedia mematuhi seluruh ketentuan dan peraturan yang berlaku di KSP KOPINKA terkait
                pinjaman yang saya ajukan.</li>
            <li>Saya bersedia membayar angsuran pokok dan bunga sesuai dengan jadwal yang telah ditentukan.</li>
            <li>Saya bersedia menanggung denda apabila terlambat membayar angsuran sesuai ketentuan yang berlaku.</li>
            <li>Saya bersedia menanggung segala konsekuensi hukum apabila saya melakukan wanprestasi terhadap
                perjanjian pinjaman ini.</li>
        </ol>

        <p class="text-justify">
            Demikian surat pernyataan ini saya buat dengan sebenar-benarnya dalam keadaan sadar dan tanpa
            paksaan dari pihak manapun.
        </p>

        <div class="signature-area">
            <div class="sig-block">
                <div>&nbsp;</div>
                <div class="sig-line">&nbsp;</div>
                <div>({{ $pinjaman->anggota->nama ?? '___________________' }})</div>
            </div>
        </div>
    </div>
</body>

</html>
