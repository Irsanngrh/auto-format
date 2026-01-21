<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>{{ $report->director->name }}</title>
    <style>
        :root { --bg: #FFFFFF; --text: #37352F; --gray: #F7F7F5; --border: #E0E0E0; --text-muted: #9B9A97; --blue: #2383E2; }
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Helvetica, Arial, sans-serif; background-color: var(--bg); color: var(--text); margin: 0; padding: 40px; }
        .container { max-width: 900px; margin: 0 auto; }

        .top-nav { display: flex; justify-content: space-between; align-items: center; margin-bottom: 40px; }
        .btn-back { display: inline-flex; align-items: center; padding: 6px 12px; border: 1px solid var(--border); border-radius: 4px; color: var(--text); text-decoration: none; font-size: 14px; transition: 0.2s; font-weight: 500; }
        .btn-back:hover { background-color: var(--gray); }

        .actions { display: flex; gap: 8px; }
        .btn-tool { padding: 6px 12px; border: 1px solid var(--border); border-radius: 4px; background: transparent; color: var(--text); text-decoration: none; font-size: 14px; cursor: pointer; transition: 0.2s; }
        .btn-tool:hover { background-color: var(--gray); }

        .doc-header { margin-bottom: 40px; }
        h1 { font-size: 40px; font-weight: 700; margin: 0 0 10px 0; letter-spacing: -1px; }
        
        .properties { display: grid; grid-template-columns: 120px 1fr; gap: 10px; margin-bottom: 30px; font-size: 14px; }
        .prop-label { color: var(--text-muted); display: flex; align-items: center; }
        .prop-value { display: flex; align-items: center; }
        .tag { background: #E3E2E0; padding: 2px 6px; border-radius: 3px; font-size: 13px; color: #32302C; }

        .callout { background: #F1F1EF; padding: 16px; border-radius: 4px; display: flex; gap: 24px; margin-bottom: 40px; }
        .callout-item { flex: 1; }
        .callout-label { font-size: 12px; color: var(--text-muted); text-transform: uppercase; font-weight: 600; margin-bottom: 4px; }
        .callout-value { font-size: 18px; font-weight: 600; font-family: 'SF Mono', monospace; }

        h3 { font-size: 18px; font-weight: 600; margin-bottom: 0; padding-bottom: 12px; }
        
        .table-wrap { border: 1px solid var(--border); border-radius: 4px; overflow: hidden; }
        table { width: 100%; border-collapse: collapse; font-size: 14px; table-layout: fixed; }
        th { text-align: left; padding: 10px 12px; color: var(--text-muted); font-weight: 600; font-size: 12px; border-bottom: 1px solid var(--border); background: #FBFBFA; }
        td { padding: 8px 12px; border-bottom: 1px solid var(--border); vertical-align: middle; height: 32px; }
        
        /* FIX FOR INPUTS */
        .input-row td { padding: 0; border-bottom: none; background: #fff; }
        .input-clean { width: 100%; height: 100%; padding: 12px; border: none; font-family: inherit; font-size: 14px; background: transparent; box-sizing: border-box; transition: box-shadow 0.2s; position: relative; }
        
        /* Agar saat fokus input naik ke atas & border menyala */
        .input-clean:focus { background: #fff; outline: none; box-shadow: inset 0 0 0 2px var(--blue); z-index: 2; }
        .input-clean::placeholder { color: #BAB8B5; }
        
        .btn-add { color: #9B9A97; background: none; border: none; font-size: 14px; padding: 12px; cursor: pointer; display: flex; align-items: center; gap: 6px; width: 100%; text-align: left; transition: 0.2s; }
        .btn-add:hover { color: var(--text); background: var(--gray); }

        .btn-del { border: none; background: none; color: #D3D1CB; cursor: pointer; font-size: 16px; }
        .btn-del:hover { color: #EB5757; }
    </style>
</head>
<body>

    <div class="container">
        <div class="top-nav">
            <a href="{{ route('reports.index') }}" class="btn-back">← Dashboard</a>
            
            <div class="actions">
                <a href="{{ route('reports.excel', ['year' => $report->year, 'month' => $report->month, 'slug' => $report->director->slug]) }}" class="btn-tool">Excel</a>
                <a href="{{ route('reports.pdf', ['year' => $report->year, 'month' => $report->month, 'slug' => $report->director->slug]) }}" class="btn-tool">PDF</a>
            </div>
        </div>

        <div class="doc-header">
            <h1>{{ $report->director->name }}</h1>
            
            <div class="properties">
                <div class="prop-label">Jabatan</div>
                <div class="prop-value">{{ $report->director->position }}</div>
                
                <div class="prop-label">Periode</div>
                <div class="prop-value"><span class="tag">{{ $report->month_name }} {{ $report->year }}</span></div>
                
                <div class="prop-label">Kartu</div>
                <div class="prop-value" style="font-family: 'SF Mono', monospace;">
                    @foreach($report->director->creditCards as $card)
                        {{ $card->card_number }}
                    @endforeach
                </div>
            </div>
        </div>

        <div class="callout">
            <div class="callout-item">
                <div class="callout-label">Pagu Awal</div>
                <div class="callout-value">Rp {{ number_format($report->credit_limit, 0, ',', '.') }}</div>
            </div>
            <div class="callout-item">
                <div class="callout-label">Realisasi</div>
                <div class="callout-value" style="color: #EB5757;">Rp {{ number_format($totalExpenses, 0, ',', '.') }}</div>
            </div>
            <div class="callout-item">
                <div class="callout-label">Sisa</div>
                <div class="callout-value" style="color: #219653;">Rp {{ number_format($remainingLimit, 0, ',', '.') }}</div>
            </div>
        </div>

        <h3>Transaksi</h3>
        <div class="table-wrap">
            <form action="{{ route('reports.transaction.store', $report->id) }}" method="POST">
            @csrf
            <table>
                <thead>
                    <tr>
                        <th width="140">Tanggal</th>
                        <th>Keterangan</th>
                        <th width="160" style="text-align: right;">Nominal</th>
                        <th width="160" style="text-align: right;">Sisa Pagu</th>
                        <th width="40"></th>
                    </tr>
                </thead>
                <tbody>
                    @php $balance = $report->credit_limit; @endphp
                    @foreach($report->transactions as $trx)
                    @php $balance -= $trx->amount; @endphp
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($trx->transaction_date)->format('d M Y') }}</td>
                        <td>{{ $trx->description }}</td>
                        <td style="text-align: right; font-family: 'SF Mono', monospace;">{{ number_format($trx->amount, 0, ',', '.') }}</td>
                        <td style="text-align: right; font-family: 'SF Mono', monospace; color: #9B9A97;">{{ number_format($balance, 0, ',', '.') }}</td>
                        <td style="text-align: center;">
                            <button type="submit" form="del-trx-{{$trx->id}}" class="btn-del" onclick="return confirm('Hapus?')">×</button>
                        </td>
                    </tr>
                    @endforeach

                    <tr class="input-row">
                        <td>
                            <input type="date" name="transaction_date" class="input-clean" min="{{ $startDate }}" max="{{ $endDate }}" value="{{ $startDate }}" required>
                        </td>
                        <td>
                            <input type="text" name="description" class="input-clean" placeholder="Ketik keterangan transaksi..." required autocomplete="off">
                        </td>
                        <td>
                            <input type="text" name="amount" id="trxAmount" class="input-clean" placeholder="0" style="text-align: right;" required>
                        </td>
                        <td colspan="2" style="padding: 0;">
                             <button type="submit" class="btn-add" style="height: 42px; border-left: 1px solid var(--border); justify-content: center;">Simpan</button>
                        </td>
                    </tr>
                </tbody>
            </table>
            </form>
        </div>
    </div>

    @foreach($report->transactions as $trx)
        <form id="del-trx-{{$trx->id}}" action="{{ route('reports.transaction.destroy', $trx->id) }}" method="POST" style="display:none;">
            @csrf @method('DELETE')
        </form>
    @endforeach

    <script>
        var trxInput = document.getElementById("trxAmount");
        trxInput.addEventListener("keyup", function(e) { trxInput.value = formatRupiah(this.value); });
        function formatRupiah(angka) {
            var number_string = angka.replace(/[^,\d]/g, "").toString(), split = number_string.split(","), sisa = split[0].length % 3, rupiah = split[0].substr(0, sisa), ribuan = split[0].substr(sisa).match(/\d{3}/gi);
            if (ribuan) { separator = sisa ? "." : ""; rupiah += separator + ribuan.join("."); }
            return split[1] != undefined ? rupiah + "," + split[1] : rupiah;
        }
    </script>
</body>
</html>