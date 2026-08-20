<!DOCTYPE html>
<html lang="id" data-theme="{{ $setting->dark_mode ?? false ? 'dark' : 'light' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Kasir') - {{ $setting->business_name ?? 'Percetakan' }}</title>

    <!-- Bootstrap 5 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

    <style>
        :root {
            --primary: {{ $setting->primary_color ?? '#0d6efd' }};
            --sidebar-width: 250px;
            --header-height: 60px;
        }

        [data-theme="dark"] {
            --bs-body-bg: #1a1d21;
            --bs-body-color: #e0e0e0;
            --bs-card-bg: #242830;
            --bs-border-color: #3a3f4a;
            --sidebar-bg: #1e2128;
            --header-bg: #1e2128;
        }

        [data-theme="light"] {
            --sidebar-bg: #ffffff;
            --header-bg: #ffffff;
        }

        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: var(--bs-body-bg, #f4f6f9);
        }

        /* Sidebar */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            width: var(--sidebar-width);
            height: 100vh;
            background: var(--sidebar-bg, #fff);
            border-right: 1px solid var(--bs-border-color, #e9ecef);
            z-index: 1000;
            transition: transform 0.3s ease;
            overflow-y: auto;
        }

        .sidebar-brand {
            padding: 1rem 1.2rem;
            border-bottom: 1px solid var(--bs-border-color, #e9ecef);
            min-height: var(--header-height);
            display: flex;
            align-items: center;
        }

        .sidebar-brand .brand-icon {
            width: 36px;
            height: 36px;
            background: var(--primary);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.1rem;
            flex-shrink: 0;
        }

        .sidebar-brand .brand-text {
            margin-left: 0.75rem;
            font-weight: 700;
            font-size: 0.95rem;
            color: var(--bs-body-color, #212529);
            line-height: 1.2;
        }

        .sidebar-brand .brand-sub {
            font-size: 0.7rem;
            color: #6c757d;
            font-weight: 400;
        }

        .sidebar-nav {
            padding: 0.75rem 0;
        }

        .nav-label {
            padding: 0.4rem 1.2rem;
            font-size: 0.65rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            color: #9ca3af;
            margin-top: 0.5rem;
        }

        .sidebar-nav .nav-link {
            display: flex;
            align-items: center;
            padding: 0.6rem 1.2rem;
            color: var(--bs-body-color, #555);
            font-size: 0.875rem;
            border-radius: 0;
            transition: all 0.15s;
            text-decoration: none;
            gap: 0.75rem;
        }

        .sidebar-nav .nav-link i {
            font-size: 1.05rem;
            width: 20px;
            text-align: center;
            flex-shrink: 0;
        }

        .sidebar-nav .nav-link:hover {
            background: rgba(var(--bs-primary-rgb, 13,110,253), 0.07);
            color: var(--primary);
        }

        .sidebar-nav .nav-link.active {
            background: rgba(var(--bs-primary-rgb, 13,110,253), 0.12);
            color: var(--primary);
            font-weight: 600;
            border-right: 3px solid var(--primary);
        }

        /* Header */
        .main-header {
            position: fixed;
            top: 0;
            left: var(--sidebar-width);
            right: 0;
            height: var(--header-height);
            background: var(--header-bg, #fff);
            border-bottom: 1px solid var(--bs-border-color, #e9ecef);
            z-index: 999;
            display: flex;
            align-items: center;
            padding: 0 1.5rem;
            justify-content: space-between;
        }

        /* Main content */
        .main-content {
            margin-left: var(--sidebar-width);
            margin-top: var(--header-height);
            padding: 1.5rem;
            min-height: calc(100vh - var(--header-height));
        }

        /* Cards */
        .card {
            border: 1px solid var(--bs-border-color, #e9ecef);
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.04);
        }

        .stat-card {
            border-radius: 12px;
            padding: 1.25rem;
            color: white;
            background: var(--primary);
        }

        .stat-icon {
            width: 48px;
            height: 48px;
            border-radius: 10px;
            background: rgba(255,255,255,0.2);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.4rem;
        }

        .btn-primary {
            background-color: var(--primary);
            border-color: var(--primary);
        }

        .btn-primary:hover {
            filter: brightness(0.9);
            background-color: var(--primary);
            border-color: var(--primary);
        }

        /* Toast */
        .toast-container {
            position: fixed;
            top: 75px;
            right: 20px;
            z-index: 9999;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }
            .sidebar.show {
                transform: translateX(0);
            }
            .main-header {
                left: 0;
            }
            .main-content {
                margin-left: 0;
            }
        }

        /* Print */
        @media print {
            .sidebar, .main-header, .no-print { display: none !important; }
            .main-content { margin: 0 !important; padding: 0 !important; }
        }

        [data-theme="dark"] .card { background: var(--bs-card-bg); }
        [data-theme="dark"] .table { --bs-table-bg: var(--bs-card-bg); color: var(--bs-body-color); }
        [data-theme="dark"] .form-control, [data-theme="dark"] .form-select {
            background: #2d313a; border-color: #3a3f4a; color: #e0e0e0;
        }
        [data-theme="dark"] .modal-content { background: #242830; }
        [data-theme="dark"] .dropdown-menu { background: #242830; border-color: #3a3f4a; }
        [data-theme="dark"] .dropdown-item { color: #e0e0e0; }
        [data-theme="dark"] .dropdown-item:hover { background: #2d313a; }
    </style>

    @stack('styles')
</head>
<body>

<!-- Sidebar Overlay (mobile) -->
<div class="sidebar-overlay d-md-none" id="sidebarOverlay" style="display:none!important; position:fixed;inset:0;background:rgba(0,0,0,0.5);z-index:999;" onclick="closeSidebar()"></div>

<!-- Sidebar -->
<nav class="sidebar" id="sidebar">
    <div class="sidebar-brand">
        @if($setting->logo ?? false)
            <img src="{{ $setting->logo_url }}" alt="Logo" style="height:36px;width:36px;object-fit:contain;border-radius:8px;">
        @else
            <div class="brand-icon"><i class="bi bi-printer"></i></div>
        @endif
        <div class="brand-text ms-2">
            <div>{{ $setting->business_name ?? 'Percetakan' }}</div>
            <div class="brand-sub">Kasir System</div>
        </div>
    </div>

    <div class="sidebar-nav">
        <div class="nav-label">Menu Utama</div>
        <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <i class="bi bi-speedometer2"></i> Dashboard
        </a>
        <a href="{{ route('kasir.index') }}" class="nav-link {{ request()->routeIs('kasir*') ? 'active' : '' }}">
            <i class="bi bi-cash-register"></i> Kasir / POS
        </a>
        <a href="{{ route('transactions.index') }}" class="nav-link {{ request()->routeIs('transactions*') ? 'active' : '' }}">
            <i class="bi bi-receipt"></i> Transaksi
        </a>

        @if(auth()->user()->isAdmin())
        <div class="nav-label">Master Data</div>
        <a href="{{ route('products.index') }}" class="nav-link {{ request()->routeIs('products*') ? 'active' : '' }}">
            <i class="bi bi-grid-3x3-gap"></i> Produk & Jasa
        </a>
        <a href="{{ route('customers.index') }}" class="nav-link {{ request()->routeIs('customers*') ? 'active' : '' }}">
            <i class="bi bi-people"></i> Pelanggan
        </a>

        <div class="nav-label">Laporan</div>
        <a href="{{ route('reports.index') }}" class="nav-link {{ request()->routeIs('reports*') ? 'active' : '' }}">
            <i class="bi bi-bar-chart-line"></i> Laporan
        </a>

        <div class="nav-label">Pengaturan</div>
        <a href="{{ route('settings.index') }}" class="nav-link {{ request()->routeIs('settings*') ? 'active' : '' }}">
            <i class="bi bi-gear"></i> Pengaturan
        </a>
        @endif
    </div>
</nav>

<!-- Header -->
<header class="main-header">
    <div class="d-flex align-items-center gap-2">
        <button class="btn btn-sm btn-light d-md-none" onclick="toggleSidebar()">
            <i class="bi bi-list fs-5"></i>
        </button>
        <h6 class="mb-0 d-none d-md-block fw-semibold text-muted" style="font-size:0.85rem;">
            @yield('breadcrumb', 'Dashboard')
        </h6>
    </div>

    <div class="d-flex align-items-center gap-2">
        <!-- Quick Kasir -->
        <a href="{{ route('kasir.index') }}" class="btn btn-sm btn-primary d-none d-md-flex align-items-center gap-1">
            <i class="bi bi-plus-circle"></i> Transaksi Baru
        </a>

        <!-- User dropdown -->
        <div class="dropdown">
            <button class="btn btn-sm btn-light dropdown-toggle d-flex align-items-center gap-2" data-bs-toggle="dropdown">
                <div style="width:28px;height:28px;border-radius:50%;background:var(--primary);display:flex;align-items:center;justify-content:center;color:white;font-size:0.75rem;font-weight:700;">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </div>
                <span class="d-none d-sm-inline" style="font-size:0.85rem;">{{ auth()->user()->name }}</span>
            </button>
            <ul class="dropdown-menu dropdown-menu-end">
                <li><span class="dropdown-item-text text-muted small">{{ auth()->user()->role }}</span></li>
                <li><hr class="dropdown-divider"></li>
                <li>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="dropdown-item text-danger">
                            <i class="bi bi-box-arrow-right me-2"></i>Logout
                        </button>
                    </form>
                </li>
            </ul>
        </div>
    </div>
</header>

<!-- Main Content -->
<main class="main-content">
    @yield('content')
</main>

<!-- Toast Container -->
<div class="toast-container" id="toastContainer"></div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
function toggleSidebar() {
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebarOverlay');
    sidebar.classList.toggle('show');
    if (sidebar.classList.contains('show')) {
        overlay.style.display = 'block';
    } else {
        overlay.style.display = 'none';
    }
}

function closeSidebar() {
    document.getElementById('sidebar').classList.remove('show');
    document.getElementById('sidebarOverlay').style.display = 'none';
}

function showToast(message, type = 'success') {
    const colors = {
        success: '#198754',
        error: '#dc3545',
        warning: '#ffc107',
        info: '#0dcaf0'
    };
    const icons = {
        success: 'bi-check-circle-fill',
        error: 'bi-x-circle-fill',
        warning: 'bi-exclamation-triangle-fill',
        info: 'bi-info-circle-fill'
    };
    const id = 'toast_' + Date.now();
    const html = `
        <div id="${id}" class="toast align-items-center text-white border-0 mb-2 show" role="alert" style="background:${colors[type]}">
            <div class="d-flex">
                <div class="toast-body d-flex align-items-center gap-2">
                    <i class="bi ${icons[type]}"></i> ${message}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" onclick="document.getElementById('${id}').remove()"></button>
            </div>
        </div>`;
    document.getElementById('toastContainer').insertAdjacentHTML('beforeend', html);
    setTimeout(() => {
        const el = document.getElementById(id);
        if (el) el.remove();
    }, 4000);
}

// Show flash messages
@if(session('success'))
    showToast("{{ session('success') }}", 'success');
@endif
@if(session('error'))
    showToast("{{ session('error') }}", 'error');
@endif
</script>

@stack('scripts')
</body>
</html>
