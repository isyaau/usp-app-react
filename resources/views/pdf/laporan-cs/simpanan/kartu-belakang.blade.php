<!DOCTYPE html>
<html>

<head>
    <style>
        @page {
            size: A4 portrait;
            margin: 50px 15px 70px 15px;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 10px;
            margin: 0;
            padding: 0;
        }

        header {
            position: fixed;
            top: -40px;
            left: 0;
            right: 0;
            height: 40px;
        }

        footer {
            position: fixed;
            bottom: -60px;
            left: 0;
            right: 0;
            height: 50px;
        }

        .card {
            width: 100%;
            border: 2px solid #000;
            padding: 20px;
            box-sizing: border-box;
            page-break-after: always;
        }

        .card-header {
            text-align: center;
            margin-bottom: 15px;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
        }

        .card-header h2 {
            margin: 5px 0 0 0;
            font-size: 14px;
        }

        .card-body {
            width: 100%;
        }

        .card-row {
            display: flex;
            margin-bottom: 8px;
            border-bottom: 1px dotted #ccc;
            padding-bottom: 4px;
        }

        .card-label {
            width: 150px;
            font-weight: bold;
            flex-shrink: 0;
        }

        .card-value {
            flex: 1;
        }

        .signature-area {
            margin-top: 60px;
            display: flex;
            justify-content: space-between;
        }

        .sig-block {
            width: 45%;
            text-align: center;
        }

        .sig-line {
            border-top: 1px solid #000;
            margin-top: 60px;
            padding-top: 5px;
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

    @foreach($items as $item)
    <div class="card">
        <div class="card-header">
            <h2>KARTU SIMPANAN</h2>
            <p>{{ $item->jenis_simpanan->nama ?? '' }}</p>
        </div>

        <div class="card-body">
            <div class="card-row">
                <div class="card-label">No. Rekening</div>
                <div class="card-value">: {{ $item->no_rekening ?? '—' }}</div>
            </div>
            <div class="card-row">
                <div class="card-label">No. Anggota</div>
                <div class="card-value">: {{ $item->anggota->no_anggota ?? '—' }}</div>
            </div>
            <div class="card-row">
                <div class="card-label">Nama</div>
                <div class="card-value">: {{ $item->anggota->nama ?? '—' }}</div>
            </div>
            <div class="card-row">
                <div class="card-label">Tanggal Buka</div>
                <div class="card-value">: {{ \Carbon\Carbon::parse($item->created_at)->format('d/m/Y') ?? '—' }}</div>
            </div>

            <div class="signature-area">
                <div class="sig-block">
                    <div>Pihak Pertama</div>
                    <div class="sig-line">&nbsp;</div>
                    <div>(___________________)</div>
                </div>
                <div class="sig-block">
                    <div>Pihak Kedua</div>
                    <div class="sig-line">&nbsp;</div>
                    <div>({{ $item->anggota->nama ?? '___________________' }})</div>
                </div>
            </div>

            <div style="margin-top:40px;text-align:center;font-size:9px;color:#666;">
                <p>Dicetak pada: {{ $generatedAt ?? now()->format('d/m/Y H:i') }}</p>
            </div>
        </div>
    </div>
    @endforeach
</body>

</html>
