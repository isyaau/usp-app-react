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
        <div class="title">SURAT PERJANJIAN PINJAMAN</div>

        <p>Surat perjanjian ini dibuat pada tanggal {{ \Carbon\Carbon::parse($pinjaman->tanggal)->format('d-m-Y') }}
            antara:</p>

        <p class="text-justify">
            <strong>PIHAK PERTAMA</strong>, yaitu PT. Koperasi Simpan Pinjam KOPINKA, dalam hal ini diwakili oleh
            pimpinan koperasi, yang berkedudukan di kantor KSP KOPINKA, selanjutnya disebut sebagai "PIHAK PERTAMA".
        </p>

        <p class="text-justify">
            <strong>PIHAK KEDUA</strong>, yaitu:
        </p>

        <table style="width: 90%; margin-left: 20px; border-collapse: collapse; margin-bottom: 15px;">
            <tr>
                <td style="width: 160px; padding: 2px 5px;">No Anggota</td>
                <td style="padding: 2px 5px;">: {{ $pinjaman->anggota->no_anggota ?? '-' }}</td>
            </tr>
            <tr>
                <td style="padding: 2px 5px;">Nama Lengkap</td>
                <td style="padding: 2px 5px;">: {{ $pinjaman->anggota->nama ?? '-' }}</td>
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
            Bahwa PIHAK PERTAMA dengan ini setuju untuk memberikan pinjaman kepada PIHAK KEDUA dengan ketentuan dan
            syarat-syarat sebagai berikut:
        </p>

        <p class="text-justify">
            <strong>Pasal 1 - Jenis dan Jumlah Pinjaman</strong><br>
            PIHAK PERTAMA memberikan pinjaman kepada PIHAK KEDUA dengan rincian sebagai berikut:
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
            <tr>
                <td style="padding: 2px 5px;">Suku Bunga</td>
                <td style="padding: 2px 5px;">: {{ $pinjaman->bunga }}% per bulan</td>
            </tr>
            <tr>
                <td style="padding: 2px 5px;">Jangka Waktu</td>
                <td style="padding: 2px 5px;">: {{ $pinjaman->jangka_waktu }} bulan</td>
            </tr>
            <tr>
                <td style="padding: 2px 5px;">Angsuran Per Bulan</td>
                <td style="padding: 2px 5px;">: Rp {{ number_format($pinjaman->angsuran_per_bulan, 0, ',', '.') }}</td>
            </tr>
        </table>

        <p class="text-justify">
            <strong>Pasal 2 - Ketentuan Pembayaran</strong><br>
            PIHAK KEDUA wajib membayar angsuran setiap bulannya sesuai jadwal yang telah ditentukan. Pembayaran
            paling lambat diterima pada tanggal jatuh tempo yang telah ditetapkan oleh PIHAK PERTAMA.
        </p>

        <p class="text-justify">
            <strong>Pasal 3 - Denda Keterlambatan</strong><br>
            Apabila PIHAK KEDUA terlambat membayar angsuran, maka akan dikenakan denda keterlambatan sesuai
            ketentuan yang berlaku di KSP KOPINKA.
        </p>

        <p class="text-justify">
            <strong>Pasal 4 - Wanprestasi</strong><br>
            Apabila PIHAK KEDUA tidak memenuhi kewajibannya, maka PIHAK PERTAMA berhak menagih seluruh sisa
            pinjaman yang belum lunas dan/atau menyita jaminan yang telah diberikan.
        </p>

        <p class="text-justify">
            <strong>Pasal 5 - Penyelesaian Sengketa</strong><br>
            Apabila timbul perselisihan yang tidak dapat diselesaikan secara musyawarah untuk mufakat, maka Para
            Pihak sepakat untuk menyelesaikannya melalui jalur hukum yang berlaku di Republik Indonesia.
        </p>

        <p class="text-justify">
            Demikian surat perjanjian ini dibuat dengan sebenar-benarnya dan berlaku efektif sejak tanggal
            penandatanganan.
        </p>

        <div class="signature-area">
            <div class="sig-block">
                <div>PIHAK PERTAMA</div>
                <div class="sig-line">&nbsp;</div>
                <div>(___________________)</div>
            </div>
            <div class="sig-block">
                <div>PIHAK KEDUA</div>
                <div class="sig-line">&nbsp;</div>
                <div>({{ $pinjaman->anggota->nama ?? '___________________' }})</div>
            </div>
        </div>
    </div>
</body>

</html>
