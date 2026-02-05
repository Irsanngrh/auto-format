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

<div class="d-flex align-items-center gap-3 mb-4">
    <div class="d-flex align-items-center text-secondary">
        <i class="bi bi-funnel-fill me-2"></i>
        <span class="filter-label mb-0" style="font-weight: 700; font-size: 11px;">FILTER</span>
    </div>
    
    <form method="GET" id="filterForm" class="d-flex align-items-center gap-2 flex-wrap">
        <input type="hidden" name="director_id" id="input_director" value="{{ $filterDirector }}">
        <input type="hidden" name="month" id="input_month" value="{{ $filterMonth }}">
        <input type="hidden" name="year" id="input_year" value="{{ $filterYear }}">
        <input type="hidden" name="type" id="input_type" value="{{ $filterType }}">

        <div class="dropdown">
            <button class="btn btn-filter dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                {{ $directors->firstWhere('id', $filterDirector)->name ?? 'Semua Direksi' }}
            </button>
            <ul class="dropdown-menu dropdown-menu-filter">
                <li><a class="dropdown-item {{ $filterDirector == '' ? 'active' : '' }}" href="javascript:void(0)" onclick="setFilter('director', '')">Semua Direksi</a></li>
                @foreach($directors as $d)
                    <li><a class="dropdown-item {{ $filterDirector == $d->id ? 'active' : '' }}" href="javascript:void(0)" onclick="setFilter('director', '{{ $d->id }}')">{{ $d->name }}</a></li>
                @endforeach
            </ul>
        </div>

        <div class="dropdown">
            <button class="btn btn-filter dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false" {{ $filterType == 'yearly' ? 'disabled' : '' }}>
                {{ $months[$filterMonth] ?? 'Semua Bulan' }}
            </button>
            <ul class="dropdown-menu dropdown-menu-filter">
                <li><a class="dropdown-item {{ $filterMonth == '' ? 'active' : '' }}" href="javascript:void(0)" onclick="setFilter('month', '')">Semua Bulan</a></li>
                @foreach($months as $k => $v)
                    <li><a class="dropdown-item {{ $filterMonth == $k ? 'active' : '' }}" href="javascript:void(0)" onclick="setFilter('month', '{{ $k }}')">{{ $v }}</a></li>
                @endforeach
            </ul>
        </div>

        <div class="dropdown">
            <button class="btn btn-filter dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                {{ $filterYear }}
            </button>
            <ul class="dropdown-menu dropdown-menu-filter">
                @foreach($availableYears as $y)
                    <li><a class="dropdown-item {{ $filterYear == $y ? 'active' : '' }}" href="javascript:void(0)" onclick="setFilter('year', '{{ $y }}')">{{ $y }}</a></li>
                @endforeach
            </ul>
        </div>

        <div class="dropdown">
            <button class="btn btn-filter dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                {{ $filterType == 'monthly' ? 'Rekap Bulanan' : 'Rekap Tahunan' }}
            </button>
            <ul class="dropdown-menu dropdown-menu-filter">
                <li><a class="dropdown-item {{ $filterType == 'monthly' ? 'active' : '' }}" href="javascript:void(0)" onclick="setFilter('type', 'monthly')">Rekap Bulanan</a></li>
                <li><a class="dropdown-item {{ $filterType == 'yearly' ? 'active' : '' }}" href="javascript:void(0)" onclick="setFilter('type', 'yearly')">Rekap Tahunan</a></li>
            </ul>
        </div>
    </form>
</div>

<div class="card-custom overflow-hidden">
    <table class="table-notion">
        <thead>
            <tr>
                <th class="ps-4">Nama Direksi</th>
                <th>Periode</th>
                <th>Detail Kartu</th>
                <th>Pagu</th>
                <th>Realisasi</th>
                <th>Sisa</th>
                <th class="text-end pe-4">Aksi & Download</th>
            </tr>
        </thead>
        <tbody>
            @foreach($reports as $r)
            <tr>
                <td class="ps-4">
                    <span class="fw-bold text-dark">{{ $r->director->name }}</span>
                </td>
                <td><span class="badge-clean">{{ $filterType == 'yearly' ? $r->year : $r->month_name . ' ' . $r->year }}</span></td>
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
                        <div class="d-flex justify-content-end gap-1">
                            <button type="button" class="btn-icon" title="PDF" onclick="openDownloadModal('pdf', {{ $r->id }})">
                                <i class="bi bi-file-pdf text-danger"></i>
                            </button>
                            <button type="button" class="btn-icon" title="Excel" onclick="openDownloadModal('excel', {{ $r->id }})">
                                <i class="bi bi-file-earmark-excel text-success"></i>
                            </button>
                            
                            <div class="vr mx-1 my-1 text-muted opacity-25"></div>

                            <a href="{{ route('reports.show', ['slug' => $r->director->slug, 'month' => $r->month, 'year' => $r->year]) }}" class="btn-icon text-primary" title="Detail">
                                <i class="bi bi-eye"></i>
                            </a>
                            <button type="button" class="btn-icon text-danger" title="Hapus" 
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

<form id="downloadForm" method="GET" action=""> 
    <div class="modal fade" id="downloadModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title h6">Export Dokumen</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3 mb-4">
                        <div class="col-6"><label class="text-label">No. Rekap</label><input type="text" name="rekap_no" class="form-control" placeholder="-"></div>
                        <div class="col-6"><label class="text-label">No. PO</label><input type="text" name="po_no" class="form-control" placeholder="-"></div>
                    </div>
                    <div class="row g-3 mb-2">
                        <div class="col-6"><label class="text-label">Tanda Tangan Kiri</label><input type="text" name="signer1_pos" class="form-control mb-2" placeholder="Jabatan"><input type="text" name="signer1_name" class="form-control" placeholder="Nama"></div>
                        <div class="col-6"><label class="text-label">Tanda Tangan Kanan</label><input type="text" name="signer2_pos" class="form-control mb-2" placeholder="Jabatan"><input type="text" name="signer2_name" class="form-control" placeholder="Nama"></div>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0 pb-4 px-4">
                    <button type="submit" class="btn btn-primary w-100 py-2">Download</button>
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
                <div class="mb-3 text-secondary"><i class="bi bi-trash3" style="font-size: 32px;"></i></div>
                <h6 class="fw-bold mb-2">Hapus Laporan?</h6>
                <p class="text-secondary small mb-4" style="line-height: 1.5;">Laporan <strong id="delName" class="text-dark"></strong> periode <strong id="delPeriod" class="text-dark"></strong> akan dihapus permanen.</p>
                <div class="d-flex gap-2"><button type="button" class="btn btn-light w-100 border" data-bs-dismiss="modal">Batal</button><button type="submit" class="btn btn-danger w-100">Hapus</button></div>
            </div>
        </form>
    </div>
</div>

<script>
    function setFilter(name, value) {
        document.getElementById('input_' + name).value = value;
        document.getElementById('filterForm').submit();
    }
    function openDownloadModal(type, id) {
        let form = document.getElementById('downloadForm');
        form.action = '/reports/' + id + (type === 'pdf' ? '/pdf' : '/excel');
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