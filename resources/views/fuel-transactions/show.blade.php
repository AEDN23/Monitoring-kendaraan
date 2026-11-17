@extends('layouts.app')

@section('title', 'Detail Transaksi BBM')
@section('header', 'Detail Transaksi BBM')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('fuel-transactions.index') }}" class="text-gray-400 hover:text-gray-600 transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <div class="flex-1 flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Detail Pengisian BBM</h1>
                <p class="text-sm text-gray-500 mt-0.5">Transaksi tanggal {{ $fuelTransaction->transaction_at->format('d F Y, H:i') }}</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('fuel-transactions.edit', $fuelTransaction) }}"
                   class="rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                    Edit
                </a>
                <form action="{{ route('fuel-transactions.destroy', $fuelTransaction) }}" method="POST"
                      onsubmit="return confirm('Hapus transaksi BBM ini?');" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="rounded-lg bg-rose-50 px-3 py-2 text-sm font-medium text-rose-700 hover:bg-rose-100">
                        Hapus
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        {{-- Card Highlight Total --}}
        <div class="bg-indigo-900 p-6 text-white flex items-center justify-between">
            <div>
                <span class="text-xs uppercase tracking-wider text-indigo-300">Total Pembayaran</span>
                <p class="text-3xl font-extrabold mt-1">Rp {{ number_format($fuelTransaction->total_amount, 0, ',', '.') }}</p>
            </div>
            <div class="text-right">
                <span class="text-xs uppercase tracking-wider text-indigo-300">Volume Didapat</span>
                <p class="text-2xl font-bold font-mono mt-1">{{ number_format($fuelTransaction->volume_liter, 3, ',', '.') }} L</p>
            </div>
        </div>

        {{-- Detail Informasi --}}
        <div class="p-6 divide-y divide-gray-100 text-sm">
            <div class="py-3 flex justify-between">
                <span class="text-gray-500">Kendaraan</span>
                <a href="{{ route('vehicles.show', $fuelTransaction->vehicle) }}" class="font-semibold text-indigo-600 hover:underline">
                    {{ $fuelTransaction->vehicle->name ?? '-' }}
                </a>
            </div>

            <div class="py-3 flex justify-between">
                <span class="text-gray-500">Jenis BBM</span>
                <span class="font-semibold text-gray-900">{{ $fuelTransaction->fuelType->name ?? '-' }}</span>
            </div>

            <div class="py-3 flex justify-between">
                <span class="text-gray-500">Harga Satuan Snapshot</span>
                <span class="font-mono text-gray-800">Rp {{ number_format($fuelTransaction->price_per_liter, 0, ',', '.') }} / Liter</span>
            </div>

            <div class="py-3 flex justify-between">
                <span class="text-gray-500">Odometer Tercatat</span>
                <span class="font-mono font-semibold text-gray-900">{{ number_format($fuelTransaction->odometer_km, 1, ',', '.') }} KM</span>
            </div>

            <div class="py-3 flex justify-between">
                <span class="text-gray-500">Tanggal Transaksi Aktual</span>
                <span class="text-gray-800">{{ $fuelTransaction->transaction_at->format('d/m/Y H:i') }}</span>
            </div>

            <div class="py-3 flex justify-between">
                <span class="text-gray-500">Waktu Input ke Aplikasi</span>
                <span class="text-gray-400 text-xs">{{ $fuelTransaction->recorded_at ? $fuelTransaction->recorded_at->format('d/m/Y H:i') : '-' }}</span>
            </div>

            @if($fuelTransaction->station_name)
                <div class="py-3 flex justify-between">
                    <span class="text-gray-500">Lokasi / SPBU</span>
                    <span class="text-gray-800">{{ $fuelTransaction->station_name }}</span>
                </div>
            @endif

            @if($fuelTransaction->notes)
                <div class="py-3">
                    <span class="text-gray-500 block mb-1">Catatan</span>
                    <p class="text-gray-700 bg-gray-50 rounded-lg p-3 text-xs">{{ $fuelTransaction->notes }}</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
