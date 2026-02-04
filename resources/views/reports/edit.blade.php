@extends('layouts.app')
@section('content')
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card-custom p-4">
            <h5 class="mb-4">Edit Laporan</h5>
            <form action="{{ route('reports.update', $report->id) }}" method="POST">
                @csrf @method('PUT')
                <div class="mb-3">
                    <label class="small text-muted">Direktur</label>
                    <input type="text" class="form-control bg-light" value="{{ $report->director->name }}" readonly>
                </div>
                <div class="mb-3">
                    <label class="small text-muted">Kartu Kredit</label>
                    <select name="credit_card_id" class="form-select">
                        @foreach($report->director->creditCards as $cc)
                            <option value="{{ $cc->id }}" {{ $report->credit_card_id == $cc->id ? 'selected' : '' }}>{{ $cc->bank_name }} - {{ $cc->card_number }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="row mb-3">
                    <div class="col-6">
                        <label class="small text-muted">Bulan</label>
                        <select name="month" class="form-select">
                            @foreach($months as $k => $v) <option value="{{ $k }}" {{ $report->month == $k ? 'selected' : '' }}>{{ $v }}</option> @endforeach
                        </select>
                    </div>
                    <div class="col-6">
                        <label class="small text-muted">Tahun</label>
                        <input type="number" name="year" class="form-control" value="{{ $report->year }}">
                    </div>
                </div>
                <div class="mb-4">
                    <label class="small text-muted">Pagu Kredit</label>
                    <input type="text" name="credit_limit" class="form-control rupiah" value="{{ number_format($report->credit_limit, 0, ',', '.') }}">
                </div>
                <button type="submit" class="btn btn-primary w-100">Update</button>
                <a href="{{ route('reports.show', $report->id) }}" class="btn btn-light w-100 mt-2">Batal</a>
            </form>
        </div>
    </div>
</div>
@endsection