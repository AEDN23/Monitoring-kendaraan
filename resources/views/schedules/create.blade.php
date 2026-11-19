@extends('layouts.app')

@section('title', 'Tambah Jadwal Perawatan')
@section('header', 'Tambah Jadwal Perawatan')

@section('content')
<div class="container-fluid p-0" style="max-width: 760px;">
    <div class="d-flex align-items-center gap-3 mb-4">
        <a href="{{ route('schedules.index', $vehicle) }}" class="btn btn-outline-secondary btn-sm rounded-circle d-flex align-items-center justify-content-center" style="width: 34px; height: 34px;">
            <i class="bi bi-arrow-left"></i>
        </a>
        <div>
            <h4 class="fw-bold text-dark mb-0">Tambah Jadwal Perawatan</h4>
            <div class="text-secondary small">Untuk kendaraan: <span class="fw-semibold text-dark">{{ $vehicle->name }}</span></div>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-3 p-4">
        <form action="{{ route('schedules.store', $vehicle) }}" method="POST">
            @csrf

            <div class="row g-3">
                {{-- Pilih Komponen --}}
                <div class="col-12">
                    <label for="maintenance_item_id" class="form-label small fw-semibold">Komponen Perawatan <span class="text-danger">*</span></label>
                    <select name="maintenance_item_id" id="maintenance_item_id" class="form-select @error('maintenance_item_id') is-invalid @enderror" required>
                        <option value="">Pilih Komponen</option>
                        @foreach ($maintenanceItems as $item)
                            <option value="{{ $item->id }}"
                                    data-km="{{ $item->default_interval_km }}"
                                    data-days="{{ $item->default_interval_days }}"
                                    data-mode="{{ $item->tracking_mode }}"
                                    {{ old('maintenance_item_id') == $item->id ? 'selected' : '' }}>
                                {{ $item->category ? "[{$item->category}] " : '' }}{{ $item->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('maintenance_item_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Interval KM & Interval Hari --}}
                <div class="col-12 col-md-6">
                    <label for="interval_km" class="form-label small fw-semibold">Interval Kilometer</label>
                    <div class="input-group">
                        <input type="number" name="interval_km" id="interval_km"
                               value="{{ old('interval_km') }}"
                               placeholder="Contoh: 3000"
                               min="100"
                               class="form-control font-monospace @error('interval_km') is-invalid @enderror">
                        <span class="input-group-text fw-semibold small text-secondary">KM</span>
                    </div>
                    <div class="form-text text-muted" style="font-size: 0.75rem;">Kosongkan jika hanya memantau waktu/kondisi.</div>
                    @error('interval_km')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-12 col-md-6">
                    <label for="interval_days" class="form-label small fw-semibold">Interval Waktu (Hari)</label>
                    <div class="input-group">
                        <input type="number" name="interval_days" id="interval_days"
                               value="{{ old('interval_days') }}"
                               placeholder="Contoh: 90 (3 bulan)"
                               min="1"
                               class="form-control font-monospace @error('interval_days') is-invalid @enderror">
                        <span class="input-group-text fw-semibold small text-secondary">Hari</span>
                    </div>
                    <div class="form-text text-muted" style="font-size: 0.75rem;">Kosongkan jika hanya memantau KM/kondisi.</div>
                    @error('interval_days')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Ambang Pengingat Sebelum Jatuh Tempo --}}
                <div class="col-12 col-md-6">
                    <label for="reminder_before_km" class="form-label small fw-semibold">Peringatkan Sebelum KM (Ambang Segera)</label>
                    <div class="input-group">
                        <input type="number" name="reminder_before_km" id="reminder_before_km"
                               value="{{ old('reminder_before_km', 200) }}"
                               placeholder="Default: 200"
                               min="0"
                               class="form-control font-monospace">
                        <span class="input-group-text fw-semibold small text-secondary">KM</span>
                    </div>
                </div>

                <div class="col-12 col-md-6">
                    <label for="reminder_before_days" class="form-label small fw-semibold">Peringatkan Sebelum Waktu (Hari)</label>
                    <div class="input-group">
                        <input type="number" name="reminder_before_days" id="reminder_before_days"
                               value="{{ old('reminder_before_days', 7) }}"
                               placeholder="Default: 7"
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
                                       value="{{ old('last_service_odometer_km', $vehicle->latestOdometerKm() ?? $vehicle->initial_odometer_km) }}"
                                       class="form-control font-monospace">
                            </div>
                            <div class="col-12 col-md-6">
                                <label for="last_service_at" class="form-label small text-secondary">Tanggal Terakhir Servis</label>
                                <input type="date" name="last_service_at" id="last_service_at"
                                       value="{{ old('last_service_at', date('Y-m-d')) }}"
                                       class="form-control">
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Status Aktif & Catatan --}}
                <div class="col-12">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                        <label class="form-check-label small fw-semibold" for="is_active">Jadwal Aktif</label>
                    </div>
                </div>

                <div class="col-12">
                    <label for="notes" class="form-label small fw-semibold">Catatan Khusus</label>
                    <textarea name="notes" id="notes" rows="2"
                              placeholder="Contoh: Pakai oli sintetis 10W-40"
                              class="form-control">{{ old('notes') }}</textarea>
                </div>
            </div>

            <div class="d-flex align-items-center gap-2 mt-4 pt-3 border-top">
                <button type="submit" class="btn btn-primary px-4">
                    Simpan Jadwal
                </button>
                <a href="{{ route('schedules.index', $vehicle) }}" class="btn btn-outline-secondary px-3">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const itemSelect = document.getElementById('maintenance_item_id');
        const kmInput    = document.getElementById('interval_km');
        const daysInput  = document.getElementById('interval_days');

        itemSelect.addEventListener('change', function () {
            const selected = this.options[this.selectedIndex];
            if (selected && selected.dataset.km) {
                kmInput.value = selected.dataset.km;
            }
            if (selected && selected.dataset.days) {
                daysInput.value = selected.dataset.days;
            }
        });
    });
</script>
@endsection
