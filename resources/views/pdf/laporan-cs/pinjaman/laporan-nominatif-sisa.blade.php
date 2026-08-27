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
            font-size: 8px;
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
            font-size: 11px;
            text-align: center;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 2px;
            vertical-align: top;
            word-wrap: break-word;
        }

        th {
            background-color: #eee;
            text-align: center;
            font-weight: bold;
            font-size: 8px;
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

    <div class="content">
        <h2>Laporan Nominatif Pinjaman (Sisa)</h2>

        <table width="100%" border="1" cellspacing="0" cellpadding="5">
            <thead>
                <tr>
                    <th width="4%">No</th>
                    <th width="12%">No Pinjaman</th>
                    <th width="9%">No Anggota</th>
                    <th width="15%">Nama</th>
                    <th width="13%">Kelompok</th>
                    <th width="12%">Jenis</th>
                    <th width="12%">Plafon</th>
                    <th width="7%">Ang. ke</th>
                    <th width="8%">Jangka Waktu</th>
                    <th width="10%">Kantor</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($pinjaman as $p)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $p->no_pinjaman }}</td>
                    <td>{{ $p->anggota->no_anggota ?? '-' }}</td>
                    <td>{{ $p->anggota->nama ?? '-' }}</td>
                    <td>{{ $p->anggota->kelompok->nama ?? '-' }}</td>
                    <td>{{ $p->jenisPinjaman->nama ?? '-' }}</td>
                    <td>Rp {{ number_format($p->plafon, 0, ',', '.') }}</td>
                    <td>{{ $p->angsuranke }}</td>
                    <td>{{ $p->jangka_waktu }}</td>
                    <td>{{ $p->kantor->nama_kantor ?? '-' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</body>

</html>
