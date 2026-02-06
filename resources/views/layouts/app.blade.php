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
        :root { --bg-body: #FBFBFA; --bg-card: #FFFFFF; --border-color: #E9E9E7; --text-primary: #37352F; --text-secondary: #787774; --primary-color: #2383E2; --hover-bg: #F0F0EF; }
        html, body { height: 100%; font-family: 'Inter', sans-serif; background: var(--bg-body); color: var(--text-primary); display: flex; flex-direction: column; }
        .navbar { background: var(--bg-card); border-bottom: 1px solid var(--border-color); padding: 12px 0; flex-shrink: 0; }
        .nav-link { color: var(--text-secondary) !important; font-weight: 500; font-size: 14px; padding: 6px 12px; border-radius: 4px; transition: 0.2s; }
        .nav-link:hover, .nav-link.active { background: var(--hover-bg); color: var(--text-primary) !important; }
        .container { max-width: 1080px; padding: 40px 12px; flex: 1 0 auto; }
        .card-custom { background: var(--bg-card); border: 1px solid var(--border-color); box-shadow: 0 1px 2px rgba(0,0,0,0.02); border-radius: 8px; }
        h3, h4, h5 { font-weight: 600; letter-spacing: -0.5px; color: var(--text-primary); }
        .text-label { font-size: 11px; text-transform: uppercase; color: var(--text-secondary); font-weight: 600; margin-bottom: 6px; }
        .btn-filter { background: #FFF; border: 1px solid #E0E0DE; color: #37352F; font-size: 13px; font-weight: 500; padding: 8px 16px; border-radius: 6px; display: flex; align-items: center; justify-content: space-between; min-width: 160px; }
        .btn-filter:hover { background: #F7F7F5; }
        .dropdown-menu-filter { border: 1px solid #E9E9E7; box-shadow: 0 4px 20px rgba(0,0,0,0.08); border-radius: 6px; padding: 4px; margin-top: 4px !important; min-width: 180px; max-height: 300px; overflow-y: auto; }
        .dropdown-item { font-size: 13px; font-weight: 500; padding: 6px 10px; border-radius: 4px; margin-bottom: 1px; }
        .dropdown-item:hover { background: #F0F0EF; }
        .dropdown-item.active { background: #EBF5FE; color: var(--text-primary); }
        .form-control { border: 1px solid #D9D9D7; border-radius: 6px; font-size: 14px; padding: 8px 12px; box-shadow: none; }
        .btn { font-weight: 500; font-size: 14px; border-radius: 6px; padding: 8px 16px; }
        .btn-primary { background: var(--primary-color); border: none; }
        .btn-icon { padding: 4px 8px; color: var(--text-secondary); border-radius: 4px; background: transparent; border: none; }
        .btn-icon:hover { background: var(--hover-bg); color: var(--text-primary); }
        .table-notion { width: 100%; border-collapse: separate; border-spacing: 0; }
        .table-notion th { font-size: 11px; text-transform: uppercase; color: var(--text-secondary); font-weight: 600; border-bottom: 1px solid var(--border-color); padding: 12px 16px; }
        .table-notion td { font-size: 14px; padding: 12px 16px; border-bottom: 1px solid var(--border-color); vertical-align: middle; }
        .table-notion tr:hover td { background: #F9F9F8; }
        .badge-clean { background: #EFEFEF; color: var(--text-primary); font-weight: 600; padding: 4px 8px; border-radius: 4px; font-size: 11px; display: inline-block; }
    </style>
</head>
<body>
    <nav class="navbar sticky-top">
        <div class="container d-flex justify-content-start gap-4 py-0">
            <div class="navbar-brand d-flex align-items-center gap-2">
                <img src="{{ asset('images/logo-asabri.png') }}" alt="Logo ASABRI" height="30">
            </div>
            <div class="vr mx-2 text-muted opacity-25"></div>
            <a href="{{ route('reports.index') }}" class="nav-link {{ request()->routeIs('reports.*') ? 'active' : '' }}">Laporan</a>
            <a href="{{ route('directors.index') }}" class="nav-link {{ request()->routeIs('directors.*') ? 'active' : '' }}">Direksi & CC</a>
        </div>
    </nav>
    <div class="container">@yield('content')</div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.querySelectorAll('.rupiah').forEach(i => i.addEventListener('keyup', function(e) {
            let n = this.value.replace(/[^,\d]/g, '').toString().split(','), r = n[0], s = r.length % 3, ru = r.substr(0, s), ri = r.substr(s).match(/\d{3}/gi);
            if (ri) ru += (s ? '.' : '') + ri.join('.');
            this.value = n[1] != undefined ? ru + ',' + n[1] : ru;
        }));
    </script>
</body>
</html>