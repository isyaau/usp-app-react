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
        <div class="title">SURAT KUASA</div>

        <p>Surat kuasa ini dibuat pada tanggal {{ \Carbon\Carbon::parse($pinjaman->tanggal)->format('d-m-Y') }}
            oleh:</p>

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

        <p>Yang bertanda tangan di bawah ini disebut sebagai <strong>"PEMBERI KUASA"</strong>.</p>

        <p class="text-justify">
            Dengan ini memberikan kuasa kepada PT. Koperasi Simpan Pinjam KOPINKA sebagai <strong>"PEMERIMA KUASA"</strong>,
            yang dalam pelaksanaannya dapat mewakili PEMBERI KUASA untuk melakukan hal-hal sebagai berikut yang
            berkaitan dengan pinjaman:
        </p>

        <table style="width: 90%; margin-left: 20px; border-collapse: collapse; margin-bottom: 15px;">
            <tr>
                <td style="width: 160px; padding: 2px 5px;">No Pinjaman</td>
                <td style="padding: 2px 5px;">: {{ $pinjaman->no_pinjaman }}</td>
            </tr>
            <tr>
                <td style="padding: 2px 5px;">Jenis Pinjaman</td>
                <td style="padding: 2px 5px;">: {{ $pinjaman->jenisPinjaman->nama ?? '-' }}</td>
            </tr>
            <tr>
                <td style="padding: 2px 5px;">Plafon Pinjaman</td>
                <td style="padding: 2px 5px;">: Rp {{ number_format($pinjaman->plafon, 0, ',', '.') }}</td>
            </tr>
        </table>

        <ol class="text-justify">
            <li>Memproses pengajuan pinjaman atas nama PEMBERI KUASA sesuai ketentuan yang berlaku di KSP KOPINKA.</li>
            <li>Mencairkan dana pinjaman sesuai dengan plafon yang telah disetujui.</li>
            <li>Menerima pembayaran angsuran pokok dan bunga atas pinjaman tersebut.</li>
            <li>Mengelola segala hal yang berkaitan dengan pelunasan pinjaman, termasuk namun tidak terbatas pada
                proses pencatatan, pelaporan, dan peng administrasian.</li>
            <li>Melakukan penagihan terhadap sisa pinjaman apabila terjadi keterlambatan pembayaran angsuran.</li>
            <li>Mengurus proses penyitaan jaminan apabila PEMBERI KUASA melakukan wanprestasi sesuai ketentuan yang
                berlaku.</li>
        </ol>

        <p class="text-justify">
            Surat kuasa ini berlaku sejak tanggal penandatanganan hingga seluruh kewajiban pinjaman PEMBERI KUASA
            telah lunas, kecuali dicabut lebih dulu oleh PEMBERI KUASA.
        </p>

        <p class="text-justify">
            Demikian surat kuasa ini saya buat dengan sebenar-benarnya dalam keadaan sadar dan tanpa paksaan
            dari pihak manapun.
        </p>

        <div class="signature-area">
            <div class="sig-block">
                <div>PEMBERI KUASA</div>
                <div class="sig-line">&nbsp;</div>
                <div>({{ $pinjaman->anggota->nama ?? '___________________' }})</div>
            </div>
            <div class="sig-block">
                <div>PEMERIMA KUASA</div>
                <div class="sig-line">&nbsp;</div>
                <div>(___________________)</div>
            </div>
        </div>
    </div>
</body>

</html>
