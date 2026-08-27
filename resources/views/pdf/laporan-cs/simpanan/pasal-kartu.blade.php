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
        <div class="title">PASAL-PASAL SYARAT DAN KETENTUAN KARTU SIMPANAN</div>

        <div class="info-box">
            <div class="info-row">
                <div class="info-label">Koperasi</div>
                <div class="info-value">: PT. Koperasi Simpan Pinjam KOPINKA</div>
            </div>
        </div>

        <div class="pasal">
            <div class="pasal-title">Pasal 1 - Ketentuan Umum</div>
            <div class="pasal-body">
                <p>1. Kartu simpanan ini merupakan bukti keanggotaan dan kepemilikan rekening simpanan pada KSP KOPINKA.</p>
                <p>2. Setiap anggota berhak memiliki kartu simpanan sesuai dengan jenis simpanan yang dimiliki.</p>
                <p>3. Kartu simpanan tidak dapat dialihkan kepada pihak lain.</p>
            </div>
        </div>

        <div class="pasal">
            <div class="pasal-title">Pasal 2 - Jenis Simpanan</div>
            <div class="pasal-body">
                <p>1. Simpanan Pokok adalah simpanan yang wajib dibayarkan oleh setiap anggota saat mendaftar menjadi anggota.</p>
                <p>2. Simpanan Wajib adalah simpanan yang wajib dibayarkan secara berkala oleh setiap anggota setiap bulannya.</p>
                <p>3. Simpanan Sukarela adalah simpanan yang dibayarkan secara sukarela oleh anggota dengan jumlah dan waktu yang dapat ditentukan sendiri.</p>
            </div>
        </div>

        <div class="pasal">
            <div class="pasal-title">Pasal 3 - Setoran dan Penarikan</div>
            <div class="pasal-body">
                <p>1. Setoran simpanan dapat dilakukan di kantor KSP KOPINKA atau melalui petugas lapangan.</p>
                <p>2. Penarikan simpanan dapat dilakukan dengan mengisi formulir penarikan dan menyerahkan kartu simpanan.</p>
                <p>3. Penarikan simpanan wajib dilakukan di kantor KSP KOPINKA dengan membawa kartu simpanan asli.</p>
            </div>
        </div>

        <div class="pasal">
            <div class="pasal-title">Pasal 4 - Bagi Hasil</div>
            <div class="pasal-body">
                <p>1. Anggota berhak mendapatkan bagi hasil atas simpanan yang dimiliki sesuai dengan ketentuan yang berlaku.</p>
                <p>2. Perhitungan bagi hasil dilakukan secara periodik sesuai dengan kebijakan KSP KOPINKA.</p>
                <p>3. Bagi hasil akan ditambahkan ke dalam saldo simpanan anggota.</p>
            </div>
        </div>

        <div class="pasal">
            <div class="pasal-title">Pasal 5 - Kehilangan Kartu</div>
            <div class="pasal-body">
                <p>1. Apabila kartu simpanan hilang, anggota wajib segera melaporkan ke kantor KSP KOPINKA.</p>
                <p>2. Penggantian kartu simpanan yang hilang dikenakan biaya sesuai ketentuan yang berlaku.</p>
                <p>3. KSP KOPINKA tidak bertanggung jawab atas penyalahgunaan kartu simpanan yang hilang sebelum dilaporkan.</p>
            </div>
        </div>

        <div class="pasal">
            <div class="pasal-title">Pasal 6 - Penutupan Rekening</div>
            <div class="pasal-body">
                <p>1. Anggota dapat menutup rekening simpanan dengan mengajukan permohonan tertulis.</p>
                <p>2. Penutupan rekening hanya dapat dilakukan di kantor KSP KOPINKA dengan menyerahkan kartu simpanan asli.</p>
                <p>3. Saldo simpanan yang tersisa akan dikembalikan kepada anggota setelah proses penutupan rekening selesai.</p>
            </div>
        </div>

        <div class="pasal">
            <div class="pasal-title">Pasal 7 - Kewajiban Anggota</div>
            <div class="pasal-body">
                <p>1. Anggota wajib mematuhi seluruh ketentuan dan peraturan yang berlaku di KSP KOPINKA.</p>
                <p>2. Anggota wajib membayar simpanan wajib secara tepat waktu setiap bulannya.</p>
                <p>3. Anggota wajib menjaga kerahasiaan nomor rekening dan kartu simpanannya.</p>
            </div>
        </div>

        <div class="pasal">
            <div class="pasal-title">Pasal 8 - Pemanggilan dan Penggunaan Data</div>
            <div class="pasal-body">
                <p>1. KSP KOPINKA berhak menghubungi anggota melalui data kontak yang terdaftar untuk keperluan operasional.</p>
                <p>2. Data pribadi anggota akan dijaga kerahasiaannya dan hanya digunakan untuk keperluan operasional koperasi.</p>
            </div>
        </div>

        <div class="pasal">
            <div class="pasal-title">Pasal 9 - Perubahan Ketentuan</div>
            <div class="pasal-body">
                <p>1. KSP KOPINKA berhak mengubah ketentuan yang berlaku dengan pemberitahuan terlebih dahulu kepada anggota.</p>
                <p>2. Perubahan ketentuan akan diberitahukan melalui media yang tersedia di kantor KSP KOPINKA.</p>
                <p>3. Anggota yang tidak keberatan dengan perubahan ketentuan dianggap telah menyetujui perubahan tersebut.</p>
            </div>
        </div>

        <div class="pasal">
            <div class="pasal-title">Pasal 10 - Penyelesaian Sengketa</div>
            <div class="pasal-body">
                <p>Apabila timbul perselisihan antara anggota dan KSP KOPINKA yang tidak dapat diselesaikan secara musyawarah
                    untuk mufakat, maka para pihak sepakat untuk menyelesaikannya melalui jalur hukum yang berlaku
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
                <div>(___________________)</div>
            </div>
        </div>
    </div>
</body>

</html>
