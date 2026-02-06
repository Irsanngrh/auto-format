@extends('layouts.app')
@section('content')
<div class="d-flex justify-content-between align-items-end mb-4">
    <div><h3 class="mb-1">Daftar Laporan</h3><p class="text-secondary small mb-0">Kelola rekapitulasi pengeluaran</p></div>
    <a href="{{ route('reports.create') }}" class="btn btn-primary d-flex align-items-center gap-2"><i class="bi bi-plus-lg"></i><span>Buat Baru</span></a>
</div>
<div class="d-flex align-items-center gap-3 mb-4">
    <div class="d-flex align-items-center text-secondary"><i class="bi bi-funnel-fill me-2"></i><span class="filter-label mb-0" style="font-weight: 700;">FILTER</span></div>
    <form method="GET" id="filterForm" class="d-flex align-items-center gap-2 flex-wrap">
        <input type="hidden" name="director_id" id="input_director" value="{{ $filterDirector }}"><input type="hidden" name="month" id="input_month" value="{{ $filterMonth }}"><input type="hidden" name="year" id="input_year" value="{{ $filterYear }}"><input type="hidden" name="type" id="input_type" value="{{ $filterType }}">
        <div class="dropdown"><button class="btn btn-filter dropdown-toggle" type="button" data-bs-toggle="dropdown">{{ $directors->firstWhere('id', $filterDirector)->name ?? 'Semua Direksi' }}</button><ul class="dropdown-menu dropdown-menu-filter"><li><a class="dropdown-item" href="#" onclick="setFilter('director', '')">Semua Direksi</a></li>@foreach($directors as $d)<li><a class="dropdown-item" href="#" onclick="setFilter('director', '{{ $d->id }}')">{{ $d->name }}</a></li>@endforeach</ul></div>
        <div class="dropdown"><button class="btn btn-filter dropdown-toggle" type="button" data-bs-toggle="dropdown">{{ $months[$filterMonth] ?? 'Semua Bulan' }}</button><ul class="dropdown-menu dropdown-menu-filter"><li><a class="dropdown-item" href="#" onclick="setFilter('month', '')">Semua Bulan</a></li>@foreach($months as $k => $v)<li><a class="dropdown-item" href="#" onclick="setFilter('month', '{{ $k }}')">{{ $v }}</a></li>@endforeach</ul></div>
        <div class="dropdown"><button class="btn btn-filter dropdown-toggle" type="button" data-bs-toggle="dropdown">{{ $filterYear }}</button><ul class="dropdown-menu dropdown-menu-filter">@foreach($availableYears as $y)<li><a class="dropdown-item" href="#" onclick="setFilter('year', '{{ $y }}')">{{ $y }}</a></li>@endforeach</ul></div>
        <div class="dropdown"><button class="btn btn-filter dropdown-toggle" type="button" data-bs-toggle="dropdown">{{ $filterType == 'monthly' ? 'Rekap Bulanan' : 'Rekap Tahunan' }}</button><ul class="dropdown-menu dropdown-menu-filter"><li><a class="dropdown-item" href="#" onclick="setFilter('type', 'monthly')">Rekap Bulanan</a></li><li><a class="dropdown-item" href="#" onclick="setFilter('type', 'yearly')">Rekap Tahunan</a></li></ul></div>
    </form>
</div>
<div class="card-custom overflow-hidden">
    <table class="table-notion">
        <thead><tr><th class="ps-4">Nama Direksi</th><th>Periode</th><th>Detail Kartu</th><th>Pagu</th><th>Realisasi</th><th>Sisa</th><th class="text-end pe-4">Aksi</th></tr></thead>
        <tbody>
            @foreach($reports as $r)
            <tr>
                <td class="ps-4 fw-bold text-dark">{{ $r->director->name }}</td>
                <td><span class="badge-clean">{{ $filterType == 'yearly' ? $r->year : $r->month_name . ' ' . $r->year }}</span></td>
                <td>@if($r->is_aggregate)<span class="text-muted small">-</span>@else<div class="d-flex flex-column" style="line-height: 1.2;"><span class="small fw-medium">{{ $r->creditCard->bank_name }}</span><span class="small text-secondary font-monospace">{{ $r->creditCard->card_number }}</span></div>@endif</td>
                <td class="text-secondary small">{{ number_format($r->credit_limit, 0, ',', '.') }}</td>
                <td class="fw-bold small">{{ number_format($r->total_expenses, 0, ',', '.') }}</td>
                <td class="text-success small fw-medium">{{ number_format($r->remaining_limit, 0, ',', '.') }}</td>
                <td class="text-end pe-4">
                    @if(!$r->is_aggregate)
                        <div class="d-flex justify-content-end gap-1">
                            <button class="btn-icon" onclick="openDownloadModal({{ $r->id }})"><i class="bi bi-download"></i></button>
                            <a href="{{ route('reports.show', ['slug' => $r->director->slug, 'month' => $r->month, 'year' => $r->year, 'card_last_digits' => substr($r->creditCard->card_number, -4)]) }}" class="btn-icon text-primary"><i class="bi bi-eye"></i></a>
                            <button class="btn-icon text-danger" onclick="confirmDelete({{ $r->id }}, '{{ $r->director->name }}')"><i class="bi bi-trash"></i></button>
                        </div>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
<div class="modal fade" id="deleteModal" tabindex="-1"><div class="modal-dialog modal-dialog-centered modal-sm"><form id="deleteForm" method="POST" class="modal-content">@csrf @method('DELETE')<div class="modal-body text-center p-4"><div class="mb-3 text-secondary"><i class="bi bi-trash3" style="font-size: 32px;"></i></div><h6 class="fw-bold mb-2">Hapus Laporan?</h6><p class="text-secondary small mb-4">Laporan <strong id="delName"></strong> akan dihapus.</p><div class="d-flex gap-2"><button type="button" class="btn btn-light w-100 border" data-bs-dismiss="modal">Batal</button><button type="submit" class="btn btn-danger w-100">Hapus</button></div></div></form></div></div>
<form id="downloadForm" method="GET"><div class="modal fade" id="downloadModal" tabindex="-1"><div class="modal-dialog modal-dialog-centered"><div class="modal-content"><div class="modal-header"><h5 class="modal-title h6">Export Dokumen</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div><div class="modal-body"><div class="row g-3 mb-2"><div class="col-6"><input type="text" name="rekap_no" class="form-control" placeholder="No Rekap"></div><div class="col-6"><input type="text" name="po_no" class="form-control" placeholder="No PO"></div></div><div class="row g-3 mb-2"><div class="col-6"><input type="text" name="signer1_pos" class="form-control mb-1" placeholder="Jabatan 1"><input type="text" name="signer1_name" class="form-control" placeholder="Nama 1"></div><div class="col-6"><input type="text" name="signer2_pos" class="form-control mb-1" placeholder="Jabatan 2"><input type="text" name="signer2_name" class="form-control" placeholder="Nama 2"></div></div><div class="d-flex gap-2"><button type="button" onclick="submitDownload('pdf')" class="btn btn-danger w-100">PDF</button><button type="button" onclick="submitDownload('excel')" class="btn btn-success w-100">Excel</button></div></div></div></div></div></form>
<script>
    function setFilter(n, v) { document.getElementById('input_' + n).value = v; document.getElementById('filterForm').submit(); }
    function confirmDelete(id, name) { document.getElementById('delName').textContent = name; document.getElementById('deleteForm').action = '/reports/' + id; new bootstrap.Modal(document.getElementById('deleteModal')).show(); }
    let currentDownloadId = 0;
    function openDownloadModal(id) { currentDownloadId = id; new bootstrap.Modal(document.getElementById('downloadModal')).show(); }
    function submitDownload(type) { document.getElementById('downloadForm').action = '/reports/' + currentDownloadId + '/' + type; document.getElementById('downloadForm').submit(); }
</script>
@endsection