<!DOCTYPE html>
<html>
<head>
    <title>Rekap Penggunaan</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 10pt; margin: 30px; line-height: 1.3; }
        
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        
        b, strong, h1, h2, h3, h4, h5, h6, th { font-weight: normal; }
        u { text-decoration: none; }
        
        .header-table { width: 100%; margin-bottom: 0px; }
        
        .title-block { text-align: center; margin-bottom: 30px; margin-top: 30px; }
        .title-row { font-size: 11pt; margin: 2px 0; text-transform: uppercase; }
        
        .content-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .content-table th, .content-table td { border: 1px solid black; padding: 8px; }
        
        .signature-table { width: 100%; margin-top: 30px; page-break-inside: avoid; }
        .signature-table td { text-align: center; vertical-align: top; }
        .upper-text { text-transform: uppercase; }
        
        .footer-note { margin-top: 40px; font-size: 9pt; font-weight: bold !important; }
    </style>
</head>
<body>
    @php
        use Carbon\Carbon;
        Carbon::setLocale('id');
        $currentDate = Carbon::now()->translatedFormat('d F Y');
    @endphp

    <table class="header-table">
        <tr>
            <td width="50%" style="vertical-align: middle;">
                @if(file_exists(public_path('images/logo-asabri.png')))
                    <img src="{{ public_path('images/logo-asabri.png') }}" alt="Logo ASABRI" style="height: 45px; width: auto;">
                @else
                    PT ASABRI (Persero)
                @endif
            </td>
            <td width="50%" class="text-right" style="vertical-align: middle;">
                PO: {{ $manualData['po_no'] }}
            </td>
        </tr>
    </table>

    <div class="title-block">
        <div class="title-row">DAFTAR REKAPITULASI PENGELUARAN</div>
        <div class="title-row" style="text-transform: none;">Rekapitulasi Pengeluaran Divisi Umum</div>
        <div class="title-row" style="text-transform: none;">PT ASABRI (Persero)</div>
        <div class="title-row" style="text-transform: none;">Nomor: {{ $manualData['rekap_no'] }}</div>
    </div>

    <table class="content-table">
        <thead>
            <tr>
                <th width="5%" class="text-center">NO</th>
                <th class="text-center">URAIAN</th>
                <th width="20%" class="text-center">JUMLAH</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="text-center" style="vertical-align: top;">1</td>
                
                <td style="padding: 10px; vertical-align: top;">
                    Rekap Realisasi Biaya Penggunaan Corporate Card Direksi<br>
                    PT ASABRI (Persero) {{ $periodText }}, dengan rincian sebagai berikut:<br><br>
                    <br>
                    {{ strtoupper($report->director->position) }} PT ASABRI (Persero)
                </td>
                
                <td class="text-right" style="vertical-align: bottom;">
                    {{ number_format($report->transactions->sum('amount'), 0, ',', '.') }}
                </td>
            </tr>
            <tr>
                <td></td>
                <td class="text-center">Jumlah Seluruhnya.......</td>
                <td class="text-right">Rp {{ number_format($report->transactions->sum('amount'), 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>

    <div style="margin-bottom: 20px; font-style: italic;">
        Terbilang: {{ $terbilang }} Rupiah
    </div>

    <table class="signature-table">
        <tr>
            <td width="50%">Menyetujui,</td>
            <td width="50%">Jakarta, {{ $currentDate }}</td>
        </tr>
        
        <tr><td colspan="2" style="height: 20px;"></td></tr>

        <tr>
            <td class="upper-text">
                {{ $manualData['signer1_pos'] }}
                <br><br><br><br><br><br>
                {{ $manualData['signer1_name'] }}
            </td>
            <td class="upper-text">
                {{ $manualData['signer2_pos'] }}
                <br><br><br><br><br><br>
                {{ $manualData['signer2_name'] }}
            </td>
        </tr>
    </table>

    <div class="footer-note">
        Keterangan: Unit Kerja Telah Memeriksa serta memastikan Keaslian dan Validitas dari Berkas Tagihan
    </div>
</body>
</html>