@extends('layouts.app')
@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <a href="{{ route('reports.index') }}" class="btn btn-light border ps-2 pe-3">
        <i class="bi bi-chevron-left small me-1"></i> Kembali
    </a>
    <div class="d-flex gap-2">
        <a href="{{ route('reports.edit', $report->id) }}" class="btn btn-light border">
            <i class="bi bi-pencil me-1"></i> Edit Properti
        </a>
        <div class="vr mx-1 text-muted opacity-25"></div>
        <a href="{{ route('reports.exportPdf', $report->id) }}" class="btn btn-light border text-danger">
            <i class="bi bi-file-pdf"></i>
        </a>
        <a href="{{ route('reports.exportExcel', $report->id) }}" class="btn btn-light border text-success">
            <i class="bi bi-file-earmark-excel"></i>
        </a>
    </div>
</div>

<div class="card-custom p-4 mb-5">
    <div class="mb-4">
        <h3 class="mb-0 fw-bold">{{ $report->director->name }}</h3>
        <span class="text-secondary">{{ $report->director->position }}</span>
    </div>
    
    <div class="row g-4 border-top pt-4">
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
                <span class="text-secondary small font-monospace bg-light px-1 rounded">{{ $report->creditCard->card_number }}</span>
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

<h5 class="mb-3 px-1">Transaksi</h5>
<div class="card-custom overflow-hidden">
    <table class="table-notion">
        <thead>
            <tr>
                <th width="15%" class="ps-4">Tanggal</th>
                <th width="50%">Deskripsi Transaksi</th>
                <th width="20%" class="text-end">Nominal (Rp)</th>
                <th width="15%" class="text-end pe-4"></th>
            </tr>
        </thead>
        <tbody>
            @foreach($report->transactions as $trx)
            <tr>
                <form id="update-form-{{ $trx->id }}" action="{{ route('transactions.update', $trx->id) }}" method="POST">@csrf @method('PUT')</form>
                
                <td class="ps-4">
                    <div class="view-mode-{{ $trx->id }}">
                        <span class="badge-notion bg-white border text-secondary">
                            {{ $trx->transaction_date->format('d M Y') }}
                        </span>
                    </div>
                    <input form="update-form-{{ $trx->id }}" type="date" name="transaction_date" 
                        class="form-control form-control-sm edit-mode-{{ $trx->id }} d-none" 
                        value="{{ $trx->transaction_date->format('Y-m-d') }}" required>
                </td>

                <td>
                    <span class="view-mode-{{ $trx->id }} fw-medium">{{ $trx->description }}</span>
                    <input form="update-form-{{ $trx->id }}" type="text" name="description" 
                        class="form-control form-control-sm edit-mode-{{ $trx->id }} d-none" 
                        value="{{ $trx->description }}" required>
                </td>

                <td class="text-end font-monospace">
                    <span class="view-mode-{{ $trx->id }}">{{ number_format($trx->amount, 0, ',', '.') }}</span>
                    <input form="update-form-{{ $trx->id }}" type="text" name="amount" 
                        class="form-control form-control-sm text-end rupiah edit-mode-{{ $trx->id }} d-none" 
                        value="{{ number_format($trx->amount, 0, ',', '.') }}" required>
                </td>

                <td class="text-end pe-4">
                    <div class="view-mode-{{ $trx->id }} opacity-50 hover-opacity-100">
                        <button type="button" class="btn btn-sm p-0 me-2 text-secondary" onclick="toggleEdit({{ $trx->id }})" title="Edit">
                            <i class="bi bi-pencil"></i>
                        </button>
                        <form action="{{ route('transactions.destroy', $trx->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm p-0 text-danger" title="Hapus"><i class="bi bi-x"></i></button>
                        </form>
                    </div>
                    <div class="edit-mode-{{ $trx->id }} d-none gap-1 justify-content-end">
                        <button type="submit" form="update-form-{{ $trx->id }}" class="btn btn-sm btn-primary py-0 px-2"><i class="bi bi-check"></i></button>
                        <button type="button" class="btn btn-sm btn-light border py-0 px-2" onclick="toggleEdit({{ $trx->id }})"><i class="bi bi-x"></i></button>
                    </div>
                </td>
            </tr>
            @endforeach
            
            <tr class="bg-light">
                <form action="{{ route('transactions.store', $report->id) }}" method="POST">
                    @csrf
                    <td class="ps-4 py-3">
                        <input type="date" name="transaction_date" class="form-control form-control-sm bg-white border shadow-sm" required>
                    </td>
                    <td class="py-3">
                        <input type="text" name="description" class="form-control form-control-sm bg-white border shadow-sm" placeholder="Ketik deskripsi baru..." required>
                    </td>
                    <td class="py-3">
                        <input type="text" name="amount" class="form-control form-control-sm bg-white border shadow-sm text-end rupiah" placeholder="0" required>
                    </td>
                    <td class="text-end py-3 pe-4">
                        <button type="submit" class="btn btn-sm btn-primary w-100" title="Tambah">
                            <i class="bi bi-plus-lg"></i>
                        </button>
                    </td>
                </form>
            </tr>
        </tbody>
    </table>
</div>

<script>
    function toggleEdit(id) {
        document.querySelectorAll(`.view-mode-${id}`).forEach(el => el.classList.toggle('d-none'));
        document.querySelectorAll(`.edit-mode-${id}`).forEach(el => {
            el.classList.toggle('d-none');
            if (el.tagName === 'DIV' && !el.classList.contains('d-none')) el.classList.add('d-flex');
            else if (el.tagName === 'DIV') el.classList.remove('d-flex');
        });
    }
</script>
@endsection