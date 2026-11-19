@extends('layouts.app')

@section('title', 'Edit Jadwal Perawatan')
@section('header', 'Edit Jadwal Perawatan')

@section('content')
<div class="container-fluid p-0" style="max-width: 760px;">
    <div class="d-flex align-items-center gap-3 mb-4">
        <a href="{{ route('schedules.index', $vehicle) }}" class="btn btn-outline-secondary btn-sm rounded-circle d-flex align-items-center justify-content-center" style="width: 34px; height: 34px;">
            <i class="bi bi-arrow-left"></i>
        </a>
        <div>
            <h4 class="fw-bold text-dark mb-0">Edit Jadwal Perawatan</h4>
            <div class="text-secondary small">{{ $vehicle->name }} &bull; {{ $schedule->maintenanceItem->name ?? '-' }}</div>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-3 p-4">
        <form action="{{ route('schedules.update', [$vehicle, $schedule]) }}" method="POST">
            @csrf
            @method('PUT')

            <input type="hidden" name="maintenance_item_id" value="{{ $schedule->maintenance_item_id }}">

            <div class="row g-3">
                <div class="col-12">
                    <label class="form-label small fw-semibold">Komponen</label>
                    <input type="text" disabled value="{{ $schedule->maintenanceItem->name ?? '-' }}" class="form-control bg-light">
                </div>

                {{-- Interval KM & Interval Hari --}}
                <div class="col-12 col-md-6">
                    <label for="interval_km" class="form-label small fw-semibold">Interval Kilometer</label>
                    <div class="input-group">
                        <input type="number" name="interval_km" id="interval_km"
                               value="{{ old('interval_km', $schedule->interval_km) }}"
                               min="100"
                               class="form-control font-monospace @error('interval_km') is-invalid @enderror">
                        <span class="input-group-text fw-semibold small text-secondary">KM</span>
                    </div>
                    @error('interval_km')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-12 col-md-6">
                    <label for="interval_days" class="form-label small fw-semibold">Interval Waktu (Hari)</label>
                    <div class="input-group">
                        <input type="number" name="interval_days" id="interval_days"
                               value="{{ old('interval_days', $schedule->interval_days) }}"
                               min="1"
                               class="form-control font-monospace @error('interval_days') is-invalid @enderror">
                        <span class="input-group-text fw-semibold small text-secondary">Hari</span>
                    </div>
                    @error('interval_days')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Ambang Pengingat --}}
                <div class="col-12 col-md-6">
                    <label for="reminder_before_km" class="form-label small fw-semibold">Peringatkan Sebelum KM</label>
                    <div class="input-group">
                        <input type="number" name="reminder_before_km" id="reminder_before_km"
                               value="{{ old('reminder_before_km', $schedule->reminder_before_km) }}"
                               min="0"
                               class="form-control font-monospace">
                        <span class="input-group-text fw-semibold small text-secondary">KM</span>
                    </div>
                </div>

                <div class="col-12 col-md-6">
                    <label for="reminder_before_days" class="form-label small fw-semibold">Peringatkan Sebelum Waktu (Hari)</label>
                    <div class="input-group">
                        <input type="number" name="reminder_before_days" id="reminder_before_days"
                               value="{{ old('reminder_before_days', $schedule->reminder_before_days) }}"
                               min="0"
                               class="form-control font-monospace">
                        <span class="input-group-text fw-semibold small text-secondary">Hari</span>
                    </div>
                </div>

                {{-- Titik Awal Servis Terakhir --}}
                <div class="col-12">
                    <div class="card bg-light border-0 p-3 rounded-3">
                        <div class="text-uppercase small fw-bold text-secondary mb-2" style="font-size: 0.75rem;">Titik Awal (Servis Terakhir Dilakukan)</div>
                        <div class="row g-3">
                            <div class="col-12 col-md-6">
                                <label for="last_service_odometer_km" class="form-label small text-secondary">KM Saat Terakhir Servis</label>
                                <input type="number" step="any" name="last_service_odometer_km" id="last_service_odometer_km"
                                       value="{{ old('last_service_odometer_km', $schedule->last_service_odometer_km) }}"
                                       class="form-control font-monospace">
                            </div>
                            <div class="col-12 col-md-6">
                                <label for="last_service_at" class="form-label small text-secondary">Tanggal Terakhir Servis</label>
                                <input type="date" name="last_service_at" id="last_service_at"
                                       value="{{ old('last_service_at', $schedule->last_service_at ? $schedule->last_service_at->format('Y-m-d') : '') }}"
                                       class="form-control">
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Status Aktif & Catatan --}}
                <div class="col-12">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', $schedule->is_active) ? 'checked' : '' }}>
                        <label class="form-check-label small fw-semibold" for="is_active">Jadwal Aktif</label>
                    </div>
                </div>

                <div class="col-12">
                    <label for="notes" class="form-label small fw-semibold">Catatan Khusus</label>
                    <textarea name="notes" id="notes" rows="2"
                              class="form-control">{{ old('notes', $schedule->notes) }}</textarea>
                </div>
            </div>

            <div class="d-flex align-items-center gap-2 mt-4 pt-3 border-top">
                <button type="submit" class="btn btn-primary px-4">
                    Simpan Perubahan
                </button>
                <a href="{{ route('schedules.index', $vehicle) }}" class="btn btn-outline-secondary px-3">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
