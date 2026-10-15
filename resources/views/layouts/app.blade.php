<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Inventory Management System') - Laravel P5</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #2563eb;
            --primary-hover: #1d4ed8;
            --primary-light: #eff6ff;
            --secondary: #0f172a;
            --accent: #0ea5e9;
            --success: #10b981;
            --success-light: #ecfdf5;
            --warning: #f59e0b;
            --warning-light: #fffbeb;
            --danger: #ef4444;
            --danger-light: #fef2f2;
            --bg: #f8fafc;
            --card-bg: #ffffff;
            --text-main: #0f172a;
            --text-muted: #64748b;
            --border: #e2e8f0;
            --border-hover: #cbd5e1;
            --shadow-sm: 0 1px 2px 0 rgb(0 0 0 / 0.05);
            --shadow: 0 4px 6px -1px rgb(0 0 0 / 0.07), 0 2px 4px -2px rgb(0 0 0 / 0.05);
            --shadow-lg: 0 10px 15px -3px rgb(0 0 0 / 0.08), 0 4px 6px -4px rgb(0 0 0 / 0.04);
            --radius: 12px;
            --radius-sm: 8px;
            --radius-lg: 16px;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background-color: var(--bg);
            color: var(--text-main);
            line-height: 1.5;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* Top Bar */
        .topbar {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            color: #ffffff;
            padding: 16px 32px;
            box-shadow: var(--shadow);
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
        }

        .topbar-container {
            max-width: 1400px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 16px;
        }

        .brand-box {
            display: flex;
            align-items: center;
            gap: 14px;
            text-decoration: none;
            color: #ffffff;
        }

        .brand-icon {
            width: 42px;
            height: 42px;
            border-radius: 10px;
            background: linear-gradient(135deg, #38bdf8 0%, #2563eb 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            font-size: 20px;
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.35);
        }

        .brand-text h1 {
            font-size: 19px;
            font-weight: 700;
            letter-spacing: -0.02em;
        }

        .brand-text p {
            font-size: 12px;
            color: #94a3b8;
            font-weight: 500;
        }

        .user-panel {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .user-badge {
            background: rgba(255, 255, 255, 0.08);
            backdrop-filter: blur(8px);
            padding: 6px 14px;
            border-radius: 9999px;
            font-size: 13px;
            display: flex;
            align-items: center;
            gap: 8px;
            border: 1px solid rgba(255, 255, 255, 0.12);
        }

        .user-role-tag {
            background: #2563eb;
            color: white;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            padding: 2px 7px;
            border-radius: 6px;
            letter-spacing: 0.03em;
        }

        .btn-logout {
            background: rgba(239, 68, 68, 0.15);
            color: #fca5a5;
            border: 1px solid rgba(239, 68, 68, 0.3);
            border-radius: var(--radius-sm);
            padding: 7px 14px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .btn-logout:hover {
            background: #ef4444;
            color: white;
            border-color: #ef4444;
        }

        /* Nav Header */
        .main-nav {
            background: #ffffff;
            border-bottom: 1px solid var(--border);
            padding: 0 32px;
            box-shadow: var(--shadow-sm);
        }

        .nav-container {
            max-width: 1400px;
            margin: 0 auto;
            display: flex;
            gap: 6px;
            overflow-x: auto;
        }

        .nav-link {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 14px 18px;
            font-size: 14px;
            font-weight: 600;
            text-decoration: none;
            color: var(--text-muted);
            border-bottom: 2px solid transparent;
            transition: all 0.2s ease;
            white-space: nowrap;
        }

        .nav-link:hover {
            color: var(--primary);
            background: var(--primary-light);
        }

        .nav-link.active {
            color: var(--primary);
            border-bottom-color: var(--primary);
            background: var(--primary-light);
        }

        /* Layout Structure */
        .page-wrapper {
            flex: 1;
            max-width: 1400px;
            width: 100%;
            margin: 0 auto;
            padding: 28px 24px;
        }

        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 16px;
            margin-bottom: 24px;
        }

        .page-title h2 {
            font-size: 24px;
            font-weight: 800;
            letter-spacing: -0.02em;
            color: var(--secondary);
        }

        .page-title p {
            font-size: 14px;
            color: var(--text-muted);
            margin-top: 4px;
        }

        /* Buttons & Actions */
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 9px 18px;
            font-size: 14px;
            font-weight: 600;
            border-radius: var(--radius-sm);
            text-decoration: none;
            border: 1px solid transparent;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .btn-primary {
            background-color: var(--primary);
            color: #ffffff;
            box-shadow: 0 2px 4px rgba(37, 99, 235, 0.2);
        }

        .btn-primary:hover {
            background-color: var(--primary-hover);
            transform: translateY(-1px);
        }

        .btn-secondary {
            background-color: #ffffff;
            color: var(--secondary);
            border-color: var(--border);
        }

        .btn-secondary:hover {
            background-color: #f1f5f9;
            border-color: var(--border-hover);
        }

        .btn-danger {
            background-color: var(--danger);
            color: #ffffff;
        }

        .btn-danger:hover {
            background-color: #dc2626;
        }

        .btn-sm {
            padding: 6px 12px;
            font-size: 13px;
        }

        /* Cards */
        .card {
            background: var(--card-bg);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            padding: 24px;
            box-shadow: var(--shadow);
            margin-bottom: 24px;
        }

        .card-header {
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 12px;
        }

        .card-header h3 {
            font-size: 18px;
            font-weight: 700;
            color: var(--secondary);
        }

        /* Stats Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 20px;
            margin-bottom: 24px;
        }

        .stat-card {
            background: #ffffff;
            border: 1px solid var(--border);
            border-radius: var(--radius);
            padding: 22px;
            box-shadow: var(--shadow);
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-lg);
        }

        .stat-info .stat-label {
            font-size: 13px;
            font-weight: 600;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }

        .stat-info .stat-value {
            font-size: 28px;
            font-weight: 800;
            color: var(--secondary);
            margin-top: 6px;
            letter-spacing: -0.02em;
        }

        .stat-info .stat-desc {
            font-size: 12px;
            color: var(--text-muted);
            margin-top: 4px;
        }

        .stat-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
        }

        .stat-icon.blue { background: #dbeafe; color: #1d4ed8; }
        .stat-icon.emerald { background: #d1fae5; color: #047857; }
        .stat-icon.amber { background: #fef3c7; color: #b45309; }
        .stat-icon.purple { background: #f3e8ff; color: #7e22ce; }

        /* Tables */
        .table-responsive {
            width: 100%;
            overflow-x: auto;
            border-radius: var(--radius-sm);
        }

        table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            font-size: 14px;
            text-align: left;
        }

        thead th {
            background-color: #f1f5f9;
            color: #475569;
            font-weight: 700;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            padding: 14px 16px;
            border-bottom: 1px solid var(--border);
        }

        thead th:first-child { border-top-left-radius: var(--radius-sm); }
        thead th:last-child { border-top-right-radius: var(--radius-sm); }

        tbody td {
            padding: 14px 16px;
            border-bottom: 1px solid var(--border);
            vertical-align: middle;
            color: #334155;
        }

        tbody tr:hover {
            background-color: #f8fafc;
        }

        tbody tr:last-child td {
            border-bottom: none;
        }

        /* Badges */
        .badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 4px 10px;
            font-size: 12px;
            font-weight: 600;
            border-radius: 9999px;
            line-height: 1;
        }

        .badge-success { background: #dcfce7; color: #15803d; }
        .badge-warning { background: #fef3c7; color: #b45309; }
        .badge-danger { background: #fee2e2; color: #b91c1c; }
        .badge-info { background: #e0f2fe; color: #0369a1; }
        .badge-neutral { background: #f1f5f9; color: #475569; }

        .pulse-dot {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background-color: currentColor;
            display: inline-block;
        }

        /* Forms */
        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
            gap: 20px;
            margin-bottom: 20px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .form-group label {
            font-size: 13px;
            font-weight: 600;
            color: #334155;
        }

        .form-control {
            width: 100%;
            padding: 10px 14px;
            font-family: inherit;
            font-size: 14px;
            color: var(--text-main);
            background-color: #ffffff;
            border: 1px solid var(--border);
            border-radius: var(--radius-sm);
            transition: all 0.2s ease;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.15);
        }

        .form-control.is-invalid {
            border-color: var(--danger);
            background-color: #fffaf0;
        }

        .invalid-feedback {
            font-size: 12px;
            color: var(--danger);
            font-weight: 500;
        }

        .checkbox-group {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
            gap: 12px;
            margin-top: 8px;
        }

        .checkbox-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 14px;
            border: 1px solid var(--border);
            border-radius: var(--radius-sm);
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .checkbox-item:hover {
            border-color: var(--primary);
            background-color: var(--primary-light);
        }

        .checkbox-item input[type="checkbox"] {
            width: 16px;
            height: 16px;
            accent-color: var(--primary);
        }

        /* Alerts */
        .alert {
            display: flex;
            align-items: flex-start;
            gap: 14px;
            padding: 14px 18px;
            border-radius: var(--radius-sm);
            margin-bottom: 20px;
            font-size: 14px;
            line-height: 1.4;
        }

        .alert-success {
            background-color: var(--success-light);
            border: 1px solid #a7f3d0;
            color: #065f46;
        }

        .alert-danger {
            background-color: var(--danger-light);
            border: 1px solid #fecaca;
            color: #991b1b;
        }

        .alert-warning {
            background-color: var(--warning-light);
            border: 1px solid #fde68a;
            color: #92400e;
        }

        .alert-info {
            background-color: var(--primary-light);
            border: 1px solid #bfdbfe;
            color: #1e40af;
        }

        .alert-icon {
            font-weight: 800;
            font-size: 16px;
        }

        /* Pagination Styles */
        .pagination-container {
            margin-top: 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 12px;
        }

        nav[role="navigation"] {
            display: flex;
            align-items: center;
            gap: 4px;
        }

        /* Footer */
        footer {
            background: #ffffff;
            border-top: 1px solid var(--border);
            padding: 20px 32px;
            text-align: center;
            color: var(--text-muted);
            font-size: 13px;
            margin-top: auto;
        }

        /* Actions wrap */
        .action-buttons {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        @media (max-width: 768px) {
            .page-wrapper { padding: 18px 14px; }
            .topbar { padding: 14px 16px; }
            .main-nav { padding: 0 16px; }
            .stat-info .stat-value { font-size: 24px; }
        }
    </style>
</head>
<body>
    <!-- Topbar Header -->
    <header class="topbar">
        <div class="topbar-container">
            <a href="{{ route('dashboard') }}" class="brand-box">
                <div class="brand-icon">📦</div>
                <div class="brand-text">
                    <h1>Inventory Management System</h1>
                    <p>Pertemuan 5 — Laravel Fundamental MVC Refactoring</p>
                </div>
            </a>

            @if(session('demo_user'))
                <div class="user-panel">
                    <div class="user-badge">
                        <span>Masuk sebagai: <b>{{ session('demo_user') }}</b></span>
                        <span class="user-role-tag">{{ session('demo_role', 'Staff') }}</span>
                    </div>
                    <form method="POST" action="{{ route('logout') }}" style="display:inline">
                        @csrf
                        <button type="submit" class="btn-logout" title="Keluar dari sesi demo">Keluar</button>
                    </form>
                </div>
            @endif
        </div>
    </header>

    <!-- Main Navigation Bar -->
    @if(session('demo_user'))
        <nav class="main-nav">
            <div class="nav-container">
                <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <span>📊</span> Dashboard
                </a>
                <a href="{{ route('products.index') }}" class="nav-link {{ request()->routeIs('products.*') ? 'active' : '' }}">
                    <span>📦</span> Data Produk
                </a>
                <a href="{{ route('categories.index') }}" class="nav-link {{ request()->routeIs('categories.*') ? 'active' : '' }}">
                    <span>🏷️</span> Kategori
                </a>
                <a href="{{ route('suppliers.index') }}" class="nav-link {{ request()->routeIs('suppliers.*') ? 'active' : '' }}">
                    <span>🏭</span> Supplier
                </a>
                <a href="{{ route('stock.index') }}" class="nav-link {{ request()->routeIs('stock.*') ? 'active' : '' }}">
                    <span>🔄</span> Transaksi Stok
                </a>
            </div>
        </nav>
    @endif

    <!-- Main Content Area -->
    <main class="page-wrapper">
        {{-- Flash Alerts using Blade Component (Slide 23) --}}
        @if(session('success'))
            <x-alert type="success">
                {{ session('success') }}
            </x-alert>
        @endif

        @if(session('warning'))
            <x-alert type="warning">
                {{ session('warning') }}
            </x-alert>
        @endif

        @if($errors->has('delete'))
            <x-alert type="danger">
                {{ $errors->first('delete') }}
            </x-alert>
        @endif

        @yield('content')
    </main>

    <!-- Footer -->
    <footer>
        <p><b>Inventory Management System</b> &copy; {{ date('Y') }} &bull; Tugas Praktikum Pertemuan 5 (P5) — Implementasi Laravel MVC Architecture</p>
    </footer>
</body>
</html>
