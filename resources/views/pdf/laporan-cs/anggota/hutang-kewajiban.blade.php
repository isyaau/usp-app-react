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

        .text-right {
            text-align: right;
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
        <h2>Hutang & Kewajiban Anggota</h2>

        <table width="100%" border="1" cellspacing="0" cellpadding="5">
            <thead>
                <tr>
                    <th width="3%">No</th>
                    <th width="8%">No Anggota</th>
                    <th width="12%">Nama</th>
                    <th width="10%">Kelompok</th>
                    <th width="10%">No Pinjaman</th>
                    <th width="12%">Produk Pinjaman</th>
                    <th width="10%">Plafon</th>
                    <th width="10%">Angsuran/Bulan</th>
                    <th width="8%">Jangka Waktu</th>
                    <th width="8%">Angsuran ke</th>
                    <th width="7%">Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($anggota as $a)
                @if (count($a->pinjaman_aktif ?? []) > 0)
                    @foreach ($a->pinjaman_aktif as $p)
                    <tr>
                        <td>{{ $loop->parent->iteration }}</td>
                        <td>{{ $a->no_anggota }}</td>
                        <td>{{ $a->nama }}</td>
                        <td>{{ $a->kelompok->nama ?? '-' }}</td>
                        <td>{{ $p->no_pinjaman }}</td>
                        <td>{{ $p->produk_pinjaman ?? '-' }}</td>
                        <td class="text-right">{{ number_format($p->plafon, 0, ',', '.') }}</td>
                        <td class="text-right">{{ number_format($a->total_angsuran_bulan, 0, ',', '.') }}</td>
                        <td>{{ $p->jangka_waktu ?? '-' }} bln</td>
                        <td>{{ $p->angsuran_ke ?? '-' }}</td>
                        <td>{{ $p->status ?? '-' }}</td>
                    </tr>
                    @endforeach
                @else
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $a->no_anggota }}</td>
                    <td>{{ $a->nama }}</td>
                    <td>{{ $a->kelompok->nama ?? '-' }}</td>
                    <td>-</td>
                    <td>-</td>
                    <td>-</td>
                    <td>-</td>
                    <td>-</td>
                    <td>-</td>
                    <td>Tidak Ada Pinjaman</td>
                </tr>
                @endif
                @endforeach
            </tbody>
        </table>
    </div>
</body>

</html>
