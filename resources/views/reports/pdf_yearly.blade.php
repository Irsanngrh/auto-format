<!DOCTYPE html>
<html>
<head>
    <title>Rekap Tahunan</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 10px; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid black; padding: 6px; vertical-align: middle; }
        .header { text-align: center; font-weight: bold; margin-bottom: 20px; font-size: 14px; }
    </style>
</head>
<body>
    <div class="header">
        REKAPITULASI PENGGUNAAN CORPORATE CREDIT CARD<br>
        DIREKSI PT ASABRI (PERSERO)<br>
        TAHUN {{ $year }}
    </div>

    <table>
        <thead>
            <tr style="background-color: #d9d9d9;">
                <th width="35%">NAMA DIREKSI</th>
                <th width="20%">JABATAN</th>
                <th width="15%" class="text-right">TOTAL PAGU (Setahun)</th>
                <th width="15%" class="text-right">TOTAL REALISASI</th>
                <th width="15%" class="text-right">SISA PAGU</th>
            </tr>
        </thead>
        <tbody>
            @foreach($reports as $report)
            <tr>
                <td>{{ $report->director->name }}</td>
                <td>{{ $report->director->position }}</td>
                <td class="text-right">{{ number_format($report->credit_limit, 0, ',', '.') }}</td>
                <td class="text-right">{{ number_format($report->total_expenses, 0, ',', '.') }}</td>
                <td class="text-right">{{ number_format($report->remaining_limit, 0, ',', '.') }}</td>
            </tr>
            @endforeach
            
            <tr style="background-color: #f0f0f0; font-weight: bold;">
                <td colspan="2" class="text-center">TOTAL KESELURUHAN</td>
                <td class="text-right">{{ number_format($reports->sum('credit_limit'), 0, ',', '.') }}</td>
                <td class="text-right">{{ number_format($reports->sum('total_expenses'), 0, ',', '.') }}</td>
                <td class="text-right">{{ number_format($reports->sum('remaining_limit'), 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>
</body>
</html>