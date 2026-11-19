@extends('layouts.app')

@section('title', $vehicle->name)
@section('header', 'Detail Kendaraan')

@section('content')
<div class="container-fluid p-0">

    {{-- ── Header & Info Kendaraan ─────────────────────────── --}}
    <div class="card border-0 shadow-sm rounded-3 p-4 mb-4">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
            <div class="d-flex align-items-center gap-3">
                <div class="d-flex align-items-center justify-content-center rounded-3 bg-primary bg-opacity-10 text-primary fw-bold fs-3" style="width: 56px; height: 56px;">
                    {{ strtoupper(substr($vehicle->name, 0, 1)) }}
                </div>
                <div>
                    <div class="d-flex align-items-center gap-2">
                        <h4 class="fw-bold text-dark mb-0">{{ $vehicle->name }}</h4>
                        <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25">
                            {{ $vehicle->vehicle_type === 'motorcycle' ? 'Motor' : ($vehicle->vehicle_type === 'car' ? 'Mobil' : 'Lainnya') }}
                        </span>
                        @if($vehicle->is_active)
                            <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25">Aktif</span>
                        @else
                            <span class="badge bg-secondary bg-opacity-10 text-secondary">Nonaktif</span>
                        @endif
                    </div>
                    <div class="text-secondary small mt-1">
                        {{ $vehicle->brand ?? '-' }} {{ $vehicle->model ?? '' }}
                        @if($vehicle->production_year) &bull; Thn {{ $vehicle->production_year }} @endif
                        @if($vehicle->license_plate) &bull; <span class="font-monospace text-dark fw-semibold">{{ $vehicle->license_plate }}</span> @endif
                    </div>
                </div>
            </div>

            <div class="d-flex align-items-center gap-2">
                <a href="{{ route('vehicles.edit', $vehicle) }}" class="btn btn-sm btn-outline-secondary">
                    <i class="bi bi-pencil me-1"></i> Edit Kendaraan
                </a>
                <a href="{{ route('schedules.index', $vehicle) }}" class="btn btn-sm btn-primary">
                    <i class="bi bi-calendar-check me-1"></i> Kelola Jadwal
                </a>
            </div>
        </div>

        {{-- Row Detail Tambahan --}}
        <div class="row g-3 mt-3 pt-3 border-top small">
            <div class="col-6 col-md-3">
                <div class="text-secondary fw-semibold">Odometer Awal</div>
                <div class="fs-6 fw-bold text-dark font-monospace mt-1">{{ number_format($vehicle->initial_odometer_km, 1, ',', '.') }} KM</div>
                <div class="text-muted" style="font-size: 0.75rem;">{{ $vehicle->initial_odometer_date ? $vehicle->initial_odometer_date->format('d/m/Y') : '-' }}</div>
            </div>
            <div class="col-6 col-md-3">
                <div class="text-secondary fw-semibold">Odometer Terkini</div>
                <div class="fs-6 fw-bold text-primary font-monospace mt-1">{{ number_format($currentOdometer, 1, ',', '.') }} KM</div>
                <div class="text-muted" style="font-size: 0.75rem;">Total jalan: {{ number_format($currentOdometer - $vehicle->initial_odometer_km, 1, ',', '.') }} KM</div>
            </div>
            <div class="col-6 col-md-3">
                <div class="text-secondary fw-semibold">BBM Bulan Ini</div>
                <div class="fs-6 fw-bold text-warning font-monospace mt-1">Rp {{ number_format($fuelThisMonth->total_cost ?? 0, 0, ',', '.') }}</div>
                <div class="text-muted" style="font-size: 0.75rem;">{{ number_format($fuelThisMonth->total_liter ?? 0, 2, ',', '.') }} Liter</div>
            </div>
            <div class="col-6 col-md-3">
                <div class="text-secondary fw-semibold">Servis Bulan Ini</div>
                <div class="fs-6 fw-bold text-success font-monospace mt-1">Rp {{ number_format($serviceThisMonth->total_cost ?? 0, 0, ',', '.') }}</div>
                <div class="text-muted" style="font-size: 0.75rem;">{{ $serviceThisMonth->total_count ?? 0 }} kunjungan</div>
            </div>
        </div>

        @if($vehicle->notes)
            <div class="mt-3 alert alert-light py-2 px-3 small mb-0 border">
                <span class="fw-semibold text-secondary">Catatan:</span> {{ $vehicle->notes }}
            </div>
        @endif
    </div>

    {{-- ── Jadwal Perawatan Kendaraan Ini ───────────────────── --}}
    <div class="card border-0 shadow-sm rounded-3 p-4 mb-4">
        <div class="d-flex align-items-center justify-content-between mb-3">
            <h5 class="fw-bold text-dark mb-0">Jadwal Perawatan</h5>
            <a href="{{ route('schedules.create', $vehicle) }}" class="btn btn-sm btn-outline-primary">
                <i class="bi bi-plus-lg me-1"></i> Tambah Jadwal
            </a>
        </div>

        @if($schedules->isEmpty())
            <div class="p-4 text-center border border-2 border-dashed rounded-3">
                <p class="text-secondary small mb-2">Belum ada komponen perawatan yang dipantau untuk kendaraan ini.</p>
                <a href="{{ route('schedules.create', $vehicle) }}" class="btn btn-sm btn-primary">
                    Atur jadwal sekarang
                </a>
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr class="small text-secondary text-uppercase">
                            <th class="ps-3 py-2.5">Komponen</th>
                            <th class="py-2.5">Interval</th>
                            <th class="py-2.5">Servis Terakhir</th>
                            <th class="py-2.5">Target Berikutnya</th>
                            <th class="py-2.5">Status</th>
                            <th class="pe-3 py-2.5 text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($schedules as $sched)
                            @php
                                $badge = match($sched->computed_status) {
                                    'overdue'          => 'bg-danger text-white',
                                    'soon'             => 'bg-warning text-dark',
                                    'needs_inspection' => 'bg-info text-dark',
                                    default            => 'bg-success text-white',
                                };
                                $label = match($sched->computed_status) {
                                    'overdue'          => 'Terlambat',
                                    'soon'             => 'Segera',
                                    'needs_inspection' => 'Periksa Fisik',
                                    default            => 'Aman',
                                };
                            @endphp
                            <tr>
                                <td class="ps-3 py-3">
                                    <div class="fw-semibold text-dark">{{ $sched->maintenanceItem->name ?? '-' }}</div>
                                    <div class="text-secondary small">{{ $sched->maintenanceItem->category ?? '' }}</div>
                                </td>
                                <td class="py-3 text-secondary small">
                                    @if($sched->interval_km) {{ number_format($sched->interval_km, 0, ',', '.') }} KM @endif
                                    @if($sched->interval_km && $sched->interval_days) / @endif
                                    @if($sched->interval_days) {{ $sched->interval_days }} hari @endif
                                    @if(!$sched->interval_km && !$sched->interval_days) Manual @endif
                                </td>
                                <td class="py-3 text-secondary small">
                                    @if($sched->last_service_odometer_km !== null)
                                        <div class="font-monospace text-dark">{{ number_format($sched->last_service_odometer_km, 1, ',', '.') }} KM</div>
                                    @endif
                                    @if($sched->last_service_at)
                                        <div class="text-muted" style="font-size: 0.75rem;">{{ $sched->last_service_at->format('d/m/Y') }}</div>
                                    @elseif($sched->last_service_odometer_km === null)
                                        -
                                    @endif
                                </td>
                                <td class="py-3 small font-monospace">
                                    @if($sched->computed_next_km)
                                        <div class="fw-semibold text-dark">{{ number_format($sched->computed_next_km, 1, ',', '.') }} KM</div>
                                    @endif
                                    @if($sched->computed_next_date)
                                        <div class="text-muted font-sans" style="font-size: 0.75rem;">
                                            {{ $sched->computed_next_date->format('d/m/Y') }}
                                        </div>
                                    @endif
                                    @if(!$sched->computed_next_km && !$sched->computed_next_date)
                                        -
                                    @endif
                                </td>
                                <td class="py-3">
                                    <span class="badge {{ $badge }} rounded-pill px-2.5 py-1 small">
                                        {{ $label }}
                                    </span>
                                </td>
                                <td class="pe-3 py-3 text-end">
                                    <a href="{{ route('schedules.edit', [$vehicle, $sched]) }}" class="btn btn-sm btn-light py-1 px-2 text-primary">
                                        Edit
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    {{-- ── 2 Kolom: Riwayat BBM Terakhir & Servis Terakhir ─── --}}
    <div class="row g-4">
        {{-- 5 BBM Terakhir --}}
        <div class="col-12 col-lg-6">
            <div class="card border-0 shadow-sm rounded-3 p-4 h-100">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <h6 class="fw-bold text-dark mb-0">5 Pengisian BBM Terakhir</h6>
                    <a href="{{ route('fuel-transactions.create', ['vehicle_id' => $vehicle->id]) }}" class="btn btn-sm btn-outline-primary py-1 px-2" style="font-size: 0.75rem;">
                        + Catat BBM
                    </a>
                </div>

                @if($lastFuelTransactions->isEmpty())
                    <p class="text-secondary small fst-italic mb-0">Belum ada riwayat pengisian BBM.</p>
                @else
                    <div class="list-group list-group-flush small">
                        @foreach ($lastFuelTransactions as $ft)
                            <div class="list-group-item px-0 py-2.5 d-flex align-items-center justify-content-between border-0 border-bottom">
                                <div>
                                    <div class="fw-semibold text-dark">{{ $ft->fuelType->name ?? 'BBM' }} &bull; {{ number_format($ft->volume_liter, 2, ',', '.') }} L</div>
                                    <div class="text-muted" style="font-size: 0.75rem;">{{ $ft->transaction_at->format('d/m/Y H:i') }} &bull; Odo: {{ number_format($ft->odometer_km, 1, ',', '.') }} KM</div>
                                </div>
                                <div class="text-end">
                                    <div class="fw-bold font-monospace text-dark">Rp {{ number_format($ft->total_amount, 0, ',', '.') }}</div>
                                    <div class="text-muted font-monospace" style="font-size: 0.7rem;">@ Rp {{ number_format($ft->price_per_liter, 0, ',', '.') }}/L</div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        {{-- 5 Servis Terakhir --}}
        <div class="col-12 col-lg-6">
            <div class="card border-0 shadow-sm rounded-3 p-4 h-100">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <h6 class="fw-bold text-dark mb-0">5 Servis Terakhir</h6>
                    <a href="{{ route('maintenance-records.create', ['vehicle_id' => $vehicle->id]) }}" class="btn btn-sm btn-outline-success py-1 px-2" style="font-size: 0.75rem;">
                        + Catat Servis
                    </a>
                </div>

                @if($lastMaintenanceRecords->isEmpty())
                    <p class="text-secondary small fst-italic mb-0">Belum ada riwayat servis tercatat.</p>
                @else
                    <div class="list-group list-group-flush small">
                        @foreach ($lastMaintenanceRecords as $mr)
                            <div class="list-group-item px-0 py-2.5 d-flex align-items-center justify-content-between border-0 border-bottom">
                                <div>
                                    <div class="fw-semibold text-dark">{{ $mr->workshop_name ?? 'Bengkel' }}</div>
                                    <div class="text-muted" style="font-size: 0.75rem;">
                                        {{ $mr->service_at->format('d/m/Y') }} &bull; Odo: {{ number_format($mr->odometer_km, 1, ',', '.') }} KM
                                        @if($mr->items->isNotEmpty())
                                            &bull; {{ $mr->items->pluck('item_name')->implode(', ') }}
                                        @endif
                                    </div>
                                </div>
                                <div class="text-end">
                                    <div class="fw-bold font-monospace text-dark">Rp {{ number_format($mr->total_cost, 0, ',', '.') }}</div>
                                    <a href="{{ route('maintenance-records.show', $mr) }}" class="text-decoration-none small text-primary" style="font-size: 0.75rem;">Detail &rarr;</a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>

</div>
@endsection
