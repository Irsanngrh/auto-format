@extends('layouts.app')
@section('content')

<div class="row justify-content-center">
    <div class="col-md-7">
        <div class="card-custom p-4 px-5">
            <h4 class="mb-4">Edit Properti Laporan</h4>
            
            <form action="{{ route('reports.update', $report->id) }}" method="POST">
                @csrf @method('PUT')
                
                <div class="mb-4">
                    <label class="text-label">DIREKTUR</label>
                    <input type="text" class="form-control bg-light text-muted" value="{{ $report->director->name }}" readonly>
                </div>

                <div class="mb-4">
                    <label class="text-label">KARTU KREDIT</label>
                    <select name="credit_card_id" class="form-select">
                        @foreach($report->director->creditCards as $cc)
                            <option value="{{ $cc->id }}" {{ $report->credit_card_id == $cc->id ? 'selected' : '' }}>
                                {{ $cc->bank_name }} - {{ $cc->card_number }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="row mb-4">
                    <div class="col-6">
                        <label class="text-label">BULAN</label>
                        <select name="month" class="form-select">
                            @foreach($months as $k => $v) 
                                <option value="{{ $k }}" {{ $report->month == $k ? 'selected' : '' }}>{{ $v }}</option> 
                            @endforeach
                        </select>
                    </div>
                    <div class="col-6">
                        <label class="text-label">TAHUN</label>
                        <input type="number" name="year" class="form-control" value="{{ $report->year }}">
                    </div>
                </div>

                <div class="mb-5">
                    <label class="text-label">PAGU KREDIT (LIMIT)</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0 text-secondary">Rp</span>
                        <input type="text" name="credit_limit" class="form-control border-start-0 ps-1 rupiah fw-bold" 
                               value="{{ number_format($report->credit_limit, 0, ',', '.') }}">
                    </div>
                </div>

                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary py-2">Simpan Perubahan</button>
                    <a href="{{ route('reports.show', ['slug' => $report->director->slug, 'month' => $report->month, 'year' => $report->year]) }}" 
                       class="btn btn-light border py-2">
                       Batal
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection