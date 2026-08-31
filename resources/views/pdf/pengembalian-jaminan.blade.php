<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <style>
        @page { size: A4 portrait; margin: 45px 50px 45px 50px; }
        body { font-family: Arial, Helvetica, sans-serif; font-size: 12px; color: #111; line-height: 1.6; }

        .kop { text-align: center; border-bottom: 3px double #000; padding-bottom: 8px; margin-bottom: 22px; }
        .kop .nama { font-size: 20px; font-weight: bold; letter-spacing: 1px; text-transform: uppercase; }
        .kop .alamat { font-size: 11px; }

        .judul { text-align: center; font-size: 14px; font-weight: bold; text-decoration: underline; margin-bottom: 20px; }

        .meta { margin-bottom: 14px; }
        .meta .baris { display: flex; align-items: baseline; }
        .meta .label { width: 130px; display: inline-block; }
        .meta .idpoint { padding-left: 6px; }

        table.detail { width: 100%; border-collapse: collapse; margin: 12px 0 18px 0; }
        table.detail th, table.detail td { border: 1px solid #000; padding: 5px 8px; font-size: 11px; }
        table.detail th { background-color: #eee; text-align: center; }

        .tujuan { margin: 6px 0 12px 0; }

        .isi { text-align: justify; }
        .isi p { margin: 0 0 10px 0; }

        .tanggal-tutup { margin-top: 26px; }
        .blok-ttd { margin-top: 8px; }
        .ttd-nama { margin-top: 62px; font-weight: bold; text-decoration: underline; }
        .ttd-jabatan { }
    </style>
</head>
<body>

    <div class="kop">
        <div class="nama">{{ $transaksi->kantor->nama_kantor ?? 'KOPERASI KOPINKA' }}</div>
        @if($transaksi->kantor && $transaksi->kantor->alamat_kantor)
            <div class="alamat">{{ $transaksi->kantor->alamat_kantor }}</div>
        @endif
    </div>

    <div class="judul">BUKTI PENGEMBALIAN JAMINAN</div>

    <div class="meta">
        <div class="baris"><span class="label">Nomor</span>: {{ $transaksi->no_transaksi }}</div>
        <div class="baris"><span class="label">Tanggal</span>: {{ \Carbon\Carbon::parse($transaksi->tgl_transaksi)->isoFormat('D MMMM YYYY') }}</div>
        <div class="baris"><span class="label">No. Pinjaman</span>: {{ $transaksi->pinjaman->no_pinjaman ?? '-' }}</div>
        <div class="baris"><span class="label">Sisa Pokok</span>: Rp {{ number_format($transaksi->sisa_pokok, 2, ',', '.') }}</div>
        <div class="baris"><span class="label">Status</span>: {{ ucfirst($transaksi->status) }}</div>
    </div>

    <p>Yang bertanda tangan di bawah ini, kami selaku pihak Koperasi, menyatakan bahwa jaminan atas pinjaman tersebut telah dikembalikan kepada:</p>

    <p class="tujuan">
        Nama: <b>{{ $transaksi->pinjaman->anggota->nama ?? '-' }}</b><br>
        No. Anggota: {{ $transaksi->pinjaman->anggota->no_anggota ?? '-' }}<br>
        di Tempat
    </p>

    <p>Adapun jaminan yang dikembalikan adalah sebagai berikut:</p>

    <table class="detail">
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="35%">Nama Jaminan</th>
                <th width="35%">Keterangan</th>
                <th width="25%">Nominal</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($transaksi->pinjaman->jaminan ?? [] as $j)
                <tr>
                    <td style="text-align:center">{{ $loop->iteration }}</td>
                    <td>{{ $j->nama }}</td>
                    <td>{{ $j->keterangan ?? '-' }}</td>
                    <td style="text-align:right">Rp {{ number_format($j->nominal ?? 0, 2, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" style="text-align:center">Tidak ada data jaminan.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    @if ($transaksi->keterangan)
        <div class="isi">
            <p>Keterangan: {!! nl2br(e($transaksi->keterangan)) !!}</p>
        </div>
    @endif

    <div class="isi">
        <p>
            Demikian bukti pengembalian jaminan ini kami buat dengan sebenarnya untuk dipergunakan
            sebagaimana mestinya. Jaminan yang telah dikembalikan menjadi tanggung jawab sepenuhnya
            dari anggota yang bersangkutan.
        </p>
    </div>

    <table class="tanggal-tutup" width="100%">
        <tr>
            <td width="50%" style="text-align:center;">
                Penerima,
                <div class="blok-ttd">
                    <div class="ttd-nama">{{ $transaksi->pinjaman->anggota->nama ?? '....................' }}</div>
                    <div class="ttd-jabatan">Anggota</div>
                </div>
            </td>
            <td width="50%" style="text-align:center;">
                {{ $transaksi->kantor->nama_kantor ?? 'Koperasi' }}, {{ \Carbon\Carbon::parse($transaksi->tgl_transaksi)->isoFormat('D MMMM YYYY') }}
                <div class="blok-ttd">
                    <div class="ttd-nama">{{ $transaksi->kantor->pejabat ?? '....................' }}</div>
                    <div class="ttd-jabatan">{{ $transaksi->kantor->jabatan ?? 'Pengurus Koperasi' }}</div>
                </div>
            </td>
        </tr>
    </table>

</body>
</html>
