@extends('layouts.app')

@section('title', 'Edit Jadwal Perawatan')
@section('header', 'Edit Jadwal Perawatan')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('schedules.index', $vehicle) }}" class="text-gray-400 hover:text-gray-600 transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Edit Jadwal Perawatan</h1>
            <p class="text-sm text-gray-500 mt-0.5">{{ $vehicle->name }} &bull; {{ $schedule->maintenanceItem->name ?? '-' }}</p>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <form action="{{ route('schedules.update', [$vehicle, $schedule]) }}" method="POST">
            @csrf
            @method('PUT')

            {{-- Hidden input maintenance_item_id --}}
            <input type="hidden" name="maintenance_item_id" value="{{ $schedule->maintenance_item_id }}">

            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Komponen</label>
                    <input type="text" disabled value="{{ $schedule->maintenanceItem->name ?? '-' }}"
                           class="mt-1 block w-full rounded-lg border border-gray-200 bg-gray-50 px-3 py-2 text-sm text-gray-500">
                </div>

                {{-- Interval KM & Interval Hari --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="interval_km" class="block text-sm font-medium text-gray-700">Interval Kilometer</label>
                        <div class="relative mt-1">
                            <input type="number" name="interval_km" id="interval_km"
                                   value="{{ old('interval_km', $schedule->interval_km) }}"
                                   min="100"
                                   class="block w-full rounded-lg border border-gray-300 pr-10 px-3 py-2 text-sm font-mono shadow-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 @error('interval_km') border-rose-500 @enderror">
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3">
                                <span class="text-xs text-gray-400 font-semibold">KM</span>
                            </div>
                        </div>
                        @error('interval_km')
                            <p class="mt-1 text-xs text-rose-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="interval_days" class="block text-sm font-medium text-gray-700">Interval Waktu (Hari)</label>
                        <div class="relative mt-1">
                            <input type="number" name="interval_days" id="interval_days"
                                   value="{{ old('interval_days', $schedule->interval_days) }}"
                                   min="1"
                                   class="block w-full rounded-lg border border-gray-300 pr-12 px-3 py-2 text-sm font-mono shadow-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 @error('interval_days') border-rose-500 @enderror">
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3">
                                <span class="text-xs text-gray-400 font-semibold">Hari</span>
                            </div>
                        </div>
                        @error('interval_days')
                            <p class="mt-1 text-xs text-rose-500">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Ambang Pengingat --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="reminder_before_km" class="block text-sm font-medium text-gray-700">Peringatkan Sebelum KM</label>
                        <div class="relative mt-1">
                            <input type="number" name="reminder_before_km" id="reminder_before_km"
                                   value="{{ old('reminder_before_km', $schedule->reminder_before_km) }}"
                                   min="0"
                                   class="block w-full rounded-lg border border-gray-300 pr-10 px-3 py-2 text-sm font-mono shadow-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3">
                                <span class="text-xs text-gray-400 font-semibold">KM</span>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label for="reminder_before_days" class="block text-sm font-medium text-gray-700">Peringatkan Sebelum Waktu (Hari)</label>
                        <div class="relative mt-1">
                            <input type="number" name="reminder_before_days" id="reminder_before_days"
                                   value="{{ old('reminder_before_days', $schedule->reminder_before_days) }}"
                                   min="0"
                                   class="block w-full rounded-lg border border-gray-300 pr-12 px-3 py-2 text-sm font-mono shadow-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3">
                                <span class="text-xs text-gray-400 font-semibold">Hari</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Titik Awal Servis Terakhir --}}
                <div class="rounded-lg bg-gray-50 border border-gray-100 p-4">
                    <p class="text-xs font-semibold uppercase tracking-wider text-gray-600 mb-2">Titik Awal (Servis Terakhir Dilakukan)</p>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="last_service_odometer_km" class="block text-xs font-medium text-gray-600">KM Saat Terakhir Servis</label>
                            <input type="number" step="any" name="last_service_odometer_km" id="last_service_odometer_km"
                                   value="{{ old('last_service_odometer_km', $schedule->last_service_odometer_km) }}"
                                   class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm font-mono shadow-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
                        </div>

                        <div>
                            <label for="last_service_at" class="block text-xs font-medium text-gray-600">Tanggal Terakhir Servis</label>
                            <input type="date" name="last_service_at" id="last_service_at"
                                   value="{{ old('last_service_at', $schedule->last_service_at ? $schedule->last_service_at->format('Y-m-d') : '') }}"
                                   class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
                        </div>
                    </div>
                </div>

                {{-- Status Aktif & Catatan --}}
                <div>
                    <label class="inline-flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', $schedule->is_active) ? 'checked' : '' }}
                               class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                        <span class="text-sm font-medium text-gray-700">Jadwal Aktif</span>
                    </label>
                </div>

                <div>
                    <label for="notes" class="block text-sm font-medium text-gray-700">Catatan Khusus</label>
                    <textarea name="notes" id="notes" rows="2"
                              class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">{{ old('notes', $schedule->notes) }}</textarea>
                </div>
            </div>

            <div class="flex items-center gap-3 mt-6 pt-6 border-t border-gray-100">
                <button type="submit"
                        class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium px-6 py-2.5 rounded-lg transition">
                    Simpan Perubahan
                </button>
                <a href="{{ route('schedules.index', $vehicle) }}"
                   class="text-gray-600 hover:text-gray-800 text-sm font-medium px-4 py-2.5 rounded-lg border border-gray-200 hover:border-gray-300 transition">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
