<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buat Laporan Baru</title>
    <style>
        body { font-family: sans-serif; max-width: 600px; margin: 50px auto; padding: 20px; border: 1px solid #ddd; border-radius: 8px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; }
        input, select { width: 100%; padding: 8px; margin-bottom: 15px; box-sizing: border-box; }
        button { background-color: #007bff; color: white; padding: 10px 15px; border: none; cursor: pointer; width: 100%; font-size: 16px; }
        button:hover { background-color: #0056b3; }
    </style>
</head>
<body>

    <h2 style="text-align: center">Buat Laporan Baru</h2>

    <form action="{{ route('reports.store') }}" method="POST">
        @csrf

        <label>Pilih Direksi:</label>
        <select name="director_id" required>
            <option value="">-- Pilih Nama --</option>
            @foreach($directors as $director)
                <option value="{{ $director->id }}">
                    {{ $director->name }} ({{ $director->position }})
                </option>
            @endforeach
        </select>

        <label>Bulan:</label>
        <select name="month" required>
            <option value="">-- Pilih Bulan --</option>
            <option value="1">JANUARI</option>
            <option value="2">FEBRUARI</option>
            <option value="3">MARET</option>
            <option value="4">APRIL</option>
            <option value="5">MEI</option>
            <option value="6">JUNI</option>
            <option value="7">JULI</option>
            <option value="8">AGUSTUS</option>
            <option value="9">SEPTEMBER</option>
            <option value="10">OKTOBER</option>
            <option value="11">NOVEMBER</option>
            <option value="12">DESEMBER</option>
        </select>

        <label>Tahun:</label>
        <input type="number" name="year" value="{{ date('Y') }}" required>

        <label>Pagu Awal / Batas Kredit (Rp):</label>
        <input type="text" name="credit_limit" id="rupiahInput" placeholder="Contoh: 42.000.000" required>

        <button type="submit">Buat Laporan</button>
    </form>

    <script>
        var rupiah = document.getElementById("rupiahInput");
        rupiah.addEventListener("keyup", function(e) {
            rupiah.value = formatRupiah(this.value);
        });

        function formatRupiah(angka) {
            var number_string = angka.replace(/[^,\d]/g, "").toString(),
                split = number_string.split(","),
                sisa = split[0].length % 3,
                rupiah = split[0].substr(0, sisa),
                ribuan = split[0].substr(sisa).match(/\d{3}/gi);

            if (ribuan) {
                separator = sisa ? "." : "";
                rupiah += separator + ribuan.join(".");
            }
            return split[1] != undefined ? rupiah + "," + split[1] : rupiah;
        }
    </script>

</body>
</html>