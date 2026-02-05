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

        html, body {
            height: 100%;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--bg-body);
            color: var(--text-primary);
            display: flex;
            flex-direction: column;
        }

        .navbar { background: var(--bg-card); border-bottom: 1px solid var(--border-color); padding: 12px 0; flex-shrink: 0; }
        .nav-link { color: var(--text-secondary) !important; font-weight: 500; font-size: 14px; padding: 6px 12px; border-radius: 4px; transition: 0.2s; }
        .nav-link:hover, .nav-link.active { background-color: var(--hover-bg); color: var(--text-primary) !important; }

        .container { 
            max-width: 1080px; 
            padding-top: 40px; 
            padding-bottom: 40px; 
            flex: 1 0 auto; 
        }
        
        .card-custom { background: var(--bg-card); border: 1px solid var(--border-color); box-shadow: 0 1px 2px rgba(0,0,0,0.02); border-radius: 8px; }

        h3, h4, h5 { font-weight: 600; letter-spacing: -0.5px; color: var(--text-primary); }
        .text-label { font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px; color: var(--text-secondary); font-weight: 600; margin-bottom: 6px; }

        .btn-filter {
            background: #FFFFFF;
            border: 1px solid #E0E0DE;
            color: #37352F;
            font-size: 13px;
            font-weight: 500;
            padding: 8px 16px;
            border-radius: 6px;
            box-shadow: 0 1px 2px rgba(0,0,0,0.05);
            transition: background 0.1s ease, border-color 0.1s ease;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            min-width: 160px;
            position: relative;
            text-align: left;
        }

        .btn-filter:hover {
            background: #F7F7F5;
            border-color: #D0D0CE;
            color: #37352F;
        }

        .btn-filter[aria-expanded="true"] {
            background: #EFEFEF;
            border-color: #D0D0CE;
            color: var(--text-primary);
        }

        .btn-filter::after {
            content: '';
            border: none;
            width: 14px;
            height: 14px;
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%239CA3AF' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6 9 12 15 18 9'%3e%3c/polyline%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: center;
            background-size: 14px;
            margin-left: auto;
        }
        
        .btn-filter[aria-expanded="true"]::after {
            transform: rotate(180deg);
        }

        .dropdown-menu-filter {
            border: 1px solid #E9E9E7;
            background: #FFFFFF;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08); 
            border-radius: 6px;
            padding: 4px;
            margin-top: 4px !important;
            min-width: 180px;
            max-height: 300px;
            overflow-y: auto;
        }

        .dropdown-item {
            font-size: 13px;
            font-weight: 500;
            padding: 6px 10px;
            border-radius: 4px;
            color: var(--text-primary);
            margin-bottom: 1px;
            position: relative;
        }

        .dropdown-item:hover {
            background-color: #F0F0EF;
            color: var(--text-primary);
        }

        .dropdown-item.active, .dropdown-item:active {
            background-color: #EBF5FE;
            color: var(--text-primary);
            font-weight: 500;
        }
        
        .dropdown-item.active::after {
            content: '✓';
            position: absolute;
            right: 10px;
            color: var(--primary-color);
            font-weight: bold;
            font-size: 12px;
        }

        .form-control, .form-select { border: 1px solid #D9D9D7; border-radius: 6px; font-size: 14px; padding: 8px 12px; box-shadow: none; }
        .form-control:focus { border-color: var(--primary-color); box-shadow: 0 0 0 2px rgba(35, 131, 226, 0.1); }

        .btn { font-weight: 500; font-size: 14px; border-radius: 6px; padding: 8px 16px; }
        .btn-primary { background: var(--primary-color); border: none; }
        .btn-primary:hover { background: #1B6CBE; }
        .btn-icon { padding: 4px 8px; color: var(--text-secondary); border-radius: 4px; border: 1px solid transparent; background: transparent; }
        .btn-icon:hover { background: var(--hover-bg); color: var(--text-primary); border-color: var(--border-color); }

        .table-notion { width: 100%; border-collapse: separate; border-spacing: 0; }
        .table-notion th { font-size: 11px; text-transform: uppercase; color: var(--text-secondary); font-weight: 600; border-bottom: 1px solid var(--border-color); padding: 12px 16px; }
        .table-notion td { font-size: 14px; padding: 12px 16px; border-bottom: 1px solid var(--border-color); color: var(--text-primary); vertical-align: middle; }
        .table-notion tr:last-child td { border-bottom: none; }
        .table-notion tr:hover td { background-color: #F9F9F8; }
        
        .badge-clean { background: #EFEFEF; color: var(--text-primary); font-weight: 500; padding: 4px 8px; border-radius: 4px; font-size: 12px; }
    </style>
</head>
<body>
    <nav class="navbar sticky-top">
        <div class="container d-flex justify-content-start gap-4 py-0" style="padding-top: 0; padding-bottom: 0;">
            <div class="navbar-brand d-flex align-items-center gap-2" style="font-size: 16px;">
                <i class="bi bi-grid-1x2-fill text-secondary"></i>
                <span>ASABRI Report</span>
            </div>
            <div class="vr mx-2 text-muted opacity-25"></div>
            <a href="{{ route('reports.index') }}" class="nav-link {{ request()->routeIs('reports.*') ? 'active' : '' }}">Laporan</a>
            <a href="{{ route('directors.index') }}" class="nav-link {{ request()->routeIs('directors.*') ? 'active' : '' }}">Direksi & CC</a>
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