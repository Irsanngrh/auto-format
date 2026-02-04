@extends('layouts.app')
@section('content')

<div class="row justify-content-center">
    <div class="col-md-7">
        <a href="{{ route('reports.index') }}" class="btn btn-link text-decoration-none text-secondary p-0 mb-3 small">
            <i class="bi bi-arrow-left me-1"></i> Kembali ke Dashboard
        </a>
        
        <div class="card-custom p-4 px-5">
            <h4 class="mb-4">Buat Laporan Baru</h4>

            @if(session('error'))
                <div class="alert alert-danger d-flex align-items-center gap-2 mb-4" role="alert">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                    <div>{{ session('error') }}</div>
                </div>
            @endif
            
            <form action="{{ route('reports.store') }}" method="POST">
                @csrf
                
                <div class="mb-4">
                    <label class="text-label">DIREKTUR</label>
                    <select name="director_id" id="director" class="form-select" onchange="updateCC()" required>
                        <option value="">Pilih Direktur...</option>
                        @foreach($directors as $d)
                            <option value="{{ $d->id }}" data-cards='{{ json_encode($d->creditCards) }}'>{{ $d->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-4">
                    <label class="text-label">KARTU KREDIT</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0 text-secondary"><i class="bi bi-credit-card"></i></span>
                        <select name="credit_card_id" id="cc" class="form-select border-start-0 ps-0" required>
                            <option value="">Pilih Direktur Terlebih Dahulu</option>
                        </select>
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-6">
                        <label class="text-label">BULAN</label>
                        <select name="month" class="form-select" required>
                            @foreach($months as $k => $v) 
                                <option value="{{ $k }}" {{ date('n')==$k ? 'selected' : '' }}>{{ $v }}</option> 
                            @endforeach
                        </select>
                    </div>
                    <div class="col-6">
                        <label class="text-label">TAHUN</label>
                        <input type="number" name="year" class="form-control" value="{{ date('Y') }}" required>
                    </div>
                </div>

                <div class="mb-5">
                    <label class="text-label">PAGU KREDIT (LIMIT)</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0 text-secondary">Rp</span>
                        <input type="text" name="credit_limit" class="form-control border-start-0 ps-1 rupiah fw-bold" placeholder="0" required>
                    </div>
                </div>

                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary py-2">Buat Laporan</button>
                    <a href="{{ route('reports.index') }}" class="btn btn-light border py-2">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function updateCC() {
    let sel = document.getElementById('director');
    let cc = document.getElementById('cc');
    let cards = JSON.parse(sel.options[sel.selectedIndex].dataset.cards || '[]');
    cc.innerHTML = cards.length ? cards.map(c => `<option value="${c.id}">${c.bank_name} - ${c.card_number}</option>`).join('') : '<option value="">Tidak ada kartu terdaftar</option>';
}
</script>
@endsection