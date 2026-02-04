@extends('layouts.app')
@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card-custom p-4">
            <h5 class="mb-4">Tambah Direksi Baru</h5>
            <form action="{{ route('directors.store') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label class="form-label small text-muted">Nama Lengkap</label>
                    <input type="text" name="name" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label small text-muted">Jabatan</label>
                    <input type="text" name="position" class="form-control" required>
                </div>
                <hr>
                <label class="form-label small text-muted mb-2">Kartu Kredit (Bisa lebih dari satu)</label>
                <div id="cc-wrapper">
                    <div class="row mb-2 g-2">
                        <div class="col-4"><input type="text" name="bank_name[]" class="form-control" placeholder="Nama Bank (misal: BNI)" required></div>
                        <div class="col-8"><input type="text" name="card_number[]" class="form-control" placeholder="Nomor Kartu" required></div>
                    </div>
                </div>
                <button type="button" class="btn btn-sm btn-light border mb-3" onclick="addCC()">+ Tambah Kartu Lain</button>
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">Simpan</button>
                    <a href="{{ route('directors.index') }}" class="btn btn-light">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
function addCC() {
    let html = `<div class="row mb-2 g-2"><div class="col-4"><input type="text" name="bank_name[]" class="form-control" placeholder="Nama Bank"></div><div class="col-8"><input type="text" name="card_number[]" class="form-control" placeholder="Nomor Kartu"></div></div>`;
    document.getElementById('cc-wrapper').insertAdjacentHTML('beforeend', html);
}
</script>
@endsection