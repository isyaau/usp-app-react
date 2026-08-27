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

        .anggota-header {
            background-color: #ddd;
            font-weight: bold;
            font-size: 9px;
            padding: 5px;
            margin-top: 10px;
            border: 1px solid #000;
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
        <h2>Detail Simpanan & Pinjaman Anggota</h2>

        @foreach ($anggota as $a)
        <div class="anggota-header">
            {{ $loop->iteration }}. {{ $a->no_anggota }} - {{ $a->nama }} ({{ $a->kelompok->nama ?? '-' }})
        </div>

        <table width="100%" border="1" cellspacing="0" cellpadding="5">
            <thead>
                <tr>
                    <th width="3%">No</th>
                    <th width="8%">No Rekening</th>
                    <th width="10%">Jenis Simpanan</th>
                    <th width="10%">Nominal Setor</th>
                    <th width="8%">No Pinjaman</th>
                    <th width="10%">Produk Pinjaman</th>
                    <th width="10%">Plafon</th>
                    <th width="7%">Bunga</th>
                    <th width="8%">Jangka Waktu</th>
                    <th width="8%">Angsuran ke</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $maxRows = max(count($a->simpanan ?? []), count($a->pinjaman ?? []));
                    if ($maxRows === 0) $maxRows = 1;
                @endphp
                @for ($i = 0; $i < $maxRows; $i++)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>{{ $a->simpanan[$i]->no_rekening ?? '-' }}</td>
                    <td>{{ $a->simpanan[$i]->jenis_simpanan ?? '-' }}</td>
                    <td class="text-right">{{ isset($a->simpanan[$i]) ? number_format($a->simpanan[$i]->nominal_setor, 0, ',', '.') : '-' }}</td>
                    <td>{{ $a->pinjaman[$i]->no_pinjaman ?? '-' }}</td>
                    <td>{{ $a->pinjaman[$i]->produk_pinjaman ?? '-' }}</td>
                    <td class="text-right">{{ isset($a->pinjaman[$i]) ? number_format($a->pinjaman[$i]->plafon, 0, ',', '.') : '-' }}</td>
                    <td class="text-right">{{ $a->pinjaman[$i]->bunga ?? '-' }}%</td>
                    <td>{{ $a->pinjaman[$i]->jangka_waktu ?? '-' }} bln</td>
                    <td>{{ $a->pinjaman[$i]->angsuran_ke ?? '-' }}</td>
                </tr>
                @endfor
            </tbody>
        </table>
        @endforeach
    </div>
</body>

</html>
