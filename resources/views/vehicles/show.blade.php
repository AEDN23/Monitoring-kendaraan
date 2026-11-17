@extends('layouts.app')

@section('title', $vehicle->name)
@section('header', 'Detail Kendaraan')

@section('content')
<div class="space-y-6">

    {{-- ── Header & Info Kendaraan ─────────────────────────── --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="h-14 w-14 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center font-bold text-2xl flex-shrink-0">
                    {{ strtoupper(substr($vehicle->name, 0, 1)) }}
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <h1 class="text-2xl font-bold text-gray-900">{{ $vehicle->name }}</h1>
                        <span class="rounded-full bg-indigo-50 px-2.5 py-0.5 text-xs font-semibold text-indigo-700">
                            {{ $vehicle->vehicle_type === 'motorcycle' ? 'Motor' : ($vehicle->vehicle_type === 'car' ? 'Mobil' : 'Lainnya') }}
                        </span>
                        @if($vehicle->is_active)
                            <span class="rounded-full bg-emerald-50 px-2 py-0.5 text-xs font-medium text-emerald-700">Aktif</span>
                        @else
                            <span class="rounded-full bg-gray-100 px-2 py-0.5 text-xs font-medium text-gray-600">Nonaktif</span>
                        @endif
                    </div>
                    <p class="text-sm text-gray-500 mt-1">
                        {{ $vehicle->brand ?? '-' }} {{ $vehicle->model ?? '' }}
                        @if($vehicle->production_year) &bull; Thn {{ $vehicle->production_year }} @endif
                        @if($vehicle->license_plate) &bull; <span class="font-mono font-medium text-gray-700">{{ $vehicle->license_plate }}</span> @endif
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <a href="{{ route('vehicles.edit', $vehicle) }}"
                   class="rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 transition">
                    Edit Kendaraan
                </a>
                <a href="{{ route('schedules.index', $vehicle) }}"
                   class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700 transition">
                    Kelola Jadwal
                </a>
            </div>
        </div>

        {{-- Row Detail Tambahan --}}
        <div class="mt-6 pt-6 border-t border-gray-100 grid grid-cols-2 sm:grid-cols-4 gap-4 text-xs">
            <div>
                <span class="text-gray-400 font-medium">Odometer Awal</span>
                <p class="mt-1 text-sm font-bold text-gray-800 font-mono">{{ number_format($vehicle->initial_odometer_km, 1, ',', '.') }} KM</p>
                <p class="text-gray-400">{{ $vehicle->initial_odometer_date ? $vehicle->initial_odometer_date->format('d/m/Y') : '-' }}</p>
            </div>
            <div>
                <span class="text-gray-400 font-medium">Odometer Terkini</span>
                <p class="mt-1 text-sm font-bold text-indigo-600 font-mono">{{ number_format($currentOdometer, 1, ',', '.') }} KM</p>
                <p class="text-gray-400">Total jalan: {{ number_format($currentOdometer - $vehicle->initial_odometer_km, 1, ',', '.') }} KM</p>
            </div>
            <div>
                <span class="text-gray-400 font-medium">BBM Bulan Ini</span>
                <p class="mt-1 text-sm font-bold text-amber-600">Rp {{ number_format($fuelThisMonth->total_cost ?? 0, 0, ',', '.') }}</p>
                <p class="text-gray-400">{{ number_format($fuelThisMonth->total_liter ?? 0, 2, ',', '.') }} Liter</p>
            </div>
            <div>
                <span class="text-gray-400 font-medium">Servis Bulan Ini</span>
                <p class="mt-1 text-sm font-bold text-emerald-600">Rp {{ number_format($serviceThisMonth->total_cost ?? 0, 0, ',', '.') }}</p>
                <p class="text-gray-400">{{ $serviceThisMonth->total_count ?? 0 }} kunjungan</p>
            </div>
        </div>

        @if($vehicle->notes)
            <div class="mt-4 rounded-lg bg-gray-50 p-3 text-xs text-gray-600">
                <span class="font-medium text-gray-700">Catatan:</span> {{ $vehicle->notes }}
            </div>
        @endif
    </div>

    {{-- ── Jadwal Perawatan Kendaraan Ini ───────────────────── --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-base font-bold text-gray-900">Jadwal Perawatan</h2>
            <a href="{{ route('schedules.create', $vehicle) }}"
               class="text-xs font-semibold text-indigo-600 hover:text-indigo-800">
                + Tambah Jadwal
            </a>
        </div>

        @if($schedules->isEmpty())
            <div class="rounded-lg border border-dashed border-gray-200 p-6 text-center text-sm text-gray-500">
                Belum ada komponen perawatan yang dipantau untuk kendaraan ini.
                <div class="mt-2">
                    <a href="{{ route('schedules.create', $vehicle) }}" class="text-indigo-600 font-medium hover:underline">
                        Atur jadwal sekarang
                    </a>
                </div>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-left text-xs">
                    <thead>
                        <tr class="text-gray-400 uppercase tracking-wider font-semibold">
                            <th class="py-2.5 px-3">Komponen</th>
                            <th class="py-2.5 px-3">Interval</th>
                            <th class="py-2.5 px-3">Servis Terakhir</th>
                            <th class="py-2.5 px-3">Target Berikutnya</th>
                            <th class="py-2.5 px-3">Status</th>
                            <th class="py-2.5 px-3 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach ($schedules as $sched)
                            @php
                                $badge = match($sched->computed_status) {
                                    'overdue'          => 'bg-rose-50 text-rose-700 border-rose-200',
                                    'soon'             => 'bg-amber-50 text-amber-700 border-amber-200',
                                    'needs_inspection' => 'bg-sky-50 text-sky-700 border-sky-200',
                                    default            => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                };
                                $label = match($sched->computed_status) {
                                    'overdue'          => 'Terlambat',
                                    'soon'             => 'Segera',
                                    'needs_inspection' => 'Periksa Fisik',
                                    default            => 'Aman',
                                };
                            @endphp
                            <tr class="hover:bg-gray-50/50">
                                <td class="py-3 px-3 font-semibold text-gray-900">
                                    {{ $sched->maintenanceItem->name ?? '-' }}
                                    <span class="block text-gray-400 font-normal">{{ $sched->maintenanceItem->category ?? '' }}</span>
                                </td>
                                <td class="py-3 px-3 text-gray-600">
                                    @if($sched->interval_km) {{ number_format($sched->interval_km, 0, ',', '.') }} KM @endif
                                    @if($sched->interval_km && $sched->interval_days) / @endif
                                    @if($sched->interval_days) {{ $sched->interval_days }} hari @endif
                                    @if(!$sched->interval_km && !$sched->interval_days) Manual @endif
                                </td>
                                <td class="py-3 px-3 text-gray-600">
                                    @if($sched->last_service_odometer_km !== null)
                                        <span class="font-mono">{{ number_format($sched->last_service_odometer_km, 1, ',', '.') }} KM</span>
                                    @endif
                                    @if($sched->last_service_at)
                                        <span class="block text-gray-400">{{ $sched->last_service_at->format('d/m/Y') }}</span>
                                    @elseif($sched->last_service_odometer_km === null)
                                        -
                                    @endif
                                </td>
                                <td class="py-3 px-3 font-mono font-medium text-gray-800">
                                    @if($sched->computed_next_km)
                                        {{ number_format($sched->computed_next_km, 1, ',', '.') }} KM
                                    @endif
                                    @if($sched->computed_next_date)
                                        <span class="block text-xs font-normal text-gray-400 font-sans">
                                            {{ $sched->computed_next_date->format('d/m/Y') }}
                                        </span>
                                    @endif
                                    @if(!$sched->computed_next_km && !$sched->computed_next_date)
                                        -
                                    @endif
                                </td>
                                <td class="py-3 px-3">
                                    <span class="rounded-full border px-2 py-0.5 text-xs font-medium {{ $badge }}">
                                        {{ $label }}
                                    </span>
                                </td>
                                <td class="py-3 px-3 text-right">
                                    <a href="{{ route('schedules.edit', [$vehicle, $sched]) }}" class="text-indigo-600 hover:text-indigo-900 font-medium">
                                        Edit
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    {{-- ── 2 Kolom: Riwayat BBM Terakhir & Servis Terakhir ─── --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        {{-- 5 BBM Terakhir --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-base font-bold text-gray-900">5 Pengisian BBM Terakhir</h2>
                <a href="{{ route('fuel-transactions.create', ['vehicle_id' => $vehicle->id]) }}"
                   class="text-xs font-semibold text-indigo-600 hover:text-indigo-800">
                    + Catat BBM
                </a>
            </div>

            @if($lastFuelTransactions->isEmpty())
                <p class="text-xs text-gray-400 italic">Belum ada riwayat pengisian BBM.</p>
            @else
                <div class="space-y-3">
                    @foreach ($lastFuelTransactions as $ft)
                        <div class="flex items-center justify-between rounded-lg border border-gray-50 bg-gray-50/50 p-3 text-xs">
                            <div>
                                <p class="font-semibold text-gray-800">{{ $ft->fuelType->name ?? 'BBM' }} &bull; {{ number_format($ft->volume_liter, 2, ',', '.') }} L</p>
                                <p class="text-gray-400 mt-0.5">{{ $ft->transaction_at->format('d/m/Y H:i') }} &bull; Odo: {{ number_format($ft->odometer_km, 1, ',', '.') }} KM</p>
                            </div>
                            <div class="text-right">
                                <span class="font-bold text-gray-900 text-sm">Rp {{ number_format($ft->total_amount, 0, ',', '.') }}</span>
                                <span class="block text-gray-400 text-2xs">@ Rp {{ number_format($ft->price_per_liter, 0, ',', '.') }}/L</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- 5 Servis Terakhir --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-base font-bold text-gray-900">5 Servis Terakhir</h2>
                <a href="{{ route('maintenance-records.create', ['vehicle_id' => $vehicle->id]) }}"
                   class="text-xs font-semibold text-emerald-600 hover:text-emerald-800">
                    + Catat Servis
                </a>
            </div>

            @if($lastMaintenanceRecords->isEmpty())
                <p class="text-xs text-gray-400 italic">Belum ada riwayat servis tercatat.</p>
            @else
                <div class="space-y-3">
                    @foreach ($lastMaintenanceRecords as $mr)
                        <div class="flex items-center justify-between rounded-lg border border-gray-50 bg-gray-50/50 p-3 text-xs">
                            <div>
                                <p class="font-semibold text-gray-800">{{ $mr->workshop_name ?? 'Bengkel' }}</p>
                                <p class="text-gray-400 mt-0.5">
                                    {{ $mr->service_at->format('d/m/Y') }} &bull; Odo: {{ number_format($mr->odometer_km, 1, ',', '.') }} KM
                                    @if($mr->items->isNotEmpty())
                                        &bull; {{ $mr->items->pluck('item_name')->implode(', ') }}
                                    @endif
                                </p>
                            </div>
                            <div class="text-right">
                                <span class="font-bold text-gray-900 text-sm">Rp {{ number_format($mr->total_cost, 0, ',', '.') }}</span>
                                <a href="{{ route('maintenance-records.show', $mr) }}" class="block text-indigo-600 hover:underline text-2xs mt-0.5">Detail &rarr;</a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

    </div>

</div>
@endsection
