<!DOCTYPE html>
<html>
<head>
    <title>Rekap Penggunaan</title>
    <style>
        body { font-family: 'Times New Roman', serif; font-size: 12px; margin: 30px; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }
        .header-table { width: 100%; margin-bottom: 20px; }
        .header-table td { vertical-align: top; }
        .content-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .content-table th, .content-table td { border: 1px solid black; padding: 8px; }
        .signature-table { width: 100%; margin-top: 50px; }
        .signature-table td { text-align: center; vertical-align: top; }
    </style>
</head>
<body>
    <table class="header-table">
        <tr>
            <td width="60%">
                <strong>DAFTAR REKAPITULASI PENGELUARAN</strong><br>
                Bidang Setum – Sekretariat Perusahaan PT ASABRI (Persero)
            </td>
            <td width="40%" class="text-right">
                Rekapitulasi Pengeluaran Bidang Kompro – Sekretariat Perusahaan PT ASABRI (Persero)
            </td>
        </tr>
        <tr>
            <td colspan="2" style="padding-top: 10px;">
                Nomor : {{ $nomorSurat }}
            </td>
        </tr>
    </table>

    <table class="content-table">
        <thead>
            <tr>
                <th width="5%">NO.</th>
                <th>U R A I A N</th>
                <th width="20%">JUMLAH<br>(Rp)</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="text-center">1</td>
                <td>
                    Rekap Realisasi Biaya Penggunaan Corporate Card {{ $report->director->position }} PT ASABRI (Persero) Periode Bulan {{ $report->month_name }} {{ $report->year }}
                </td>
                <td class="text-right">
                    {{ number_format($report->transactions->sum('amount'), 0, ',', '.') }}
                </td>
            </tr>
            <tr>
                <td colspan="2" class="text-right font-bold">JUMLAH .....</td>
                <td class="text-right font-bold">
                    {{ number_format($report->transactions->sum('amount'), 0, ',', '.') }}
                </td>
            </tr>
        </tbody>
    </table>

    <div style="margin-bottom: 20px; font-style: italic;">
        Terbilang : ({{ $terbilang }} Rupiah)
    </div>

    <table class="signature-table">
        <tr>
            <td colspan="2" class="text-right" style="padding-bottom: 20px;">
                Jakarta, {{ date('d F Y') }}
            </td>
        </tr>
        <tr>
            <td width="50%">
                Menyetujui<br>
                Sekretaris Perusahaan,<br><br><br><br><br>
                <strong>(Nama Sekretaris)</strong>
            </td>
            <td width="50%">
                Menyetujui<br>
                Kabid Sekretariat Umum<br><br><br><br><br>
                <strong>(Nama Kabid)</strong>
            </td>
        </tr>
        <tr>
            <td colspan="2" style="text-align: left; padding-top: 20px; font-size: 10px;">
                Keterangan: Unit Kerja Telah Memeriksa serta Memastikan Keaslian dan Validitas dari Berkas Tagihan
            </td>
        </tr>
    </table>
</body>
</html>