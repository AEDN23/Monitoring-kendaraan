@extends('layouts.app')

@section('title', 'Dashboard')
@section('header', 'Dashboard Monitoring Kendaraan')

@section('content')
<div class="container-fluid p-0">

    {{-- ── Kartu Ringkasan Statistik ──────────────────────────────── --}}
    <div class="row g-3 mb-4">
        {{-- Total Kendaraan --}}
        <div class="col-12 col-md-4">
            <div class="card border-0 shadow-sm rounded-3 p-3 h-100">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-uppercase text-secondary fw-semibold small" style="font-size: 0.75rem;">Kendaraan Aktif</div>
                        <div class="fs-2 fw-bold text-dark mt-1">{{ $totalVehicles }}</div>
                    </div>
                    <div class="rounded-3 p-3 bg-primary bg-opacity-10 text-primary">
                        <i class="bi bi-truck fs-3"></i>
                    </div>
                </div>
            </div>
        </div>

        {{-- Biaya BBM Bulan Ini --}}
        <div class="col-12 col-md-4">
            <div class="card border-0 shadow-sm rounded-3 p-3 h-100">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-uppercase text-secondary fw-semibold small" style="font-size: 0.75rem;">Biaya BBM (Bulan Ini)</div>
                        <div class="fs-3 fw-bold text-dark mt-1">Rp {{ number_format($totalFuelThisMonth, 0, ',', '.') }}</div>
                    </div>
                    <div class="rounded-3 p-3 bg-warning bg-opacity-10 text-warning">
                        <i class="bi bi-fuel-pump fs-3"></i>
                    </div>
                </div>
            </div>
        </div>

        {{-- Biaya Servis Bulan Ini --}}
        <div class="col-12 col-md-4">
            <div class="card border-0 shadow-sm rounded-3 p-3 h-100">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-uppercase text-secondary fw-semibold small" style="font-size: 0.75rem;">Biaya Servis (Bulan Ini)</div>
                        <div class="fs-3 fw-bold text-dark mt-1">Rp {{ number_format($totalServiceThisMonth, 0, ',', '.') }}</div>
                    </div>
                    <div class="rounded-3 p-3 bg-success bg-opacity-10 text-success">
                        <i class="bi bi-tools fs-3"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ── Status Pemantauan Per Kendaraan ──────────────────────────── --}}
    <div class="card border-0 shadow-sm rounded-3 p-4">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
            <div>
                <h5 class="fw-bold text-dark mb-1">Kondisi & Jadwal Kendaraan</h5>
                <p class="text-secondary small mb-0">Pantau kilometer dan komponen yang memerlukan perhatian.</p>
            </div>
            <a href="{{ route('vehicles.create') }}" class="btn btn-primary d-inline-flex align-items-center gap-2">
                <i class="bi bi-plus-lg"></i>
                <span>Tambah Kendaraan</span>
            </a>
        </div>

        @if($vehicleStatuses->isEmpty())
            <div class="text-center py-5 border border-2 border-dashed rounded-3">
                <div class="d-inline-flex align-items-center justify-content-center bg-primary bg-opacity-10 text-primary rounded-circle mb-3" style="width: 54px; height: 54px;">
                    <i class="bi bi-truck fs-3"></i>
                </div>
                <h6 class="fw-bold text-dark mb-1">Belum ada kendaraan terdaftar</h6>
                <p class="text-secondary small mb-3">Mulai dengan menambahkan kendaraan pertama Anda untuk memantau odometer dan jadwal perawatan.</p>
                <a href="{{ route('vehicles.create') }}" class="btn btn-primary btn-sm">
                    Tambah Kendaraan Sekarang
                </a>
            </div>
        @else
            <div class="row g-4">
                @foreach ($vehicleStatuses as $item)
                    @php
                        $v = $item['vehicle'];
                        $statusBadge = match($item['overall_status']) {
                            'overdue'          => ['class' => 'bg-danger text-white', 'label' => 'Terlambat'],
                            'soon'             => ['class' => 'bg-warning text-dark', 'label' => 'Segera'],
                            'needs_inspection' => ['class' => 'bg-info text-dark', 'label' => 'Periksa Kondisi'],
                            default            => ['class' => 'bg-success text-white', 'label' => 'Aman'],
                        };
                    @endphp
                    <div class="col-12 col-md-6 col-xl-4">
                        <div class="card h-100 border rounded-3 p-3 shadow-none">
                            <div class="d-flex align-items-start justify-content-between mb-3">
                                <div>
                                    <span class="text-uppercase text-secondary fw-bold" style="font-size: 0.7rem;">
                                        {{ $v->vehicle_type === 'motorcycle' ? 'Motor' : ($v->vehicle_type === 'car' ? 'Mobil' : 'Lainnya') }}
                                    </span>
                                    <h6 class="fw-bold text-dark mb-1">
                                        <a href="{{ route('vehicles.show', $v) }}" class="text-decoration-none text-dark hover-primary">
                                            {{ $v->name }}
                                        </a>
                                    </h6>
                                    @if($v->license_plate)
                                        <span class="badge bg-light text-secondary font-monospace border">
                                            {{ $v->license_plate }}
                                        </span>
                                    @endif
                                </div>
                                <span class="badge {{ $statusBadge['class'] }} rounded-pill px-2.5 py-1.5 small">
                                    {{ $statusBadge['label'] }}
                                </span>
                            </div>

                            {{-- Odometer --}}
                            <div class="d-flex align-items-center justify-content-between bg-light rounded-2 px-3 py-2 small mb-3">
                                <span class="text-secondary">Odometer Terkini:</span>
                                <span class="fw-bold font-monospace text-dark">{{ number_format($item['odometer'], 1, ',', '.') }} KM</span>
                            </div>

                            {{-- Ringkasan Item Jadwal --}}
                            <div class="mb-3">
                                <div class="text-uppercase text-secondary fw-bold mb-2" style="font-size: 0.7rem;">Jadwal Perawatan</div>
                                @if(count($item['schedule_detail']) > 0)
                                    <div class="list-group list-group-flush small" style="max-height: 140px; overflow-y: auto;">
                                        @foreach ($item['schedule_detail'] as $detail)
                                            @php
                                                $dotBg = match($detail['status']) {
                                                    'overdue'          => 'bg-danger',
                                                    'soon'             => 'bg-warning',
                                                    'needs_inspection' => 'bg-info',
                                                    default            => 'bg-success',
                                                };
                                            @endphp
                                            <div class="list-group-item px-0 py-1.5 d-flex align-items-center justify-content-between border-0">
                                                <div class="d-flex align-items-center gap-2 text-truncate pe-2">
                                                    <span class="rounded-circle {{ $dotBg }}" style="width: 8px; height: 8px; display: inline-block;"></span>
                                                    <span class="text-truncate">{{ $detail['name'] }}</span>
                                                </div>
                                                <span class="text-secondary small fw-medium flex-shrink-0">
                                                    {{ match($detail['status']) {
                                                        'overdue' => 'Lewat',
                                                        'soon' => 'Mendekati',
                                                        'needs_inspection' => 'Inspeksi',
                                                        default => 'Aman'
                                                    } }}
                                                </span>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <p class="text-muted small fst-italic mb-0">Belum ada jadwal yang dikonfigurasi.</p>
                                @endif
                            </div>

                            {{-- Tombol Aksi Cepat --}}
                            <div class="mt-auto pt-3 border-top d-flex align-items-center justify-content-between small">
                                <a href="{{ route('schedules.index', $v) }}" class="text-decoration-none fw-semibold">
                                    Atur Jadwal &rarr;
                                </a>
                                <div class="d-flex gap-1">
                                    <a href="{{ route('fuel-transactions.create', ['vehicle_id' => $v->id]) }}" class="btn btn-sm btn-outline-primary py-0 px-2" style="font-size: 0.75rem;">
                                        + BBM
                                    </a>
                                    <a href="{{ route('maintenance-records.create', ['vehicle_id' => $v->id]) }}" class="btn btn-sm btn-outline-success py-0 px-2" style="font-size: 0.75rem;">
                                        + Servis
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

</div>
@endsection
