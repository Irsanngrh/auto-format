@extends('layouts.app')
@section('content')
<div class="row justify-content-center">
    <div class="col-md-7">
        <div class="card-custom p-4">
            <h4 class="mb-4">Edit Properti Laporan</h4>
            @if(session('error'))<div class="alert alert-danger small mb-4">{{ session('error') }}</div>@endif
            <form action="{{ route('reports.update', $report->id) }}" method="POST">
                @csrf @method('PUT')
                <div class="mb-4"><label class="text-label">DIREKTUR</label><input type="text" class="form-control bg-light" value="{{ $report->director->name }}" readonly></div>
                <div class="mb-4">
                    <label class="text-label">KARTU KREDIT</label><input type="hidden" name="credit_card_id" id="credit_card_id" value="{{ $report->credit_card_id }}">
                    <div class="dropdown"><button class="btn btn-filter dropdown-toggle w-100" type="button" data-bs-toggle="dropdown" id="btnCC">{{ $report->creditCard->bank_name }} - {{ $report->creditCard->card_number }}</button><ul class="dropdown-menu dropdown-menu-filter w-100">@foreach($report->director->creditCards as $cc)<li><a class="dropdown-item" href="#" onclick="document.getElementById('credit_card_id').value='{{ $cc->id }}';document.getElementById('btnCC').innerText='{{ $cc->bank_name }} - {{ $cc->card_number }}'">{{ $cc->bank_name }} - {{ $cc->card_number }}</a></li>@endforeach</ul></div>
                </div>
                <div class="row mb-4">
                    <div class="col-6"><label class="text-label">BULAN</label><input type="hidden" name="month" id="month" value="{{ $report->month }}"><div class="dropdown"><button class="btn btn-filter dropdown-toggle w-100" type="button" data-bs-toggle="dropdown" id="btnMonth">{{ $months[$report->month] }}</button><ul class="dropdown-menu dropdown-menu-filter w-100">@foreach($months as $k => $v)<li><a class="dropdown-item" href="#" onclick="document.getElementById('month').value='{{ $k }}';document.getElementById('btnMonth').innerText='{{ $v }}'">{{ $v }}</a></li>@endforeach</ul></div></div>
                    <div class="col-6"><label class="text-label">TAHUN</label><input type="number" name="year" class="form-control" value="{{ $report->year }}"></div>
                </div>
                <div class="mb-5"><label class="text-label">PAGU KREDIT</label><div class="input-group"><span class="input-group-text bg-light border-end-0">Rp</span><input type="text" name="credit_limit" class="form-control border-start-0 fw-bold rupiah" value="{{ number_format($report->credit_limit, 0, ',', '.') }}"></div></div>
                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary py-2">Simpan Perubahan</button>
                    <a href="{{ route('reports.show', ['slug' => $report->director->slug, 'month' => $report->month, 'year' => $report->year, 'card_last_digits' => substr($report->creditCard->card_number, -4)]) }}" class="btn btn-light border py-2">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection