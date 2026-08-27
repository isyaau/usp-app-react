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
            page-break-after: always;
        }

        .card-header {
            text-align: center;
            margin-bottom: 15px;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
        }

        .card-header h2 {
            margin: 5px 0 0 0;
            font-size: 14px;
        }

        .card-header p {
            margin: 2px 0 0 0;
            font-size: 10px;
            color: #666;
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

    @foreach($items as $item)
    <div class="card">
        <div class="card-header">
            <h2>KARTU SIMPANAN</h2>
            <p>KSP KOPINKA</p>
        </div>

        <div class="card-body">
            <div class="card-row">
                <div class="card-label">No. Rekening</div>
                <div class="card-value">: {{ $item->no_rekening ?? '—' }}</div>
            </div>
            <div class="card-row">
                <div class="card-label">No. Anggota</div>
                <div class="card-value">: {{ $item->anggota->no_anggota ?? '—' }}</div>
            </div>
            <div class="card-row">
                <div class="card-label">Nama</div>
                <div class="card-value">: {{ $item->anggota->nama ?? '—' }}</div>
            </div>
            <div class="card-row">
                <div class="card-label">Jenis Kelamin</div>
                <div class="card-value">: {{ $item->anggota->jenis_kelamin ?? '—' }}</div>
            </div>
            <div class="card-row">
                <div class="card-label">Tempat Lahir</div>
                <div class="card-value">: {{ $item->anggota->tempat_lahir ?? '—' }}</div>
            </div>
            <div class="card-row">
                <div class="card-label">Tanggal Lahir</div>
                <div class="card-value">: {{ \Carbon\Carbon::parse($item->anggota->tgl_lahir)->format('d/m/Y') ?? '—' }}</div>
            </div>
            <div class="card-row">
                <div class="card-label">Alamat</div>
                <div class="card-value">: {{ $item->anggota->alamat ?? '—' }}</div>
            </div>
            <div class="card-row">
                <div class="card-label">Kelurahan</div>
                <div class="card-value">: {{ $item->anggota->kelurahan->name ?? '—' }}</div>
            </div>
            <div class="card-row">
                <div class="card-label">Kota</div>
                <div class="card-value">: {{ $item->anggota->kota->name ?? '—' }}</div>
            </div>
            <div class="card-row">
                <div class="card-label">Provinsi</div>
                <div class="card-value">: {{ $item->anggota->provinsi->name ?? '—' }}</div>
            </div>
            <div class="card-row">
                <div class="card-label">Jenis Simpanan</div>
                <div class="card-value">: {{ $item->jenis_simpanan->nama ?? '—' }}</div>
            </div>
            <div class="card-row">
                <div class="card-label">Kantor</div>
                <div class="card-value">: {{ $item->kantor->nama_kantor ?? '—' }}</div>
            </div>
        </div>
    </div>
    @endforeach
</body>

</html>
