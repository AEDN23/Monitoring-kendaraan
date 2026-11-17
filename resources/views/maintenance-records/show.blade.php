@extends('layouts.app')

@section('title', 'Detail Riwayat Servis')
@section('header', 'Detail Riwayat Servis')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('maintenance-records.index') }}" class="text-gray-400 hover:text-gray-600 transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <div class="flex-1 flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Detail Servis Kendaraan</h1>
                <p class="text-sm text-gray-500 mt-0.5">{{ $record->service_at->format('d F Y') }} &bull; {{ $record->vehicle->name ?? '-' }}</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('maintenance-records.edit', $record) }}"
                   class="rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                    Edit
                </a>
                <form action="{{ route('maintenance-records.destroy', $record) }}" method="POST"
                      onsubmit="return confirm('Hapus catatan servis ini?');" class="inline">
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
        {{-- Card Highlight Total Biaya --}}
        <div class="bg-emerald-800 p-6 text-white flex items-center justify-between">
            <div>
                <span class="text-xs uppercase tracking-wider text-emerald-200">Total Biaya Servis</span>
                <p class="text-3xl font-extrabold mt-1">Rp {{ number_format($record->total_cost, 0, ',', '.') }}</p>
                <div class="flex gap-4 mt-2 text-xs text-emerald-100">
                    <span>Suku Cadang: Rp {{ number_format($record->total_parts_cost, 0, ',', '.') }}</span>
                    <span>&bull;</span>
                    <span>Jasa: Rp {{ number_format($record->total_labor_cost, 0, ',', '.') }}</span>
                </div>
            </div>
            <div class="text-right">
                <span class="text-xs uppercase tracking-wider text-emerald-200">Odometer Tercatat</span>
                <p class="text-2xl font-bold font-mono mt-1">{{ number_format($record->odometer_km, 1, ',', '.') }} KM</p>
            </div>
        </div>

        {{-- Detail Header Info --}}
        <div class="p-6 border-b border-gray-100 grid grid-cols-2 sm:grid-cols-4 gap-4 text-xs">
            <div>
                <span class="text-gray-400 font-medium">Kendaraan</span>
                <p class="mt-1 font-semibold text-gray-800">
                    <a href="{{ route('vehicles.show', $record->vehicle) }}" class="text-indigo-600 hover:underline">
                        {{ $record->vehicle->name ?? '-' }}
                    </a>
                </p>
            </div>
            <div>
                <span class="text-gray-400 font-medium">Bengkel / Lokasi</span>
                <p class="mt-1 font-semibold text-gray-800">{{ $record->workshop_name ?? '-' }}</p>
            </div>
            <div>
                <span class="text-gray-400 font-medium">Tanggal Servis</span>
                <p class="mt-1 font-semibold text-gray-800">{{ $record->service_at->format('d/m/Y') }}</p>
            </div>
            <div>
                <span class="text-gray-400 font-medium">Waktu Input</span>
                <p class="mt-1 font-semibold text-gray-400">{{ $record->recorded_at ? $record->recorded_at->format('d/m/Y H:i') : '-' }}</p>
            </div>
        </div>

        {{-- Tabel Items --}}
        <div class="p-6">
            <h3 class="text-sm font-bold text-gray-900 mb-3">Rincian Pekerjaan & Suku Cadang</h3>
            <div class="overflow-x-auto rounded-lg border border-gray-200">
                <table class="min-w-full divide-y divide-gray-200 text-left text-xs">
                    <thead class="bg-gray-50">
                        <tr class="text-gray-500 uppercase tracking-wider font-semibold">
                            <th class="py-2.5 px-3">Item / Pekerjaan</th>
                            <th class="py-2.5 px-3">Komponen Terkait</th>
                            <th class="py-2.5 px-3 text-right">Qty</th>
                            <th class="py-2.5 px-3 text-right">Harga Satuan</th>
                            <th class="py-2.5 px-3 text-right">Total Barang</th>
                            <th class="py-2.5 px-3 text-right">Biaya Jasa</th>
                            <th class="py-2.5 px-3 text-right">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach ($record->items as $item)
                            <tr>
                                <td class="py-3 px-3 font-semibold text-gray-900">{{ $item->item_name }}</td>
                                <td class="py-3 px-3 text-gray-600">
                                    @if($item->maintenanceItem)
                                        <span class="rounded bg-indigo-50 px-2 py-0.5 text-2xs font-medium text-indigo-700">
                                            {{ $item->maintenanceItem->name }}
                                        </span>
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                                <td class="py-3 px-3 text-right font-mono text-gray-800">{{ (float)$item->quantity }}</td>
                                <td class="py-3 px-3 text-right font-mono text-gray-600">Rp {{ number_format($item->unit_price, 0, ',', '.') }}</td>
                                <td class="py-3 px-3 text-right font-mono text-gray-800">Rp {{ number_format($item->parts_cost, 0, ',', '.') }}</td>
                                <td class="py-3 px-3 text-right font-mono text-gray-800">Rp {{ number_format($item->labor_cost, 0, ',', '.') }}</td>
                                <td class="py-3 px-3 text-right font-mono font-bold text-gray-900">
                                    Rp {{ number_format($item->totalCost(), 0, ',', '.') }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-gray-50 font-bold text-xs border-t border-gray-200">
                        <tr>
                            <td colspan="4" class="py-2.5 px-3 text-right text-gray-700">Total:</td>
                            <td class="py-2.5 px-3 text-right font-mono text-gray-900">Rp {{ number_format($record->total_parts_cost, 0, ',', '.') }}</td>
                            <td class="py-2.5 px-3 text-right font-mono text-gray-900">Rp {{ number_format($record->total_labor_cost, 0, ',', '.') }}</td>
                            <td class="py-2.5 px-3 text-right font-mono text-emerald-700 text-sm">Rp {{ number_format($record->total_cost, 0, ',', '.') }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            @if($record->notes)
                <div class="mt-4 rounded-lg bg-gray-50 p-3 text-xs text-gray-700">
                    <span class="font-bold text-gray-800">Catatan Servis:</span> {{ $record->notes }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
