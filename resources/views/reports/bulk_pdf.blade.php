<!DOCTYPE html>
<html>
<head>
    <title>Laporan Gabungan</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 11px; }
        .page-break { page-break-after: always; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }
        
        table { width: 100%; border-collapse: collapse; margin-top: 5px; }
        th, td { border: 1px solid black; padding: 4px; vertical-align: middle; }
        
        .main-header { text-align: center; font-weight: bold; margin-bottom: 20px; font-size: 12px; }
        .info-table td { border: none; padding: 2px; }
    </style>
</head>
<body>

    @foreach($reports as $report)
        @php
            $totalExpenses = $report->transactions->sum('amount');
            $remainingLimit = $report->credit_limit - $totalExpenses;
        @endphp

        <div class="main-header">
            LAPORAN REALISASI PEMAKAIAN CREDIT CARD CORPORATE<br>
            DIREKSI PT ASABRI (PERSERO)<br>
            PERIODE {{ $report->month_name }} TAHUN {{ $report->year }}
        </div>

        <table class="info-table" style="margin-bottom: 10px; border: none;">
            <tr>
                <td width="15%" class="font-bold">NAMA</td>
                <td width="2%">:</td>
                <td>{{ $report->director->name }}</td>
            </tr>
            <tr>
                <td class="font-bold">JABATAN</td>
                <td>:</td>
                <td>{{ $report->director->position }}</td>
            </tr>
            <tr>
                <td class="font-bold">NO. KARTU</td>
                <td>:</td>
                <td>
                    @foreach($report->director->creditCards as $card)
                        {{ $card->card_number }}<br>
                    @endforeach
                </td>
            </tr>
        </table>

        <table>
            <thead>
                <tr style="background-color: #d9d9d9;">
                    <th width="5%">NO</th>
                    <th width="15%">TANGGAL TRANSAKSI</th>
                    <th>KETERANGAN</th>
                    <th width="15%">JUMLAH (Rp)</th>
                    <th width="15%">BATAS KREDIT (PAGU)</th>
                    <th width="15%">SISA PAGU (Rp)</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="text-center">1</td>
                    <td></td>
                    <td class="font-bold">BATAS KREDIT (PAGU)</td>
                    <td style="background-color: #f2f2f2;"></td>
                    <td class="text-right font-bold">{{ number_format($report->credit_limit, 0, ',', '.') }}</td>
                    <td style="background-color: #f2f2f2;"></td>
                </tr>

                @php $balance = $report->credit_limit; @endphp

                @forelse($report->transactions as $index => $trx)
                @php $balance -= $trx->amount; @endphp
                <tr>
                    <td class="text-center">{{ $index + 2 }}</td>
                    <td class="text-center">{{ \Carbon\Carbon::parse($trx->transaction_date)->format('d/m/Y') }}</td>
                    <td>{{ $trx->description }}</td>
                    <td class="text-right">{{ number_format($trx->amount, 0, ',', '.') }}</td>
                    <td style="background-color: #f2f2f2;"></td> 
                    <td class="text-right">{{ number_format($balance, 0, ',', '.') }}</td> 
                </tr>
                @empty
                <tr>
                    <td class="text-center">2</td>
                    <td colspan="5" class="text-center font-bold">TIDAK ADA TRANSAKSI</td>
                </tr>
                @endforelse

                <tr>
                    <td colspan="3" class="text-right font-bold">TOTAL REALISASI</td>
                    <td class="text-right font-bold">{{ number_format($totalExpenses, 0, ',', '.') }}</td>
                    <td colspan="2" style="background-color: #f2f2f2;"></td>
                </tr>
                
                <tr>
                    <td colspan="5" class="text-right font-bold">SISA PAGU AKHIR</td>
                    <td class="text-right font-bold">{{ number_format($remainingLimit, 0, ',', '.') }}</td>
                </tr>
            </tbody>
        </table>

        @if(!$loop->last)
            <div class="page-break"></div>
        @endif

    @endforeach

</body>
</html>