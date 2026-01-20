<table>
    <thead>
        <tr>
            <td colspan="6" style="text-align: center; font-weight: bold;">
                LAPORAN REALISASI PEMAKAIAN CREDIT CARD CORPORATE
            </td>
        </tr>
        <tr>
            <td colspan="6" style="text-align: center; font-weight: bold;">
                DIREKSI PT ASABRI (PERSERO)
            </td>
        </tr>
        <tr>
            <td colspan="6" style="text-align: center; font-weight: bold;">
                PERIODE {{ $report->month_name }} TAHUN {{ $report->year }}
            </td>
        </tr>
        <tr><td colspan="6"></td></tr>

        <tr>
            <td style="font-weight: bold;">NAMA</td>
            <td>: {{ $report->director->name }}</td>
            <td colspan="4"></td>
        </tr>
        <tr>
            <td style="font-weight: bold;">JABATAN</td>
            <td>: {{ $report->director->position }}</td>
            <td colspan="4"></td>
        </tr>
        <tr>
            <td style="font-weight: bold;">NO. KARTU</td>
            <td>: 
                @foreach($report->director->creditCards as $card)
                    {{ $card->card_number }} 
                @endforeach
            </td>
            <td colspan="4"></td>
        </tr>
        <tr><td colspan="6"></td></tr>

        <tr>
            <th style="font-weight: bold; border: 1px solid black; text-align: center;">NO</th>
            <th style="font-weight: bold; border: 1px solid black; text-align: center;">TANGGAL TRANSAKSI</th>
            <th style="font-weight: bold; border: 1px solid black; text-align: center;">KETERANGAN</th>
            <th style="font-weight: bold; border: 1px solid black; text-align: center;">JUMLAH (Rp)</th>
            <th style="font-weight: bold; border: 1px solid black; text-align: center;">BATAS KREDIT (PAGU)</th>
            <th style="font-weight: bold; border: 1px solid black; text-align: center;">SISA PAGU (Rp)</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td style="border: 1px solid black; text-align: center;">1</td>
            <td style="border: 1px solid black;"></td>
            <td style="border: 1px solid black; font-weight: bold;">BATAS KREDIT (PAGU)</td>
            <td style="border: 1px solid black;"></td>
            <td style="border: 1px solid black; text-align: right; font-weight: bold;">{{ number_format($report->credit_limit, 0, ',', '.') }}</td>
            <td style="border: 1px solid black;"></td>
        </tr>

        @php $balance = $report->credit_limit; @endphp

        @forelse($report->transactions as $index => $trx)
        @php $balance -= $trx->amount; @endphp
        <tr>
            <td style="border: 1px solid black; text-align: center;">{{ $index + 2 }}</td>
            <td style="border: 1px solid black; text-align: center;">{{ \Carbon\Carbon::parse($trx->transaction_date)->format('d/m/Y') }}</td>
            <td style="border: 1px solid black;">{{ $trx->description }}</td>
            <td style="border: 1px solid black; text-align: right;">{{ number_format($trx->amount, 0, ',', '.') }}</td>
            <td style="border: 1px solid black;"></td>
            <td style="border: 1px solid black; text-align: right;">{{ number_format($balance, 0, ',', '.') }}</td>
        </tr>
        @empty
        <tr>
            <td style="border: 1px solid black; text-align: center;">2</td>
            <td colspan="5" style="border: 1px solid black; text-align: center;">TIDAK ADA TRANSAKSI</td>
        </tr>
        @endforelse

        <tr>
            <td colspan="3" style="border: 1px solid black; text-align: right; font-weight: bold;">TOTAL REALISASI</td>
            <td style="border: 1px solid black; text-align: right; font-weight: bold;">{{ number_format($totalExpenses, 0, ',', '.') }}</td>
            <td colspan="2" style="border: 1px solid black;"></td>
        </tr>
        
        <tr>
            <td colspan="5" style="border: 1px solid black; text-align: right; font-weight: bold;">SISA PAGU AKHIR</td>
            <td style="border: 1px solid black; text-align: right; font-weight: bold;">{{ number_format($remainingLimit, 0, ',', '.') }}</td>
        </tr>
    </tbody>
</table>