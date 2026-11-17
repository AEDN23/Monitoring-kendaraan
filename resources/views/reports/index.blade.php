@extends('layouts.app')

@section('title', 'Laporan Biaya & Pengeluaran')
@section('header', 'Laporan Biaya & Pengeluaran')

@section('content')
<div class="space-y-6">

    {{-- ── Filter Form ─────────────────────────────────────────── --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
        <form method="GET" action="{{ route('reports.index') }}" class="grid grid-cols-1 sm:grid-cols-4 gap-4 items-end">
            <div>
                <label for="vehicle_id" class="block text-xs font-semibold text-gray-600 uppercase tracking-wider">Kendaraan</label>
                <select name="vehicle_id" id="vehicle_id"
                        class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
                    <option value="">Semua Kendaraan</option>
                    @foreach ($vehicles as $v)
                        <option value="{{ $v->id }}" {{ $vehicleId == $v->id ? 'selected' : '' }}>
                            {{ $v->name }} {{ $v->license_plate ? "({$v->license_plate})" : '' }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="month" class="block text-xs font-semibold text-gray-600 uppercase tracking-wider">Bulan</label>
                @php
                    $months = [
                        1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                        5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                        9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
                    ];
                @endphp
                <select name="month" id="month"
                        class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
                    @foreach ($months as $num => $name)
                        <option value="{{ $num }}" {{ $month == $num ? 'selected' : '' }}>
                            {{ $name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="year" class="block text-xs font-semibold text-gray-600 uppercase tracking-wider">Tahun</label>
                <select name="year" id="year"
                        class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
                    @for ($y = date('Y'); $y >= date('Y') - 5; $y--)
                        <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>
                            {{ $y }}
                        </option>
                    @endfor
                </select>
            </div>

            <div class="flex gap-2">
                <button type="submit"
                        class="w-full rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700 transition">
                    Terapkan Filter
                </button>
            </div>
        </form>
    </div>

    {{-- ── Kartu Ringkasan Periode ─────────────────────────────── --}}
    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
        {{-- Grand Total --}}
        <div class="rounded-xl bg-indigo-900 text-white p-5 shadow-sm">
            <span class="text-xs uppercase tracking-wider text-indigo-300">Total Pengeluaran</span>
            <p class="mt-1 text-2xl font-black">Rp {{ number_format($grandTotal, 0, ',', '.') }}</p>
            <p class="mt-2 text-2xs text-indigo-200">{{ $months[$month] }} {{ $year }}</p>
        </div>

        {{-- Biaya BBM --}}
        <div class="rounded-xl bg-white border border-gray-100 p-5 shadow-sm">
            <span class="text-xs uppercase tracking-wider text-gray-400 font-semibold">Total Biaya BBM</span>
            <p class="mt-1 text-2xl font-bold text-amber-600">Rp {{ number_format($fuelStats['total_amount'], 0, ',', '.') }}</p>
            <p class="mt-2 text-2xs text-gray-500">{{ number_format($fuelStats['total_liter'], 2, ',', '.') }} Liter &bull; {{ $fuelStats['total_fill'] }} kali isi</p>
        </div>

        {{-- Biaya Suku Cadang --}}
        <div class="rounded-xl bg-white border border-gray-100 p-5 shadow-sm">
            <span class="text-xs uppercase tracking-wider text-gray-400 font-semibold">Suku Cadang Servis</span>
            <p class="mt-1 text-2xl font-bold text-gray-800">Rp {{ number_format($serviceStats['total_parts'], 0, ',', '.') }}</p>
            <p class="mt-2 text-2xs text-gray-500">Biaya suku cadang/barang</p>
        </div>

        {{-- Biaya Jasa Servis --}}
        <div class="rounded-xl bg-white border border-gray-100 p-5 shadow-sm">
            <span class="text-xs uppercase tracking-wider text-gray-400 font-semibold">Biaya Jasa Bengkel</span>
            <p class="mt-1 text-2xl font-bold text-emerald-600">Rp {{ number_format($serviceStats['total_labor'], 0, ',', '.') }}</p>
            <p class="mt-2 text-2xs text-gray-500">{{ $serviceStats['total_visits'] }} kunjungan servis</p>
        </div>
    </div>

    {{-- ── 2 Kolom Rincian Transaksi ───────────────────────────── --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        {{-- Rincian BBM --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-sm font-bold text-gray-900">Transaksi BBM Periode Ini</h2>
                <span class="text-xs text-gray-500">{{ $fuelTransactions->count() }} transaksi</span>
            </div>

            @if($fuelTransactions->isEmpty())
                <p class="text-xs text-gray-400 italic py-4 text-center">Tidak ada transaksi BBM pada periode ini.</p>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-100 text-left text-xs">
                        <thead>
                            <tr class="text-gray-400 uppercase tracking-wider">
                                <th class="py-2">Tgl</th>
                                <th class="py-2">Kendaraan</th>
                                <th class="py-2">BBM</th>
                                <th class="py-2 text-right">Liter</th>
                                <th class="py-2 text-right">Total</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @foreach ($fuelTransactions as $ft)
                                <tr>
                                    <td class="py-2 font-medium text-gray-800">{{ $ft->transaction_at->format('d/m') }}</td>
                                    <td class="py-2 text-gray-700">{{ $ft->vehicle->name ?? '-' }}</td>
                                    <td class="py-2 text-gray-600">{{ $ft->fuelType->name ?? '-' }}</td>
                                    <td class="py-2 text-right font-mono">{{ number_format($ft->volume_liter, 2, ',', '.') }} L</td>
                                    <td class="py-2 text-right font-mono font-semibold text-gray-900">Rp {{ number_format($ft->total_amount, 0, ',', '.') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        {{-- Rincian Servis --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-sm font-bold text-gray-900">Transaksi Servis Periode Ini</h2>
                <span class="text-xs text-gray-500">{{ $maintenanceRecords->count() }} kunjungan</span>
            </div>

            @if($maintenanceRecords->isEmpty())
                <p class="text-xs text-gray-400 italic py-4 text-center">Tidak ada servis pada periode ini.</p>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-100 text-left text-xs">
                        <thead>
                            <tr class="text-gray-400 uppercase tracking-wider">
                                <th class="py-2">Tgl</th>
                                <th class="py-2">Kendaraan</th>
                                <th class="py-2">Bengkel</th>
                                <th class="py-2 text-right">Part + Jasa</th>
                                <th class="py-2 text-right">Total</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @foreach ($maintenanceRecords as $mr)
                                <tr>
                                    <td class="py-2 font-medium text-gray-800">{{ $mr->service_at->format('d/m') }}</td>
                                    <td class="py-2 text-gray-700">{{ $mr->vehicle->name ?? '-' }}</td>
                                    <td class="py-2 text-gray-600">{{ $mr->workshop_name ?? '-' }}</td>
                                    <td class="py-2 text-right font-mono text-gray-500 text-2xs">
                                        {{ number_format($mr->total_parts_cost, 0, ',', '.') }} + {{ number_format($mr->total_labor_cost, 0, ',', '.') }}
                                    </td>
                                    <td class="py-2 text-right font-mono font-semibold text-gray-900">
                                        <a href="{{ route('maintenance-records.show', $mr) }}" class="text-indigo-600 hover:underline">
                                            Rp {{ number_format($mr->total_cost, 0, ',', '.') }}
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

    </div>

</div>
@endsection
