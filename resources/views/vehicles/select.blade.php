<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Pilih Kendaraan – {{ config('app.name', 'Vehicle Monitor') }}</title>

    {{-- Offline Bootstrap 5 & Icons --}}
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/bootstrap-icons.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/app-custom.css') }}">

    <style>
        body {
            background-color: #0f172a;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #f8fafc;
            padding: 2rem 1rem;
        }
        .vehicle-card {
            background: #1e293b;
            border: 2px solid #334155;
            border-radius: 1rem;
            transition: all 0.25s ease-in-out;
            cursor: pointer;
            text-decoration: none;
            color: inherit;
            display: flex;
            flex-direction: column;
            height: 100%;
        }
        .vehicle-card:hover {
            transform: translateY(-6px);
            border-color: #3b82f6;
            box-shadow: 0 12px 24px -8px rgba(59, 130, 246, 0.4);
            color: inherit;
        }
        .vehicle-card.active-card {
            border-color: #10b981;
            box-shadow: 0 0 0 2px rgba(16, 185, 129, 0.3);
        }
        .vehicle-icon-box {
            width: 68px;
            height: 68px;
            border-radius: 1rem;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #0f172a;
            color: #3b82f6;
            font-size: 2rem;
            margin-bottom: 1rem;
            transition: all 0.25s ease;
        }
        .vehicle-card:hover .vehicle-icon-box {
            background: #3b82f6;
            color: #ffffff;
        }
        .license-badge {
            background: #000000;
            color: #ffffff;
            border: 1px solid #475569;
            padding: 0.25rem 0.65rem;
            border-radius: 0.375rem;
            font-family: monospace;
            font-weight: bold;
            letter-spacing: 1px;
            font-size: 0.85rem;
        }
        .btn-add-vehicle {
            border: 2px dashed #475569;
            background: rgba(30, 41, 59, 0.5);
            border-radius: 1rem;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            color: #94a3b8;
            text-decoration: none;
            min-height: 240px;
            transition: all 0.2s ease;
        }
        .btn-add-vehicle:hover {
            border-color: #3b82f6;
            color: #3b82f6;
            background: rgba(30, 41, 59, 0.9);
            transform: translateY(-4px);
        }
    </style>
</head>
<body>

<div class="container" style="max-width: 960px;">
    {{-- Header --}}
    <div class="text-center mb-5">
        <div class="d-inline-flex align-items-center justify-content-center bg-primary bg-opacity-25 text-primary rounded-circle p-3 mb-3">
            <i class="bi bi-speedometer2 fs-2"></i>
        </div>
        <h2 class="fw-bold mb-2">Pilih Kendaraan Anda</h2>
        <p class="text-secondary mb-0">Pilih kendaraan untuk memantau data pengisian BBM, jadwal perawatan, dan riwayat servis.</p>
    </div>

    {{-- Flash Notifications --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 bg-success bg-opacity-25 text-white" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if (session('info'))
        <div class="alert alert-info alert-dismissible fade show border-0 bg-info bg-opacity-25 text-white" role="alert">
            <i class="bi bi-info-circle-fill me-2"></i> {{ session('info') }}
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Grid Kendaraan --}}
    <div class="row g-4 justify-content-center">
        @foreach ($vehicles as $v)
            @php
                $isActive = ($activeVehicleId == $v->id);
                $isMotor = ($v->vehicle_type === 'motorcycle');
            @endphp
            <div class="col-12 col-md-6 col-lg-4">
                <form action="{{ route('vehicle.select.post', $v) }}" method="POST" class="h-100">
                    @csrf
                    <button type="submit" class="vehicle-card p-4 text-start w-100 {{ $isActive ? 'active-card' : '' }}">
                        <div class="d-flex align-items-start justify-content-between">
                            <div class="vehicle-icon-box">
                                <i class="bi {{ $isMotor ? 'bi-bicycle' : 'bi-car-front-fill' }}"></i>
                            </div>
                            @if($isActive)
                                <span class="badge bg-success bg-opacity-25 text-success border border-success border-opacity-50 px-2 py-1 small">
                                    <i class="bi bi-check2-circle me-1"></i> Sedang Aktif
                                </span>
                            @endif
                        </div>

                        <h4 class="fw-bold mb-1 text-white">{{ $v->name }}</h4>
                        <div class="text-secondary small mb-3">
                            {{ ucfirst($v->brand ?? '-') }} {{ $v->model ?? '' }} {{ $v->production_year ? "({$v->production_year})" : '' }}
                        </div>

                        @if ($v->license_plate)
                            <div class="mb-3">
                                <span class="license-badge">{{ $v->license_plate }}</span>
                            </div>
                        @endif

                        <div class="mt-auto pt-3 border-top border-secondary border-opacity-25 d-flex align-items-center justify-content-between text-secondary small">
                            <span>Odometer:</span>
                            <span class="fw-bold text-white font-monospace">
                                {{ number_format($v->latestOdometerKm() ?? 0, 0, ',', '.') }} KM
                            </span>
                        </div>

                        <div class="mt-3 btn btn-sm {{ $isActive ? 'btn-success' : 'btn-outline-primary' }} w-100 rounded-pill fw-semibold">
                            {{ $isActive ? 'Lanjut Pantau' : 'Pilih Kendaraan Ini' }} <i class="bi bi-arrow-right ms-1"></i>
                        </div>
                    </button>
                </form>
            </div>
        @endforeach

        {{-- Kartu Tambah Kendaraan Baru --}}
        <div class="col-12 col-md-6 col-lg-4">
            <a href="{{ route('vehicles.create') }}" class="btn-add-vehicle p-4 h-100 text-center">
                <div class="rounded-circle bg-secondary bg-opacity-25 p-3 mb-3 text-secondary" style="width: 60px; height: 60px; display: inline-flex; align-items: center; justify-content: center;">
                    <i class="bi bi-plus-lg fs-3"></i>
                </div>
                <div class="fw-bold text-white fs-5 mb-1">Tambah Kendaraan</div>
                <p class="text-secondary small mb-0">Daftarkan motor atau mobil baru untuk mulai dipantau.</p>
            </a>
        </div>
    </div>
</div>

<script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
</body>
</html>
