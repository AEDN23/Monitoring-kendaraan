@extends('layouts.app')

@section('title', 'Dashboard')
@section('header', 'Dashboard Monitoring Kendaraan')

@section('content')
<div class="space-y-6">

    {{-- ── Kartu Ringkasan Statistik ──────────────────────────────── --}}
    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3">
        {{-- Total Kendaraan --}}
        <div class="overflow-hidden rounded-xl bg-white p-5 shadow-sm border border-gray-100 flex items-center justify-between">
            <div>
                <p class="text-xs font-medium uppercase tracking-wider text-gray-500">Kendaraan Aktif</p>
                <p class="mt-1 text-3xl font-bold text-gray-900">{{ $totalVehicles }}</p>
            </div>
            <div class="rounded-xl bg-indigo-50 p-3 text-indigo-600">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 17l4-4 4 4m0 0l-4-4-4 4M4 7h16" />
                </svg>
            </div>
        </div>

        {{-- Biaya BBM Bulan Ini --}}
        <div class="overflow-hidden rounded-xl bg-white p-5 shadow-sm border border-gray-100 flex items-center justify-between">
            <div>
                <p class="text-xs font-medium uppercase tracking-wider text-gray-500">Biaya BBM (Bulan Ini)</p>
                <p class="mt-1 text-2xl font-bold text-gray-900">Rp {{ number_format($totalFuelThisMonth, 0, ',', '.') }}</p>
            </div>
            <div class="rounded-xl bg-amber-50 p-3 text-amber-600">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
                </svg>
            </div>
        </div>

        {{-- Biaya Servis Bulan Ini --}}
        <div class="overflow-hidden rounded-xl bg-white p-5 shadow-sm border border-gray-100 flex items-center justify-between">
            <div>
                <p class="text-xs font-medium uppercase tracking-wider text-gray-500">Biaya Servis (Bulan Ini)</p>
                <p class="mt-1 text-2xl font-bold text-gray-900">Rp {{ number_format($totalServiceThisMonth, 0, ',', '.') }}</p>
            </div>
            <div class="rounded-xl bg-emerald-50 p-3 text-emerald-600">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 4a2 2 0 114 0v1a1 1 0 001 1h3a1 1 0 011 1v3a1 1 0 01-1 1h-1a2 2 0 100 4h1a1 1 0 011 1v3a1 1 0 01-1 1h-3a1 1 0 01-1-1v-1a2 2 0 10-4 0v1a1 1 0 01-1 1H7a1 1 0 01-1-1v-3a1 1 0 00-1-1H4a2 2 0 110-4h1a1 1 0 001-1V7a1 1 0 011-1h3a1 1 0 001-1V4z" />
                </svg>
            </div>
        </div>
    </div>

    {{-- ── Status Pemantauan Per Kendaraan ──────────────────────────── --}}
    <div class="rounded-xl bg-white shadow-sm border border-gray-100 p-6">
        <div class="flex items-center justify-between mb-5">
            <div>
                <h2 class="text-lg font-bold text-gray-900">Kondisi & Jadwal Kendaraan</h2>
                <p class="text-xs text-gray-500">Pantau kilometer dan komponen yang memerlukan perhatian.</p>
            </div>
            <a href="{{ route('vehicles.create') }}"
               class="inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 transition">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Tambah Kendaraan
            </a>
        </div>

        @if($vehicleStatuses->isEmpty())
            <div class="rounded-lg border-2 border-dashed border-gray-200 p-12 text-center">
                <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-indigo-50 text-indigo-600">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                    </svg>
                </div>
                <h3 class="mt-3 text-sm font-medium text-gray-900">Belum ada kendaraan terdaftar</h3>
                <p class="mt-1 text-sm text-gray-500">Mulai dengan menambahkan kendaraan pertama Anda untuk memantau odometer dan jadwal perawatan.</p>
                <div class="mt-4">
                    <a href="{{ route('vehicles.create') }}" class="inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700">
                        Tambah Kendaraan Sekarang
                    </a>
                </div>
            </div>
        @else
            <div class="grid grid-cols-1 gap-6 md:grid-cols-2 xl:grid-cols-3">
                @foreach ($vehicleStatuses as $item)
                    @php
                        $v = $item['vehicle'];
                        $statusBadge = match($item['overall_status']) {
                            'overdue'          => ['bg' => 'bg-rose-50 text-rose-700 border-rose-200', 'label' => 'Terlambat'],
                            'soon'             => ['bg' => 'bg-amber-50 text-amber-700 border-amber-200', 'label' => 'Segera'],
                            'needs_inspection' => ['bg' => 'bg-sky-50 text-sky-700 border-sky-200', 'label' => 'Periksa Kondisi'],
                            default            => ['bg' => 'bg-emerald-50 text-emerald-700 border-emerald-200', 'label' => 'Aman'],
                        };
                    @endphp
                    <div class="rounded-xl border border-gray-200 bg-white p-5 hover:shadow-md transition">
                        <div class="flex items-start justify-between">
                            <div>
                                <span class="text-xs uppercase tracking-wider text-gray-400 font-semibold">
                                    {{ $v->vehicle_type === 'motorcycle' ? 'Motor' : ($v->vehicle_type === 'car' ? 'Mobil' : 'Lainnya') }}
                                </span>
                                <h3 class="text-base font-bold text-gray-900">
                                    <a href="{{ route('vehicles.show', $v) }}" class="hover:text-indigo-600">
                                        {{ $v->name }}
                                    </a>
                                </h3>
                                @if($v->license_plate)
                                    <span class="inline-block mt-0.5 rounded bg-gray-100 px-2 py-0.5 text-xs font-mono text-gray-600">
                                        {{ $v->license_plate }}
                                    </span>
                                @endif
                            </div>
                            <span class="rounded-full border px-2.5 py-0.5 text-xs font-medium {{ $statusBadge['bg'] }}">
                                {{ $statusBadge['label'] }}
                            </span>
                        </div>

                        {{-- Odometer --}}
                        <div class="mt-4 flex items-center justify-between rounded-lg bg-gray-50 px-3 py-2 text-xs">
                            <span class="text-gray-500">Odometer Terkini:</span>
                            <span class="font-bold text-gray-900 font-mono">{{ number_format($item['odometer'], 1, ',', '.') }} KM</span>
                        </div>

                        {{-- Ringkasan Item Jadwal --}}
                        <div class="mt-4">
                            <p class="text-xs font-semibold uppercase tracking-wider text-gray-400 mb-2">Jadwal Perawatan</p>
                            @if(count($item['schedule_detail']) > 0)
                                <div class="space-y-1.5 max-h-36 overflow-y-auto pr-1">
                                    @foreach ($item['schedule_detail'] as $detail)
                                        @php
                                            $dotColor = match($detail['status']) {
                                                'overdue'          => 'bg-rose-500',
                                                'soon'             => 'bg-amber-500',
                                                'needs_inspection' => 'bg-sky-500',
                                                default            => 'bg-emerald-500',
                                            };
                                        @endphp
                                        <div class="flex items-center justify-between text-xs py-1 border-b border-gray-50 last:border-0">
                                            <span class="flex items-center gap-1.5 text-gray-700 truncate">
                                                <span class="h-2 w-2 rounded-full {{ $dotColor }} flex-shrink-0"></span>
                                                <span class="truncate">{{ $detail['name'] }}</span>
                                            </span>
                                            <span class="capitalize text-gray-500 font-medium flex-shrink-0">
                                                {{ match($detail['status']) {
                                                    'overdue' => 'Lewat',
                                                    'soon' => 'Mendekati',
                                                    'needs_inspection' => 'Inspeksi',
                                                    default => 'Aman'
                                                } }}
                                            </span>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-xs text-gray-400 italic">Belum ada jadwal yang dikonfigurasi.</p>
                            @endif
                        </div>

                        {{-- Tombol Aksi Cepat --}}
                        <div class="mt-5 pt-3 border-t border-gray-100 flex items-center justify-between gap-2 text-xs">
                            <a href="{{ route('schedules.index', $v) }}" class="text-indigo-600 hover:text-indigo-800 font-medium">
                                Atur Jadwal &rarr;
                            </a>
                            <div class="flex gap-2">
                                <a href="{{ route('fuel-transactions.create', ['vehicle_id' => $v->id]) }}"
                                   class="rounded bg-indigo-50 px-2.5 py-1 text-indigo-700 hover:bg-indigo-100 font-medium">
                                    + Isi BBM
                                </a>
                                <a href="{{ route('maintenance-records.create', ['vehicle_id' => $v->id]) }}"
                                   class="rounded bg-emerald-50 px-2.5 py-1 text-emerald-700 hover:bg-emerald-100 font-medium">
                                    + Catat Servis
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

</div>
@endsection
