<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Buat Laporan</title>
    <style>
        :root { --bg: #FFFFFF; --text: #37352F; --border: #E0E0E0; --text-muted: #9B9A97; --blue: #2383E2; }
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Helvetica, Arial, sans-serif; background-color: var(--bg); color: var(--text); display: flex; justify-content: center; padding-top: 80px; }
        
        .page-container { width: 100%; max-width: 600px; padding: 0 20px; }
        
        .nav-back { display: inline-block; color: var(--text-muted); text-decoration: none; font-size: 14px; margin-bottom: 24px; transition: 0.2s; }
        .nav-back:hover { color: var(--text); }

        h1 { font-size: 32px; font-weight: 700; margin-bottom: 40px; border-bottom: 1px solid var(--border); padding-bottom: 20px; }

        .form-group { margin-bottom: 24px; display: grid; grid-template-columns: 120px 1fr; align-items: center; gap: 20px; }
        label { font-size: 14px; color: var(--text-muted); font-weight: 500; }
        
        input, select { width: 100%; padding: 8px 12px; border: 1px solid transparent; border-radius: 4px; background: #F7F7F5; font-size: 14px; color: var(--text); transition: 0.2s; box-sizing: border-box; }
        input:focus, select:focus { outline: none; background: white; border-color: var(--blue); box-shadow: 0 0 0 1px var(--blue); }
        input::placeholder { color: #BAB8B5; }

        .btn-submit { background-color: var(--blue); color: white; border: none; padding: 10px 24px; border-radius: 4px; font-size: 14px; font-weight: 500; cursor: pointer; margin-top: 20px; float: right; }
        .btn-submit:hover { background-color: #0070DA; }

        .error-msg { background: #FFEBEE; color: #C62828; padding: 12px; border-radius: 4px; font-size: 14px; margin-bottom: 24px; }
    </style>
</head>
<body>
    <div class="page-container">
        <a href="{{ route('reports.index') }}" class="nav-back">← Kembali ke Dashboard</a>

        @if($errors->any())
            <div class="error-msg">{{ $errors->first() }}</div>
        @endif

        <h1>Laporan Baru</h1>

        <form action="{{ route('reports.store') }}" method="POST">
            @csrf

            <div class="form-group">
                <label>Direksi</label>
                <select name="director_id" required>
                    <option value="">Pilih...</option>
                    @foreach($directors as $director)
                        <option value="{{ $director->id }}" {{ old('director_id') == $director->id ? 'selected' : '' }}>{{ $director->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label>Periode</label>
                <div style="display: flex; gap: 10px;">
                    <select name="month" required>
                        @foreach([1=>'Januari', 2=>'Februari', 3=>'Maret', 4=>'April', 5=>'Mei', 6=>'Juni', 7=>'Juli', 8=>'Agustus', 9=>'September', 10=>'Oktober', 11=>'November', 12=>'Desember'] as $k => $v)
                            <option value="{{ $k }}" {{ old('month') == $k ? 'selected' : '' }}>{{ $v }}</option>
                        @endforeach
                    </select>
                    <input type="number" name="year" value="{{ old('year', date('Y')) }}" required style="width: 100px;">
                </div>
            </div>

            <div class="form-group">
                <label>Pagu (Rp)</label>
                <input type="text" name="credit_limit" id="rupiahInput" placeholder="0" value="{{ old('credit_limit') }}" required>
            </div>

            <button type="submit" class="btn-submit">Simpan Data</button>
        </form>
    </div>

    <script>
        var rupiah = document.getElementById("rupiahInput");
        rupiah.addEventListener("keyup", function(e) { rupiah.value = formatRupiah(this.value); });
        function formatRupiah(angka) {
            var number_string = angka.replace(/[^,\d]/g, "").toString(), split = number_string.split(","), sisa = split[0].length % 3, rupiah = split[0].substr(0, sisa), ribuan = split[0].substr(sisa).match(/\d{3}/gi);
            if (ribuan) { separator = sisa ? "." : ""; rupiah += separator + ribuan.join("."); }
            return split[1] != undefined ? rupiah + "," + split[1] : rupiah;
        }
    </script>
</body>
</html>