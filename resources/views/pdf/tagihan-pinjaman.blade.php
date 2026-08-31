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
        <h2>Tagihan Pinjaman</h2>

        @php
            $totalPlafon = $pinjaman->sum(fn ($i) => (float) $i->plafon);
            $totalSisa = 0;
            foreach ($pinjaman as $i) {
                $pokok = (float) \App\Models\AngsuranPinjaman::where('pinjaman_id', $i->id)->sum('nominal_pokok');
                $totalSisa += max(0, (float) $i->plafon - $pokok);
            }
        @endphp

        <p style="margin: 0 0 6px 0; font-size: 8px;">
            Jumlah Pinjaman: {{ $pinjaman->count() }} &nbsp;|&nbsp;
            Total Plafon: Rp {{ number_format($totalPlafon, 0, ',', '.') }} &nbsp;|&nbsp;
            Total Sisa Pokok: Rp {{ number_format($totalSisa, 0, ',', '.') }}
            @if (!empty($filters['mulai']) || !empty($filters['sampai']))
                &nbsp;|&nbsp; Jatuh Tempo: {{ $filters['mulai'] ?? '*' }} s/d {{ $filters['sampai'] ?? '*' }}
            @endif
        </p>

        <table width="100%" border="1" cellspacing="0" cellpadding="5">
            <thead>
                <tr>
                    <th width="3%">No</th>
                    <th width="9%">No Pinjaman</th>
                    <th width="8%">Tgl Bayar</th>
                    <th width="9%">No Anggota</th>
                    <th width="13%">Nama</th>
                    <th width="10%">Produk</th>
                    <th width="9%">Plafon</th>
                    <th width="7%">Jangka Waktu</th>
                    <th width="5%">Satuan</th>
                    <th width="9%">Angsuran</th>
                    <th width="9%">Sisa Pokok</th>
                    <th width="9%">Tunggakan</th>
                    <th width="9%">Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($pinjaman as $item)
                @php
                    $pokokTerbayar = (float) \App\Models\AngsuranPinjaman::where('pinjaman_id', $item->id)->sum('nominal_pokok');
                    $sisa = max(0, (float) $item->plafon - $pokokTerbayar);
                @endphp
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $item->no_pinjaman }}</td>
                    <td>{{ $item->tgl_bayar ?? '-' }}</td>
                    <td>{{ $item->anggota->no_anggota ?? '-' }}</td>
                    <td>{{ $item->anggota->nama ?? '-' }}</td>
                    <td>{{ $item->jenisPinjaman->nama ?? '-' }}</td>
                    <td>Rp {{ number_format($item->plafon, 0, ',', '.') }}</td>
                    <td>{{ $item->jangka_waktu }}</td>
                    <td>{{ $item->satuan }}</td>
                    <td>Rp {{ number_format($item->nominal_angsuran, 0, ',', '.') }}</td>
                    <td>Rp {{ number_format($sisa, 0, ',', '.') }}</td>
                    <td>Rp {{ number_format($sisa, 0, ',', '.') }}</td>
                    <td>{{ $sisa <= 0 ? 'LUNAS' : 'BELUM LUNAS' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</body>

</html>
