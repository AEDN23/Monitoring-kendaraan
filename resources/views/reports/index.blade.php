@extends('layouts.app')

@section('title', 'Laporan Biaya & Pengeluaran')
@section('header', 'Laporan Biaya & Pengeluaran')

@section('content')
<div class="container-fluid p-0">

    {{-- ── Filter Form ─────────────────────────────────────────── --}}
    <div class="card border-0 shadow-sm rounded-3 p-4 mb-4">
        <form method="GET" action="{{ route('reports.index') }}" class="row g-3 align-items-end">
            <div class="col-12 col-sm-6 col-lg-3">
                <label for="vehicle_id" class="form-label small fw-semibold text-secondary">Kendaraan</label>
                <select name="vehicle_id" id="vehicle_id" class="form-select form-select-sm">
                    <option value="all" {{ ($vehicleId == 'all') ? 'selected' : '' }}>Semua Kendaraan</option>
                    @foreach ($vehicles as $v)
                        <option value="{{ $v->id }}" {{ ($vehicleId == $v->id) ? 'selected' : '' }}>
                            {{ $v->name }} {{ $v->license_plate ? "({$v->license_plate})" : '' }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-12 col-sm-6 col-lg-3">
                <label for="month" class="form-label small fw-semibold text-secondary">Bulan</label>
                @php
                    $months = [
                        1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                        5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                        9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
                    ];
                @endphp
                <select name="month" id="month" class="form-select form-select-sm">
                    @foreach ($months as $num => $name)
                        <option value="{{ $num }}" {{ $month == $num ? 'selected' : '' }}>
                            {{ $name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-12 col-sm-6 col-lg-3">
                <label for="year" class="form-label small fw-semibold text-secondary">Tahun</label>
                <select name="year" id="year" class="form-select form-select-sm">
                    @for ($y = date('Y'); $y >= date('Y') - 5; $y--)
                        <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>
                            {{ $y }}
                        </option>
                    @endfor
                </select>
            </div>

            <div class="col-12 col-sm-6 col-lg-3">
                <button type="submit" class="btn btn-primary btn-sm w-100">
                    <i class="bi bi-funnel me-1"></i> Terapkan Filter
                </button>
            </div>
        </form>
    </div>

    {{-- ── Kartu Ringkasan Periode ─────────────────────────────── --}}
    <div class="row g-3 mb-4">
        {{-- Grand Total --}}
        <div class="col-12 col-sm-6 col-lg-3">
            <div class="card border-0 shadow-sm rounded-3 p-3 text-white h-100" style="background-color: var(--sidebar-bg);">
                <div class="text-uppercase small text-white-50" style="font-size: 0.75rem;">Total Pengeluaran</div>
                <div class="fs-4 fw-bold font-monospace mt-1">Rp {{ number_format($grandTotal, 0, ',', '.') }}</div>
                <div class="text-white-50 small mt-1" style="font-size: 0.75rem;">{{ $months[$month] }} {{ $year }}</div>
            </div>
        </div>

        {{-- Biaya BBM --}}
        <div class="col-12 col-sm-6 col-lg-3">
            <div class="card border-0 shadow-sm rounded-3 p-3 h-100">
                <div class="text-uppercase small text-secondary fw-semibold" style="font-size: 0.75rem;">Total Biaya BBM</div>
                <div class="fs-4 fw-bold text-warning font-monospace mt-1">Rp {{ number_format($fuelStats['total_amount'], 0, ',', '.') }}</div>
                <div class="text-muted small mt-1" style="font-size: 0.75rem;">{{ number_format($fuelStats['total_liter'], 2, ',', '.') }} L &bull; {{ $fuelStats['total_fill'] }}x isi</div>
            </div>
        </div>

        {{-- Biaya Suku Cadang --}}
        <div class="col-12 col-sm-6 col-lg-3">
            <div class="card border-0 shadow-sm rounded-3 p-3 h-100">
                <div class="text-uppercase small text-secondary fw-semibold" style="font-size: 0.75rem;">Suku Cadang Servis</div>
                <div class="fs-4 fw-bold text-dark font-monospace mt-1">Rp {{ number_format($serviceStats['total_parts'], 0, ',', '.') }}</div>
                <div class="text-muted small mt-1" style="font-size: 0.75rem;">Biaya suku cadang/barang</div>
            </div>
        </div>

        {{-- Biaya Jasa Servis --}}
        <div class="col-12 col-sm-6 col-lg-3">
            <div class="card border-0 shadow-sm rounded-3 p-3 h-100">
                <div class="text-uppercase small text-secondary fw-semibold" style="font-size: 0.75rem;">Biaya Jasa Bengkel</div>
                <div class="fs-4 fw-bold text-success font-monospace mt-1">Rp {{ number_format($serviceStats['total_labor'], 0, ',', '.') }}</div>
                <div class="text-muted small mt-1" style="font-size: 0.75rem;">{{ $serviceStats['total_visits'] }} kunjungan servis</div>
            </div>
        </div>
    </div>

    {{-- ── 2 Kolom Rincian Transaksi ───────────────────────────── --}}
    <div class="row g-4">
        {{-- Rincian BBM --}}
        <div class="col-12 col-lg-6">
            <div class="card border-0 shadow-sm rounded-3 p-4 h-100">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <h6 class="fw-bold text-dark mb-0">Transaksi BBM Periode Ini</h6>
                    <span class="badge bg-light text-secondary border">{{ $fuelTransactions->count() }} transaksi</span>
                </div>

                @if($fuelTransactions->isEmpty())
                    <p class="text-secondary small fst-italic py-4 text-center mb-0">Tidak ada transaksi BBM pada periode ini.</p>
                @else
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0 small">
                            <thead class="table-light">
                                <tr class="text-secondary small text-uppercase">
                                    <th class="ps-2 py-2">Tgl</th>
                                    <th class="py-2">Kendaraan</th>
                                    <th class="py-2">BBM</th>
                                    <th class="py-2 text-end">Liter</th>
                                    <th class="pe-2 py-2 text-end">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($fuelTransactions as $ft)
                                    <tr>
                                        <td class="ps-2 py-2 fw-semibold text-dark">{{ $ft->transaction_at->format('d/m') }}</td>
                                        <td class="py-2 text-secondary">{{ $ft->vehicle->name ?? '-' }}</td>
                                        <td class="py-2">
                                            <span class="badge bg-warning bg-opacity-10 text-dark border border-warning border-opacity-25">
                                                {{ $ft->fuelType->name ?? '-' }}
                                            </span>
                                        </td>
                                        <td class="py-2 text-end font-monospace text-secondary">{{ number_format($ft->volume_liter, 2, ',', '.') }} L</td>
                                        <td class="pe-2 py-2 text-end font-monospace fw-bold text-dark">Rp {{ number_format($ft->total_amount, 0, ',', '.') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>

        {{-- Rincian Servis --}}
        <div class="col-12 col-lg-6">
            <div class="card border-0 shadow-sm rounded-3 p-4 h-100">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <h6 class="fw-bold text-dark mb-0">Transaksi Servis Periode Ini</h6>
                    <span class="badge bg-light text-secondary border">{{ $maintenanceRecords->count() }} kunjungan</span>
                </div>

                @if($maintenanceRecords->isEmpty())
                    <p class="text-secondary small fst-italic py-4 text-center mb-0">Tidak ada servis pada periode ini.</p>
                @else
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0 small">
                            <thead class="table-light">
                                <tr class="text-secondary small text-uppercase">
                                    <th class="ps-2 py-2">Tgl</th>
                                    <th class="py-2">Kendaraan</th>
                                    <th class="py-2">Bengkel</th>
                                    <th class="py-2 text-end">Part + Jasa</th>
                                    <th class="pe-2 py-2 text-end">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($maintenanceRecords as $mr)
                                    <tr>
                                        <td class="ps-2 py-2 fw-semibold text-dark">{{ $mr->service_at->format('d/m') }}</td>
                                        <td class="py-2 text-secondary">{{ $mr->vehicle->name ?? '-' }}</td>
                                        <td class="py-2 text-secondary">{{ $mr->workshop_name ?? '-' }}</td>
                                        <td class="py-2 text-end font-monospace text-muted" style="font-size: 0.75rem;">
                                            {{ number_format($mr->total_parts_cost, 0, ',', '.') }} + {{ number_format($mr->total_labor_cost, 0, ',', '.') }}
                                        </td>
                                        <td class="pe-2 py-2 text-end font-monospace fw-bold">
                                            <a href="{{ route('maintenance-records.show', $mr) }}" class="text-decoration-none text-primary">
                                                Rp {{ number_format($mr->total_cost, 0, ',', '.') }}
                                            </a>
                                        </td>
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
@endsection
