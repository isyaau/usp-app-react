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
            font-size: 9px;
            margin: 0;
            padding: 0;
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
            border: 1px solid #d1d5db;
            padding: 4px;
            vertical-align: top;
            word-wrap: break-word;
        }

        th {
            background-color: #f3f4f6;
            text-align: center;
            font-weight: bold;
            font-size: 9px;
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
        <h2>{{ $variantTitle ?? 'Jatuh Tempo Simpanan' }}</h2>
        <p style="text-align:center;font-size:10px;color:#666;">{{ $generatedAt }}</p>

        <table>
            <thead>
                <tr>
                    <th style="width:4%;">No</th>
                    <th style="width:12%;">No Rekening</th>
                    <th style="width:10%;">No Anggota</th>
                    <th style="width:16%;">Nama Anggota</th>
                    <th style="width:14%;">Jenis Simpanan</th>
                    <th style="width:14%;">Tanggal Jatuh Tempo</th>
                    <th style="width:14%;">Nominal</th>
                    <th style="width:10%;">Kantor</th>
                </tr>
            </thead>
            <tbody>
                @forelse($items as $i => $item)
                <tr>
                    <td style="text-align:center;">{{ $i + 1 }}</td>
                    <td>{{ $item->no_rekening ?? '—' }}</td>
                    <td>{{ $item->anggota->no_anggota ?? '—' }}</td>
                    <td>{{ $item->anggota->nama ?? '—' }}</td>
                    <td>{{ $item->jenis_simpanan->nama ?? '—' }}</td>
                    <td style="text-align:center;">{{ \Carbon\Carbon::parse($item->tanggal_jatuh_tempo)->format('d/m/Y') ?? '—' }}</td>
                    <td style="text-align:right;">Rp {{ number_format($item->nominal ?? 0, 0, ',', '.') }}</td>
                    <td>{{ $item->kantor->nama_kantor ?? '—' }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" style="text-align:center;">Tidak ada data.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</body>

</html>
