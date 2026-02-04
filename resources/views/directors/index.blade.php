@extends('layouts.app')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4>Direksi & Kartu Kredit</h4>
    <a href="{{ route('directors.create') }}" class="btn btn-primary btn-sm">+ Tambah</a>
</div>
@if(session('success')) <div class="alert alert-success py-2">{{ session('success') }}</div> @endif
<div class="row">
    @foreach($directors as $director)
    <div class="col-md-6 mb-3">
        <div class="card-custom p-3 h-100">
            <div class="d-flex justify-content-between">
                <div>
                    <h5 class="mb-1">{{ $director->name }}</h5>
                    <p class="text-secondary small mb-2">{{ $director->position }}</p>
                </div>
                <div>
                    <a href="{{ route('directors.edit', $director->id) }}" class="btn btn-sm btn-light border"><i class="bi bi-pencil"></i></a>
                    <form action="{{ route('directors.destroy', $director->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus?')">
                        @csrf @method('DELETE')
                        <button class="btn btn-sm btn-light border text-danger"><i class="bi bi-trash"></i></button>
                    </form>
                </div>
            </div>
            <hr class="my-2">
            <div class="small">
                @forelse($director->creditCards as $cc)
                    <div class="d-flex justify-content-between text-muted mb-1">
                        <span>{{ $cc->bank_name }}</span>
                        <span class="font-monospace">{{ $cc->card_number }}</span>
                    </div>
                @empty
                    <span class="text-muted fst-italic">Belum ada kartu kredit</span>
                @endforelse
            </div>
        </div>
    </div>
    @endforeach
</div>
@endsection