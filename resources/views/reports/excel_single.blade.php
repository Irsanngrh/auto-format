<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
@php
    use Carbon\Carbon;
    Carbon::setLocale('id');
    $dateNow = Carbon::now()->translatedFormat('d F Y');
    $bulanStr = ucfirst(strtolower($report->month_name)); 
@endphp

<table>
    <tr height="45">
        <td></td>
        <td></td>
        <td></td>
        <td style="text-align: right; vertical-align: middle; font-weight: bold;">PO: {{ $manualData['po_no'] }}</td>
    </tr>

    <tr height="20"><td colspan="4"></td></tr>

    <tr height="20">
        <td colspan="4" style="text-align: center; vertical-align: middle; font-size: 14px;">DAFTAR REKAPITULASI PENGELUARAN</td>
    </tr>
    <tr height="18">
        <td colspan="4" style="text-align: center; vertical-align: middle; font-size: 12px;">Rekapitulasi Pengeluaran Divisi Umum</td>
    </tr>
    <tr height="18">
        <td colspan="4" style="text-align: center; vertical-align: middle; font-size: 12px;">PT ASABRI (Persero)</td>
    </tr>
    <tr height="18">
        <td colspan="4" style="text-align: center; vertical-align: middle; font-size: 12px;">Nomor: {{ $manualData['rekap_no'] }}</td>
    </tr>

    <tr height="10"><td colspan="4"></td></tr>

    <tr height="25">
        <td style="border: 1px solid #000000; text-align: center; vertical-align: middle; font-weight: bold;">NO</td>
        <td colspan="2" style="border: 1px solid #000000; text-align: center; vertical-align: middle; font-weight: bold;">URAIAN</td>
        <td style="border: 1px solid #000000; text-align: center; vertical-align: middle; font-weight: bold;">JUMLAH</td>
    </tr>
    <tr height="15">
        <td style="border: 1px solid #000000; text-align: center; vertical-align: middle;">1</td>
        <td colspan="2" style="border: 1px solid #000000; text-align: center; vertical-align: middle;">2</td>
        <td style="border: 1px solid #000000; text-align: center; vertical-align: middle;">3</td>
    </tr>

    <tr>
        <td style="border-top: 1px solid #000000; border-left: 1px solid #000000; border-right: 1px solid #000000; border-bottom: none; text-align: center; vertical-align: middle;">1</td>
        <td colspan="2" style="border-top: 1px solid #000000; border-left: 1px solid #000000; border-right: 1px solid #000000; border-bottom: none; vertical-align: middle; wrap-text: true;">
            Rekap Realisasi Biaya Penggunaan Corporate Card Direksi PT ASABRI (Persero) Periode Bulan {{ $bulanStr }} {{ $report->year }}, dengan rincian sebagai berikut:
        </td>
        <td style="border-top: 1px solid #000000; border-left: 1px solid #000000; border-right: 1px solid #000000; border-bottom: none;"></td>
    </tr>

    <tr>
        <td style="border-bottom: 1px solid #000000; border-left: 1px solid #000000; border-right: 1px solid #000000; border-top: none;"></td>
        <td colspan="2" style="border-bottom: 1px solid #000000; border-left: 1px solid #000000; border-right: 1px solid #000000; border-top: none; vertical-align: middle; wrap-text: true;">
            {{ strtoupper($report->director->position) }} PT ASABRI (Persero)
        </td>
        <td style="border-bottom: 1px solid #000000; border-left: 1px solid #000000; border-right: 1px solid #000000; border-top: none; text-align: right; vertical-align: middle;">
            {{ number_format($report->transactions->sum('amount'), 0, ',', '.') }}
        </td>
    </tr>

    <tr height="25">
        <td style="border: 1px solid #000000;"></td>
        <td colspan="2" style="border: 1px solid #000000; text-align: center; vertical-align: middle; font-weight: bold;">Jumlah Seluruhnya.......</td>
        <td style="border: 1px solid #000000; text-align: right; vertical-align: middle; font-weight: bold;">Rp {{ number_format($report->transactions->sum('amount'), 0, ',', '.') }}</td>
    </tr>

    <tr height="15"><td colspan="4"></td></tr>

    <tr height="30">
        <td colspan="4" style="font-style: italic; vertical-align: middle;">
            Terbilang: {{ $terbilang }} Rupiah
        </td>
    </tr>

    <tr height="15"><td colspan="4"></td></tr>

    <tr>
        <td colspan="2" style="text-align: center; vertical-align: middle;">Menyetujui,</td>
        <td></td>
        <td style="text-align: center; vertical-align: middle;">Jakarta, {{ $dateNow }}</td>
    </tr>

    <tr>
        <td colspan="2" style="text-align: center; vertical-align: middle;">{{ $manualData['signer1_pos'] }}</td>
        <td></td>
        <td style="text-align: center; vertical-align: middle;">{{ $manualData['signer2_pos'] }}</td>
    </tr>

    <tr height="20"><td colspan="4"></td></tr>
    <tr height="20"><td colspan="4"></td></tr>
    <tr height="20"><td colspan="4"></td></tr>

    <tr>
        <td colspan="2" style="text-align: center; vertical-align: middle;">{{ $manualData['signer1_name'] }}</td>
        <td></td>
        <td style="text-align: center; vertical-align: middle;">{{ $manualData['signer2_name'] }}</td>
    </tr>

    <tr height="30"><td colspan="4"></td></tr>
    
    <tr>
        <td colspan="4" style="font-weight: bold; font-size: 10px; vertical-align: middle;">
            Keterangan: Unit Kerja Telah Memeriksa serta memastikan Keaslian dan Validitas dari Berkas Tagihan
        </td>
    </tr>
</table>