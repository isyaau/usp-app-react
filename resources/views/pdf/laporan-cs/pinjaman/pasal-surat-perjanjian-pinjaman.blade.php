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

        .info-box {
            border: 1px solid #000;
            padding: 10px;
            margin-bottom: 15px;
        }

        .info-row {
            display: flex;
            margin-bottom: 2px;
        }

        .info-label {
            width: 160px;
            font-weight: bold;
            flex-shrink: 0;
        }

        .info-value {
            flex: 1;
        }

        .pasal {
            margin-bottom: 12px;
        }

        .pasal-title {
            font-weight: bold;
            text-decoration: underline;
            margin-bottom: 4px;
        }

        .pasal-body {
            text-align: justify;
            padding-left: 20px;
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
        <div class="title">PASAL-PASAL SURAT PERJANJIAN PINJAMAN</div>

        <div class="info-box">
            <div class="info-row">
                <div class="info-label">No Pinjaman</div>
                <div class="info-value">: {{ $pinjaman->no_pinjaman }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Nama Anggota</div>
                <div class="info-value">: {{ $pinjaman->anggota->nama ?? '-' }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Tanggal</div>
                <div class="info-value">: {{ \Carbon\Carbon::parse($pinjaman->tanggal)->format('d-m-Y') }}</div>
            </div>
        </div>

        <div class="pasal">
            <div class="pasal-title">Pasal 1 - Pihak-Pihak Yang Berjanji</div>
            <div class="pasal-body">
                <p>Yang bertanda tangan di bawah ini:</p>
                <p>
                    <strong>Pihak Pertama</strong>, PT. Koperasi Simpan Pinjam KOPINKA, yang selanjutnya disebut
                    sebagai "PIHAK PERTAMA", dengan ini mewakili kepentingan koperasi.
                </p>
                <p>
                    <strong>Pihak Kedua</strong>, seorang anggota KSP KOPINKA dengan data sebagai berikut:
                </p>
                <table style="width: 80%; margin-left: 20px; border-collapse: collapse;">
                    <tr>
                        <td style="width: 150px; padding: 2px 5px;">No Anggota</td>
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
                <p>Yang selanjutnya disebut sebagai "PIHAK KEDUA".</p>
                <p>Pihak Pertama dan Pihak Kedua secara bersama-sama disebut sebagai "Para Pihak".</p>
            </div>
        </div>

        <div class="pasal">
            <div class="pasal-title">Pasal 2 - Objek Perjanjian</div>
            <div class="pasal-body">
                <p>Pihak Pertama dengan ini setuju memberikan pinjaman kepada Pihak Kedua dengan ketentuan sebagai
                    berikut:</p>
                <table style="width: 80%; margin-left: 20px; border-collapse: collapse;">
                    <tr>
                        <td style="width: 150px; padding: 2px 5px;">Jenis Pinjaman</td>
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
            </div>
        </div>

        <div class="pasal">
            <div class="pasal-title">Pasal 3 - Ketentuan Pembayaran Angsuran</div>
            <div class="pasal-body">
                <p>1. Pihak Kedua wajib membayar angsuran setiap bulannya sesuai dengan jadwal yang telah ditentukan
                    oleh Pihak Pertama.</p>
                <p>2. Pembayaran angsuran paling lambat diterima oleh Pihak Pertama pada tanggal jatuh tempo yang
                    telah ditetapkan.</p>
                <p>3. Setiap pembayaran angsuran akan diberikan tanda bukti resmi dari Pihak Pertama.</p>
                <p>4. Pelunasan lebih awal diperbolehkan dengan ketentuan seluruh sisa pokok pinjaman dan bunga
                    berjalan harus dibayar lunas.</p>
            </div>
        </div>

        <div class="pasal">
            <div class="pasal-title">Pasal 4 - Denda Keterlambatan Pembayaran</div>
            <div class="pasal-body">
                <p>Apabila Pihak Kedua terlambat membayar angsuran, maka Pihak Kedua akan dikenakan denda
                    keterlambatan sesuai dengan ketentuan yang berlaku di KSP KOPINKA. Denda tersebut merupakan
                    bagian yang tidak terpisahkan dari perjanjian ini.</p>
            </div>
        </div>

        <div class="pasal">
            <div class="pasal-title">Pasal 5 - Wanprestasi dan Penyitaan Jaminan</div>
            <div class="pasal-body">
                <p>1. Apabila Pihak Kedua melakukan wanprestasi (ingkar janji) berupa tidak membayar angsuran
                    melebihi jangka waktu yang ditentukan, maka Pihak Pertama berhak menagih seluruh sisa
                    pinjaman yang belum lunas.</p>
                <p>2. Apabila Pihak Kedua tidak dapat memenuhi kewajibannya, maka jaminan yang telah diberikan
                    dapat disita dan dijual untuk melunasi seluruh hutang pinjaman Pihak Kedua.</p>
                <p>3. Sisa hasil penjualan jaminan setelah dipotong seluruh kewajiban Pihak Kedua, akan
                    dikembalikan kepada Pihak Kedua.</p>
            </div>
        </div>

        <div class="pasal">
            <div class="pasal-title">Pasal 6 - Penyelesaian Sengketa</div>
            <div class="pasal-body">
                <p>Apabila timbul perselisihan antara Para Pihak yang tidak dapat diselesaikan secara musyawarah
                    untuk mufakat, maka Para Pihak sepakat untuk menyelesaikannya melalui jalur hukum yang berlaku
                    di Republik Indonesia.</p>
            </div>
        </div>

        <div class="signature-area">
            <div class="sig-block">
                <div>Pihak Pertama</div>
                <div class="sig-line">&nbsp;</div>
                <div>(___________________)</div>
            </div>
            <div class="sig-block">
                <div>Pihak Kedua</div>
                <div class="sig-line">&nbsp;</div>
                <div>({{ $pinjaman->anggota->nama ?? '___________________' }})</div>
            </div>
        </div>
    </div>
</body>

</html>
