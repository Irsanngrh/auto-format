@extends('layouts.app')
@section('content')
<div class="d-flex justify-content-between align-items-end mb-4">
    <div><h3 class="mb-1">Daftar Direksi</h3><p class="text-secondary small mb-0">Kelola data direksi dan kartu kredit</p></div>
    <a href="{{ route('directors.create') }}" class="btn btn-primary d-flex align-items-center gap-2"><i class="bi bi-plus-lg"></i><span>Buat Baru</span></a>
</div>
<div class="card-custom overflow-hidden">
    <table class="table-notion">
        <thead><tr><th class="ps-4">Nama Direksi</th><th>Jabatan</th><th>Kartu Terdaftar</th><th class="text-end pe-4">Aksi</th></tr></thead>
        <tbody>
            @forelse($directors as $d)
            <tr>
                <td class="ps-4 fw-bold text-dark">{{ $d->name }}</td>
                <td class="text-secondary">{{ $d->position }}</td>
                <td>
                    @foreach($d->creditCards as $cc)
                        <div class="d-flex align-items-center py-1">
                            <span class="badge-clean text-secondary me-3" style="min-width: 50px; text-align: center;">{{ $cc->bank_name }}</span>
                            <span class="small font-monospace text-muted" style="letter-spacing: 0.5px;">{{ $cc->card_number }}</span>
                        </div>
                    @endforeach
                </td>
                <td class="text-end pe-4">
                    <a href="{{ route('directors.edit', $d->id) }}" class="btn-icon text-primary"><i class="bi bi-pencil"></i></a>
                    <button class="btn-icon text-danger" onclick="confirmDelete({{ $d->id }}, '{{ $d->name }}')"><i class="bi bi-trash"></i></button>
                </td>
            </tr>
            @empty <tr><td colspan="4" class="text-center py-5 text-muted">Belum ada data.</td></tr> @endforelse
        </tbody>
    </table>
</div>
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <form id="deleteForm" method="POST" class="modal-content">
            @csrf @method('DELETE')
            <div class="modal-body text-center p-4">
                <div class="mb-3 text-secondary"><i class="bi bi-trash3" style="font-size: 32px;"></i></div>
                <h6 class="fw-bold mb-2">Hapus Direksi?</h6>
                <p class="text-secondary small mb-4">Data <strong id="delName"></strong> akan dihapus permanen.</p>
                <div class="d-flex gap-2"><button type="button" class="btn btn-light w-100 border" data-bs-dismiss="modal">Batal</button><button type="submit" class="btn btn-danger w-100">Hapus</button></div>
            </div>
        </form>
    </div>
</div>
<script>
    function confirmDelete(id, name) {
        document.getElementById('delName').textContent = name;
        document.getElementById('deleteForm').action = '/directors/' + id;
        new bootstrap.Modal(document.getElementById('deleteModal')).show();
    }
</script>
@endsection