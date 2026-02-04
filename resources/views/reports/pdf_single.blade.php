<!DOCTYPE html>
<html>
<head>
    <title>Rekap Penggunaan</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; margin: 30px; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .fw-normal { font-weight: normal; }
        .header-table { width: 100%; margin-bottom: 10px; }
        .header-table td { vertical-align: top; }
        .title-block { text-align: center; margin-bottom: 25px; margin-top: 20px; }
        .title-main { font-size: 16px; margin-bottom: 4px; }
        .content-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .content-table th, .content-table td { border: 1px solid black; padding: 6px; vertical-align: top; }
        .signature-table { width: 100%; margin-top: 40px; page-break-inside: avoid; }
        .signature-table td { text-align: center; vertical-align: top; }
        .upper-text { text-transform: uppercase; }
        .footer-note { margin-top: 50px; font-size: 11px; font-weight: bold; }
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
                <img src="{{ public_path('images/logo-asabri.png') }}" alt="Logo ASABRI" style="height: 35px; width: auto;">
            </td>
            <td width="50%" class="text-right" style="vertical-align: middle;">
                PO: {{ $manualData['po_no'] }}
            </td>
        </tr>
    </table>

    <div class="title-block">
        <div class="title-main">DAFTAR REKAPITULASI PENGELUARAN</div>
        <div style="font-size: 13px;">Rekapitulasi Pengeluaran Divisi Umum</div>
        <div style="font-size: 13px;">PT ASABRI (Persero)</div>
        <div style="font-size: 13px;">Nomor: {{ $manualData['rekap_no'] }}</div>
    </div>

    <table class="content-table">
        <thead>
            <tr>
                <th width="5%" class="text-center fw-normal">NO</th>
                <th class="text-center fw-normal">URAIAN</th>
                <th width="20%" class="text-center fw-normal">JUMLAH</th>
            </tr>
            <tr style="font-size: 10px;">
                <th class="text-center fw-normal">1</th>
                <th class="text-center fw-normal">2</th>
                <th class="text-center fw-normal">3</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="text-center">1</td>
                <td>
                    Rekap Realisasi Biaya Penggunaan Corporate Card Direksi<br>
                    PT ASABRI (Persero) {{ $periodText }}, dengan rincian sebagai berikut:<br><br>
                    {{ strtoupper($report->director->position) }} PT ASABRI (Persero)
                </td>
                <td class="text-right" style="vertical-align: bottom;">
                    <br><br><br>
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
            <td width="50%" style="padding-bottom: 20px;">Menyetujui,</td>
            <td width="50%" style="padding-bottom: 20px;">Jakarta, {{ $currentDate }}</td>
        </tr>
        <tr>
            <td class="upper-text">
                {{ $manualData['signer1_pos'] }}
                <br><br><br><br><br>
                {{ $manualData['signer1_name'] }}
            </td>
            <td class="upper-text">
                {{ $manualData['signer2_pos'] }}
                <br><br><br><br><br>
                {{ $manualData['signer2_name'] }}
            </td>
        </tr>
    </table>

    <div class="footer-note">
        Keterangan: Unit Kerja Telah Memeriksa serta memastikan Keaslian dan Validitas dari Berkas Tagihan
    </div>
</body>
</html>