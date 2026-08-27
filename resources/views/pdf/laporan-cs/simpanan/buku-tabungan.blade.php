<!DOCTYPE html>
<html>

<head>
    <style>
        @page {
            margin: 0;
        }

        body {
            font-family: 'Courier New', Courier, monospace;
            font-size: {{ $fontSize }}px;
            margin: 0;
            padding: 0;
            color: #000;
        }

        .passbook-container {
            padding-top: {{ $marginTop }}mm;
            padding-left: {{ $marginLeft }}mm;
            padding-right: {{ $marginRight }}mm;
        }

        .trans-table {
            width: 100%;
            border-collapse: collapse;
            font-size: {{ $fontSize }}px;
        }

        .trans-table td {
            padding: 1mm 0;
            vertical-align: top;
            font-size: {{ $fontSize }}px;
            line-height: {{ $lineHeight }}mm;
            white-space: nowrap;
        }

        .col-no {
            width: {{ $colNo }}mm;
            text-align: center;
        }

        .col-tanggal {
            width: {{ $colTanggal }}mm;
            text-align: center;
        }

        .col-kode {
            width: {{ $colKode }}mm;
            text-align: center;
        }

        .col-debet {
            width: {{ $colDebet }}mm;
            text-align: right;
        }

        .col-kredit {
            width: {{ $colKredit }}mm;
            text-align: right;
        }

        .col-opt {
            width: {{ $colOpt }}mm;
            text-align: center;
        }

        .col-saldo {
            text-align: right;
        }
    </style>
</head>

<body>
    <div class="passbook-container">
        <table class="trans-table">
            <tbody>
                @php
                    $totalLines = (int) $totalLines;
                    $skipLines = (int) $skipLines;
                    $startFrom = (int) $startFrom;
                @endphp
                {{-- Skip lines for already-printed rows --}}
                @for($s = 0; $s < $skipLines; $s++)
                    <tr>
                        <td class="col-no">&nbsp;</td>
                        <td class="col-tanggal"></td>
                        <td class="col-kode"></td>
                        <td class="col-debet"></td>
                        <td class="col-kredit"></td>
                        <td class="col-saldo"></td>
                        <td class="col-opt"></td>
                    </tr>
                @endfor

                @php
                    $runningBalance = $preBalance ?? 0;
                    $remaining = max(0, $totalLines - $skipLines);
                    $sliced = $items->take($remaining);
                    $seq = $startFrom;
                @endphp
                @foreach($sliced as $item)
                    @php
                        $nominal = (float) ($item->nominal ?? 0);
                        $isSetoran = (bool) ($item->kodeTransaksi->setoran ?? false);
                        if ($isSetoran) {
                            $runningBalance += $nominal;
                        } else {
                            $runningBalance -= $nominal;
                        }
                    @endphp
                    <tr>
                        <td class="col-no">{{ $seq }}</td>
                        <td class="col-tanggal">{{ \Carbon\Carbon::parse($item->tgl_transaksi)->format('d/m/y') }}</td>
                        <td class="col-kode">{{ $item->kodeTransaksi->kode ?? '—' }}</td>
                        <td class="col-debet">{{ !$isSetoran ? number_format($nominal, 0, ',', '.') : '' }}</td>
                        <td class="col-kredit">{{ $isSetoran ? number_format($nominal, 0, ',', '.') : '' }}</td>
                        <td class="col-saldo">{{ number_format($runningBalance, 0, ',', '.') }}</td>
                        <td class="col-opt">{{ $item->user->username ?? '—' }}</td>
                    </tr>
                    @php $seq++; @endphp
                @endforeach

                {{-- Fill remaining empty lines to complete the page --}}
                @php
                    $printed = $skipLines + $sliced->count();
                    $emptyLines = max(0, $totalLines - $printed);
                @endphp
                @for($i = 0; $i < $emptyLines; $i++)
                    <tr>
                        <td class="col-no">&nbsp;</td>
                        <td class="col-tanggal"></td>
                        <td class="col-kode"></td>
                        <td class="col-debet"></td>
                        <td class="col-kredit"></td>
                        <td class="col-saldo"></td>
                        <td class="col-opt"></td>
                    </tr>
                @endfor
            </tbody>
        </table>
    </div>
</body>

</html>
