<div class="row g-3">
    {{-- Nama Kendaraan & Tipe --}}
    <div class="col-12 col-md-6">
        <label for="name" class="form-label small fw-semibold">Nama Panggilan Kendaraan <span class="text-danger">*</span></label>
        <input type="text" name="name" id="name"
               value="{{ old('name', $vehicle->name ?? '') }}"
               placeholder="Contoh: Vario Harian, Mobil Keluarga"
               class="form-control @error('name') is-invalid @enderror"
               required>
        @error('name')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-12 col-md-6">
        <label for="vehicle_type" class="form-label small fw-semibold">Jenis Kendaraan <span class="text-danger">*</span></label>
        <select name="vehicle_type" id="vehicle_type" class="form-select @error('vehicle_type') is-invalid @enderror" required>
            <option value="">Pilih Jenis</option>
            <option value="motorcycle" {{ old('vehicle_type', $vehicle->vehicle_type ?? '') === 'motorcycle' ? 'selected' : '' }}>Sepeda Motor</option>
            <option value="car" {{ old('vehicle_type', $vehicle->vehicle_type ?? '') === 'car' ? 'selected' : '' }}>Mobil</option>
            <option value="other" {{ old('vehicle_type', $vehicle->vehicle_type ?? '') === 'other' ? 'selected' : '' }}>Lainnya</option>
        </select>
        @error('vehicle_type')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    {{-- Merek & Model --}}
    <div class="col-12 col-md-6">
        <label for="brand" class="form-label small fw-semibold">Merek</label>
        <input type="text" name="brand" id="brand"
               value="{{ old('brand', $vehicle->brand ?? '') }}"
               placeholder="Contoh: Honda, Yamaha, Toyota"
               class="form-control">
    </div>

    <div class="col-12 col-md-6">
        <label for="model" class="form-label small fw-semibold">Model / Varian</label>
        <input type="text" name="model" id="model"
               value="{{ old('model', $vehicle->model ?? '') }}"
               placeholder="Contoh: Vario 160 CBS, Avanza G"
               class="form-control">
    </div>

    {{-- Nomor Polisi & Tahun Produksi --}}
    <div class="col-12 col-md-6">
        <label for="license_plate" class="form-label small fw-semibold">Nomor Polisi (Plat)</label>
        <input type="text" name="license_plate" id="license_plate"
               value="{{ old('license_plate', $vehicle->license_plate ?? '') }}"
               placeholder="Contoh: B 1234 XYZ"
               class="form-control font-monospace text-uppercase">
    </div>

    <div class="col-12 col-md-6">
        <label for="production_year" class="form-label small fw-semibold">Tahun Produksi</label>
        <input type="number" name="production_year" id="production_year"
               value="{{ old('production_year', $vehicle->production_year ?? '') }}"
               placeholder="Contoh: 2022"
               min="1950" max="{{ date('Y') + 1 }}"
               class="form-control">
    </div>

    {{-- Odometer Awal & Tanggal Berlaku --}}
    <div class="col-12 col-md-6">
        <label for="initial_odometer_km" class="form-label small fw-semibold">Kilometer Awal <span class="text-danger">*</span></label>
        <div class="input-group">
            <input type="number" step="any" name="initial_odometer_km" id="initial_odometer_km"
                   value="{{ old('initial_odometer_km', $vehicle->initial_odometer_km ?? '0') }}"
                   min="0"
                   class="form-control font-monospace @error('initial_odometer_km') is-invalid @enderror"
                   required>
            <span class="input-group-text fw-semibold small text-secondary">KM</span>
        </div>
        @error('initial_odometer_km')
            <div class="text-danger small mt-1">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-12 col-md-6">
        <label for="initial_odometer_date" class="form-label small fw-semibold">Tanggal KM Awal Berlaku <span class="text-danger">*</span></label>
        <input type="date" name="initial_odometer_date" id="initial_odometer_date"
               value="{{ old('initial_odometer_date', isset($vehicle) && $vehicle->initial_odometer_date ? $vehicle->initial_odometer_date->format('Y-m-d') : date('Y-m-d')) }}"
               class="form-control @error('initial_odometer_date') is-invalid @enderror"
               required>
        @error('initial_odometer_date')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    {{-- Catatan --}}
    <div class="col-12">
        <label for="notes" class="form-label small fw-semibold">Catatan Tambahan</label>
        <textarea name="notes" id="notes" rows="3"
                  placeholder="Contoh: Beli second bulan lalu, oli terakhir diganti pemilik sebelumnya di 12.000 KM"
                  class="form-control">{{ old('notes', $vehicle->notes ?? '') }}</textarea>
    </div>
</div>
