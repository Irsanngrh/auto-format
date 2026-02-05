@extends('layouts.app')
@section('content')

<div class="row justify-content-center">
    <div class="col-md-7">
        
        <div class="mb-2">
            <a href="{{ route('directors.index') }}" class="btn btn-link text-decoration-none text-secondary ps-0 fw-medium" style="font-size: 14px;">
                <i class="bi bi-arrow-left me-1"></i> Kembali ke Dashboard
            </a>
        </div>

        <div class="card-custom p-4">
            
            <div class="border-bottom pb-3 mb-4">
                <h5 class="fw-bold mb-0">Edit Data Direksi</h5>
            </div>

            <form action="{{ route('directors.update', $director->id) }}" method="POST">
                @csrf @method('PUT')
                
                <div class="mb-4">
                    <label class="text-label">NAMA LENGKAP</label>
                    <input type="text" name="name" class="form-control" value="{{ $director->name }}" required>
                </div>

                <div class="mb-4">
                    <label class="text-label">JABATAN</label>
                    <input type="text" name="position" class="form-control" value="{{ $director->position }}" required>
                </div>

                <div class="mb-5">
                    <label class="text-label mb-2">KARTU KREDIT</label>
                    <div id="cc-wrapper">
                        @forelse($director->creditCards as $cc)
                            <div class="row g-2 mb-2 cc-row">
                                <div class="col-5">
                                    <input type="text" name="bank_name[]" class="form-control" value="{{ $cc->bank_name }}" placeholder="Nama Bank">
                                </div>
                                <div class="col-7 d-flex gap-2">
                                    <input type="text" name="card_number[]" class="form-control" value="{{ $cc->card_number }}" placeholder="Nomor Kartu">
                                    <button type="button" class="btn btn-light border text-danger px-2" onclick="removeCC(this)">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </div>
                        @empty
                            <div class="row g-2 mb-2 cc-row">
                                <div class="col-5">
                                    <input type="text" name="bank_name[]" class="form-control" placeholder="Nama Bank">
                                </div>
                                <div class="col-7 d-flex gap-2">
                                    <input type="text" name="card_number[]" class="form-control" placeholder="Nomor Kartu">
                                    <button type="button" class="btn btn-light border text-secondary px-2" onclick="removeCC(this)" disabled>
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </div>
                        @endforelse
                    </div>
                    <button type="button" class="btn btn-sm btn-light border text-secondary mt-1 w-100" onclick="addCC()">
                        <i class="bi bi-plus-lg me-1"></i> Tambah Kartu Lain
                    </button>
                </div>

                <div class="d-grid">
                    <button type="submit" class="btn btn-primary py-2">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        checkRows();
    });

    function addCC() {
        let wrapper = document.getElementById('cc-wrapper');
        let div = document.createElement('div');
        div.className = 'row g-2 mb-2 cc-row';
        div.innerHTML = `
            <div class="col-5">
                <input type="text" name="bank_name[]" class="form-control" placeholder="Nama Bank">
            </div>
            <div class="col-7 d-flex gap-2">
                <input type="text" name="card_number[]" class="form-control" placeholder="Nomor Kartu">
                <button type="button" class="btn btn-light border text-danger px-2" onclick="removeCC(this)">
                    <i class="bi bi-trash"></i>
                </button>
            </div>
        `;
        wrapper.appendChild(div);
        checkRows();
    }

    function removeCC(btn) {
        let rows = document.querySelectorAll('.cc-row');
        if (rows.length > 1) {
            btn.closest('.row').remove();
        } else {
            alert("Minimal harus ada 1 kartu kredit.");
        }
        checkRows();
    }

    function checkRows() {
        let rows = document.querySelectorAll('.cc-row');
        rows.forEach(row => {
            let btn = row.querySelector('button');
            if (rows.length === 1) {
                btn.disabled = true;
                btn.classList.remove('text-danger');
                btn.classList.add('text-secondary');
            } else {
                btn.disabled = false;
                btn.classList.add('text-danger');
                btn.classList.remove('text-secondary');
            }
        });
    }
</script>
@endsection