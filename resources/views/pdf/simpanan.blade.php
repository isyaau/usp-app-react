<!DOCTYPE html>
<html>

<head>
    <style>
        /* Halaman landscape */
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
            width: 100px;
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
        <h2>Daftar Simpanan</h2>

        <table width="100%" border="1" cellspacing="0" cellpadding="5">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Tanggal</th>
                    <th>No. Rekening</th>
                    <th>Produk</th>
                    <th>Jenis</th>
                    <th>No. Anggota</th>
                    <th>Nama Anggota</th>
                    <th>Bagi Hasil</th>
                    <th>Marketing</th>
                    <th>Kantor</th>
                    <th>Nominal Setor</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($simpanan as $s)
                <tr>
                    <td style="text-align:center">{{ $loop->iteration }}</td>
                    <td>{{ \Carbon\Carbon::parse($s->tanggal)->format('d-m-Y') }}</td>
                    <td>{{ $s->no_rekening }}</td>
                    <td>{{ $s->jenis_simpanan?->nama ?? '-' }}</td>
                    <td>
                        @php
                            $jenisLabels = [1=>'Pokok',2=>'Wajib',3=>'Sukarela',4=>'Wajib Pinjaman',5=>'Saham',6=>'Pokok Pinjaman',7=>'Rencana'];
                        @endphp
                        {{ $jenisLabels[$s->jenis_simpanan?->jenis ?? 0] ?? '-' }}
                    </td>
                    <td>{{ $s->anggota?->no_anggota ?? '-' }}</td>
                    <td>{{ $s->anggota?->nama ?? '-' }}</td>
                    <td>{{ ($s->jenis_simpanan?->bunga ?? $s->bunga) }}%</td>
                    <td>{{ $s->marketing?->nama ?? '-' }}</td>
                    <td>{{ $s->kantor?->nama_kantor ?? '-' }}</td>
                    <td>Rp {{ number_format((float)$s->nominal_setor, 0, ',', '.') }}</td>
                    <td>{{ $s->aktif === '1' ? 'Aktif' : 'Nonaktif' }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="12" style="text-align:center">Tidak ada data simpanan.</td>
                </tr>
                @endforelse
            </tbody>
        </table>

    </div>

</body>

</html>
