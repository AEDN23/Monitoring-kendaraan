<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Vehicle Monitor') }} – @yield('title', 'Dashboard')</title>

    {{-- Offline Bootstrap 5 & Icons (Zero CDN / Zero Lag) --}}
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/bootstrap-icons.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/app-custom.css') }}">
</head>
<body>

{{-- Mobile Sidebar Backdrop --}}
<div class="sidebar-backdrop" id="sidebarBackdrop"></div>

{{-- Sidebar Navigation --}}
<aside class="app-sidebar" id="appSidebar">
    <div class="sidebar-brand">
        <i class="bi bi-speedometer2 text-primary me-2 fs-4"></i>
        <span>Vehicle Monitor</span>
        <button type="button" class="btn-close btn-close-white ms-auto d-lg-none" id="closeSidebarBtn" aria-label="Close"></button>
    </div>

    <div class="sidebar-menu">
        <ul class="nav flex-column mb-3">
            @php
                $menus = [
                    ['label' => 'Dashboard',            'route' => 'dashboard',               'icon' => 'bi-speedometer2'],
                    ['label' => 'Kendaraan Saya',        'route' => 'vehicles.index',          'icon' => 'bi-truck'],
                    ['label' => 'Pengisian BBM',          'route' => 'fuel-transactions.index', 'icon' => 'bi-fuel-pump'],
                    ['label' => 'Jadwal Perawatan',      'route' => 'schedules.index',         'icon' => 'bi-calendar-check'],
                    ['label' => 'Riwayat Servis',        'route' => 'maintenance-records.index','icon' => 'bi-tools'],
                    ['label' => 'Laporan Pengeluaran',   'route' => 'reports.index',           'icon' => 'bi-bar-chart-line'],
                ];
            @endphp

            @foreach ($menus as $menu)
                @php $active = request()->routeIs($menu['route']) || request()->routeIs(str_replace('.index', '.*', $menu['route'])); @endphp
                <li class="nav-item">
                    <a href="{{ route($menu['route']) }}" class="nav-link {{ $active ? 'active' : '' }}">
                        <i class="bi {{ $menu['icon'] }}"></i>
                        <span>{{ $menu['label'] }}</span>
                    </a>
                </li>
            @endforeach
        </ul>

        <div class="menu-header">Pengaturan</div>
        <ul class="nav flex-column">
            @php
                $settings = [
                    ['label' => 'Jenis BBM',              'route' => 'fuel-types.index',         'icon' => 'bi-droplet-half'],
                    ['label' => 'Komponen Perawatan',     'route' => 'maintenance-items.index',  'icon' => 'bi-gear'],
                ];
            @endphp
            @foreach ($settings as $menu)
                @php $active = request()->routeIs($menu['route']) || request()->routeIs(str_replace('.index', '.*', $menu['route'])); @endphp
                <li class="nav-item">
                    <a href="{{ route($menu['route']) }}" class="nav-link {{ $active ? 'active' : '' }}">
                        <i class="bi {{ $menu['icon'] }}"></i>
                        <span>{{ $menu['label'] }}</span>
                    </a>
                </li>
            @endforeach
        </ul>
    </div>

    <div class="p-3 border-top border-secondary border-opacity-25 text-white-50 small">
        <div class="fw-semibold text-white">Monitoring Kendaraan</div>
        <div class="text-xs">Versi Pribadi (Tanpa Login)</div>
    </div>
</aside>

{{-- Main Page Container --}}
<div class="app-main">
    {{-- Header / Topbar --}}
    <header class="app-header">
        <button class="btn btn-sm btn-outline-secondary d-lg-none" type="button" id="openSidebarBtn">
            <i class="bi bi-list fs-5"></i>
        </button>

        <div class="d-none d-md-block fw-semibold text-secondary">
            @yield('header', 'Dashboard')
        </div>

        <div class="ms-auto d-flex align-items-center">
            <span class="badge badge-subtle-primary py-2 px-3 rounded-pill d-flex align-items-center gap-2">
                <span class="badge bg-success p-1 rounded-circle"></span>
                <span>{{ auth()->user()->name ?? 'Pemilik Kendaraan' }}</span>
            </span>
        </div>
    </header>

    {{-- Flash Notifications --}}
    <div class="px-4 pt-3">
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show d-flex align-items-center" role="alert">
                <i class="bi bi-check-circle-fill me-2 fs-5"></i>
                <div>{{ session('success') }}</div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2 fs-5"></i>
                <div>{{ session('error') }}</div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
    </div>

    {{-- Content --}}
    <main class="app-content">
        @yield('content')
    </main>
</div>

{{-- Offline Bootstrap 5 JS Bundle --}}
<script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Mobile Sidebar toggler
        const sidebar = document.getElementById('appSidebar');
        const backdrop = document.getElementById('sidebarBackdrop');
        const openBtn = document.getElementById('openSidebarBtn');
        const closeBtn = document.getElementById('closeSidebarBtn');

        function toggleSidebar(show) {
            if (show) {
                sidebar.classList.add('show');
                backdrop.classList.add('show');
            } else {
                sidebar.classList.remove('show');
                backdrop.classList.remove('show');
            }
        }

        if (openBtn) openBtn.addEventListener('click', () => toggleSidebar(true));
        if (closeBtn) closeBtn.addEventListener('click', () => toggleSidebar(false));
        if (backdrop) backdrop.addEventListener('click', () => toggleSidebar(false));

        // Auto dismiss flash alerts
        setTimeout(() => {
            document.querySelectorAll('.alert').forEach(function (alertEl) {
                const bsAlert = bootstrap.Alert.getOrCreateInstance(alertEl);
                if (bsAlert) bsAlert.close();
            });
        }, 4000);

        // Global Anti Double-Submit
        document.querySelectorAll('form').forEach(function (form) {
            form.addEventListener('submit', function (e) {
                if (form.dataset.submitting === 'true') {
                    e.preventDefault();
                    return false;
                }
                form.dataset.submitting = 'true';
                const submitBtn = form.querySelector('button[type="submit"]');
                if (submitBtn) {
                    submitBtn.disabled = true;
                    submitBtn.classList.add('disabled');
                    const originalText = submitBtn.innerHTML;
                    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Menyimpan...';
                }
            });
        });
    });
</script>
</body>
</html>
