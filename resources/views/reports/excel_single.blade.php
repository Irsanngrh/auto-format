<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
@php
    use Carbon\Carbon;
    Carbon::setLocale('id');
    $dateNow = Carbon::now()->translatedFormat('d F Y');
@endphp

<table>
    <tr height="45">
        <td></td><td></td><td></td>
        <td style="text-align: right; vertical-align: middle;">PO: {{ $manualData['po_no'] }}</td>
    </tr>

    <tr height="20"><td colspan="4"></td></tr>

    <tr><td colspan="4" style="text-align: center; font-size: 11pt;">DAFTAR REKAPITULASI PENGELUARAN</td></tr>
    <tr><td colspan="4" style="text-align: center; font-size: 11pt;">Rekapitulasi Pengeluaran Divisi Umum</td></tr>
    <tr><td colspan="4" style="text-align: center; font-size: 11pt;">PT ASABRI (Persero)</td></tr>
    <tr><td colspan="4" style="text-align: center; font-size: 11pt;">Nomor: {{ $manualData['rekap_no'] }}</td></tr>

    <tr height="20"><td colspan="4"></td></tr>

    <tr height="30">
        <td style="border: 1px solid #000; text-align: center; vertical-align: middle;">NO</td>
        <td colspan="2" style="border: 1px solid #000; text-align: center; vertical-align: middle;">URAIAN</td>
        <td style="border: 1px solid #000; text-align: center; vertical-align: middle;">JUMLAH</td>
    </tr>

    <tr>
        <td style="border-top: 1px solid #000; border-left: 1px solid #000; border-right: 1px solid #000; text-align: center; vertical-align: middle;">1</td>
        <td colspan="2" style="border-top: 1px solid #000; border-left: 1px solid #000; border-right: 1px solid #000; vertical-align: middle; wrap-text: true;">
            Rekap Realisasi Biaya Penggunaan Corporate Card Direksi PT ASABRI (Persero) {{ $periodText }}, dengan rincian sebagai berikut:
        </td>
        <td style="border-top: 1px solid #000; border-left: 1px solid #000; border-right: 1px solid #000;"></td>
    </tr>

    <tr>
        <td style="border-bottom: 1px solid #000; border-left: 1px solid #000; border-right: 1px solid #000;"></td>
        <td colspan="2" style="border-bottom: 1px solid #000; border-left: 1px solid #000; border-right: 1px solid #000; vertical-align: middle; wrap-text: true;">
            {{ strtoupper($report->director->position) }} PT ASABRI (Persero)
        </td>
        <td style="border-bottom: 1px solid #000; border-left: 1px solid #000; border-right: 1px solid #000; text-align: right; vertical-align: middle;">
            {{ number_format($report->transactions->sum('amount'), 0, ',', '.') }}
        </td>
    </tr>

    <tr height="30">
        <td style="border: 1px solid #000;"></td>
        <td colspan="2" style="border: 1px solid #000; text-align: center; vertical-align: middle;">Jumlah Seluruhnya.......</td>
        <td style="border: 1px solid #000; text-align: right; vertical-align: middle;">Rp {{ number_format($report->transactions->sum('amount'), 0, ',', '.') }}</td>
    </tr>

    <tr height="15"><td colspan="4"></td></tr>

    <tr height="30">
        <td colspan="4" style="font-style: italic; vertical-align: middle;">
            Terbilang: {{ $terbilang }} Rupiah
        </td>
    </tr>

    <tr height="15"><td colspan="4"></td></tr>

    <tr>
        <td colspan="2" style="text-align: left; padding-left: 30px;">Menyetujui,</td>
        <td></td>
        <td style="text-align: center;">Jakarta, {{ $dateNow }}</td>
    </tr>

    <tr height="20"><td colspan="4"></td></tr>

    <tr>
        <td colspan="2" style="text-align: left; padding-left: 30px;">{{ $manualData['signer1_pos'] }}</td>
        <td></td>
        <td style="text-align: center;">{{ $manualData['signer2_pos'] }}</td>
    </tr>

    <tr height="20"><td colspan="4"></td></tr>
    <tr height="20"><td colspan="4"></td></tr>
    <tr height="20"><td colspan="4"></td></tr>
    <tr height="20"><td colspan="4"></td></tr>

    <tr>
        <td colspan="2" style="text-align: left; padding-left: 30px;">{{ $manualData['signer1_name'] }}</td>
        <td></td>
        <td style="text-align: center;">{{ $manualData['signer2_name'] }}</td>
    </tr>

    <tr height="30"><td colspan="4"></td></tr>
    
    <tr>
        <td colspan="4" style="font-weight: bold; font-size: 10px; vertical-align: middle;">
            Keterangan: Unit Kerja Telah Memeriksa serta memastikan Keaslian dan Validitas dari Berkas Tagihan
        </td>
    </tr>
</table>