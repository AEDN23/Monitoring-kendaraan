@extends('layouts.app')

@section('title', 'Riwayat Pengisian BBM')
@section('header', 'Riwayat Pengisian BBM')

@section('content')
<div class="space-y-6">

    {{-- ── Filter & Aksi Header ────────────────────────────────── --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        {{-- Filter Kendaraan --}}
        <form method="GET" action="{{ route('fuel-transactions.index') }}" class="flex items-center gap-2">
            <select name="vehicle_id" onchange="this.form.submit()"
                    class="rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
                <option value="">Semua Kendaraan</option>
                @foreach ($vehicles as $v)
                    <option value="{{ $v->id }}" {{ request('vehicle_id') == $v->id ? 'selected' : '' }}>
                        {{ $v->name }} {{ $v->license_plate ? "({$v->license_plate})" : '' }}
                    </option>
                @endforeach
            </select>
            @if(request('vehicle_id'))
                <a href="{{ route('fuel-transactions.index') }}" class="text-xs text-gray-500 hover:text-gray-700 underline">
                    Reset
                </a>
            @endif
        </form>

        <a href="{{ route('fuel-transactions.create', ['vehicle_id' => request('vehicle_id')]) }}"
           class="inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Catat Pengisian BBM
        </a>
    </div>

    {{-- ── Tabel Transaksi BBM ─────────────────────────────────── --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        @if($fuelTransactions->isEmpty())
            <div class="p-12 text-center text-sm text-gray-500">
                Belum ada transaksi pengisian BBM yang dicatat.
                <div class="mt-3">
                    <a href="{{ route('fuel-transactions.create') }}" class="text-indigo-600 font-medium hover:underline">
                        Catat transaksi pertama Anda
                    </a>
                </div>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-left text-xs">
                    <thead class="bg-gray-50">
                        <tr class="text-gray-500 uppercase tracking-wider font-semibold">
                            <th class="py-3 px-4">Tanggal Transaksi</th>
                            <th class="py-3 px-4">Kendaraan</th>
                            <th class="py-3 px-4">BBM</th>
                            <th class="py-3 px-4 text-right">Odometer</th>
                            <th class="py-3 px-4 text-right">Volume</th>
                            <th class="py-3 px-4 text-right">Harga / Liter</th>
                            <th class="py-3 px-4 text-right">Total Biaya</th>
                            <th class="py-3 px-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach ($fuelTransactions as $ft)
                            <tr class="hover:bg-gray-50/50">
                                <td class="py-3.5 px-4">
                                    <span class="font-medium text-gray-900">{{ $ft->transaction_at->format('d/m/Y') }}</span>
                                    <span class="block text-gray-400 text-2xs">{{ $ft->transaction_at->format('H:i') }}</span>
                                </td>
                                <td class="py-3.5 px-4 font-medium text-gray-800">
                                    {{ $ft->vehicle->name ?? '-' }}
                                </td>
                                <td class="py-3.5 px-4">
                                    <span class="inline-block rounded bg-amber-50 px-2 py-0.5 font-medium text-amber-800">
                                        {{ $ft->fuelType->name ?? '-' }}
                                    </span>
                                    @if($ft->station_name)
                                        <span class="block text-gray-400 text-2xs mt-0.5">{{ $ft->station_name }}</span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-4 text-right font-mono text-gray-700">
                                    {{ number_format($ft->odometer_km, 1, ',', '.') }} KM
                                </td>
                                <td class="py-3.5 px-4 text-right font-mono font-medium text-gray-900">
                                    {{ number_format($ft->volume_liter, 3, ',', '.') }} L
                                </td>
                                <td class="py-3.5 px-4 text-right text-gray-600 font-mono">
                                    Rp {{ number_format($ft->price_per_liter, 0, ',', '.') }}
                                </td>
                                <td class="py-3.5 px-4 text-right font-bold text-gray-900 font-mono">
                                    Rp {{ number_format($ft->total_amount, 0, ',', '.') }}
                                </td>
                                <td class="py-3.5 px-4 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ route('fuel-transactions.show', $ft) }}" class="text-indigo-600 hover:text-indigo-900 font-medium">
                                            Detail
                                        </a>
                                        <a href="{{ route('fuel-transactions.edit', $ft) }}" class="text-gray-500 hover:text-gray-700 font-medium">
                                            Edit
                                        </a>
                                        <form action="{{ route('fuel-transactions.destroy', $ft) }}" method="POST"
                                              onsubmit="return confirm('Hapus transaksi BBM ini?');" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-rose-600 hover:text-rose-900 font-medium">
                                                Hapus
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if($fuelTransactions->hasPages())
                <div class="p-4 border-t border-gray-100">
                    {{ $fuelTransactions->links() }}
                </div>
            @endif
        @endif
    </div>

</div>
@endsection
