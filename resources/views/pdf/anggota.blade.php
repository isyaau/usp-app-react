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
            /* 🔥 kunci agar muat */
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
            /* 🔥 sangat penting */
        }

        th,
        td {
            border: 1px solid #000;
            padding: 2px;
            /* 🔥 perkecil padding */
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

    <!-- HEADER otomatis tampil di setiap halaman -->
    <header>
        @include('pdf.partials.header')
    </header>

    <!-- FOOTER otomatis tampil di setiap halaman -->
    <footer>
        @include('pdf.partials.footer')
    </footer>

    <!-- Konten PDF -->
    <div class="content">
        <h2>Daftar Anggota</h2>

        <table width="100%" border="1" cellspacing="0" cellpadding="5">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Kelompok</th>
                    <th>Kantor</th>
                    <th>Nomor Anggota</th>
                    <th>PIN</th>
                    <th>Nama</th>
                    <th>Email</th>
                    <th>Alamat</th>
                    <th>Tempat Lahir</th>
                    <th>Tanggal Lahir</th>
                    <th>Jenis Kelamin</th>
                    <th>Agama</th>
                    <th>Telepon</th>
                    <th>Nomor HP</th>
                    <th>Pendidikan</th>
                    <th>Pekerjaan</th>
                    <th>Status Perkawinan</th>
                    <th>Nama Pasangan</th>
                    <th>Nama Ibu</th>
                    <th>Jenis Identitas</th>
                    <th>Nomor Identitas</th>
                    <th>NPWP</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($anggota as $u)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $u->kelompok_id }}</td>
                    <td>{{ $u->kantor_id }}</td>
                    <td>{{ $u->no_anggota }}</td>
                    <td>{{ $u->pin }}</td>
                    <td>{{ $u->nama }}</td>
                    <td>{{ $u->email }}</td>
                    <td>{{ $u->alamat }}</td>
                    <td>{{ $u->tempat_lahir }}</td>
                    <td>{{ \Carbon\Carbon::parse($u->tanggal_lahir)->format('d-m-Y') }}</td>
                    <td>{{ $u->jenis_kelamin }}</td>
                    <td>{{ $u->agama }}</td>
                    <td>{{ $u->telepon }}</td>
                    <td>{{ $u->nomor_hp }}</td>
                    <td>{{ $u->pendidikan }}</td>
                    <td>{{ $u->pekerjaan }}</td>
                    <td>{{ $u->status_perkawinan }}</td>
                    <td>{{ $u->nama_pasangan }}</td>
                    <td>{{ $u->nama_ibu }}</td>
                    <td>{{ $u->jenis_identitas }}</td>
                    <td>{{ $u->nomor_identitas }}</td>
                    <td>{{ $u->npwp }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

    </div>




</body>

</html>