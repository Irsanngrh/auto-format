@extends('layouts.app')
@section('content')
<div class="row justify-content-center">
    <div class="col-md-7">
        <div class="mb-2"><a href="{{ route('reports.index') }}" class="btn btn-link text-decoration-none text-secondary ps-0 fw-medium small"><i class="bi bi-arrow-left me-1"></i> Kembali ke Dashboard</a></div>
        <div class="card-custom p-4">
            <div class="border-bottom pb-3 mb-4"><h5 class="fw-bold mb-0">Buat Laporan Baru</h5></div>
            @if(session('error'))<div class="alert alert-danger small mb-4">{{ session('error') }}</div>@endif
            <form action="{{ route('reports.store') }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label class="text-label">DIREKTUR</label><input type="hidden" name="director_id" id="director_id" required>
                    <div class="dropdown w-100"><button class="btn btn-filter dropdown-toggle w-100 justify-content-between" type="button" data-bs-toggle="dropdown" id="btnDirector">Pilih Direktur...</button><ul class="dropdown-menu dropdown-menu-filter w-100">@foreach($directors as $d)<li><a class="dropdown-item" href="#" onclick="selectDirector('{{ $d->id }}', '{{ $d->name }}', '{{ json_encode($d->creditCards) }}')">{{ $d->name }}</a></li>@endforeach</ul></div>
                </div>
                <div class="mb-4">
                    <label class="text-label">KARTU KREDIT</label><input type="hidden" name="credit_card_id" id="credit_card_id" required>
                    <div class="dropdown w-100"><button class="btn btn-filter dropdown-toggle w-100 justify-content-between disabled" type="button" data-bs-toggle="dropdown" id="btnCC">Pilih Direktur Terlebih Dahulu</button><ul class="dropdown-menu dropdown-menu-filter w-100" id="ccList"></ul></div>
                </div>
                <div class="row mb-4">
                    <div class="col-6"><label class="text-label">BULAN</label><input type="hidden" name="month" id="month" value="{{ date('n') }}"><div class="dropdown"><button class="btn btn-filter dropdown-toggle w-100" type="button" data-bs-toggle="dropdown" id="btnMonth">{{ $months[date('n')] }}</button><ul class="dropdown-menu dropdown-menu-filter w-100">@foreach($months as $k => $v)<li><a class="dropdown-item" href="#" onclick="setOpt('month', '{{ $k }}', '{{ $v }}')">{{ $v }}</a></li>@endforeach</ul></div></div>
                    <div class="col-6"><label class="text-label">TAHUN</label><input type="number" name="year" class="form-control" value="{{ date('Y') }}"></div>
                </div>
                <div class="mb-5"><label class="text-label">PAGU KREDIT</label><div class="input-group"><span class="input-group-text bg-light border-end-0">Rp</span><input type="text" name="credit_limit" class="form-control border-start-0 fw-bold rupiah" required></div></div>
                <div class="d-grid"><button type="submit" class="btn btn-primary py-2">Simpan Laporan</button></div>
            </form>
        </div>
    </div>
</div>
<script>
    function selectDirector(id, name, cards) {
        document.getElementById('director_id').value = id; document.getElementById('btnDirector').innerText = name;
        let ccList = document.getElementById('ccList'), btnCC = document.getElementById('btnCC'), inputCC = document.getElementById('credit_card_id');
        ccList.innerHTML = ''; inputCC.value = '';
        JSON.parse(cards).forEach(c => { let li = document.createElement('li'); li.innerHTML = `<a class="dropdown-item" href="#" onclick="document.getElementById('credit_card_id').value='${c.id}';document.getElementById('btnCC').innerText='${c.bank_name} - ${c.card_number}'">${c.bank_name} - ${c.card_number}</a>`; ccList.appendChild(li); });
        btnCC.classList.remove('disabled'); btnCC.innerText = 'Pilih Kartu...';
    }
    function setOpt(id, val, txt) { document.getElementById(id).value = val; document.getElementById('btn'+id.charAt(0).toUpperCase()+id.slice(1)).innerText = txt; }
</script>
@endsection