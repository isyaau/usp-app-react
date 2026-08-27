<!DOCTYPE html>
<html>

<head>
    <style>
        @page { size: A4 landscape; margin: 100px 15px 70px 15px; }
        body { font-family: Arial, Helvetica, sans-serif; font-size: 9px; margin: 0; padding: 0; }
        header { position: fixed; top: -90px; left: 0; right: 0; height: 80px; }
        footer { position: fixed; bottom: -60px; left: 0; right: 0; height: 50px; }
        .content { margin-bottom: 10px; }
        h2 { margin: 0 0 5px 0; font-size: 11px; text-align: center; }
        table { width: 100%; border-collapse: collapse; table-layout: fixed; }
        th, td { border: 1px solid #d1d5db; padding: 4px; vertical-align: top; word-wrap: break-word; }
        th { background-color: #f3f4f6; text-align: center; font-weight: bold; font-size: 9px; }
    </style>
</head>

<body>
    <header>@include('pdf.partials.header')</header>
    <footer>@include('pdf.partials.footer')</footer>

    <div class="content">
        <h2>Laporan Tagihan Marketing Detail</h2>
        <p style="text-align:center;font-size:10px;color:#666;">{{ $generatedAt }}</p>

        <table>
            <thead>
                <tr>
                    <th style="width:3%;">No</th>
                    <th style="width:9%;">No Pinjaman</th>
                    <th style="width:7%;">Tanggal</th>
                    <th style="width:7%;">No Anggota</th>
                    <th style="width:12%;">Nama Anggota</th>
                    <th style="width:10%;">Produk</th>
                    <th style="width:10%;">Plafon</th>
                    <th style="width:10%;">Terbayar</th>
                    <th style="width:10%;">Sisa Pokok</th>
                    <th style="width:8%;">Angsuran</th>
                    <th style="width:9%;">Marketing</th>
                </tr>
            </thead>
            <tbody>
                @forelse($items as $i => $item)
                <tr>
                    <td style="text-align:center;">{{ $i + 1 }}</td>
                    <td>{{ $item['no_pinjaman'] ?? '—' }}</td>
                    <td>{{ $item['tanggal'] ?? '—' }}</td>
                    <td>{{ $item['no_anggota'] ?? '—' }}</td>
                    <td>{{ $item['nama_anggota'] ?? '—' }}</td>
                    <td>{{ $item['produk'] ?? '—' }}</td>
                    <td style="text-align:right;">Rp {{ number_format($item['plafon'] ?? 0, 0, ',', '.') }}</td>
                    <td style="text-align:right;">Rp {{ number_format($item['terbayar'] ?? 0, 0, ',', '.') }}</td>
                    <td style="text-align:right;">Rp {{ number_format($item['sisa_pokok'] ?? 0, 0, ',', '.') }}</td>
                    <td style="text-align:center;">{{ $item['angsuran_ke'] ?? 0 }}/{{ $item['jangka_waktu'] ?? 0 }}</td>
                    <td>{{ $item['marketing'] ?? '—' }}</td>
                </tr>
                @empty
                <tr><td colspan="11" style="text-align:center;">Tidak ada data.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</body>

</html>
