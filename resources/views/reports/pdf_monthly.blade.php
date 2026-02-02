<!DOCTYPE html>
<html>
<head>
    <title>Laporan Bulanan</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 10px; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid black; padding: 5px; vertical-align: top; }
        .header { text-align: center; font-weight: bold; margin-bottom: 20px; font-size: 12px; }
    </style>
</head>
<body>
    @php
        $first = $reports->first();
        $monthName = strtoupper($first->month_name);
        $year = $first->year;
    @endphp

    <div class="header">
        LAPORAN REALISASI PEMAKAIAN CORPORATE CREDIT CARD<br>
        DIREKSI PT ASABRI (PERSERO)<br>
        PENGGUNAAN BULAN {{ $monthName }} TAHUN {{ $year }}
    </div>

    <table>
        <thead>
            <tr style="background-color: #d9d9d9;">
                <th rowspan="2" width="3%">NO</th>
                <th rowspan="2" width="20%">PEMEGANG KARTU</th>
                <th rowspan="2" width="12%">NOMOR KARTU</th>
                <th rowspan="2" width="8%">MASA AKTIF</th>
                <th rowspan="2" width="10%">LIMIT KARTU</th>
                <th colspan="3">REALISASI</th>
                <th rowspan="2" width="15%">KETERANGAN</th>
                <th rowspan="2" width="10%">SISA LIMIT<br>(Rp)</th>
            </tr>
            <tr style="background-color: #d9d9d9;">
                <th width="8%">TANGGAL</th>
                <th>URAIAN TRANSAKSI</th>
                <th width="10%">JUMLAH (Rp)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($reports as $index => $report)
                @php 
                    $trxCount = $report->transactions->count();
                    $rowSpan = $trxCount > 0 ? $trxCount : 1;
                    $totalExp = $report->transactions->sum('amount');
                    $sisa = $report->credit_limit - $totalExp;
                @endphp

                @if($trxCount > 0)
                    @foreach($report->transactions as $key => $trx)
                    <tr>
                        @if($key === 0)
                            <td rowspan="{{ $rowSpan }}" class="text-center">{{ $index + 1 }}</td>
                            <td rowspan="{{ $rowSpan }}">
                                <strong>{{ $report->director->name }}</strong><br>
                                {{ $report->director->position }}
                            </td>
                            <td rowspan="{{ $rowSpan }}" class="text-center">
                                @foreach($report->director->creditCards as $c) {{ $c->card_number }}<br> @endforeach
                            </td>
                            <td rowspan="{{ $rowSpan }}" class="text-center">-</td>
                            <td rowspan="{{ $rowSpan }}" class="text-right">{{ number_format($report->credit_limit, 0, ',', '.') }}</td>
                        @endif

                        <td class="text-center">{{ \Carbon\Carbon::parse($trx->transaction_date)->format('d/m/Y') }}</td>
                        <td>{{ $trx->description }}</td>
                        <td class="text-right">{{ number_format($trx->amount, 0, ',', '.') }}</td>

                        @if($key === 0)
                            <td rowspan="{{ $rowSpan }}"></td>
                            <td rowspan="{{ $rowSpan }}" class="text-right">{{ number_format($sisa, 0, ',', '.') }}</td>
                        @endif
                    </tr>
                    @endforeach
                @else
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td>
                            <strong>{{ $report->director->name }}</strong><br>
                            {{ $report->director->position }}
                        </td>
                        <td class="text-center">
                            @foreach($report->director->creditCards as $c) {{ $c->card_number }}<br> @endforeach
                        </td>
                        <td class="text-center">-</td>
                        <td class="text-right">{{ number_format($report->credit_limit, 0, ',', '.') }}</td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td class="text-center">TIDAK ADA TRANSAKSI</td>
                        <td class="text-right">{{ number_format($sisa, 0, ',', '.') }}</td>
                    </tr>
                @endif
            @endforeach
        </tbody>
    </table>
</body>
</html>