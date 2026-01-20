<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan - {{ $report->director->name }}</title>
    <style>
        body { font-family: sans-serif; padding: 20px; max-width: 1000px; margin: auto; }
        .header-info { margin-bottom: 20px; padding: 15px; background: #f9f9f9; border: 1px solid #ddd; }
        .stats { display: flex; justify-content: space-between; margin-bottom: 20px; }
        .stat-box { background: #eee; padding: 15px; width: 30%; text-align: center; border-radius: 5px; }
        .stat-val { font-size: 1.2em; font-weight: bold; color: #333; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #999; padding: 8px; text-align: left; }
        th { background-color: #ddd; text-align: center; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .form-area { background: #e3f2fd; padding: 15px; border-radius: 5px; margin-bottom: 20px; }
        .btn-green { background-color: #28a745; color: white; padding: 10px; border: none; cursor: pointer; text-decoration: none; border-radius: 3px; }
        .btn-red { background-color: #dc3545; color: white; padding: 10px; text-decoration: none; border-radius: 3px; }
        input { padding: 8px; margin-right: 5px; }
    </style>
</head>
<body>

    <div style="display: flex; justify-content: space-between; align-items: center;">
        <h2>Laporan: {{ $report->director->name }}</h2>
        <a href="{{ route('reports.create') }}">Buat Baru</a>
    </div>

    <div class="header-info">
        <strong>Jabatan:</strong> {{ $report->director->position }} <br>
        <strong>Periode:</strong> {{ $report->month_name }} {{ $report->year }} <br>
        <strong>No Kartu:</strong> 
        @foreach($report->director->creditCards as $card)
            {{ $card->card_number }} <br>
        @endforeach
    </div>

    <div class="stats">
        <div class="stat-box">
            <div>Batas Kredit (Pagu)</div>
            <div class="stat-val">Rp {{ number_format($report->credit_limit, 0, ',', '.') }}</div>
        </div>
        <div class="stat-box">
            <div>Total Pemakaian</div>
            <div class="stat-val" style="color: #dc3545;">Rp {{ number_format($totalExpenses, 0, ',', '.') }}</div>
        </div>
        <div class="stat-box" style="background-color: #d4edda;">
            <div>Sisa Pagu Akhir</div>
            <div class="stat-val" style="color: #28a745;">Rp {{ number_format($remainingLimit, 0, ',', '.') }}</div>
        </div>
    </div>

    <div style="margin-bottom: 20px; text-align: right;">
        <a href="{{ route('reports.excel', $report->id) }}" class="btn-green" style="margin-right: 10px;">Download Excel</a>
        <a href="{{ route('reports.pdf', $report->id) }}" class="btn-red" target="_blank">Download PDF</a>
    </div>

    <div class="form-area">
        <h4>Input Pengeluaran Baru</h4>
        <form action="{{ route('reports.transaction.store', $report->id) }}" method="POST">
            @csrf
            <input type="date" name="transaction_date" required>
            <input type="text" name="description" placeholder="Keterangan" style="width: 300px;" required>
            <input type="text" name="amount" id="trxAmount" placeholder="Jumlah (Rp)" required>
            <button type="submit" class="btn-green">Simpan</button>
        </form>
    </div>

    <h3>Rekapitulasi Transaksi</h3>
    <table>
        <thead>
            <tr>
                <th width="5%" class="text-center">No</th>
                <th width="15%" class="text-center">Tanggal</th>
                <th>Keterangan</th>
                <th width="20%" class="text-center">Jumlah (Realisasi)</th>
                <th width="20%" class="text-center">Sisa Pagu</th>
                <th width="10%" class="text-center">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @php $balance = $report->credit_limit; @endphp

            @forelse($report->transactions as $index => $trx)
            @php $balance -= $trx->amount; @endphp
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td class="text-center">{{ \Carbon\Carbon::parse($trx->transaction_date)->format('d/m/Y') }}</td>
                <td>{{ $trx->description }}</td>
                <td class="text-right">Rp {{ number_format($trx->amount, 0, ',', '.') }}</td>
                <td class="text-right">Rp {{ number_format($balance, 0, ',', '.') }}</td>
                <td class="text-center">
                    <form action="{{ route('reports.transaction.destroy', $trx->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus data ini?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" style="background: red; color: white; border: none; padding: 5px 10px; cursor: pointer; border-radius: 4px;">Hapus</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="text-center">Belum ada transaksi bulan ini.</td>
            </tr>
            @endforelse
            
            <tr style="background-color: #f0f0f0; font-weight: bold;">
                <td colspan="3" class="text-right">TOTAL REALISASI</td>
                <td class="text-right">Rp {{ number_format($totalExpenses, 0, ',', '.') }}</td>
                <td colspan="2"></td>
            </tr>
        </tbody>
    </table>

    <script>
        var trxInput = document.getElementById("trxAmount");
        trxInput.addEventListener("keyup", function(e) {
            trxInput.value = formatRupiah(this.value);
        });

        function formatRupiah(angka) {
            var number_string = angka.replace(/[^,\d]/g, "").toString(),
                split = number_string.split(","),
                sisa = split[0].length % 3,
                rupiah = split[0].substr(0, sisa),
                ribuan = split[0].substr(sisa).match(/\d{3}/gi);

            if (ribuan) {
                separator = sisa ? "." : "";
                rupiah += separator + ribuan.join(".");
            }
            return split[1] != undefined ? rupiah + "," + split[1] : rupiah;
        }
    </script>
</body>
</html>