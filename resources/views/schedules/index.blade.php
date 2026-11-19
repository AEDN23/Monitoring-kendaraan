@extends('layouts.app')

@section('title', 'Jadwal Perawatan')
@section('header', 'Jadwal Perawatan Berkala')

@section('content')
<div class="container-fluid p-0">

    @if(!$vehicle)
        <div class="card border-0 shadow-sm rounded-3 p-5 text-center">
            <h5 class="fw-bold text-dark mb-1">Belum ada kendaraan terdaftar</h5>
            <p class="text-secondary small mb-3">Silakan tambahkan kendaraan terlebih dahulu untuk mengatur jadwal perawatan.</p>
            <div>
                <a href="{{ route('vehicles.create') }}" class="btn btn-primary btn-sm">
                    Tambah Kendaraan
                </a>
            </div>
        </div>
    @else
        {{-- ── Switcher Kendaraan & Tombol Tambah ─────────────────── --}}
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
            <div class="d-flex align-items-center gap-2">
                <label for="vehicle_select" class="small fw-bold text-secondary text-uppercase" style="font-size: 0.75rem;">Kendaraan:</label>
                <select id="vehicle_select" onchange="window.location.href='/schedules/' + this.value" class="form-select form-select-sm" style="min-width: 220px;">
                    @foreach ($vehicles as $v)
                        <option value="{{ $v->id }}" {{ $v->id === $vehicle->id ? 'selected' : '' }}>
                            {{ $v->name }} ({{ number_format($v->latestOdometerKm() ?? 0, 0, ',', '.') }} KM)
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="d-flex align-items-center gap-2">
                <a href="{{ route('maintenance-records.create', ['vehicle_id' => $vehicle->id]) }}" class="btn btn-sm btn-outline-success">
                    <i class="bi bi-tools me-1"></i> Catat Servis Selesai
                </a>
                <a href="{{ route('schedules.create', $vehicle) }}" class="btn btn-sm btn-primary">
                    <i class="bi bi-plus-lg me-1"></i> Tambah Jadwal Komponen
                </a>
            </div>
        </div>

        {{-- ── Info Banner Odometer ────────────────────────────────── --}}
        <div class="alert alert-primary bg-primary bg-opacity-10 border-0 rounded-3 p-3 mb-4 d-flex flex-wrap align-items-center justify-content-between gap-2">
            <div>
                <div class="text-uppercase small fw-bold text-primary" style="font-size: 0.75rem;">Status Acuan Odometer</div>
                <div class="small text-secondary">Status jadwal dihitung dari kilometer terkini dibandingkan target interval.</div>
            </div>
            <div class="text-end">
                <span class="small text-secondary">Odometer Terkini:</span>
                <span class="fw-bold font-monospace text-primary fs-6 ms-1">{{ number_format($currentKm, 1, ',', '.') }} KM</span>
            </div>
        </div>

        {{-- ── Daftar Jadwal Komponen ──────────────────────────────── --}}
        @if($schedules->isEmpty())
            <div class="card border-0 shadow-sm rounded-3 p-5 text-center border border-2 border-dashed">
                <h6 class="fw-bold text-dark mb-1">Belum ada komponen yang dijadwalkan</h6>
                <p class="text-secondary small mb-3">Tambahkan jadwal servis rutin seperti oli mesin, kampas rem, filter udara, dll.</p>
                <div>
                    <a href="{{ route('schedules.create', $vehicle) }}" class="btn btn-primary btn-sm">
                        Tambah Jadwal Sekarang
                    </a>
                </div>
            </div>
        @else
            <div class="row g-4">
                @foreach ($schedules as $sched)
                    @php
                        $badge = match($sched->computed_status) {
                            'overdue'          => ['class' => 'bg-danger text-white', 'label' => 'Terlambat!'],
                            'soon'             => ['class' => 'bg-warning text-dark', 'label' => 'Segera Servis'],
                            'needs_inspection' => ['class' => 'bg-info text-dark', 'label' => 'Perlu Inspeksi'],
                            default            => ['class' => 'bg-success text-white', 'label' => 'Kondisi Aman'],
                        };
                        $nextKm = $sched->nextDueKm();
                        $nextDate = $sched->nextDueDate();
                    @endphp
                    <div class="col-12 col-md-6 col-xl-4">
                        <div class="card border-0 shadow-sm rounded-3 p-4 h-100 d-flex flex-column justify-content-between">
                            <div>
                                <div class="d-flex align-items-start justify-content-between mb-3">
                                    <div>
                                        <span class="text-uppercase text-secondary fw-bold" style="font-size: 0.7rem;">
                                            {{ $sched->maintenanceItem->category ?? 'Komponen' }}
                                        </span>
                                        <h6 class="fw-bold text-dark mb-0">{{ $sched->maintenanceItem->name ?? '-' }}</h6>
                                    </div>
                                    <span class="badge {{ $badge['class'] }} rounded-pill px-2.5 py-1 small">
                                        {{ $badge['label'] }}
                                    </span>
                                </div>

                                {{-- Parameter Interval --}}
                                <div class="small text-secondary">
                                    <div class="d-flex justify-content-between py-1 border-bottom">
                                        <span>Interval:</span>
                                        <span class="fw-semibold text-dark">
                                            @if($sched->interval_km) {{ number_format($sched->interval_km, 0, ',', '.') }} KM @endif
                                            @if($sched->interval_km && $sched->interval_days) / @endif
                                            @if($sched->interval_days) {{ $sched->interval_days }} hari @endif
                                            @if(!$sched->interval_km && !$sched->interval_days) Cek Manual @endif
                                        </span>
                                    </div>

                                    <div class="d-flex justify-content-between py-1 border-bottom">
                                        <span>Servis Terakhir:</span>
                                        <span class="font-monospace text-dark text-end">
                                            @if($sched->last_service_odometer_km !== null)
                                                {{ number_format($sched->last_service_odometer_km, 1, ',', '.') }} KM
                                            @else
                                                Belum tercatat
                                            @endif
                                            @if($sched->last_service_at)
                                                <div class="text-muted font-sans" style="font-size: 0.7rem;">
                                                    {{ $sched->last_service_at->format('d/m/Y') }}
                                                </div>
                                            @endif
                                        </span>
                                    </div>

                                    <div class="d-flex justify-content-between py-2 fw-semibold">
                                        <span class="text-dark">Target Jatuh Tempo:</span>
                                        <span class="font-monospace text-primary text-end">
                                            @if($nextKm)
                                                {{ number_format($nextKm, 1, ',', '.') }} KM
                                            @endif
                                            @if($nextDate)
                                                <div class="text-muted font-sans small" style="font-size: 0.75rem;">
                                                    {{ $nextDate->format('d/m/Y') }}
                                                </div>
                                            @endif
                                            @if(!$nextKm && !$nextDate)
                                                Berdasarkan fisik
                                            @endif
                                        </span>
                                    </div>
                                </div>
                            </div>

                            {{-- Action Buttons --}}
                            <div class="mt-3 pt-3 border-top d-flex align-items-center justify-content-between small">
                                <div class="d-flex align-items-center gap-2">
                                    <a href="{{ route('schedules.edit', [$vehicle, $sched]) }}" class="btn btn-sm btn-light py-1 px-2 text-secondary">
                                        Edit
                                    </a>
                                    <form action="{{ route('schedules.destroy', [$vehicle, $sched]) }}" method="POST"
                                          onsubmit="return confirm('Hapus jadwal ini?');" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-light py-1 px-2 text-danger">
                                            Hapus
                                        </button>
                                    </form>
                                </div>

                                <a href="{{ route('maintenance-records.create', ['vehicle_id' => $vehicle->id, 'item_id' => $sched->maintenance_item_id]) }}" class="btn btn-sm btn-outline-primary py-1 px-2" style="font-size: 0.75rem;">
                                    + Servis
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    @endif

</div>
@endsection
