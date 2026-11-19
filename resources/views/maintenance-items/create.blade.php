@extends('layouts.app')

@section('title', 'Tambah Komponen Perawatan')
@section('header', 'Tambah Komponen Perawatan')

@section('content')
<div class="container-fluid p-0" style="max-width: 760px;">
    <div class="d-flex align-items-center gap-3 mb-4">
        <a href="{{ route('maintenance-items.index') }}" class="btn btn-outline-secondary btn-sm rounded-circle d-flex align-items-center justify-content-center" style="width: 34px; height: 34px;">
            <i class="bi bi-arrow-left"></i>
        </a>
        <h4 class="fw-bold text-dark mb-0">Tambah Komponen Perawatan</h4>
    </div>

    <div class="card border-0 shadow-sm rounded-3 p-4">
        <form action="{{ route('maintenance-items.store') }}" method="POST">
            @csrf

            <div class="row g-3">
                {{-- Nama & Kategori --}}
                <div class="col-12 col-md-6">
                    <label for="name" class="form-label small fw-semibold">Nama Komponen <span class="text-danger">*</span></label>
                    <input type="text" name="name" id="name"
                           value="{{ old('name') }}"
                           placeholder="Contoh: Oli Mesin, Kampas Rem Depan"
                           class="form-control @error('name') is-invalid @enderror"
                           required>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-12 col-md-6">
                    <label for="category" class="form-label small fw-semibold">Kategori</label>
                    <input type="text" name="category" id="category"
                           value="{{ old('category') }}"
                           placeholder="Contoh: Mesin, Rem, Ban, Kelistrikan"
                           class="form-control">
                </div>

                {{-- Mode Pantau & Berlaku Untuk --}}
                <div class="col-12 col-md-6">
                    <label for="tracking_mode" class="form-label small fw-semibold">Metode Pemantauan <span class="text-danger">*</span></label>
                    <select name="tracking_mode" id="tracking_mode" class="form-select" required>
                        <option value="km_or_time" {{ old('tracking_mode') === 'km_or_time' ? 'selected' : '' }}>KM atau Waktu (mana duluan)</option>
                        <option value="km" {{ old('tracking_mode') === 'km' ? 'selected' : '' }}>Hanya Kilometer (KM)</option>
                        <option value="time" {{ old('tracking_mode') === 'time' ? 'selected' : '' }}>Hanya Waktu (Hari/Bulan)</option>
                        <option value="condition" {{ old('tracking_mode') === 'condition' ? 'selected' : '' }}>Pemeriksaan Kondisi Fisik</option>
                    </select>
                </div>

                <div class="col-12 col-md-6">
                    <label for="applies_to" class="form-label small fw-semibold">Berlaku Untuk Kendaraan <span class="text-danger">*</span></label>
                    <select name="applies_to" id="applies_to" class="form-select" required>
                        <option value="both" {{ old('applies_to', 'both') === 'both' ? 'selected' : '' }}>Sepeda Motor & Mobil</option>
                        <option value="motorcycle" {{ old('applies_to') === 'motorcycle' ? 'selected' : '' }}>Sepeda Motor Saja</option>
                        <option value="car" {{ old('applies_to') === 'car' ? 'selected' : '' }}>Mobil Saja</option>
                        <option value="other" {{ old('applies_to') === 'other' ? 'selected' : '' }}>Lainnya</option>
                    </select>
                </div>

                {{-- Default Interval --}}
                <div class="col-12 col-md-6">
                    <label for="default_interval_km" class="form-label small fw-semibold">Interval Bawaan (KM)</label>
                    <div class="input-group">
                        <input type="number" name="default_interval_km" id="default_interval_km"
                               value="{{ old('default_interval_km') }}"
                               placeholder="Contoh: 3000"
                               min="100"
                               class="form-control font-monospace">
                        <span class="input-group-text small text-secondary">KM</span>
                    </div>
                </div>

                <div class="col-12 col-md-6">
                    <label for="default_interval_days" class="form-label small fw-semibold">Interval Bawaan (Hari)</label>
                    <div class="input-group">
                        <input type="number" name="default_interval_days" id="default_interval_days"
                               value="{{ old('default_interval_days') }}"
                               placeholder="Contoh: 90"
                               min="1"
                               class="form-control font-monospace">
                        <span class="input-group-text small text-secondary">Hari</span>
                    </div>
                </div>

                <div class="col-12">
                    <label for="description" class="form-label small fw-semibold">Deskripsi / Rekomendasi Pabrikan</label>
                    <textarea name="description" id="description" rows="2"
                              placeholder="Keterangan cara cek atau tipe suku cadang rekomendasi"
                              class="form-control">{{ old('description') }}</textarea>
                </div>

                <div class="col-12">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                        <label class="form-check-label small fw-semibold" for="is_active">Aktif</label>
                    </div>
                </div>
            </div>

            <div class="d-flex align-items-center gap-2 mt-4 pt-3 border-top">
                <button type="submit" class="btn btn-primary px-4">
                    Simpan Komponen
                </button>
                <a href="{{ route('maintenance-items.index') }}" class="btn btn-outline-secondary px-3">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
