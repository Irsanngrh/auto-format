@extends('layouts.app')
@section('content')
<div class="row justify-content-center">
    <div class="col-md-12">
        <div class="mb-2">
            <a href="{{ route('reports.index') }}" class="btn btn-link text-decoration-none text-secondary ps-0 fw-medium small">
                <i class="bi bi-arrow-left me-1"></i> Kembali ke Dashboard
            </a>
        </div>
        
        <div class="card-custom p-4 mb-4">
            <div class="d-flex justify-content-between align-items-center border-bottom pb-3 mb-4">
                <div>
                    <h5 class="fw-bold mb-1">{{ $report->director->name }}</h5>
                    <span class="text-secondary small">{{ $report->director->position }}</span>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('reports.edit', $report->id) }}" class="btn btn-light border btn-sm text-secondary">
                        <i class="bi bi-pencil me-1"></i> Edit Properti
                    </a>
                    <div class="vr mx-1 text-muted opacity-25"></div>
                    
                    <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#exportModal">
                        <i class="bi bi-printer me-1"></i> Export / Preview
                    </button>
                </div>
            </div>
            
            <div class="row g-4 mb-2">
                <div class="col-md-3">
                    <div class="text-label">PERIODE</div>
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-calendar4-week text-secondary small"></i>
                        <span class="fw-medium">{{ $report->month_name }} {{ $report->year }}</span>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="text-label">KARTU KREDIT</div>
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-credit-card text-secondary small"></i>
                        <span class="fw-medium">{{ $report->creditCard->bank_name }}</span>
                        <span class="text-secondary small font-monospace bg-light px-1 rounded border">
                            {{ $report->creditCard->card_number }}
                        </span>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="text-label">PAGU KREDIT</div>
                    <div class="fw-medium text-secondary">Rp {{ number_format($report->credit_limit, 0, ',', '.') }}</div>
                </div>
                <div class="col-md-2">
                    <div class="text-label">REALISASI</div>
                    <div class="fw-medium">Rp {{ number_format($totalExpenses, 0, ',', '.') }}</div>
                </div>
                <div class="col-md-2">
                    <div class="text-label">SISA SALDO</div>
                    <div class="fw-bold text-success">Rp {{ number_format($remainingLimit, 0, ',', '.') }}</div>
                </div>
            </div>
        </div>

        <h6 class="fw-bold mb-3 px-1">Rincian Transaksi</h6>
        <div class="card-custom overflow-hidden">
            <table class="table-notion">
                <thead>
                    <tr>
                        <th width="18%" class="ps-4">Tanggal</th>
                        <th width="45%">Deskripsi Transaksi</th>
                        <th width="20%" class="text-end">Nominal (Rp)</th>
                        <th width="17%" class="text-end pe-4">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($report->transactions as $trx)
                    <tr>
                        <form id="update-form-{{ $trx->id }}" action="{{ route('reports.transactions.update', $trx->id) }}" method="POST">
                            @csrf @method('PUT')
                        </form>
                        
                        <td class="ps-4">
                            <div class="view-mode-{{ $trx->id }}">
                                <span class="badge-clean border text-secondary">
                                    {{ \Carbon\Carbon::parse($trx->transaction_date)->format('d M Y') }}
                                </span>
                            </div>
                            <input form="update-form-{{ $trx->id }}" type="date" name="transaction_date" class="form-control form-control-sm edit-mode-{{ $trx->id }} d-none" value="{{ $trx->transaction_date }}" min="{{ $startDate }}" max="{{ $endDate }}" required>
                        </td>
                        
                        <td>
                            <span class="view-mode-{{ $trx->id }} fw-medium">{{ $trx->description }}</span>
                            <input form="update-form-{{ $trx->id }}" type="text" name="description" class="form-control form-control-sm edit-mode-{{ $trx->id }} d-none" value="{{ $trx->description }}" required>
                        </td>
                        
                        <td class="text-end font-monospace">
                            <span class="view-mode-{{ $trx->id }}">{{ number_format($trx->amount, 0, ',', '.') }}</span>
                            <input form="update-form-{{ $trx->id }}" type="text" name="amount" class="form-control form-control-sm text-end rupiah edit-mode-{{ $trx->id }} d-none" value="{{ number_format($trx->amount, 0, ',', '.') }}" required>
                        </td>
                        
                        <td class="text-end pe-4">
                            <div class="view-mode-{{ $trx->id }}">
                                <button type="button" class="btn btn-icon text-primary p-1" onclick="toggleEdit({{ $trx->id }})"><i class="bi bi-pencil"></i></button>
                                <button type="button" class="btn btn-icon text-danger p-1" onclick="confirmDeleteTransaction({{ $trx->id }})"><i class="bi bi-trash"></i></button>
                            </div>
                            <div class="edit-mode-{{ $trx->id }} d-none gap-2 justify-content-end">
                                <button type="submit" form="update-form-{{ $trx->id }}" class="btn btn-primary btn-sm px-3 py-1"><i class="bi bi-check-lg"></i></button>
                                <button type="button" class="btn btn-light border btn-sm px-2 py-1" onclick="toggleEdit({{ $trx->id }})"><i class="bi bi-x-lg"></i></button>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                    
                    <tr class="bg-light">
                        <form action="{{ route('reports.transactions.store', $report->id) }}" method="POST">
                            @csrf
                            <td class="ps-4 py-3">
                                <input type="date" name="transaction_date" class="form-control form-control-sm" min="{{ $startDate }}" max="{{ $endDate }}" required>
                            </td>
                            <td class="py-3">
                                <input type="text" name="description" class="form-control form-control-sm" placeholder="Deskripsi..." required>
                            </td>
                            <td class="py-3">
                                <input type="text" name="amount" class="form-control form-control-sm text-end rupiah" placeholder="0" required>
                            </td>
                            <td class="text-end py-3 pe-4">
                                <button type="submit" class="btn btn-sm btn-primary w-100"><i class="bi bi-plus-lg"></i> Tambah</button>
                            </td>
                        </form>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <form id="deleteForm" method="POST" class="modal-content">
            @csrf @method('DELETE')
            <div class="modal-body text-center p-4">
                <div class="mb-3 text-secondary"><i class="bi bi-trash3" style="font-size: 32px;"></i></div>
                <h6 class="fw-bold mb-2">Hapus Transaksi?</h6>
                <p class="text-secondary small mb-4">Transaksi akan dihapus permanen.</p>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-light w-100 border" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger w-100">Hapus</button>
                </div>
            </div>
        </form>
    </div>
</div>

<form id="exportForm" method="GET" target="_self">
    <div class="modal fade" id="exportModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header"><h5 class="modal-title h6">Export & Preview</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                <div class="modal-body">
                    <div class="row g-3 mb-2">
                        <div class="col-6"><input type="text" name="rekap_no" class="form-control" placeholder="No Rekap"></div>
                        <div class="col-6"><input type="text" name="po_no" class="form-control" placeholder="No PO"></div>
                    </div>
                    <div class="row g-3 mb-2">
                        <div class="col-6"><input type="text" name="signer1_pos" class="form-control mb-1" placeholder="Jabatan (Kiri)"><input type="text" name="signer1_name" class="form-control" placeholder="Nama (Kiri)"></div>
                        <div class="col-6"><input type="text" name="signer2_pos" class="form-control mb-1" placeholder="Jabatan (Kanan)"><input type="text" name="signer2_name" class="form-control" placeholder="Nama (Kanan)"></div>
                    </div>
                    <div class="d-flex gap-2 mt-4">
                        <button type="button" onclick="submitExport('{{ route('reports.export_pdf', $report->id) }}', '_self')" class="btn btn-danger flex-fill"><i class="bi bi-file-pdf me-2"></i>PDF</button>
                        
                        <button type="button" onclick="submitExport('{{ route('reports.preview', $report->id) }}', '_blank')" class="btn btn-primary flex-fill"><i class="bi bi-eye me-2"></i>Preview</button>
                        
                        <button type="button" onclick="submitExport('{{ route('reports.export_excel', $report->id) }}', '_self')" class="btn btn-success flex-fill"><i class="bi bi-file-excel me-2"></i>Excel</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

<script>
    function toggleEdit(id) { 
        document.querySelectorAll(`.view-mode-${id}`).forEach(e=>e.classList.toggle('d-none')); 
        document.querySelectorAll(`.edit-mode-${id}`).forEach(e=>{
            e.classList.toggle('d-none'); 
            if(e.tagName==='DIV' && !e.classList.contains('d-none')) e.classList.add('d-flex');
            else if(e.tagName==='DIV') e.classList.remove('d-flex');
        }); 
    }
    
    function confirmDeleteTransaction(id) { 
        document.getElementById('deleteForm').action = '/transactions/' + id; 
        new bootstrap.Modal(document.getElementById('deleteModal')).show(); 
    }

    function submitExport(url, target) {
        let form = document.getElementById('exportForm');
        form.action = url;
        form.target = target;
        form.submit();
        setTimeout(() => { form.target = '_self'; }, 100);
    }
</script>
@endsection