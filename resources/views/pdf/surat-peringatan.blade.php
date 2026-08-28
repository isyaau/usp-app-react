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

        .meta { margin-bottom: 14px; }
        .meta .baris { display: flex; align-items: baseline; }
        .meta .label { width: 90px; display: inline-block; }
        .meta .idpoint { padding-left: 6px; }

        .tujuan { margin: 6px 0 12px 0; }
        .tujuan .d/a { display: block; }

        .perihal { margin-bottom: 6px; }

        .isi { text-align: justify; }
        .isi p { margin: 0 0 10px 0; }

        .tanggal-tutup { margin-top: 18px; }
        .blok-ttd { margin-top: 8px; }

        .ttd-nama { margin-top: 62px; font-weight: bold; text-decoration: underline; }
        .ttd-jabatan { }
    </style>
</head>
<body>

    <div class="kop">
        <div class="nama">{{ $surat->kantor->nama_kantor ?? 'KOPERASI KOPINKA' }}</div>
        @if($surat->kantor && $surat->kantor->alamat_kantor)
            <div class="alamat">{{ $surat->kantor->alamat_kantor }}</div>
        @endif
    </div>

    <div class="meta">
        <div class="baris"><span class="label">Nomor</span>: {{ $surat->no_transaksi }}</div>
        <div class="baris"><span class="label">Lampiran</span>: -</div>
        <div class="baris"><span class="label">Perihal</span>: Surat Peringatan {{ $surat->tahap }}</div>
    </div>

    <p class="tujuan">
        Kepada Yth.<br>
        Sdr/i, <b>{{ $surat->pinjaman->anggota->nama ?? '-' }}</b><br>
        No. Anggota: {{ $surat->pinjaman->anggota->no_anggota ?? '-' }}<br>
        @if($surat->pinjaman->anggota && $surat->pinjaman->anggota->alamat)
            Alamat: {{ $surat->pinjaman->anggota->alamat }}<br>
        @endif
        di Tempat
    </p>

    <p class="perihal">Dengan hormat,</p>

    <div class="isi">
        @if($surat->isi)
            {!! nl2br(e($surat->isi)) !!}
        @else
            <p>
                Bersama surat ini, kami dari pihak koperasi bermaksud menyampaikan surat peringatan
                {{ $surat->tahap }} kepada Bapak/Ibu selaku anggota koperasi dengan nomor pinjaman
                <b>{{ $surat->pinjaman->no_pinjaman ?? '-' }}</b>.
            </p>
            <p>
                Dikarenakan sampai dengan tanggal <b>{{ \Carbon\Carbon::parse($surat->tgl_transaksi)->isoFormat('D MMMM YYYY') }}</b>
                pembayaran angsuran pinjaman Bapak/Ibu belum kami terima, maka dengan ini kami meminta
                agar Bapak/Ibu segera menyelesaikan seluruh kewajiban angsuran yang masih terhutang.
            </p>
            <p>
                Surat peringatan ini merupakan panggilan {{ $surat->tahap }} dari koperasi. Apabila dalam
                tenggang waktu yang telah ditentukan kewajiban tersebut belum juga dipenuhi, maka pihak
                koperasi akan menempuh langkah selanjutnya sesuai dengan ketentuan dan perjanjian pinjaman
                yang telah disepakati.
            </p>
            <p>Demikian surat peringatan ini kami sampaikan untuk diperhatikan dan dilaksanakan dengan penuh tanggung jawab.</p>
        @endif
    </div>

    <table class="tanggal-tutup" width="100%">
        <tr>
            <td width="55%"></td>
            <td width="45%" style="text-align: center;">
                {{ $surat->kantor->nama_kantor ?? 'Koperasi' }}, {{ \Carbon\Carbon::parse($surat->tgl_transaksi)->isoFormat('D MMMM YYYY') }}
                <div class="blok-ttd">
                    <div class="ttd-nama">{{ $surat->kantor->pejabat ?? '.' }}</div>
                    <div class="ttd-jabatan">{{ $surat->kantor->jabatan ?? 'Pengurus Koperasi' }}</div>
                </div>
            </td>
        </tr>
    </table>

</body>
</html>