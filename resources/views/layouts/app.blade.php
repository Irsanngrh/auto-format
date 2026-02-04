<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Laporan ASABRI</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-body: #FBFBFA;
            --bg-card: #FFFFFF;
            --border-color: #E9E9E7;
            --text-primary: #37352F;
            --text-secondary: #787774;
            --primary-color: #2383E2;
            --hover-bg: #F0F0EF;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background-color: var(--bg-body);
            color: var(--text-primary);
            -webkit-font-smoothing: antialiased;
        }

        /* Navbar */
        .navbar {
            background: var(--bg-card);
            border-bottom: 1px solid var(--border-color);
            padding: 12px 0;
        }
        .navbar-brand { font-weight: 700; font-size: 18px; letter-spacing: -0.5px; }
        .nav-link {
            color: var(--text-secondary) !important;
            font-weight: 500;
            font-size: 14px;
            padding: 8px 16px !important;
            border-radius: 6px;
            transition: all 0.2s;
        }
        .nav-link:hover, .nav-link.active {
            background-color: var(--hover-bg);
            color: var(--text-primary) !important;
        }

        /* Cards & Containers */
        .container { max-width: 1080px; padding-top: 40px; padding-bottom: 60px; }
        .card-custom {
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            box-shadow: 0 1px 2px rgba(0,0,0,0.02);
            border-radius: 8px;
        }

        /* Headings */
        h3, h4, h5 { font-weight: 600; letter-spacing: -0.5px; color: var(--text-primary); }
        .text-label {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.6px;
            color: var(--text-secondary);
            font-weight: 600;
            margin-bottom: 4px;
        }

        /* Buttons */
        .btn {
            font-weight: 500;
            font-size: 14px;
            border-radius: 6px;
            padding: 8px 16px;
            transition: all 0.2s;
        }
        .btn-primary { background: var(--primary-color); border: none; }
        .btn-primary:hover { background: #1B6CBE; }
        .btn-outline-secondary { border-color: var(--border-color); color: var(--text-primary); }
        .btn-outline-secondary:hover { background: var(--hover-bg); border-color: var(--border-color); color: var(--text-primary); }
        .btn-light { background: transparent; border: 1px solid transparent; color: var(--text-secondary); }
        .btn-light:hover { background: var(--hover-bg); color: var(--text-primary); }

        /* Forms */
        .form-control, .form-select {
            border: 1px solid #D9D9D7;
            border-radius: 6px;
            font-size: 14px;
            padding: 8px 12px;
            box-shadow: none;
        }
        .form-control:focus, .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 2px rgba(35, 131, 226, 0.1);
        }

        /* Tables */
        .table-notion { width: 100%; border-collapse: separate; border-spacing: 0; }
        .table-notion th {
            font-size: 11px;
            text-transform: uppercase;
            color: var(--text-secondary);
            font-weight: 600;
            border-bottom: 1px solid var(--border-color);
            padding: 12px 16px;
        }
        .table-notion td {
            font-size: 14px;
            padding: 12px 16px;
            border-bottom: 1px solid var(--border-color);
            color: var(--text-primary);
            vertical-align: middle;
        }
        .table-notion tr:last-child td { border-bottom: none; }
        .table-notion tr:hover td { background-color: #F7F7F5; }

        /* Badges */
        .badge-notion {
            font-weight: 500;
            font-size: 12px;
            padding: 4px 8px;
            border-radius: 4px;
            background: #EFEFEF;
            color: var(--text-primary);
        }

        /* Modals */
        .modal-content { border: none; border-radius: 12px; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1); }
        .modal-header { border-bottom: 1px solid var(--border-color); padding: 16px 24px; }
        .modal-body { padding: 24px; }
    </style>
</head>
<body>
    <nav class="navbar sticky-top">
        <div class="container d-flex justify-content-start gap-4 py-0">
            <div class="navbar-brand d-flex align-items-center gap-2">
                <i class="bi bi-grid-1x2-fill text-primary"></i>
                <span>ASABRI Report</span>
            </div>
            <div class="vr mx-2 text-muted opacity-25"></div>
            <a href="{{ route('reports.index') }}" class="nav-link {{ request()->routeIs('reports.*') ? 'active' : '' }}">
                Laporan
            </a>
            <a href="{{ route('directors.index') }}" class="nav-link {{ request()->routeIs('directors.*') ? 'active' : '' }}">
                Direksi & CC
            </a>
        </div>
    </nav>
    <div class="container">
        @yield('content')
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function formatRupiah(angka) {
            var number_string = angka.replace(/[^,\d]/g, '').toString(),
            split = number_string.split(','),
            sisa = split[0].length % 3,
            rupiah = split[0].substr(0, sisa),
            ribuan = split[0].substr(sisa).match(/\d{3}/gi);
            if (ribuan) {
                separator = sisa ? '.' : '';
                rupiah += separator + ribuan.join('.');
            }
            return split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
        }
        document.querySelectorAll('.rupiah').forEach(item => {
            item.addEventListener('keyup', function(e) { this.value = formatRupiah(this.value); });
        });
    </script>
</body>
</html>