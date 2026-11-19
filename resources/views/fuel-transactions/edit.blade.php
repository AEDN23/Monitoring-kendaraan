@extends('layouts.app')

@section('title', 'Edit Transaksi BBM')
@section('header', 'Edit Transaksi BBM')

@section('content')
<div class="container-fluid p-0" style="max-width: 760px;">
    <div class="d-flex align-items-center gap-3 mb-4">
        <a href="{{ route('fuel-transactions.index') }}" class="btn btn-outline-secondary btn-sm rounded-circle d-flex align-items-center justify-content-center" style="width: 34px; height: 34px;">
            <i class="bi bi-arrow-left"></i>
        </a>
        <div>
            <h4 class="fw-bold text-dark mb-0">Edit Transaksi BBM</h4>
            <div class="text-secondary small">Perbarui rincian pengisian BBM</div>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-3 p-4">
        <form action="{{ route('fuel-transactions.update', $fuelTransaction) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="row g-3">
                {{-- Kendaraan & Jenis BBM --}}
                <div class="col-12 col-md-6">
                    <label for="vehicle_id" class="form-label small fw-semibold">Kendaraan <span class="text-danger">*</span></label>
                    <select name="vehicle_id" id="vehicle_id" class="form-select @error('vehicle_id') is-invalid @enderror" required>
                        @foreach ($vehicles as $v)
                            <option value="{{ $v->id }}" {{ old('vehicle_id', $fuelTransaction->vehicle_id) == $v->id ? 'selected' : '' }}>
                                {{ $v->name }} {{ $v->license_plate ? "({$v->license_plate})" : '' }}
                            </option>
                        @endforeach
                    </select>
                    @error('vehicle_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-12 col-md-6">
                    <label for="fuel_type_id" class="form-label small fw-semibold">Jenis BBM <span class="text-danger">*</span></label>
                    <select name="fuel_type_id" id="fuel_type_id" class="form-select @error('fuel_type_id') is-invalid @enderror" required>
                        @foreach ($fuelTypes as $ft)
                            <option value="{{ $ft->id }}" {{ old('fuel_type_id', $fuelTransaction->fuel_type_id) == $ft->id ? 'selected' : '' }}>
                                {{ $ft->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('fuel_type_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Tanggal Transaksi & Odometer --}}
                <div class="col-12 col-md-6">
                    <label for="transaction_at" class="form-label small fw-semibold">Tanggal & Waktu Transaksi <span class="text-danger">*</span></label>
                    <input type="datetime-local" name="transaction_at" id="transaction_at"
                           value="{{ old('transaction_at', $fuelTransaction->transaction_at ? $fuelTransaction->transaction_at->format('Y-m-d\TH:i') : '') }}"
                           class="form-control @error('transaction_at') is-invalid @enderror"
                           required>
                    @error('transaction_at')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-12 col-md-6">
                    <label for="odometer_km" class="form-label small fw-semibold">Odometer Saat Pengisian <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <input type="number" step="any" name="odometer_km" id="odometer_km"
                               value="{{ old('odometer_km', $fuelTransaction->odometer_km) }}"
                               min="0"
                               class="form-control font-monospace @error('odometer_km') is-invalid @enderror"
                               required>
                        <span class="input-group-text fw-semibold small text-secondary">KM</span>
                    </div>
                    @error('odometer_km')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Total Pembayaran & Harga Per Liter --}}
                <div class="col-12 col-md-6">
                    <label for="total_amount" class="form-label small fw-semibold">Total Pembayaran (Rp) <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text fw-semibold small text-secondary">Rp</span>
                        <input type="number" step="any" name="total_amount" id="total_amount"
                               value="{{ old('total_amount', (int)$fuelTransaction->total_amount) }}"
                               min="0"
                               class="form-control font-monospace @error('total_amount') is-invalid @enderror"
                               required>
                    </div>
                    @error('total_amount')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-12 col-md-6">
                    <label for="price_per_liter" class="form-label small fw-semibold">Harga Per Liter (Rp) <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text fw-semibold small text-secondary">Rp</span>
                        <input type="number" step="any" name="price_per_liter" id="price_per_liter"
                               value="{{ old('price_per_liter', (int)$fuelTransaction->price_per_liter) }}"
                               min="1"
                               class="form-control font-monospace @error('price_per_liter') is-invalid @enderror"
                               required>
                    </div>
                    @error('price_per_liter')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Box Kalkulasi Otomatis Volume --}}
                <div class="col-12">
                    <div class="alert alert-primary bg-primary bg-opacity-10 border-0 p-3 mb-0 rounded-3">
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center gap-2 text-primary fw-semibold small">
                                <i class="bi bi-info-circle fs-5"></i>
                                <span>Estimasi Volume Didapat:</span>
                            </div>
                            <span id="preview_volume" class="fs-5 fw-bold font-monospace text-primary">
                                {{ number_format($fuelTransaction->volume_liter, 3, ',', '.') }} Liter
                            </span>
                        </div>
                    </div>
                </div>

                {{-- Nama SPBU & Catatan --}}
                <div class="col-12 col-md-6">
                    <label for="station_name" class="form-label small fw-semibold">Nama SPBU / Lokasi</label>
                    <input type="text" name="station_name" id="station_name"
                           value="{{ old('station_name', $fuelTransaction->station_name) }}"
                           placeholder="Contoh: SPBU Pertamina Dago"
                           class="form-control">
                </div>

                <div class="col-12 col-md-6">
                    <label for="notes" class="form-label small fw-semibold">Catatan</label>
                    <input type="text" name="notes" id="notes"
                           value="{{ old('notes', $fuelTransaction->notes) }}"
                           placeholder="Contoh: Isi full tank"
                           class="form-control">
                </div>
            </div>

            <div class="d-flex align-items-center gap-2 mt-4 pt-3 border-top">
                <button type="submit" class="btn btn-primary px-4">
                    Perbarui Transaksi BBM
                </button>
                <a href="{{ route('fuel-transactions.index') }}" class="btn btn-outline-secondary px-3">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const totalInput = document.getElementById('total_amount');
        const priceInput = document.getElementById('price_per_liter');
        const preview    = document.getElementById('preview_volume');

        function updateVolume() {
            const total = parseFloat(totalInput.value) || 0;
            const price = parseFloat(priceInput.value) || 0;
            if (price > 0 && total > 0) {
                const vol = (total / price).toFixed(3);
                preview.textContent = vol + ' Liter';
            } else {
                preview.textContent = '0.000 Liter';
            }
        }

        totalInput.addEventListener('input', updateVolume);
        priceInput.addEventListener('input', updateVolume);
    });
</script>
@endsection
