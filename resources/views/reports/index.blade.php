@extends('layouts.app')
@section('content')

<div class="d-flex justify-content-between align-items-end mb-4">
    <div>
        <h3 class="mb-1">Daftar Laporan</h3>
        <p class="text-secondary small mb-0">Kelola rekapitulasi pengeluaran corporate card</p>
    </div>
    <a href="{{ route('reports.create') }}" class="btn btn-primary d-flex align-items-center gap-2">
        <i class="bi bi-plus-lg"></i>
        <span>Buat Baru</span>
    </a>
</div>

<div class="card-custom p-3 mb-4">
    <div class="d-flex align-items-center gap-2 mb-2">
        <i class="bi bi-funnel-fill text-secondary small"></i>
        <span class="text-label text-secondary" style="margin-bottom: 0;">FILTER DATA</span>
    </div>
    <form method="GET" id="filterForm" class="d-flex flex-wrap align-items-center gap-3">
        
        <select name="director_id" class="form-select form-select-sm w-auto border-0 bg-light fw-medium px-3" onchange="this.form.submit()">
            <option value="">Semua Direksi</option>
            @foreach($directors as $d) 
                <option value="{{ $d->id }}" {{ $filterDirector == $d->id ? 'selected' : '' }}>{{ $d->name }}</option> 
            @endforeach
        </select>

        <select name="month" class="form-select form-select-sm w-auto border-0 bg-light fw-medium px-3" {{ $filterType == 'yearly' ? 'disabled' : '' }} onchange="this.form.submit()">
            <option value="">Semua Bulan</option>
            @foreach($months as $k => $v) 
                <option value="{{ $k }}" {{ $filterMonth == $k ? 'selected' : '' }}>{{ $v }}</option> 
            @endforeach
        </select>

        <select name="year" class="form-select form-select-sm w-auto border-0 bg-light fw-medium px-3" onchange="this.form.submit()">
            @foreach($availableYears as $y) 
                <option value="{{ $y }}" {{ $filterYear == $y ? 'selected' : '' }}>{{ $y }}</option> 
            @endforeach
        </select>

        <select name="type" class="form-select form-select-sm w-auto border-0 bg-light fw-medium px-3" onchange="this.form.submit()">
            <option value="monthly" {{ $filterType == 'monthly' ? 'selected' : '' }}>Rekap Bulanan</option>
            <option value="yearly" {{ $filterType == 'yearly' ? 'selected' : '' }}>Rekap Tahunan</option>
        </select>
    </form>
</div>

<form id="bulkForm" method="POST" action="{{ route('reports.bulkAction') }}">
    @csrf
    <input type="hidden" name="type" value="{{ $filterType }}">

    <div class="card-custom overflow-hidden">
        <div class="border-bottom px-3 py-2 bg-light d-flex justify-content-end align-items-center">
            <div class="d-flex gap-2">
                <button type="button" class="btn btn-sm btn-light border" onclick="openDownloadModal('pdf')">
                    <i class="bi bi-file-pdf text-danger me-1"></i> PDF
                </button>
                <button type="button" class="btn btn-sm btn-light border" onclick="openDownloadModal('excel')">
                    <i class="bi bi-file-earmark-excel text-success me-1"></i> Excel
                </button>
            </div>
        </div>

        <table class="table-notion">
            <thead>
                <tr>
                    <th width="40" class="ps-4"><input type="checkbox" id="checkAll" class="form-check-input"></th>
                    <th>Nama Direksi</th>
                    <th>Periode</th>
                    <th>Detail Kartu</th>
                    <th>Pagu</th>
                    <th>Realisasi</th>
                    <th>Sisa</th>
                    <th class="text-end pe-4">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($reports as $r)
                <tr>
                    <td class="ps-4">
                        <input type="checkbox" name="report_ids[]" value="{{ $r->id }}" class="chk form-check-input">
                    </td>
                    <td>
                        <span class="fw-bold text-primary">{{ $r->director->name }}</span>
                    </td>
                    <td><span class="badge-notion">{{ $filterType == 'yearly' ? $r->year : $r->month_name . ' ' . $r->year }}</span></td>
                    <td>
                        @if($r->is_aggregate)
                            <span class="text-muted small">-</span>
                        @else
                            <div class="d-flex flex-column" style="line-height: 1.2;">
                                <span class="small fw-medium">{{ $r->creditCard->bank_name ?? '-' }}</span>
                                <span class="small text-secondary font-monospace">{{ $r->creditCard->card_number ?? '' }}</span>
                            </div>
                        @endif
                    </td>
                    <td class="text-secondary small">{{ number_format($r->credit_limit, 0, ',', '.') }}</td>
                    <td class="fw-bold small">{{ number_format($r->total_expenses, 0, ',', '.') }}</td>
                    <td class="text-success small fw-medium">{{ number_format($r->remaining_limit, 0, ',', '.') }}</td>
                    <td class="text-end pe-4">
                        @if(!$r->is_aggregate)
                            <div class="btn-group">
                                <a href="{{ route('reports.show', ['slug' => $r->director->slug, 'month' => $r->month, 'year' => $r->year]) }}" class="btn btn-sm btn-light text-secondary" title="Detail">
                                    <i class="bi bi-arrow-right"></i>
                                </a>
                                <button type="button" class="btn btn-sm btn-light text-danger" title="Hapus" 
                                    onclick="confirmDelete({{ $r->id }}, '{{ $r->director->name }}', '{{ $filterType == 'yearly' ? 'Tahun ' . $r->year : $r->month_name . ' ' . $r->year }}')">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="modal fade" id="downloadModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title h6">Export Dokumen</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3 mb-4">
                        <div class="col-6">
                            <label class="text-label">No. Rekap</label>
                            <input type="text" name="rekap_no" class="form-control" placeholder="-">
                        </div>
                        <div class="col-6">
                            <label class="text-label">No. PO</label>
                            <input type="text" name="po_no" class="form-control" placeholder="-">
                        </div>
                    </div>
                    
                    <div class="row g-3 mb-2">
                        <div class="col-6">
                            <label class="text-label">Tanda Tangan Kiri</label>
                            <input type="text" name="signer1_pos" class="form-control mb-2" placeholder="Jabatan">
                            <input type="text" name="signer1_name" class="form-control" placeholder="Nama">
                        </div>
                        <div class="col-6">
                            <label class="text-label">Tanda Tangan Kanan</label>
                            <input type="text" name="signer2_pos" class="form-control mb-2" placeholder="Jabatan">
                            <input type="text" name="signer2_name" class="form-control" placeholder="Nama">
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0 pb-4 px-4">
                    <button type="submit" name="action" id="btnAction" class="btn btn-primary w-100 py-2">Download</button>
                </div>
            </div>
        </div>
    </div>
</form>

<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <form id="deleteForm" method="POST" class="modal-content">
            @csrf @method('DELETE')
            <div class="modal-body text-center p-4">
                <div class="mb-3 text-secondary">
                    <i class="bi bi-trash3" style="font-size: 32px;"></i>
                </div>
                <h6 class="fw-bold mb-2">Hapus Laporan?</h6>
                <p class="text-secondary small mb-4" style="line-height: 1.5;">
                    Laporan <strong id="delName" class="text-dark"></strong> 
                    periode <strong id="delPeriod" class="text-dark"></strong> 
                    akan dihapus permanen.
                </p>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-light w-100 border" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger w-100">Hapus</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    document.getElementById('checkAll').addEventListener('click', function() { 
        document.querySelectorAll('.chk').forEach(c => c.checked = this.checked); 
    });
    
    function openDownloadModal(type) {
        if(document.querySelectorAll('.chk:checked').length === 0) { 
            alert('Pilih minimal satu laporan.'); return; 
        }
        showModal(type);
    }

    function showModal(type) {
        let btn = document.getElementById('btnAction');
        btn.value = type;
        btn.innerHTML = type === 'pdf' ? 'Download PDF' : 'Download Excel';
        new bootstrap.Modal(document.getElementById('downloadModal')).show();
    }

    function confirmDelete(id, name, period) {
        document.getElementById('delName').textContent = name;
        document.getElementById('delPeriod').textContent = period;
        document.getElementById('deleteForm').action = '/reports/' + id;
        new bootstrap.Modal(document.getElementById('deleteModal')).show();
    }
</script>
@endsection