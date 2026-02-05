@extends('layouts.app')
@section('content')

<div class="d-flex justify-content-between align-items-end mb-4">
    <div>
        <h3 class="mb-1">Daftar Direksi</h3>
        <p class="text-secondary small mb-0">Kelola data direksi dan kartu kredit korporat</p>
    </div>
    <a href="{{ route('directors.create') }}" class="btn btn-primary d-flex align-items-center gap-2">
        <i class="bi bi-plus-lg"></i>
        <span>Buat Baru</span>
    </a>
</div>

<div class="card-custom overflow-hidden">
    <table class="table-notion">
        <thead>
            <tr>
                <th class="ps-4">Nama Direksi</th>
                <th>Jabatan</th>
                <th>Kartu Terdaftar</th>
                <th class="text-end pe-4">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($directors as $d)
            <tr>
                <td class="ps-4">
                    <span class="fw-bold text-dark">{{ $d->name }}</span>
                </td>
                <td><span class="text-secondary">{{ $d->position }}</span></td>
                <td>
                    @if($d->creditCards->count() > 0)
                        <div class="d-flex flex-column gap-1">
                            @foreach($d->creditCards as $cc)
                                <div class="d-flex align-items-center gap-2">
                                    <span class="badge-clean text-secondary" style="font-size: 11px;">{{ $cc->bank_name }}</span>
                                    <span class="small font-monospace text-muted">{{ $cc->card_number }}</span>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <span class="text-muted small italic">- Tidak ada kartu -</span>
                    @endif
                </td>
                <td class="text-end pe-4">
                    <div class="d-flex justify-content-end gap-1">
                        <a href="{{ route('directors.edit', $d->id) }}" class="btn-icon text-primary" title="Edit">
                            <i class="bi bi-pencil"></i>
                        </a>
                        <form action="{{ route('directors.destroy', $d->id) }}" method="POST" onsubmit="return confirm('Hapus direksi ini? Data laporan terkait mungkin akan error.')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn-icon text-danger" title="Hapus">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="4" class="text-center py-5 text-muted">Belum ada data direksi.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection