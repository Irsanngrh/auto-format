<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
    <style>
        :root { --bg: #FFFFFF; --text: #37352F; --gray-hover: #F7F7F5; --border: #E0E0E0; --text-muted: #9B9A97; --blue: #2383E2; }
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Helvetica, Arial, sans-serif; background-color: var(--bg); color: var(--text); margin: 0; padding: 40px; }
        .container { max-width: 1200px; margin: 0 auto; }
        
        /* HEADER */
        .header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 24px; }
        .header h1 { font-size: 32px; font-weight: 700; margin: 0; letter-spacing: -0.5px; }
        .btn-new { background-color: var(--blue); color: white; border: none; padding: 8px 16px; border-radius: 4px; font-size: 14px; font-weight: 500; text-decoration: none; transition: 0.2s; }
        .btn-new:hover { background-color: #0070DA; }

        /* FILTER BAR (Notion Style) */
        .toolbar { display: flex; flex-wrap: wrap; gap: 8px; margin-bottom: 20px; align-items: center; padding-bottom: 12px; border-bottom: 1px solid var(--border); }
        .filter-label { font-size: 12px; color: var(--text-muted); font-weight: 600; text-transform: uppercase; margin-right: 8px; display: flex; align-items: center; gap: 6px; }
        
        .filter-pill { position: relative; display: inline-block; }
        .filter-pill select { appearance: none; -webkit-appearance: none; background-color: transparent; border: 1px solid var(--border); border-radius: 100px; padding: 6px 32px 6px 12px; font-size: 13px; color: var(--text); cursor: pointer; transition: 0.2s; font-family: inherit; font-weight: 500; }
        .filter-pill select:hover { background-color: var(--gray-hover); border-color: #C0C0C0; }
        .filter-pill select:focus { outline: none; border-color: var(--blue); box-shadow: 0 0 0 2px rgba(35, 131, 226, 0.2); }
        
        /* Icon panah dropdown custom */
        .filter-pill::after { content: '▼'; font-size: 8px; color: var(--text-muted); position: absolute; right: 12px; top: 50%; transform: translateY(-50%); pointer-events: none; }

        .bulk-actions { margin-left: auto; display: flex; gap: 8px; }
        .btn-action { background: transparent; border: 1px solid var(--border); padding: 6px 12px; border-radius: 4px; font-size: 13px; cursor: pointer; color: var(--text); font-weight: 500; }
        .btn-action:hover { background: var(--gray-hover); }

        /* TABLE */
        .table-container { border: 1px solid var(--border); border-radius: 6px; overflow: hidden; box-shadow: 0 1px 2px rgba(0,0,0,0.02); }
        table { width: 100%; border-collapse: collapse; }
        
        /* SORTABLE HEADERS */
        th { text-align: left; background: #FBFBFA; border-bottom: 1px solid var(--border); padding: 0; }
        th a { display: block; padding: 12px 16px; font-size: 12px; font-weight: 600; color: var(--text-muted); text-decoration: none; transition: 0.2s; user-select: none; }
        th a:hover { background: #F0F0F0; color: var(--text); }
        th a.active { color: var(--blue); background: #EFF6FC; }
        .sort-icon { float: right; font-size: 10px; margin-top: 2px; }

        td { padding: 12px 16px; font-size: 14px; border-bottom: 1px solid var(--border); vertical-align: middle; }
        tr:last-child td { border-bottom: none; }
        tr:hover { background-color: var(--gray-hover); }

        .status-badge { display: inline-block; padding: 2px 8px; border-radius: 4px; font-size: 12px; font-weight: 500; background: #E3E2E0; color: #32302C; }
        .amount-text { font-family: 'SF Mono', 'Consolas', monospace; font-size: 13px; letter-spacing: -0.3px; }
        
        .row-actions a { color: var(--text); text-decoration: none; margin-right: 12px; font-weight: 500; font-size: 13px; opacity: 0.6; }
        .row-actions a:hover { opacity: 1; text-decoration: underline; }
        .btn-row-delete { background: none; border: none; color: #EB5757; cursor: pointer; padding: 0; font-size: 13px; opacity: 0.6; }
        .btn-row-delete:hover { opacity: 1; text-decoration: underline; }

        .empty-state { padding: 60px; text-align: center; color: var(--text-muted); font-size: 14px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Laporan Keuangan</h1>
            <a href="{{ route('reports.create') }}" class="btn-new">Baru +</a>
        </div>

        <form action="{{ route('reports.index') }}" method="GET" class="toolbar">
            <div class="filter-label">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"></polygon></svg>
                Filter:
            </div>

            <div class="filter-pill">
                <select name="year" onchange="this.form.submit()">
                    @foreach($availableYears as $year)
                        <option value="{{ $year }}" {{ $filterYear == $year ? 'selected' : '' }}>Tahun {{ $year }}</option>
                    @endforeach
                    @if($availableYears->isEmpty())
                        <option value="{{ date('Y') }}">Tahun {{ date('Y') }}</option>
                    @endif
                </select>
            </div>

            <div class="filter-pill">
                <select name="month" onchange="this.form.submit()">
                    <option value="">Semua Bulan</option>
                    @foreach([1=>'Januari', 2=>'Februari', 3=>'Maret', 4=>'April', 5=>'Mei', 6=>'Juni', 7=>'Juli', 8=>'Agustus', 9=>'September', 10=>'Oktober', 11=>'November', 12=>'Desember'] as $k => $v)
                        <option value="{{ $k }}" {{ $filterMonth == $k ? 'selected' : '' }}>{{ $v }}</option>
                    @endforeach
                </select>
            </div>

            <div class="filter-pill">
                <select name="type" onchange="this.form.submit()">
                    <option value="monthly" {{ $filterType == 'monthly' ? 'selected' : '' }}>Tampilan Bulanan</option>
                    <option value="yearly" {{ $filterType == 'yearly' ? 'selected' : '' }}>Rekap Tahunan</option>
                </select>
            </div>

            <input type="hidden" name="sort" id="sortInput" value="{{ $sortBy }}">

            <div class="bulk-actions">
                @if($filterType == 'monthly')
                    <button type="submit" formaction="{{ route('reports.bulk_action') }}" name="action" value="excel" class="btn-action" formmethod="POST">Unduh Excel</button>
                    <button type="submit" formaction="{{ route('reports.bulk_action') }}" name="action" value="pdf" class="btn-action" formmethod="POST">Unduh PDF</button>
                    @csrf 
                @endif
            </div>
        </form>

        <form action="{{ route('reports.bulk_action') }}" method="POST">
            @csrf
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            @if($filterType == 'monthly')
                                <th style="width: 40px; text-align: center;"><input type="checkbox" onclick="toggle(this)"></th>
                            @endif
                            <th style="width: 250px;"><a href="#">Direksi</a></th>
                            
                            <th style="width: 150px;">
                                <a href="?year={{$filterYear}}&month={{$filterMonth}}&type={{$filterType}}&sort={{ $sortBy == 'period_desc' ? 'period_asc' : 'period_desc' }}" class="{{ str_contains($sortBy, 'period') ? 'active' : '' }}">
                                    Periode
                                    <span class="sort-icon">{{ $sortBy == 'period_desc' ? '↓' : ($sortBy == 'period_asc' ? '↑' : '') }}</span>
                                </a>
                            </th>
                            <th style="width: 150px;">
                                <a href="?year={{$filterYear}}&month={{$filterMonth}}&type={{$filterType}}&sort={{ $sortBy == 'pagu_high' ? 'pagu_low' : 'pagu_high' }}" class="{{ str_contains($sortBy, 'pagu') ? 'active' : '' }}">
                                    Pagu Awal
                                    <span class="sort-icon">{{ $sortBy == 'pagu_high' ? '↓' : ($sortBy == 'pagu_low' ? '↑' : '') }}</span>
                                </a>
                            </th>
                            <th style="width: 150px;">
                                <a href="?year={{$filterYear}}&month={{$filterMonth}}&type={{$filterType}}&sort={{ $sortBy == 'realisasi_high' ? 'realisasi_low' : 'realisasi_high' }}" class="{{ str_contains($sortBy, 'realisasi') ? 'active' : '' }}">
                                    Realisasi
                                    <span class="sort-icon">{{ $sortBy == 'realisasi_high' ? '↓' : ($sortBy == 'realisasi_low' ? '↑' : '') }}</span>
                                </a>
                            </th>
                            <th style="width: 150px;">
                                <a href="?year={{$filterYear}}&month={{$filterMonth}}&type={{$filterType}}&sort={{ $sortBy == 'sisa_high' ? 'sisa_low' : 'sisa_high' }}" class="{{ str_contains($sortBy, 'sisa') ? 'active' : '' }}">
                                    Sisa Pagu
                                    <span class="sort-icon">{{ $sortBy == 'sisa_high' ? '↓' : ($sortBy == 'sisa_low' ? '↑' : '') }}</span>
                                </a>
                            </th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($reports as $report)
                        <tr>
                            @if(!$report->is_aggregate)
                                <td style="text-align: center;"><input type="checkbox" name="report_ids[]" value="{{ $report->id }}"></td>
                            @elseif($filterType == 'monthly')
                                <td></td>
                            @endif

                            <td>
                                <div style="font-weight: 500;">{{ $report->director->name }}</div>
                                <div style="font-size: 12px; color: var(--text-muted); margin-top: 2px;">{{ $report->director->position }}</div>
                            </td>
                            
                            <td>
                                @if($report->is_aggregate)
                                    <span class="status-badge">Tahun {{ $report->year }}</span>
                                @else
                                    <span class="status-badge">{{ $report->month_name }} {{ $report->year }}</span>
                                @endif
                            </td>

                            <td class="amount-text">Rp {{ number_format($report->credit_limit, 0, ',', '.') }}</td>
                            <td class="amount-text">Rp {{ number_format($report->total_expenses, 0, ',', '.') }}</td>
                            <td class="amount-text" style="color: {{ $report->remaining_limit < 0 ? '#EB5757' : 'inherit' }}">Rp {{ number_format($report->remaining_limit, 0, ',', '.') }}</td>
                            
                            <td class="row-actions" style="text-align: right;">
                                @if(!$report->is_aggregate)
                                    <a href="{{ route('reports.show', ['year' => $report->year, 'month' => $report->month, 'slug' => $report->director->slug]) }}">Buka</a>
                                    <button type="button" class="btn-row-delete" onclick="confirmDelete('{{ $report->id }}')">Hapus</button>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="empty-state">Tidak ada data ditemukan.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </form>
    </div>

    @foreach($reports as $report)
        @if(!$report->is_aggregate)
            <form id="del-form-{{ $report->id }}" action="{{ route('reports.destroy', $report->id) }}" method="POST" style="display:none;">
                @csrf @method('DELETE')
            </form>
        @endif
    @endforeach

    <script>
        function toggle(source) {
            checkboxes = document.getElementsByName('report_ids[]');
            for(var i=0, n=checkboxes.length;i<n;i++) { checkboxes[i].checked = source.checked; }
        }
        function confirmDelete(id) {
            if(confirm('Hapus laporan ini permanen?')) {
                document.getElementById('del-form-' + id).submit();
            }
        }
    </script>
</body>
</html>