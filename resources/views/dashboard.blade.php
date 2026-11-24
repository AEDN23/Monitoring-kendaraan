@extends('layouts.app')

@section('title', 'Dashboard')
@section('header', 'Dashboard Monitoring Kendaraan')

@section('content')
<div class="container-fluid p-0">

    @if ($activeVehicle)
        @php
            $isMotor = ($activeVehicle->vehicle_type === 'motorcycle');
            $statusBadge = match($worstStatus) {
                'overdue'          => ['class' => 'bg-danger text-white', 'label' => 'Perlu Servis Segera'],
                'soon'             => ['class' => 'bg-warning text-dark', 'label' => 'Mendekati Waktu Servis'],
                'needs_inspection' => ['class' => 'bg-info text-dark', 'label' => 'Perlu Inspeksi Kondisi'],
                default            => ['class' => 'bg-success text-white', 'label' => 'Kondisi Aman'],
            };
            $currentOdo = $activeVehicle->latestOdometerKm() ?? 0;
            $grandTotalThisMonth = $totalFuelThisMonth + $totalServiceThisMonth;
        @endphp

        {{-- ── Banner Kendaraan Aktif ───────────────────────────────────── --}}
        <div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden" style="background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%); color: #fff;">
            <div class="card-body p-4">
                <div class="row align-items-center g-3">
                    <div class="col-12 col-md-auto text-center text-md-start">
                        <div class="rounded-4 bg-primary bg-opacity-25 text-primary p-3 d-inline-flex align-items-center justify-content-center" style="width: 80px; height: 80px; font-size: 2.5rem;">
                            <i class="bi {{ $isMotor ? 'bi-bicycle' : 'bi-car-front-fill' }}"></i>
                        </div>
                    </div>
                    <div class="col-12 col-md text-center text-md-start">
                        <div class="d-flex flex-wrap align-items-center justify-content-center justify-content-md-start gap-2 mb-1">
                            <h3 class="fw-bold text-white mb-0">{{ $activeVehicle->name }}</h3>
                            @if($activeVehicle->license_plate)
                                <span class="badge bg-black text-white font-monospace border border-secondary px-2.5 py-1" style="font-size: 0.85rem; letter-spacing: 1px;">
                                    {{ $activeVehicle->license_plate }}
                                </span>
                            @endif
                            <span class="badge {{ $statusBadge['class'] }} rounded-pill px-3 py-1">
                                {{ $statusBadge['label'] }}
                            </span>
                        </div>
                        <p class="text-secondary small mb-2">
                            {{ ucfirst($activeVehicle->brand ?? '-') }} {{ $activeVehicle->model ?? '' }} {{ $activeVehicle->production_year ? "({$activeVehicle->production_year})" : '' }}
                            &bull; Tipe: <span class="text-white">{{ $isMotor ? 'Sepeda Motor' : ($activeVehicle->vehicle_type === 'car' ? 'Mobil' : 'Lainnya') }}</span>
                        </p>
                        <div class="d-inline-flex align-items-center gap-2 bg-dark bg-opacity-50 border border-secondary border-opacity-50 rounded-pill px-3 py-1 text-light small font-monospace">
                            <i class="bi bi-speedometer2 text-primary"></i>
                            <span>Odometer Terkini: <strong>{{ number_format($currentOdo, 0, ',', '.') }} KM</strong></span>
                        </div>
                    </div>
                    <div class="col-12 col-md-auto text-center text-md-end">
                        <div class="d-flex flex-wrap justify-content-center justify-content-md-end gap-2">
                            <a href="{{ route('fuel-transactions.create', ['vehicle_id' => $activeVehicle->id]) }}" class="btn btn-primary d-inline-flex align-items-center gap-1 shadow-sm">
                                <i class="bi bi-fuel-pump"></i> + Catat BBM
                            </a>
                            <a href="{{ route('maintenance-records.create', ['vehicle_id' => $activeVehicle->id]) }}" class="btn btn-success d-inline-flex align-items-center gap-1 shadow-sm">
                                <i class="bi bi-tools"></i> + Catat Servis
                            </a>
                            <a href="{{ route('vehicle.select') }}" class="btn btn-outline-light d-inline-flex align-items-center gap-1" title="Ganti ke kendaraan lain">
                                <i class="bi bi-arrow-left-right"></i> Ganti
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ── Kartu Ringkasan Statistik Kendaraan Ini ───────────────────── --}}
        <div class="row g-3 mb-4">
            {{-- Biaya BBM --}}
            <div class="col-12 col-md-4">
                <div class="card border-0 shadow-sm rounded-3 p-3 h-100">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <div class="text-uppercase text-secondary fw-semibold small" style="font-size: 0.75rem;">Biaya BBM (Bulan Ini)</div>
                            <div class="fs-3 fw-bold text-dark mt-1">Rp {{ number_format($totalFuelThisMonth, 0, ',', '.') }}</div>
                            <div class="text-muted small mt-1">Khusus {{ $activeVehicle->name }}</div>
                        </div>
                        <div class="rounded-3 p-3 bg-warning bg-opacity-10 text-warning">
                            <i class="bi bi-fuel-pump fs-3"></i>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Biaya Servis --}}
            <div class="col-12 col-md-4">
                <div class="card border-0 shadow-sm rounded-3 p-3 h-100">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <div class="text-uppercase text-secondary fw-semibold small" style="font-size: 0.75rem;">Biaya Servis (Bulan Ini)</div>
                            <div class="fs-3 fw-bold text-dark mt-1">Rp {{ number_format($totalServiceThisMonth, 0, ',', '.') }}</div>
                            <div class="text-muted small mt-1">Perawatan & Sparepart</div>
                        </div>
                        <div class="rounded-3 p-3 bg-success bg-opacity-10 text-success">
                            <i class="bi bi-tools fs-3"></i>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Total Biaya Operasional --}}
            <div class="col-12 col-md-4">
                <div class="card border-0 shadow-sm rounded-3 p-3 h-100">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <div class="text-uppercase text-secondary fw-semibold small" style="font-size: 0.75rem;">Total Pengeluaran (Bulan Ini)</div>
                            <div class="fs-3 fw-bold text-primary mt-1">Rp {{ number_format($grandTotalThisMonth, 0, ',', '.') }}</div>
                            <div class="text-muted small mt-1">BBM + Biaya Servis</div>
                        </div>
                        <div class="rounded-3 p-3 bg-primary bg-opacity-10 text-primary">
                            <i class="bi bi-cash-stack fs-3"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ── Jadwal Perawatan Kendaraan Ini ───────────────────────────── --}}
        <div class="card border-0 shadow-sm rounded-3 mb-4">
            <div class="card-header bg-white border-0 pt-4 px-4 pb-0 d-flex flex-wrap align-items-center justify-content-between gap-2">
                <div>
                    <h5 class="fw-bold text-dark mb-1">Status Komponen & Jadwal Servis</h5>
                    <p class="text-secondary small mb-0">Pantau kilometer batas servis dan ganti oli untuk {{ $activeVehicle->name }}.</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('schedules.index', $activeVehicle) }}" class="btn btn-sm btn-outline-primary">
                        Kelola Jadwal &rarr;
                    </a>
                </div>
            </div>
            <div class="card-body p-4">
                @if ($schedules->isEmpty())
                    <div class="text-center py-4 bg-light rounded-3">
                        <i class="bi bi-calendar-check text-secondary fs-2 mb-2 d-block"></i>
                        <p class="text-muted small mb-2">Belum ada jadwal perawatan yang dikonfigurasi untuk kendaraan ini.</p>
                        <a href="{{ route('schedules.create', $activeVehicle) }}" class="btn btn-sm btn-primary">
                            + Tambah Jadwal Pertama
                        </a>
                    </div>
                @else
                    <div class="row g-3">
                        @foreach ($schedules as $s)
                            @php
                                $badgeClass = match($s['status']) {
                                    'overdue'          => 'bg-danger text-white',
                                    'soon'             => 'bg-warning text-dark',
                                    'needs_inspection' => 'bg-info text-dark',
                                    default            => 'bg-success text-white',
                                };
                                $statusLabel = match($s['status']) {
                                    'overdue'          => 'Lewat Batas',
                                    'soon'             => 'Mendekati Batas',
                                    'needs_inspection' => 'Periksa Kondisi',
                                    default            => 'Aman',
                                };
                            @endphp
                            <div class="col-12 col-md-6 col-lg-4">
                                <div class="p-3 rounded-3 border bg-light h-100 d-flex flex-column justify-content-between">
                                    <div class="d-flex align-items-start justify-content-between mb-2">
                                        <div class="fw-bold text-dark">{{ $s['name'] }}</div>
                                        <span class="badge {{ $badgeClass }} small rounded-pill px-2.5 py-1">
                                            {{ $statusLabel }}
                                        </span>
                                    </div>
                                    <div class="small text-secondary mb-2">
                                        @if($s['next_due_km'])
                                            <div>Target Servis: <span class="fw-bold text-dark font-monospace">{{ number_format($s['next_due_km'], 0, ',', '.') }} KM</span></div>
                                        @endif
                                        @if($s['next_date'])
                                            <div>Estimasi Tgl: <span class="fw-bold text-dark">{{ $s['next_date']->format('d M Y') }}</span></div>
                                        @endif
                                    </div>
                                    <div class="pt-2 border-top border-secondary border-opacity-10 d-flex justify-content-end">
                                        <a href="{{ route('maintenance-records.create', ['vehicle_id' => $activeVehicle->id]) }}" class="btn btn-sm btn-outline-success py-0 px-2" style="font-size: 0.75rem;">
                                            Catat Ganti &rarr;
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        {{-- ── Dua Kolom: Transaksi BBM & Servis Terkini ─────────────────── --}}
        <div class="row g-4">
            {{-- 5 BBM Terakhir --}}
            <div class="col-12 col-lg-6">
                <div class="card border-0 shadow-sm rounded-3 h-100">
                    <div class="card-header bg-white border-0 pt-4 px-4 pb-0 d-flex align-items-center justify-content-between">
                        <h6 class="fw-bold text-dark mb-0">Pengisian BBM Terakhir</h6>
                        <a href="{{ route('fuel-transactions.index') }}" class="small text-decoration-none">Semua &rarr;</a>
                    </div>
                    <div class="card-body p-4">
                        @if ($recentFuels->isEmpty())
                            <p class="text-muted small fst-italic mb-0">Belum ada catatan pengisian BBM.</p>
                        @else
                            <div class="table-responsive">
                                <table class="table table-sm table-borderless align-middle mb-0">
                                    <thead class="text-secondary small border-bottom">
                                        <tr>
                                            <th>Tanggal</th>
                                            <th>BBM</th>
                                            <th>Odo</th>
                                            <th class="text-end">Total</th>
                                        </tr>
                                    </thead>
                                    <tbody class="small">
                                        @foreach ($recentFuels as $f)
                                            <tr class="border-bottom border-light">
                                                <td class="text-nowrap">{{ $f->transaction_at->format('d/m/Y') }}</td>
                                                <td><span class="badge bg-light text-dark border">{{ $f->fuelType->name ?? '-' }}</span></td>
                                                <td class="font-monospace">{{ number_format($f->odometer_km, 0, ',', '.') }}</td>
                                                <td class="text-end fw-bold font-monospace">Rp {{ number_format($f->total_amount, 0, ',', '.') }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- 5 Servis Terakhir --}}
            <div class="col-12 col-lg-6">
                <div class="card border-0 shadow-sm rounded-3 h-100">
                    <div class="card-header bg-white border-0 pt-4 px-4 pb-0 d-flex align-items-center justify-content-between">
                        <h6 class="fw-bold text-dark mb-0">Riwayat Servis Terakhir</h6>
                        <a href="{{ route('maintenance-records.index') }}" class="small text-decoration-none">Semua &rarr;</a>
                    </div>
                    <div class="card-body p-4">
                        @if ($recentServices->isEmpty())
                            <p class="text-muted small fst-italic mb-0">Belum ada catatan riwayat servis.</p>
                        @else
                            <div class="table-responsive">
                                <table class="table table-sm table-borderless align-middle mb-0">
                                    <thead class="text-secondary small border-bottom">
                                        <tr>
                                            <th>Tanggal</th>
                                            <th>Bengkel</th>
                                            <th>Item Servis</th>
                                            <th class="text-end">Biaya</th>
                                        </tr>
                                    </thead>
                                    <tbody class="small">
                                        @foreach ($recentServices as $s)
                                            <tr class="border-bottom border-light">
                                                <td class="text-nowrap">{{ $s->service_at->format('d/m/Y') }}</td>
                                                <td class="text-truncate" style="max-width: 110px;">{{ $s->workshop_name ?? '-' }}</td>
                                                <td>
                                                    <span class="badge bg-secondary bg-opacity-10 text-dark">
                                                        {{ $s->items->count() }} Komponen
                                                    </span>
                                                </td>
                                                <td class="text-end fw-bold font-monospace">Rp {{ number_format($s->total_cost, 0, ',', '.') }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

    @else
        {{-- Jika tidak ada kendaraan sama sekali --}}
        <div class="text-center py-5 border border-2 border-dashed rounded-3 bg-white">
            <div class="d-inline-flex align-items-center justify-content-center bg-primary bg-opacity-10 text-primary rounded-circle mb-3" style="width: 60px; height: 60px;">
                <i class="bi bi-truck fs-3"></i>
            </div>
            <h5 class="fw-bold text-dark mb-1">Belum ada kendaraan yang didaftarkan</h5>
            <p class="text-secondary small mb-3">Tambahkan motor atau mobil Anda terlebih dahulu untuk memulai pemantauan.</p>
            <a href="{{ route('vehicles.create') }}" class="btn btn-primary">
                + Tambah Kendaraan Pertama
            </a>
        </div>
    @endif

</div>
@endsection
