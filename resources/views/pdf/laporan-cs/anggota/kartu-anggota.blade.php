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
            font-size: 10px;
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

        .card {
            width: 100%;
            border: 2px solid #000;
            padding: 20px;
            box-sizing: border-box;
        }

        .card-header {
            text-align: center;
            margin-bottom: 20px;
        }

        .card-header img {
            width: 150px;
            height: auto;
        }

        .card-header h2 {
            margin: 5px 0 0 0;
            font-size: 14px;
        }

        .card-body {
            width: 100%;
        }

        .card-row {
            display: flex;
            margin-bottom: 8px;
            border-bottom: 1px dotted #ccc;
            padding-bottom: 4px;
        }

        .card-label {
            width: 150px;
            font-weight: bold;
            flex-shrink: 0;
        }

        .card-value {
            flex: 1;
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

    <div class="card">
        <div class="card-header">
            <img src="{{ public_path('img/logo-banner.jpg') }}" alt="Logo KSP KOPINKA">
            <h2>KARTU ANGGOTA</h2>
            <p>KSP KOPINKA</p>
        </div>

        <div class="card-body">
            <div class="card-row">
                <div class="card-label">No Anggota</div>
                <div class="card-value">: {{ $anggota->no_anggota }}</div>
            </div>
            <div class="card-row">
                <div class="card-label">Nama</div>
                <div class="card-value">: {{ $anggota->nama }}</div>
            </div>
            <div class="card-row">
                <div class="card-label">PIN</div>
                <div class="card-value">: {{ $anggota->pin }}</div>
            </div>
            <div class="card-row">
                <div class="card-label">Kelompok</div>
                <div class="card-value">: {{ $anggota->kelompok->nama ?? '-' }}</div>
            </div>
            <div class="card-row">
                <div class="card-label">Kantor</div>
                <div class="card-value">: {{ $anggota->kantor->nama_kantor ?? '-' }}</div>
            </div>
            <div class="card-row">
                <div class="card-label">Alamat</div>
                <div class="card-value">: {{ $anggota->alamat }}</div>
            </div>
            <div class="card-row">
                <div class="card-label">Tempat Lahir</div>
                <div class="card-value">: {{ $anggota->tempat_lahir }}</div>
            </div>
            <div class="card-row">
                <div class="card-label">Tanggal Lahir</div>
                <div class="card-value">: {{ \Carbon\Carbon::parse($anggota->tanggal_lahir)->format('d-m-Y') }}</div>
            </div>
            <div class="card-row">
                <div class="card-label">Jenis Kelamin</div>
                <div class="card-value">: {{ $anggota->jenis_kelamin }}</div>
            </div>
            <div class="card-row">
                <div class="card-label">Agama</div>
                <div class="card-value">: {{ $anggota->agama }}</div>
            </div>
            <div class="card-row">
                <div class="card-label">Telepon</div>
                <div class="card-value">: {{ $anggota->telepon }}</div>
            </div>
            <div class="card-row">
                <div class="card-label">No HP</div>
                <div class="card-value">: {{ $anggota->nomor_hp ?? '-' }}</div>
            </div>
            <div class="card-row">
                <div class="card-label">Jenis Identitas</div>
                <div class="card-value">: {{ $anggota->jenis_identitas }}</div>
            </div>
            <div class="card-row">
                <div class="card-label">No Identitas</div>
                <div class="card-value">: {{ $anggota->nomor_identitas }}</div>
            </div>
        </div>
    </div>
</body>

</html>
