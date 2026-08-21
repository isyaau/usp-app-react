<!DOCTYPE html>
<html>

<head>
    <style>
        /* Ruang untuk header & footer */
        @page {
            margin: 120px 25px 80px 25px;
        }

        header {
            position: fixed;
            top: -100px;
            /* sesuaikan dengan tinggi header */
            left: 0;
            right: 0;
            height: 80px;
        }

        footer {
            position: fixed;
            bottom: -60px;
            /* sesuaikan dengan tinggi footer */
            left: 0;
            right: 0;
            height: 50px;
        }

        .content {

            margin-bottom: 20px;
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
        <h2>Daftar Kantor</h2>

        <table width="100%" border="1" cellspacing="0" cellpadding="5">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Kode</th>
                    <th>Nama Kantor</th>
                    <th>Alamat Kantor</th>
                    <th>Pejabat</th>
                    <th>Jabatan</th>
                    <th>Bendahara</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($kantor as $u)
                <tr>
                    <td>{{ $loop->index+1 }}</td>
                    <td>{{ $u->kode }}</td>
                    <td>{{ $u->nama_kantor }}</td>
                    <td>{{ $u->alamat_kantor }}</td>
                    <td>{{ $u->pejabat }}</td>
                    <td>{{ $u->jabatan }}</td>
                    <td>{{ $u->bendahara }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>




</body>

</html>