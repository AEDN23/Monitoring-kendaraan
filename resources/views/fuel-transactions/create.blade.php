@extends('layouts.app')

@section('title', 'Catat Pengisian BBM')
@section('header', 'Catat Pengisian BBM')

@section('content')
<div class="container-fluid p-0" style="max-width: 820px;">
    {{-- Top Header & Navigation --}}
    <div class="d-flex align-items-center gap-3 mb-4">
        <a href="{{ route('fuel-transactions.index') }}" class="btn btn-outline-secondary btn-sm rounded-circle d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
            <i class="bi bi-arrow-left"></i>
        </a>
        <div>
            <h4 class="fw-bold text-dark mb-0">Catat Pengisian BBM</h4>
            <div class="text-secondary small">Volume liter dihitung otomatis dari Total Bayar dibagi Harga per Liter</div>
        </div>
    </div>

    @php
        $v = $currentVehicle ?? $activeVehicle ?? $vehicles->first();
        $isMotor = $v ? ($v->vehicle_type === 'motorcycle') : false;
    @endphp

    <div class="card border-0 shadow-sm rounded-3 p-4">
        <form action="{{ route('fuel-transactions.store') }}" method="POST" id="fuelForm">
            @csrf

            {{-- ── 1. Banner Info Kendaraan Terpilih (Otomatis dari Pilihan di Awal) ────────────────────────── --}}
            <div class="p-3 mb-4 rounded-3 border bg-light d-flex flex-wrap align-items-center justify-content-between gap-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-3 bg-primary bg-opacity-10 text-primary p-2 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px; font-size: 1.5rem;">
                        <i class="bi {{ $isMotor ? 'bi-bicycle' : 'bi-car-front-fill' }}"></i>
                    </div>
                    <div>
                        <div class="d-flex align-items-center gap-2">
                            <span class="text-secondary text-uppercase fw-bold" style="font-size: 0.7rem; letter-spacing: 0.5px;">Kendaraan Terpilih:</span>
                            <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25" style="font-size: 0.7rem;">
                                <i class="bi bi-check2"></i> Aktif
                            </span>
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
                            {{ ucfirst($v->brand ?? '-') }} {{ $v->model ?? '' }} &bull; Odometer Terkini: <strong>{{ number_format($v->latestOdometerKm() ?? 0, 0, ',', '.') }} KM</strong>
                        </div>
                    </div>
                </div>

                {{-- Tombol Ganti Kendaraan jika ingin beralih --}}
                <div>
                    <a href="{{ route('vehicle.select') }}" class="btn btn-sm btn-outline-primary rounded-pill px-3 py-1" style="font-size: 0.8rem;">
                        <i class="bi bi-arrow-left-right me-1"></i> Ganti Kendaraan
                    </a>
                </div>

                {{-- Hidden input vehicle_id --}}
                <input type="hidden" name="vehicle_id" id="vehicle_id" value="{{ $v->id }}">
            </div>

            <div class="row g-3">
                {{-- Jenis BBM & Waktu Pengisian --}}
                <div class="col-12 col-md-6">
                    <label for="fuel_type_id" class="form-label small fw-semibold">Jenis BBM <span class="text-danger">*</span></label>
                    <select name="fuel_type_id" id="fuel_type_id" class="form-select @error('fuel_type_id') is-invalid @enderror" required>
                        <option value="">-- Pilih Jenis BBM --</option>
                        @foreach ($fuelTypes as $ft)
                            <option value="{{ $ft->id }}" {{ old('fuel_type_id') == $ft->id ? 'selected' : '' }}>
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
                           value="{{ old('transaction_at', date('Y-m-d\TH:i')) }}"
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
                               value="{{ old('odometer_km') ? number_format((float) str_replace('.', '', old('odometer_km')), 0, ',', '.') : '' }}"
                               placeholder="Contoh: 15.420"
                               class="form-control font-monospace format-rupiah @error('odometer_km') is-invalid @enderror"
                               required>
                        <span class="input-group-text fw-semibold small text-secondary">KM</span>
                    </div>
                    <div class="form-text text-muted" style="font-size: 0.75rem;">
                        Masukkan kilometer speedometer saat Anda mengisi BBM (titik otomatis bertambah saat mengetik).
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
                                           value="{{ old('total_amount') ? number_format((float) str_replace('.', '', old('total_amount')), 0, ',', '.') : '' }}"
                                           placeholder="Cth: 15.000"
                                           class="form-control font-monospace fw-bold format-rupiah @error('total_amount') is-invalid @enderror"
                                           required>
                                </div>
                                <div class="text-muted" style="font-size: 0.7rem; margin-top: 3px;">Uang yang dibayarkan (otomatis titik)</div>

                                {{-- Pilihan Cepat Nominal (Quick Chips) --}}
                                <div class="d-flex flex-wrap gap-1 mt-2">
                                    <button type="button" class="btn btn-outline-secondary btn-sm py-0 px-1.5 btn-quick-amount" data-amount="10000" style="font-size: 0.7rem;">10rb</button>
                                    <button type="button" class="btn btn-outline-secondary btn-sm py-0 px-1.5 btn-quick-amount" data-amount="15000" style="font-size: 0.7rem;">15rb</button>
                                    <button type="button" class="btn btn-outline-secondary btn-sm py-0 px-1.5 btn-quick-amount" data-amount="20000" style="font-size: 0.7rem;">20rb</button>
                                    <button type="button" class="btn btn-outline-secondary btn-sm py-0 px-1.5 btn-quick-amount" data-amount="50000" style="font-size: 0.7rem;">50rb</button>
                                    <button type="button" class="btn btn-outline-secondary btn-sm py-0 px-1.5 btn-quick-amount" data-amount="100000" style="font-size: 0.7rem;">100rb</button>
                                </div>

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
                                           value="{{ old('price_per_liter') ? number_format((float) str_replace('.', '', old('price_per_liter')), 0, ',', '.') : '' }}"
                                           placeholder="Cth: 10.000"
                                           class="form-control font-monospace fw-bold format-rupiah @error('price_per_liter') is-invalid @enderror"
                                           required>
                                </div>
                                <div class="text-muted" style="font-size: 0.7rem; margin-top: 3px;">Harga BBM per liter</div>

                                {{-- Pilihan Cepat Harga BBM Umum --}}
                                <div class="d-flex flex-wrap gap-1 mt-2">
                                    <button type="button" class="btn btn-outline-secondary btn-sm py-0 px-1.5 btn-quick-price" data-price="10000" style="font-size: 0.7rem;">10.000</button>
                                    <button type="button" class="btn btn-outline-secondary btn-sm py-0 px-1.5 btn-quick-price" data-price="12500" style="font-size: 0.7rem;">12.500</button>
                                    <button type="button" class="btn btn-outline-secondary btn-sm py-0 px-1.5 btn-quick-price" data-price="13700" style="font-size: 0.7rem;">13.700</button>
                                    <button type="button" class="btn btn-outline-secondary btn-sm py-0 px-1.5 btn-quick-price" data-price="6800" style="font-size: 0.7rem;">6.800</button>
                                </div>

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
                                           value="0.000"
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
                                <span id="formula_text" class="fw-semibold text-dark">Rp 0 &divide; Rp 0 = 0.000 Liter</span>
                            </span>
                            <span class="badge bg-primary rounded-pill px-2.5 py-1 font-monospace" id="badge_volume" style="font-size: 0.75rem;">
                                0.000 L
                            </span>
                        </div>
                    </div>
                </div>

                {{-- Nama SPBU & Catatan Tambahan --}}
                <div class="col-12 col-md-6">
                    <label for="station_name" class="form-label small fw-semibold">Nama SPBU / Lokasi (Opsional)</label>
                    <input type="text" name="station_name" id="station_name"
                           value="{{ old('station_name') }}"
                           placeholder="Contoh: SPBU Pertamina Dago / Shell"
                           class="form-control">
                </div>

                <div class="col-12 col-md-6">
                    <label for="notes" class="form-label small fw-semibold">Catatan (Opsional)</label>
                    <input type="text" name="notes" id="notes"
                           value="{{ old('notes') }}"
                           placeholder="Contoh: Isi full tank"
                           class="form-control">
                </div>
            </div>

            {{-- Tombol Aksi --}}
            <div class="d-flex align-items-center gap-2 mt-4 pt-3 border-top">
                <button type="submit" class="btn btn-primary px-4 py-2 fw-semibold">
                    <i class="bi bi-check-lg me-1"></i> Simpan Transaksi BBM
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
        const form = document.getElementById('fuelForm');
        const totalInput = document.getElementById('total_amount');
        const priceInput = document.getElementById('price_per_liter');
        const odoInput = document.getElementById('odometer_km');
        const volumeDisplay = document.getElementById('calculated_volume');
        const formulaText = document.getElementById('formula_text');
        const badgeVolume = document.getElementById('badge_volume');

        // Fungsi format titik ribuan Indonesia
        function formatNumberWithDots(val) {
            if (!val) return '';
            // Hanya ambil digit
            const clean = val.toString().replace(/\D/g, '');
            if (!clean) return '';
            return new Intl.NumberFormat('id-ID').format(clean);
        }

        // Ambil nilai angka murni
        function getRawNumber(val) {
            if (!val) return 0;
            const clean = val.toString().replace(/\./g, '').replace(/,/g, '.');
            return parseFloat(clean) || 0;
        }

        // Terapkan auto-format saat user mengetik
        function attachRupiahMask(inputEl) {
            inputEl.addEventListener('input', function (e) {
                const cursorPos = this.selectionStart;
                const prevLen = this.value.length;

                const rawVal = this.value.replace(/\D/g, '');
                this.value = rawVal ? formatNumberWithDots(rawVal) : '';

                // Sesuaikan posisi kursor agar nyaman
                const newLen = this.value.length;
                const newCursorPos = cursorPos + (newLen - prevLen);
                this.setSelectionRange(newCursorPos, newCursorPos);

                calculateVolume();
            });
        }

        attachRupiahMask(totalInput);
        attachRupiahMask(priceInput);
        attachRupiahMask(odoInput);

        // Quick buttons untuk Total Pembayaran
        document.querySelectorAll('.btn-quick-amount').forEach(btn => {
            btn.addEventListener('click', function () {
                const amt = this.getAttribute('data-amount');
                totalInput.value = formatNumberWithDots(amt);
                calculateVolume();
            });
        });

        // Quick buttons untuk Harga BBM
        document.querySelectorAll('.btn-quick-price').forEach(btn => {
            btn.addEventListener('click', function () {
                const prc = this.getAttribute('data-price');
                priceInput.value = formatNumberWithDots(prc);
                calculateVolume();
            });
        });

        // Hitung volume otomatis: Total Bayar / Harga per Liter
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

        // Sebelum submit, biarkan titik dibersihkan di backend atau di form
        form.addEventListener('submit', function () {
            // Bersihkan titik agar data bersih
            totalInput.value = totalInput.value.replace(/\./g, '');
            priceInput.value = priceInput.value.replace(/\./g, '');
            odoInput.value = odoInput.value.replace(/\./g, '');
        });

        // Jalankan kalkulasi awal jika ada isi lama
        calculateVolume();
    });
</script>
@endsection
