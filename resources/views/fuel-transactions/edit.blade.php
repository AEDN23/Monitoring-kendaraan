@extends('layouts.app')

@section('title', 'Edit Transaksi BBM')
@section('header', 'Edit Transaksi BBM')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('fuel-transactions.index') }}" class="text-gray-400 hover:text-gray-600 transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Edit Transaksi BBM</h1>
            <p class="text-sm text-gray-500 mt-0.5">Perbarui rincian pengisian BBM</p>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <form action="{{ route('fuel-transactions.update', $fuelTransaction) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="space-y-4">
                {{-- Kendaraan & Jenis BBM --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="vehicle_id" class="block text-sm font-medium text-gray-700">Kendaraan <span class="text-rose-500">*</span></label>
                        <select name="vehicle_id" id="vehicle_id"
                                class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 @error('vehicle_id') border-rose-500 @enderror"
                                required>
                            @foreach ($vehicles as $v)
                                <option value="{{ $v->id }}" {{ old('vehicle_id', $fuelTransaction->vehicle_id) == $v->id ? 'selected' : '' }}>
                                    {{ $v->name }} {{ $v->license_plate ? "({$v->license_plate})" : '' }}
                                </option>
                            @endforeach
                        </select>
                        @error('vehicle_id')
                            <p class="mt-1 text-xs text-rose-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="fuel_type_id" class="block text-sm font-medium text-gray-700">Jenis BBM <span class="text-rose-500">*</span></label>
                        <select name="fuel_type_id" id="fuel_type_id"
                                class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 @error('fuel_type_id') border-rose-500 @enderror"
                                required>
                            @foreach ($fuelTypes as $ft)
                                <option value="{{ $ft->id }}" {{ old('fuel_type_id', $fuelTransaction->fuel_type_id) == $ft->id ? 'selected' : '' }}>
                                    {{ $ft->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('fuel_type_id')
                            <p class="mt-1 text-xs text-rose-500">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Tanggal Transaksi & Odometer --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="transaction_at" class="block text-sm font-medium text-gray-700">Tanggal & Waktu Transaksi <span class="text-rose-500">*</span></label>
                        <input type="datetime-local" name="transaction_at" id="transaction_at"
                               value="{{ old('transaction_at', $fuelTransaction->transaction_at ? $fuelTransaction->transaction_at->format('Y-m-d\TH:i') : '') }}"
                               class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 @error('transaction_at') border-rose-500 @enderror"
                               required>
                        @error('transaction_at')
                            <p class="mt-1 text-xs text-rose-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="odometer_km" class="block text-sm font-medium text-gray-700">Odometer Saat Pengisian <span class="text-rose-500">*</span></label>
                        <div class="relative mt-1">
                            <input type="number" step="0.1" name="odometer_km" id="odometer_km"
                                   value="{{ old('odometer_km', $fuelTransaction->odometer_km) }}"
                                   min="0"
                                   class="block w-full rounded-lg border border-gray-300 pr-10 px-3 py-2 text-sm font-mono shadow-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 @error('odometer_km') border-rose-500 @enderror"
                                   required>
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3">
                                <span class="text-xs text-gray-400 font-semibold">KM</span>
                            </div>
                        </div>
                        @error('odometer_km')
                            <p class="mt-1 text-xs text-rose-500">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Total Pembayaran & Harga Per Liter --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="total_amount" class="block text-sm font-medium text-gray-700">Total Pembayaran (Rp) <span class="text-rose-500">*</span></label>
                        <div class="relative mt-1">
                            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                <span class="text-xs text-gray-400 font-semibold">Rp</span>
                            </div>
                            <input type="number" step="100" name="total_amount" id="total_amount"
                                   value="{{ old('total_amount', (int)$fuelTransaction->total_amount) }}"
                                   min="0"
                                   class="block w-full rounded-lg border border-gray-300 pl-9 px-3 py-2 text-sm font-mono shadow-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 @error('total_amount') border-rose-500 @enderror"
                                   required>
                        </div>
                        @error('total_amount')
                            <p class="mt-1 text-xs text-rose-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="price_per_liter" class="block text-sm font-medium text-gray-700">Harga Per Liter (Rp) <span class="text-rose-500">*</span></label>
                        <div class="relative mt-1">
                            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                <span class="text-xs text-gray-400 font-semibold">Rp</span>
                            </div>
                            <input type="number" step="50" name="price_per_liter" id="price_per_liter"
                                   value="{{ old('price_per_liter', (int)$fuelTransaction->price_per_liter) }}"
                                   min="1"
                                   class="block w-full rounded-lg border border-gray-300 pl-9 px-3 py-2 text-sm font-mono shadow-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 @error('price_per_liter') border-rose-500 @enderror"
                                   required>
                        </div>
                        @error('price_per_liter')
                            <p class="mt-1 text-xs text-rose-500">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Box Kalkulasi Otomatis Volume --}}
                <div class="rounded-lg bg-indigo-50/70 border border-indigo-100 p-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span class="text-xs font-semibold text-indigo-900">Volume Dihitung:</span>
                        </div>
                        <span id="preview_volume" class="font-mono text-lg font-bold text-indigo-700">
                            {{ number_format($fuelTransaction->volume_liter, 3, ',', '.') }} Liter
                        </span>
                    </div>
                </div>

                {{-- Nama SPBU & Catatan --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="station_name" class="block text-sm font-medium text-gray-700">Nama SPBU / Lokasi</label>
                        <input type="text" name="station_name" id="station_name"
                               value="{{ old('station_name', $fuelTransaction->station_name) }}"
                               class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
                    </div>

                    <div>
                        <label for="notes" class="block text-sm font-medium text-gray-700">Catatan</label>
                        <input type="text" name="notes" id="notes"
                               value="{{ old('notes', $fuelTransaction->notes) }}"
                               class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
                    </div>
                </div>
            </div>

            <div class="flex items-center gap-3 mt-6 pt-6 border-t border-gray-100">
                <button type="submit"
                        class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium px-6 py-2.5 rounded-lg transition">
                    Simpan Perubahan
                </button>
                <a href="{{ route('fuel-transactions.index') }}"
                   class="text-gray-600 hover:text-gray-800 text-sm font-medium px-4 py-2.5 rounded-lg border border-gray-200 hover:border-gray-300 transition">
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
            }
        }

        totalInput.addEventListener('input', updateVolume);
        priceInput.addEventListener('input', updateVolume);
    });
</script>
@endsection
