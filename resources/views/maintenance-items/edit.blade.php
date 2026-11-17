@extends('layouts.app')

@section('title', 'Edit Komponen Perawatan')
@section('header', 'Edit Komponen Perawatan')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('maintenance-items.index') }}" class="text-gray-400 hover:text-gray-600 transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <h1 class="text-xl font-bold text-gray-800">Edit Komponen: {{ $maintenanceItem->name }}</h1>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <form action="{{ route('maintenance-items.update', $maintenanceItem) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="space-y-4">
                {{-- Nama & Kategori --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700">Nama Komponen <span class="text-rose-500">*</span></label>
                        <input type="text" name="name" id="name"
                               value="{{ old('name', $maintenanceItem->name) }}"
                               class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 @error('name') border-rose-500 @enderror"
                               required>
                        @error('name')
                            <p class="mt-1 text-xs text-rose-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="category" class="block text-sm font-medium text-gray-700">Kategori</label>
                        <input type="text" name="category" id="category"
                               value="{{ old('category', $maintenanceItem->category) }}"
                               class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
                    </div>
                </div>

                {{-- Mode Pantau & Berlaku Untuk --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="tracking_mode" class="block text-sm font-medium text-gray-700">Metode Pemantauan <span class="text-rose-500">*</span></label>
                        <select name="tracking_mode" id="tracking_mode"
                                class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500" required>
                            <option value="km_or_time" {{ old('tracking_mode', $maintenanceItem->tracking_mode) === 'km_or_time' ? 'selected' : '' }}>KM atau Waktu</option>
                            <option value="km" {{ old('tracking_mode', $maintenanceItem->tracking_mode) === 'km' ? 'selected' : '' }}>Hanya Kilometer (KM)</option>
                            <option value="time" {{ old('tracking_mode', $maintenanceItem->tracking_mode) === 'time' ? 'selected' : '' }}>Hanya Waktu (Hari/Bulan)</option>
                            <option value="condition" {{ old('tracking_mode', $maintenanceItem->tracking_mode) === 'condition' ? 'selected' : '' }}>Pemeriksaan Kondisi Manual</option>
                        </select>
                    </div>

                    <div>
                        <label for="applies_to" class="block text-sm font-medium text-gray-700">Berlaku Untuk Kendaraan <span class="text-rose-500">*</span></label>
                        <select name="applies_to" id="applies_to"
                                class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500" required>
                            <option value="both" {{ old('applies_to', $maintenanceItem->applies_to) === 'both' ? 'selected' : '' }}>Sepeda Motor & Mobil</option>
                            <option value="motorcycle" {{ old('applies_to', $maintenanceItem->applies_to) === 'motorcycle' ? 'selected' : '' }}>Sepeda Motor Saja</option>
                            <option value="car" {{ old('applies_to', $maintenanceItem->applies_to) === 'car' ? 'selected' : '' }}>Mobil Saja</option>
                            <option value="other" {{ old('applies_to', $maintenanceItem->applies_to) === 'other' ? 'selected' : '' }}>Lainnya</option>
                        </select>
                    </div>
                </div>

                {{-- Default Interval --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="default_interval_km" class="block text-sm font-medium text-gray-700">Interval Bawaan (KM)</label>
                        <div class="relative mt-1">
                            <input type="number" name="default_interval_km" id="default_interval_km"
                                   value="{{ old('default_interval_km', $maintenanceItem->default_interval_km) }}"
                                   min="100"
                                   class="block w-full rounded-lg border border-gray-300 pr-10 px-3 py-2 text-sm font-mono shadow-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3">
                                <span class="text-xs text-gray-400 font-semibold">KM</span>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label for="default_interval_days" class="block text-sm font-medium text-gray-700">Interval Bawaan (Hari)</label>
                        <div class="relative mt-1">
                            <input type="number" name="default_interval_days" id="default_interval_days"
                                   value="{{ old('default_interval_days', $maintenanceItem->default_interval_days) }}"
                                   min="1"
                                   class="block w-full rounded-lg border border-gray-300 pr-12 px-3 py-2 text-sm font-mono shadow-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3">
                                <span class="text-xs text-gray-400 font-semibold">Hari</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700">Deskripsi / Rekomendasi Pabrikan</label>
                    <textarea name="description" id="description" rows="2"
                              class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">{{ old('description', $maintenanceItem->description) }}</textarea>
                </div>

                <div>
                    <label class="inline-flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', $maintenanceItem->is_active) ? 'checked' : '' }}
                               class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                        <span class="text-sm font-medium text-gray-700">Aktif</span>
                    </label>
                </div>
            </div>

            <div class="flex items-center gap-3 mt-6 pt-6 border-t border-gray-100">
                <button type="submit"
                        class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium px-5 py-2.5 rounded-lg transition">
                    Simpan Perubahan
                </button>
                <a href="{{ route('maintenance-items.index') }}"
                   class="text-gray-600 hover:text-gray-800 text-sm font-medium px-4 py-2.5 rounded-lg border border-gray-200 hover:border-gray-300 transition">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
