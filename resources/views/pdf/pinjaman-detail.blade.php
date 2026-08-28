<!DOCTYPE html>
<html>

<head>
    <style>
        @page {
            size: A4 portrait;
            margin: 100px 20px 70px 20px;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 9px;
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

        h2 {
            margin: 0 0 8px 0;
            font-size: 13px;
            text-align: center;
        }

        h3 {
            margin: 12px 0 4px 0;
            font-size: 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 4px;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 3px;
            vertical-align: top;
        }

        th {
            background-color: #eee;
            text-align: left;
            font-weight: bold;
            font-size: 9px;
            width: 30%;
        }

        .tabel-head th {
            text-align: center;
            width: auto;
        }

        .status-aktif {
            font-weight: bold;
            color: #006600;
        }

        .status-nonaktif {
            font-weight: bold;
            color: #990000;
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
        <h2>Data Pinjaman</h2>

        <table>
            <tr>
                <th>No. Pinjaman</th>
                <td>{{ $pinjaman->no_pinjaman }}</td>
                <th>Tanggal</th>
                <td>{{ \Carbon\Carbon::parse($pinjaman->tanggal)->format('d-m-Y') }}</td>
            </tr>
            <tr>
                <th>Produk</th>
                <td>{{ $pinjaman->jenisPinjaman->nama ?? '-' }}</td>
                <th>Status</th>
                <td>
                    <span class="{{ $pinjaman->aktif === '1' ? 'status-aktif' : 'status-nonaktif' }}">
                        {{ $pinjaman->aktif === '1' ? 'Aktif' : 'Nonaktif' }}
                    </span>
                </td>
            </tr>
            <tr>
                <th>Plafon</th>
                <td>Rp {{ number_format($pinjaman->plafon, 0, ',', '.') }}</td>
                <th>Bagi Hasil</th>
                <td>{{ $pinjaman->bunga }}%</td>
            </tr>
            <tr>
                <th>Jangka Waktu</th>
                <td>{{ $pinjaman->jangka_waktu }} {{ $pinjaman->satuan }}</td>
                <th>Nominal Angsuran</th>
                <td>Rp {{ number_format($pinjaman->nominal_angsuran, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <th>Periode / Angsuran ke</th>
                <td>{{ $pinjaman->periode }} / ke-{{ $pinjaman->angsuranke }}</td>
                <th>Jatuh Tempo</th>
                <td>{{ $pinjaman->jatuh_tempo ? \Carbon\Carbon::parse($pinjaman->jatuh_tempo)->format('d-m-Y') : '-' }}</td>
            </tr>
        </table>

        <h3>Data Anggota</h3>
        <table>
            <tr>
                <th>Nama</th>
                <td>{{ $pinjaman->anggota->nama ?? '-' }}</td>
                <th>No. Anggota</th>
                <td>{{ $pinjaman->anggota->no_anggota ?? '-' }}</td>
            </tr>
            <tr>
                <th>No. Identitas</th>
                <td>{{ $pinjaman->anggota->no_identitas ?? '-' }}</td>
                <th>Telepon</th>
                <td>{{ $pinjaman->anggota->telepon ?? '-' }}</td>
            </tr>
            <tr>
                <th>Alamat</th>
                <td colspan="3">{{ $pinjaman->anggota->alamat ?? '-' }}</td>
            </tr>
            <tr>
                <th>Kantor</th>
                <td>{{ $pinjaman->kantor->nama_kantor ?? '-' }}</td>
                <th>Dicatat oleh</th>
                <td>{{ $pinjaman->user->nama ?? '-' }}</td>
            </tr>
        </table>

        @if ($pinjaman->biaya->isNotEmpty())
        <h3>Biaya</h3>
        <table>
            <thead>
                <tr class="tabel-head">
                    <th>Nama</th>
                    <th>Jenis</th>
                    <th>Nominal</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($pinjaman->biaya as $b)
                <tr>
                    <td>{{ $b->nama }}</td>
                    <td>{{ $b->persen === '1' ? 'Persentase ('.$b->nominal.'%)' : 'Nominal' }}</td>
                    <td>{{ $b->persen === '1' ? '-' : 'Rp '.number_format($b->nominal, 0, ',', '.') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif

        @if ($pinjaman->jaminan->isNotEmpty())
        <h3>Jaminan</h3>
        <table>
            <thead>
                <tr class="tabel-head">
                    <th>Nama</th>
                    <th>Keterangan</th>
                    <th>Nominal</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($pinjaman->jaminan as $j)
                <tr>
                    <td>{{ $j->nama }}</td>
                    <td>{{ $j->keterangan }}</td>
                    <td>Rp {{ number_format($j->nominal, 0, ',', '.') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif

        @if ($pinjaman->saksi->isNotEmpty())
        <h3>Saksi</h3>
        <table>
            <thead>
                <tr class="tabel-head">
                    <th>Nama</th>
                    <th>Tempat / Tgl Lahir</th>
                    <th>No. KTP</th>
                    <th>Alamat</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($pinjaman->saksi as $s)
                <tr>
                    <td>{{ $s->nama }}</td>
                    <td>{{ $s->tempat_lahir }}{{ $s->tgl_lahir ? ', '.$s->tgl_lahir : '' }}</td>
                    <td>{{ $s->no_ktp }}</td>
                    <td>{{ $s->alamat }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif

        @if ($pinjaman->surat->isNotEmpty())
        <h3>Surat</h3>
        <table>
            <thead>
                <tr class="tabel-head">
                    <th>Jenis Surat</th>
                    <th>Keterangan</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($pinjaman->surat as $s)
                <tr>
                    <td>{{ $s->surat }}</td>
                    <td>{{ $s->keterangan }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif

        @if ($pinjaman->penjamin->isNotEmpty())
        <h3>Penjamin</h3>
        <table>
            <thead>
                <tr class="tabel-head">
                    <th>Nama</th>
                    <th>Hubungan</th>
                    <th>Alamat</th>
                    <th>No. KTP</th>
                    <th>Telepon</th>
                    <th>Ibu</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($pinjaman->penjamin as $p)
                <tr>
                    <td>{{ $p->nama }}</td>
                    <td>{{ $p->hubungan }}</td>
                    <td>{{ $p->alamat }}</td>
                    <td>{{ $p->no_ktp }}</td>
                    <td>{{ $p->telepon }}</td>
                    <td>{{ $p->ibu }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif
    </div>
</body>

</html>