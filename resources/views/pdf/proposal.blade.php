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
        }

        .tabel-head th {
            text-align: center;
            width: auto;
        }

        .total-baris td {
            font-weight: bold;
            background-color: #f3f3f3;
        }

        .status-aktif {
            font-weight: bold;
            color: #006600;
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
        <h2>Proposal Pinjaman</h2>

        <table>
            <tr>
                <th style="width: 25%">No. Bukti</th>
                <td>{{ $proposal->no_bukti }}</td>
                <th style="width: 20%">Tanggal</th>
                <td>{{ \Carbon\Carbon::parse($proposal->tanggal)->format('d-m-Y') }}</td>
            </tr>
            <tr>
                <th>Produk</th>
                <td>{{ $proposal->jenisPinjaman->nama ?? '-' }}</td>
                <th>Metode Angsuran</th>
                <td>{{ $proposal->jenis_angsuran }}</td>
            </tr>
            <tr>
                <th>Plafon</th>
                <td>Rp {{ number_format($proposal->plafon, 0, ',', '.') }}</td>
                <th>Bunga</th>
                <td>{{ $proposal->bunga }}%</td>
            </tr>
            <tr>
                <th>Jangka Waktu</th>
                <td>{{ $proposal->jangka_waktu }} {{ $proposal->satuan }}</td>
                <th>Nominal Angsuran</th>
                <td>Rp {{ number_format($proposal->nominal_angsuran, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <th>Pembayaran</th>
                <td>{{ $proposal->pembayaran }}</td>
                <th>Bayar Pokok Per</th>
                <td>{{ $proposal->bayar_pokok_per ?: '-' }}</td>
            </tr>
            <tr>
                <th>Penggunaan Kredit</th>
                <td>{{ $proposal->penggunaan_kredit ?: '-' }}</td>
                <th>Jaminan</th>
                <td>{{ $proposal->jaminan ?: '-' }}</td>
            </tr>
        </table>

        <h3>Data Debitur</h3>
        <table>
            <tr>
                <th style="width: 25%">Nama</th>
                <td>{{ $proposal->anggota->nama ?? '-' }}</td>
                <th style="width: 20%">No. Anggota</th>
                <td>{{ $proposal->anggota->no_anggota ?? '-' }}</td>
            </tr>
            <tr>
                <th>No. Identitas</th>
                <td>{{ $proposal->anggota->no_identitas ?? '-' }}</td>
                <th>Telepon</th>
                <td>{{ $proposal->anggota->telepon ?? '-' }}</td>
            </tr>
            <tr>
                <th>Alamat</th>
                <td colspan="3">{{ $proposal->anggota->alamat ?? '-' }}</td>
            </tr>
            <tr>
                <th>Marketing</th>
                <td>{{ $proposal->marketing ? $proposal->marketing->nama.' ('.$proposal->marketing->kode.')' : '-' }}</td>
                <th>Kantor</th>
                <td>{{ $proposal->kantor->nama_kantor ?? '-' }}</td>
            </tr>
            <tr>
                <th>Dicatat oleh</th>
                <td>{{ $proposal->user->nama ?? '-' }}</td>
                <th>Status</th>
                <td>
                    <span class="{{ $proposal->status === '1' ? 'status-aktif' : '' }}">
                        {{ $proposal->status === '1' ? 'Aktif' : 'Nonaktif' }}
                    </span>
                </td>
            </tr>
        </table>

        <h3>Biaya & Pencairan</h3>
        <table>
            <thead>
                <tr class="tabel-head">
                    <th>Nama</th>
                    <th>Jenis</th>
                    <th>Nominal</th>
                    <th>Potongan Pencairan</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($proposal->biaya as $b)
                <tr>
                    <td>{{ $b->nama }}</td>
                    <td>{{ $b->persen === '1' ? 'Persentase ('.$b->nominal.'%)' : 'Nominal' }}</td>
                    <td>{{ $b->persen === '1' ? '-' : 'Rp '.number_format($b->nominal, 0, ',', '.') }}</td>
                    <td>{{ $b->is_deducted_from_disbursement === '1' ? 'Potong' : '-' }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" style="text-align: center">Tidak ada biaya.</td>
                </tr>
                @endforelse
                <tr class="total-baris">
                    <td colspan="2">Total Biaya</td>
                    <td colspan="2">Rp {{ number_format($proposal->total_biaya ?: 0, 0, ',', '.') }}</td>
                </tr>
                <tr class="total-baris">
                    <td colspan="2">Total Terima</td>
                    <td colspan="2">Rp {{ number_format($proposal->total_terima ?: 0, 0, ',', '.') }}</td>
                </tr>
            </tbody>
        </table>
    </div>
</body>

</html>