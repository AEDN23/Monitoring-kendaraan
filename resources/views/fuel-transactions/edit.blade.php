@extends('layouts.app')

@section('title', 'Edit Transaksi BBM')
@section('header', 'Edit Transaksi BBM')

@section('content')
<div class="container-fluid p-0" style="max-width: 820px;">
    {{-- Top Header & Navigation --}}
    <div class="d-flex align-items-center gap-3 mb-4">
        <a href="{{ route('fuel-transactions.index') }}" class="btn btn-outline-secondary btn-sm rounded-circle d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
            <i class="bi bi-arrow-left"></i>
        </a>
        <div>
            <h4 class="fw-bold text-dark mb-0">Edit Transaksi BBM</h4>
            <div class="text-secondary small">Perbarui rincian pengisian BBM</div>
        </div>
    </div>

    @php
        $v = $fuelTransaction->vehicle;
        $isMotor = $v ? ($v->vehicle_type === 'motorcycle') : false;
    @endphp

    <div class="card border-0 shadow-sm rounded-3 p-4">
        <form action="{{ route('fuel-transactions.update', $fuelTransaction) }}" method="POST" id="fuelEditForm">
            @csrf
            @method('PUT')

            {{-- ── 1. Banner Info Kendaraan ────────────────────────── --}}
            <div class="p-3 mb-4 rounded-3 border bg-light d-flex flex-wrap align-items-center justify-content-between gap-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-3 bg-primary bg-opacity-10 text-primary p-2 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px; font-size: 1.5rem;">
                        <i class="bi {{ $isMotor ? 'bi-bicycle' : 'bi-car-front-fill' }}"></i>
                    </div>
                    <div>
                        <div class="d-flex align-items-center gap-2">
                            <span class="text-secondary text-uppercase fw-bold" style="font-size: 0.7rem; letter-spacing: 0.5px;">Kendaraan:</span>
                        </div>
                        <h5 class="fw-bold text-dark mb-0 mt-0.5">
                            {{ $v->name ?? 'Kendaraan' }}
                            @if(!empty($v->license_plate))
                                <span class="badge bg-dark text-white font-monospace ms-1" style="font-size: 0.75rem;">
                                    {{ $v->license_plate }}
                                </span>
                            @endif
                        </h5>
                        <div class="text-muted small" style="font-size: 0.75rem;">
                            {{ ucfirst($v->brand ?? '-') }} {{ $v->model ?? '' }}
                        </div>
                    </div>
                </div>

                {{-- Hidden input vehicle_id --}}
                <input type="hidden" name="vehicle_id" id="vehicle_id" value="{{ $v->id }}">
            </div>

            <div class="row g-3">
                {{-- Jenis BBM & Waktu Pengisian --}}
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

                {{-- Odometer Saat Pengisian --}}
                <div class="col-12">
                    <label for="odometer_km" class="form-label small fw-semibold">Odometer Saat Pengisian (KM) <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <input type="text" inputmode="numeric" name="odometer_km" id="odometer_km"
                               value="{{ number_format((float) old('odometer_km', $fuelTransaction->odometer_km), 0, ',', '.') }}"
                               placeholder="Contoh: 15.420"
                               class="form-control font-monospace format-rupiah @error('odometer_km') is-invalid @enderror"
                               required>
                        <span class="input-group-text fw-semibold small text-secondary">KM</span>
                    </div>
                    @error('odometer_km')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>

                {{-- ── 2. Bagian Perhitungan Total Bayar, Harga / Liter, dan Total Liter ─────────────────── --}}
                <div class="col-12">
                    <div class="p-3 rounded-3 border bg-light">
                        <div class="row g-3 align-items-start">
                            {{-- Total Bayar --}}
                            <div class="col-12 col-md-4">
                                <label for="total_amount" class="form-label small fw-bold text-dark mb-1">
                                    Total Pembayaran <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text fw-semibold small text-secondary">Rp</span>
                                    <input type="text" inputmode="numeric" name="total_amount" id="total_amount"
                                           value="{{ number_format((float) old('total_amount', $fuelTransaction->total_amount), 0, ',', '.') }}"
                                           placeholder="Cth: 15.000"
                                           class="form-control font-monospace fw-bold format-rupiah @error('total_amount') is-invalid @enderror"
                                           required>
                                </div>
                                <div class="text-muted" style="font-size: 0.7rem; margin-top: 3px;">Uang yang dibayarkan</div>
                                @error('total_amount')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Harga Per Liter --}}
                            <div class="col-12 col-md-4">
                                <label for="price_per_liter" class="form-label small fw-bold text-dark mb-1">
                                    Harga Per Liter <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text fw-semibold small text-secondary">Rp</span>
                                    <input type="text" inputmode="numeric" name="price_per_liter" id="price_per_liter"
                                           value="{{ number_format((float) old('price_per_liter', $fuelTransaction->price_per_liter), 0, ',', '.') }}"
                                           placeholder="Cth: 10.000"
                                           class="form-control font-monospace fw-bold format-rupiah @error('price_per_liter') is-invalid @enderror"
                                           required>
                                </div>
                                <div class="text-muted" style="font-size: 0.7rem; margin-top: 3px;">Harga BBM per liter</div>
                                @error('price_per_liter')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Hasil Total Liter --}}
                            <div class="col-12 col-md-4">
                                <label class="form-label small fw-bold text-primary mb-1">
                                    <i class="bi bi-calculator me-1"></i> Total Liter (Otomatis)
                                </label>
                                <div class="input-group">
                                    <input type="text" id="calculated_volume"
                                           class="form-control font-monospace fw-bold bg-white text-primary fs-5"
                                           value="{{ number_format($fuelTransaction->volume_liter, 3, '.', '') }}"
                                           readonly>
                                    <span class="input-group-text fw-bold text-primary bg-primary bg-opacity-10">Liter</span>
                                </div>
                                <div class="text-primary fw-medium" style="font-size: 0.7rem; margin-top: 3px;">
                                    Rumus: Total Bayar / Harga per Liter
                                </div>
                            </div>
                        </div>

                        {{-- Ringkasan Rumus Realtime --}}
                        <div class="mt-3 pt-2 border-top border-secondary border-opacity-15 d-flex align-items-center justify-content-between small">
                            <span class="text-secondary" style="font-size: 0.8rem;">
                                <i class="bi bi-info-circle me-1"></i> Rincian:
                                <span id="formula_text" class="fw-semibold text-dark">
                                    Rp {{ number_format($fuelTransaction->total_amount, 0, ',', '.') }} &divide; Rp {{ number_format($fuelTransaction->price_per_liter, 0, ',', '.') }} = <strong>{{ number_format($fuelTransaction->volume_liter, 3, ',', '.') }} Liter</strong>
                                </span>
                            </span>
                            <span class="badge bg-primary rounded-pill px-2.5 py-1 font-monospace" id="badge_volume" style="font-size: 0.75rem;">
                                {{ number_format($fuelTransaction->volume_liter, 3, '.', '') }} L
                            </span>
                        </div>
                    </div>
                </div>

                {{-- Nama SPBU & Catatan Tambahan --}}
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

            {{-- Tombol Aksi --}}
            <div class="d-flex align-items-center gap-2 mt-4 pt-3 border-top">
                <button type="submit" class="btn btn-primary px-4 py-2 fw-semibold">
                    <i class="bi bi-check-lg me-1"></i> Perbarui Transaksi BBM
                </button>
                <a href="{{ route('fuel-transactions.index') }}" class="btn btn-outline-secondary px-3 py-2">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const form = document.getElementById('fuelEditForm');
        const totalInput = document.getElementById('total_amount');
        const priceInput = document.getElementById('price_per_liter');
        const odoInput = document.getElementById('odometer_km');
        const volumeDisplay = document.getElementById('calculated_volume');
        const formulaText = document.getElementById('formula_text');
        const badgeVolume = document.getElementById('badge_volume');

        function formatNumberWithDots(val) {
            if (!val) return '';
            const clean = val.toString().replace(/\D/g, '');
            if (!clean) return '';
            return new Intl.NumberFormat('id-ID').format(clean);
        }

        function getRawNumber(val) {
            if (!val) return 0;
            const clean = val.toString().replace(/\./g, '').replace(/,/g, '.');
            return parseFloat(clean) || 0;
        }

        function attachRupiahMask(inputEl) {
            inputEl.addEventListener('input', function (e) {
                const cursorPos = this.selectionStart;
                const prevLen = this.value.length;

                const rawVal = this.value.replace(/\D/g, '');
                this.value = rawVal ? formatNumberWithDots(rawVal) : '';

                const newLen = this.value.length;
                const newCursorPos = cursorPos + (newLen - prevLen);
                this.setSelectionRange(newCursorPos, newCursorPos);

                calculateVolume();
            });
        }

        attachRupiahMask(totalInput);
        attachRupiahMask(priceInput);
        attachRupiahMask(odoInput);

        function calculateVolume() {
            const total = getRawNumber(totalInput.value);
            const price = getRawNumber(priceInput.value);

            if (total > 0 && price > 0) {
                const liter = (total / price);
                const literFormatted = liter.toFixed(3);

                volumeDisplay.value = literFormatted;
                badgeVolume.textContent = literFormatted + ' L';
                formulaText.innerHTML = 'Rp ' + formatNumberWithDots(total) + ' &divide; Rp ' + formatNumberWithDots(price) + ' = <strong>' + literFormatted + ' Liter</strong>';
            } else {
                volumeDisplay.value = '0.000';
                badgeVolume.textContent = '0.000 L';
                formulaText.innerHTML = 'Rp ' + formatNumberWithDots(total) + ' &divide; Rp ' + formatNumberWithDots(price) + ' = 0.000 Liter';
            }
        }

        form.addEventListener('submit', function () {
            totalInput.value = totalInput.value.replace(/\./g, '');
            priceInput.value = priceInput.value.replace(/\./g, '');
            odoInput.value = odoInput.value.replace(/\./g, '');
        });

        calculateVolume();
    });
</script>
@endsection
