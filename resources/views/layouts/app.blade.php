<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Vehicle Monitor') }} – @yield('title', 'Dashboard')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-gray-50 font-[Inter] antialiased">

<div class="flex h-screen overflow-hidden" x-data="{ sidebarOpen: false }">

    {{-- ── Overlay mobile ─────────────────────────── --}}
    <div
        class="fixed inset-0 z-20 bg-black/50 lg:hidden"
        x-show="sidebarOpen"
        x-transition:enter="transition ease-out duration-150"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        @click="sidebarOpen = false"
    ></div>

    {{-- ── Sidebar ─────────────────────────────────── --}}
    <aside
        class="fixed inset-y-0 left-0 z-30 flex w-64 flex-col bg-indigo-900 text-white transition-transform duration-200 ease-in-out lg:static lg:translate-x-0"
        :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
    >
        {{-- Logo / Brand --}}
        <div class="flex h-16 items-center justify-between px-6 border-b border-indigo-700">
            <span class="flex items-center gap-2 text-lg font-semibold tracking-tight">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-indigo-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M8 17l-4-4m0 0l4-4m-4 4h16M4 7h16"/>
                </svg>
                Vehicle Monitor
            </span>
            <button class="lg:hidden text-indigo-300 hover:text-white" @click="sidebarOpen = false">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                </svg>
            </button>
        </div>

        {{-- Navigation --}}
        <nav class="flex-1 overflow-y-auto px-3 py-4 space-y-1">

            @php
                $navItem = function(string $label, string $route, string $icon) {
                    $active = request()->routeIs($route) || request()->routeIs($route.'.*');
                    return compact('label', 'route', 'icon', 'active');
                };
                $menus = [
                    ['label' => 'Dashboard',            'route' => 'dashboard',               'icon' => 'home'],
                    ['label' => 'Kendaraan Saya',        'route' => 'vehicles.index',          'icon' => 'truck'],
                    ['label' => 'Pengisian BBM',          'route' => 'fuel-transactions.index', 'icon' => 'fuel'],
                    ['label' => 'Jadwal Perawatan',      'route' => 'schedules.index',         'icon' => 'calendar'],
                    ['label' => 'Riwayat Servis',        'route' => 'maintenance-records.index','icon' => 'wrench'],
                    ['label' => 'Laporan Pengeluaran',   'route' => 'reports.index',           'icon' => 'chart'],
                ];
            @endphp

            @foreach ($menus as $menu)
                @php $active = request()->routeIs($menu['route']) || request()->routeIs(str_replace('.index', '.*', $menu['route'])); @endphp
                <a href="{{ route($menu['route']) }}"
                   class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-colors
                          {{ $active ? 'bg-indigo-700 text-white' : 'text-indigo-200 hover:bg-indigo-800 hover:text-white' }}">
                    @include('layouts._sidebar_icon', ['icon' => $menu['icon']])
                    {{ $menu['label'] }}
                </a>
            @endforeach

            {{-- Pengaturan group --}}
            <div class="pt-4">
                <p class="px-3 text-xs font-semibold uppercase tracking-widest text-indigo-400">Pengaturan</p>
                <div class="mt-1 space-y-1">
                    @php
                        $settings = [
                            ['label' => 'Jenis BBM',              'route' => 'fuel-types.index',         'icon' => 'droplet'],
                            ['label' => 'Komponen Perawatan',     'route' => 'maintenance-items.index',  'icon' => 'cog'],
                        ];
                    @endphp
                    @foreach ($settings as $menu)
                        @php $active = request()->routeIs($menu['route']) || request()->routeIs(str_replace('.index', '.*', $menu['route'])); @endphp
                        <a href="{{ route($menu['route']) }}"
                           class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-colors
                                  {{ $active ? 'bg-indigo-700 text-white' : 'text-indigo-200 hover:bg-indigo-800 hover:text-white' }}">
                            @include('layouts._sidebar_icon', ['icon' => $menu['icon']])
                            {{ $menu['label'] }}
                        </a>
                    @endforeach
                </div>
            </div>
        </nav>

        {{-- App info at bottom --}}
        <div class="border-t border-indigo-700 p-4">
            <p class="text-xs text-indigo-300 font-medium">Monitoring Kendaraan</p>
            <p class="text-2xs text-indigo-400 mt-0.5">Versi Pribadi (Tanpa Login)</p>
        </div>
    </aside>

    {{-- ── Main area ───────────────────────────────── --}}
    <div class="flex flex-1 flex-col overflow-hidden">

        {{-- Topbar --}}
        <header class="flex h-16 items-center justify-between border-b bg-white px-6 shadow-sm">
            <button class="text-gray-500 hover:text-gray-700 lg:hidden" @click="sidebarOpen = true">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
            </button>

            <div class="ml-auto flex items-center gap-3">
                <span class="inline-flex items-center gap-1.5 rounded-full bg-indigo-50 px-3 py-1 text-xs font-semibold text-indigo-700">
                    <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
                    {{ auth()->user()->name ?? 'Pemilik Kendaraan' }}
                </span>
            </div>
        </header>

        {{-- Flash messages --}}
        <div class="px-6 pt-4 space-y-2">
            @if (session('success'))
                <div class="flex items-start gap-3 rounded-lg bg-green-50 border border-green-200 px-4 py-3 text-sm text-green-800" role="alert">
                    <svg xmlns="http://www.w3.org/2000/svg" class="mt-0.5 h-4 w-4 shrink-0 text-green-500" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    {{ session('success') }}
                </div>
            @endif
            @if (session('error'))
                <div class="flex items-start gap-3 rounded-lg bg-red-50 border border-red-200 px-4 py-3 text-sm text-red-800" role="alert">
                    <svg xmlns="http://www.w3.org/2000/svg" class="mt-0.5 h-4 w-4 shrink-0 text-red-500" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                    </svg>
                    {{ session('error') }}
                </div>
            @endif
        </div>

        {{-- Page content --}}
        <main class="flex-1 overflow-y-auto p-6">
            @yield('content')
        </main>
    </div>

</div>

<script>
    // Simple Alpine.js-free fallback using vanilla JS for sidebar toggle
    // Alpine.js is assumed to be loaded via app.js
    document.addEventListener('DOMContentLoaded', () => {
        // Flash message auto-dismiss
        setTimeout(() => {
            document.querySelectorAll('[role="alert"]').forEach(el => {
                el.style.transition = 'opacity 0.3s';
                el.style.opacity = '0';
                setTimeout(() => el.remove(), 300);
            });
        }, 4000);
    });
</script>

</body>
</html>
