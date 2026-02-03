<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
    <style>
        :root { --bg: #FFFFFF; --text: #37352F; --gray-hover: #F7F7F5; --border: #E0E0E0; --text-muted: #9B9A97; --blue: #2383E2; }
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Helvetica, Arial, sans-serif; background-color: var(--bg); color: var(--text); margin: 0; padding: 40px; }
        .container { max-width: 1200px; margin: 0 auto; }
        .header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 24px; }
        .header h1 { font-size: 32px; font-weight: 700; margin: 0; letter-spacing: -0.5px; }
        .btn-new { background-color: var(--blue); color: white; border: none; padding: 8px 16px; border-radius: 4px; font-size: 14px; font-weight: 500; text-decoration: none; transition: 0.2s; }
        .btn-new:hover { background-color: #0070DA; }
        .toolbar { display: flex; flex-wrap: wrap; gap: 8px; margin-bottom: 20px; align-items: center; padding-bottom: 12px; border-bottom: 1px solid var(--border); }
        .filter-label { font-size: 12px; color: var(--text-muted); font-weight: 600; text-transform: uppercase; margin-right: 8px; display: flex; align-items: center; gap: 6px; }
        .filter-pill { position: relative; display: inline-block; }
        .filter-pill select { appearance: none; -webkit-appearance: none; background-color: transparent; border: 1px solid var(--border); border-radius: 100px; padding: 6px 32px 6px 12px; font-size: 13px; color: var(--text); cursor: pointer; transition: 0.2s; font-family: inherit; font-weight: 500; }
        .filter-pill select:hover { background-color: var(--gray-hover); border-color: #C0C0C0; }
        .filter-pill select:focus { outline: none; border-color: var(--blue); box-shadow: 0 0 0 2px rgba(35, 131, 226, 0.2); }
        .filter-pill::after { content: '▼'; font-size: 8px; color: var(--text-muted); position: absolute; right: 12px; top: 50%; transform: translateY(-50%); pointer-events: none; }
        .bulk-actions { margin-left: auto; display: flex; gap: 8px; }
        .btn-action { background: transparent; border: 1px solid var(--border); padding: 6px 12px; border-radius: 4px; font-size: 13px; cursor: pointer; color: var(--text); font-weight: 500; }
        .btn-action:hover { background: var(--gray-hover); }
        .table-container { border: 1px solid var(--border); border-radius: 6px; overflow: hidden; box-shadow: 0 1px 2px rgba(0,0,0,0.02); }
        table { width: 100%; border-collapse: collapse; }
        th { text-align: left; background: #FBFBFA; border-bottom: 1px solid var(--border); padding: 0; }
        th a { display: block; padding: 12px 16px; font-size: 12px; font-weight: 600; color: var(--text-muted); text-decoration: none; transition: 0.2s; user-select: none; }
        th a:hover { background: #F0F0F0; color: var(--text); }
        th a.active { color: var(--blue); background: #EFF6FC; }
        .sort-icon { float: right; font-size: 10px; margin-top: 2px; }
        td { padding: 12px 16px; font-size: 14px; border-bottom: 1px solid var(--border); vertical-align: middle; }
        tr:last-child td { border-bottom: none; }
        tr:hover { background-color: var(--gray-hover); }
        .status-badge { display: inline-block; padding: 2px 8px; border-radius: 4px; font-size: 12px; font-weight: 500; background: #E3E2E0; color: #32302C; }
        .row-actions a { color: var(--text); text-decoration: none; margin-right: 12px; font-weight: 500; font-size: 13px; opacity: 0.6; }
        .row-actions a:hover { opacity: 1; text-decoration: underline; }
        .btn-row-delete { background: none; border: none; color: #EB5757; cursor: pointer; padding: 0; font-size: 13px; opacity: 0.6; }
        .btn-row-delete:hover { opacity: 1; text-decoration: underline; }
        .empty-state { padding: 60px; text-align: center; color: var(--text-muted); font-size: 14px; }
        .modal-overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(15, 15, 15, 0.6); z-index: 100; display: none; align-items: center; justify-content: center; backdrop-filter: blur(2px); }
        .modal-box { background: white; width: 450px; border-radius: 6px; padding: 24px; box-shadow: 0 8px 30px rgba(0,0,0,0.12); border: 1px solid var(--border); animation: modalIn 0.2s ease-out; }
        .modal-title { font-size: 18px; font-weight: 700; margin-bottom: 16px; border-bottom: 1px solid var(--border); padding-bottom: 10px; }
        .modal-body { margin-bottom: 20px; }
        .input-group { margin-bottom: 12px; }
        .input-group label { display: block; font-size: 12px; font-weight: 600; color: var(--text-muted); margin-bottom: 4px; }
        .input-group input { width: 100%; padding: 8px; border: 1px solid var(--border); border-radius: 4px; font-size: 13px; box-sizing: border-box; }
        .row-inputs { display: flex; gap: 10px; }
        .row-inputs .input-group { flex: 1; }
        .modal-actions { display: flex; justify-content: flex-end; gap: 8px; }
        .btn-cancel { background: transparent; border: 1px solid var(--border); padding: 8px 16px; border-radius: 4px; font-size: 13px; cursor: pointer; color: var(--text); transition: 0.2s; }
        .btn-cancel:hover { background: var(--gray-hover); }
        .btn-primary { background: var(--blue); border: 1px solid var(--blue); padding: 8px 16px; border-radius: 4px; font-size: 13px; cursor: pointer; color: white; transition: 0.2s; }
        .btn-primary:hover { background: #0070DA; }
        .btn-danger { background: #EB5757; border: 1px solid #EB5757; padding: 6px 12px; border-radius: 4px; font-size: 13px; cursor: pointer; color: white; transition: 0.2s; }
        .btn-danger:hover { background: #C93C3C; }
        @keyframes modalIn { from { opacity: 0; transform: scale(0.95); } to { opacity: 1; transform: scale(1); } }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Laporan Keuangan</h1>
            <a href="{{ route('reports.create') }}" class="btn-new">Baru +</a>
        </div>

        <form action="{{ route('reports.index') }}" method="GET" class="toolbar" id="filterForm">
            <div class="filter-label">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"></polygon></svg>
                Filter:
            </div>
            
            <div class="filter-pill">
                <select name="year" onchange="cleanSubmit(this.form)">
                    @foreach($availableYears as $year)
                        <option value="{{ $year }}" {{ $filterYear == $year ? 'selected' : '' }}>Tahun {{ $year }}</option>
                    @endforeach
                    @if($availableYears->isEmpty())
                        <option value="{{ date('Y') }}">Tahun {{ date('Y') }}</option>
                    @endif
                </select>
            </div>
            
            <div class="filter-pill" style="{{ $filterType == 'yearly' ? 'opacity: 0.5; pointer-events: none;' : '' }}">
                <select name="month" onchange="cleanSubmit(this.form)">
                    <option value="">Semua Bulan</option>
                    @foreach([1=>'Januari', 2=>'Februari', 3=>'Maret', 4=>'April', 5=>'Mei', 6=>'Juni', 7=>'Juli', 8=>'Agustus', 9=>'September', 10=>'Oktober', 11=>'November', 12=>'Desember'] as $k => $v)
                        <option value="{{ $k }}" {{ $filterMonth == $k ? 'selected' : '' }}>{{ $v }}</option>
                    @endforeach
                </select>
            </div>
            
            <div class="filter-pill">
                <select name="director_id" onchange="cleanSubmit(this.form)">
                    <option value="">Semua Direksi</option>
                    @foreach($directors as $d)
                        <option value="{{ $d->id }}" {{ $filterDirector == $d->id ? 'selected' : '' }}>{{ $d->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="filter-pill">
                <select name="type" onchange="cleanSubmit(this.form)">
                    <option value="monthly" {{ $filterType == 'monthly' ? 'selected' : '' }}>Rekap Bulanan</option>
                    <option value="yearly" {{ $filterType == 'yearly' ? 'selected' : '' }}>Rekap Tahunan</option>
                </select>
            </div>
            <input type="hidden" name="sort" value="{{ $sortBy }}">

            <div class="bulk-actions">
                <button type="button" class="btn-action" onclick="openDownloadModal('excel')">Unduh Excel</button>
                <button type="button" class="btn-action" onclick="openDownloadModal('pdf')">Unduh PDF</button>
            </div>
        </form>

        <form action="{{ route('reports.bulk_action') }}" method="POST" id="bulkForm">
            @csrf
            <input type="hidden" name="type" value="{{ $filterType }}">
            <input type="hidden" name="year" value="{{ $filterYear }}">
            <input type="hidden" name="action" id="downloadAction">
            
            <input type="hidden" name="rekap_no" id="formRekapNo">
            <input type="hidden" name="po_no" id="formPoNo">
            <input type="hidden" name="signer1_name" id="formSigner1Name">
            <input type="hidden" name="signer1_pos" id="formSigner1Pos">
            <input type="hidden" name="signer2_name" id="formSigner2Name">
            <input type="hidden" name="signer2_pos" id="formSigner2Pos">

            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th style="width: 40px; text-align: center;"><input type="checkbox" onclick="toggle(this)"></th>
                            <th style="width: 250px;"><a href="#">Direksi</a></th>
                            <th style="width: 150px;">
                                <a href="javascript:void(0)" onclick="cleanSort('period_{{ $sortBy == 'period_desc' ? 'asc' : 'desc' }}')" class="{{ str_contains($sortBy, 'period') ? 'active' : '' }}">
                                    Periode <span class="sort-icon">{{ $sortBy == 'period_desc' ? '↓' : ($sortBy == 'period_asc' ? '↑' : '') }}</span>
                                </a>
                            </th>
                            <th style="width: 150px;">
                                <a href="javascript:void(0)" onclick="cleanSort('pagu_{{ $sortBy == 'pagu_high' ? 'low' : 'high' }}')" class="{{ str_contains($sortBy, 'pagu') ? 'active' : '' }}">
                                    Pagu Awal <span class="sort-icon">{{ $sortBy == 'pagu_high' ? '↓' : ($sortBy == 'pagu_low' ? '↑' : '') }}</span>
                                </a>
                            </th>
                            <th style="width: 150px;">
                                <a href="javascript:void(0)" onclick="cleanSort('realisasi_{{ $sortBy == 'realisasi_high' ? 'low' : 'high' }}')" class="{{ str_contains($sortBy, 'realisasi') ? 'active' : '' }}">
                                    Realisasi <span class="sort-icon">{{ $sortBy == 'realisasi_high' ? '↓' : ($sortBy == 'realisasi_low' ? '↑' : '') }}</span>
                                </a>
                            </th>
                            <th style="width: 150px;">
                                <a href="javascript:void(0)" onclick="cleanSort('sisa_{{ $sortBy == 'sisa_high' ? 'low' : 'high' }}')" class="{{ str_contains($sortBy, 'sisa') ? 'active' : '' }}">
                                    Sisa Pagu <span class="sort-icon">{{ $sortBy == 'sisa_high' ? '↓' : ($sortBy == 'sisa_low' ? '↑' : '') }}</span>
                                </a>
                            </th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($reports as $report)
                        <tr>
                            <td style="text-align: center;"><input type="checkbox" name="report_ids[]" value="{{ $report->id }}"></td>
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
                            <td>Rp {{ number_format($report->credit_limit, 0, ',', '.') }}</td>
                            <td>Rp {{ number_format($report->total_expenses, 0, ',', '.') }}</td>
                            <td style="color: {{ $report->remaining_limit < 0 ? '#EB5757' : 'inherit' }}">Rp {{ number_format($report->remaining_limit, 0, ',', '.') }}</td>
                            <td class="row-actions" style="text-align: right;">
                                @if(!$report->is_aggregate)
                                    <a href="{{ route('reports.show', ['year' => $report->year, 'month' => $report->month, 'slug' => $report->director->slug]) }}">Buka</a>
                                    <button type="button" class="btn-row-delete" onclick="openDeleteModal('del-form-{{ $report->id }}')">Hapus</button>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="7" class="empty-state">Tidak ada data ditemukan.</td></tr>
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

    <div id="downloadModal" class="modal-overlay" style="display: none;">
        <div class="modal-box">
            <div class="modal-title">Konfigurasi Laporan</div>
            <div class="modal-body">
                <div class="row-inputs">
                    <div class="input-group">
                        <label>Nomor Urut Surat (Angka Saja)</label>
                        <input type="text" id="inputRekapNo" placeholder="Contoh: 457">
                    </div>
                    <div class="input-group">
                        <label>Nomor PO</label>
                        <input type="text" id="inputPoNo" placeholder="Contoh: 2512-0059">
                    </div>
                </div>

                <div class="input-group" style="margin-top: 10px; border-bottom: 1px solid #eee; padding-bottom: 5px;">
                    <strong>Penyetuju 1 (Kiri)</strong>
                </div>
                <div class="row-inputs">
                    <div class="input-group">
                        <label>Jabatan</label>
                        <input type="text" id="inputSigner1Pos" placeholder="Cth: KEPALA DIVISI UMUM">
                    </div>
                    <div class="input-group">
                        <label>Nama</label>
                        <input type="text" id="inputSigner1Name" placeholder="Nama Pejabat">
                    </div>
                </div>

                <div class="input-group" style="margin-top: 10px; border-bottom: 1px solid #eee; padding-bottom: 5px;">
                    <strong>Penyetuju 2 (Kanan)</strong>
                </div>
                <div class="row-inputs">
                    <div class="input-group">
                        <label>Jabatan</label>
                        <input type="text" id="inputSigner2Pos" placeholder="Cth: KEPALA BIDANG URUMGA">
                    </div>
                    <div class="input-group">
                        <label>Nama</label>
                        <input type="text" id="inputSigner2Name" placeholder="Nama Pejabat">
                    </div>
                </div>
            </div>
            <div class="modal-actions">
                <button type="button" class="btn-cancel" onclick="closeDownloadModal()">Batal</button>
                <button type="button" class="btn-primary" onclick="submitDownload()">Unduh File</button>
            </div>
        </div>
    </div>

    <div id="deleteModal" class="modal-overlay" style="display: none;">
        <div class="modal-box" style="width: 320px;">
            <div class="modal-title">Hapus Laporan?</div>
            <div class="modal-desc">Tindakan ini tidak dapat dibatalkan.</div>
            <div class="modal-actions">
                <button type="button" class="btn-cancel" onclick="closeDeleteModal()">Batal</button>
                <button type="button" id="confirmDeleteBtn" class="btn-danger">Ya, Hapus</button>
            </div>
        </div>
    </div>

    <script>
        function cleanSubmit(form) {
            const inputs = form.querySelectorAll('select, input');
            inputs.forEach(input => { if (!input.value) input.disabled = true; });
            form.submit();
        }

        function cleanSort(sortValue) {
            const form = document.getElementById('filterForm');
            const sortInput = form.querySelector('input[name="sort"]');
            sortInput.value = sortValue;
            cleanSubmit(form);
        }

        let pendingAction = '';
        
        function openDownloadModal(action) {
            const checkboxes = document.querySelectorAll('input[name="report_ids[]"]:checked');
            if (checkboxes.length === 0 && '{{ $filterType }}' !== 'yearly') {
                alert('Pilih minimal satu laporan untuk diunduh.');
                return;
            }
            pendingAction = action;
            document.getElementById('downloadModal').style.display = 'flex';
        }

        function closeDownloadModal() {
            document.getElementById('downloadModal').style.display = 'none';
        }

        function submitDownload() {
            document.getElementById('formRekapNo').value = document.getElementById('inputRekapNo').value;
            document.getElementById('formPoNo').value = document.getElementById('inputPoNo').value;
            document.getElementById('formSigner1Name').value = document.getElementById('inputSigner1Name').value;
            document.getElementById('formSigner1Pos').value = document.getElementById('inputSigner1Pos').value;
            document.getElementById('formSigner2Name').value = document.getElementById('inputSigner2Name').value;
            document.getElementById('formSigner2Pos').value = document.getElementById('inputSigner2Pos').value;
            
            document.getElementById('downloadAction').value = pendingAction;
            
            document.getElementById('bulkForm').submit();
            closeDownloadModal();
        }

        let formToDelete = null;
        function openDeleteModal(formId) {
            formToDelete = formId;
            document.getElementById('deleteModal').style.display = 'flex';
        }
        function closeDeleteModal() {
            document.getElementById('deleteModal').style.display = 'none';
            formToDelete = null;
        }
        document.getElementById('confirmDeleteBtn').onclick = function() {
            if (formToDelete) document.getElementById(formToDelete).submit();
        };

        function toggle(source) {
            checkboxes = document.getElementsByName('report_ids[]');
            for(var i=0, n=checkboxes.length;i<n;i++) { checkboxes[i].checked = source.checked; }
        }
    </script>
</body>
</html>