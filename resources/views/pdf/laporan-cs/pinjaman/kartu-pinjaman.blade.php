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

        table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
            margin-top: 15px;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 3px;
            vertical-align: top;
            word-wrap: break-word;
        }

        th {
            background-color: #eee;
            text-align: center;
            font-weight: bold;
            font-size: 9px;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
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
            <h2>KARTU PINJAMAN</h2>
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
                <div class="card-label">Kelompok</div>
                <div class="card-value">: {{ $anggota->kelompok->nama ?? '-' }}</div>
            </div>
            <div class="card-row">
                <div class="card-label">Kantor</div>
                <div class="card-value">: {{ $anggota->kantor->nama_kantor ?? '-' }}</div>
            </div>

            <table width="100%" border="1" cellspacing="0" cellpadding="5">
                <thead>
                    <tr>
                        <th width="5%">No</th>
                        <th width="15%">No Pinjaman</th>
                        <th width="15%">Jenis</th>
                        <th width="15%">Plafon</th>
                        <th width="13%">Angsuran/bulan</th>
                        <th width="12%">Jangka Waktu</th>
                        <th width="10%">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($anggota->pinjaman as $p)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $p->no_pinjaman }}</td>
                        <td>{{ $p->jenisPinjaman->nama ?? '-' }}</td>
                        <td>Rp {{ number_format($p->plafon, 0, ',', '.') }}</td>
                        <td>Rp {{ number_format($p->angsuran_per_bulan, 0, ',', '.') }}</td>
                        <td>{{ $p->jangka_waktu }}</td>
                        <td>{{ $p->aktif ? 'Aktif' : 'Lunas' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</body>

</html>
