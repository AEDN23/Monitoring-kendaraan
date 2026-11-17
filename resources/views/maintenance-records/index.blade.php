@extends('layouts.app')

@section('title', 'Riwayat Servis & Perawatan')
@section('header', 'Riwayat Servis & Perawatan')

@section('content')
<div class="space-y-6">

    {{-- ── Filter & Header ─────────────────────────────────────── --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        {{-- Filter Kendaraan --}}
        <form method="GET" action="{{ route('maintenance-records.index') }}" class="flex items-center gap-2">
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
                <a href="{{ route('maintenance-records.index') }}" class="text-xs text-gray-500 hover:text-gray-700 underline">
                    Reset
                </a>
            @endif
        </form>

        <a href="{{ route('maintenance-records.create', ['vehicle_id' => request('vehicle_id')]) }}"
           class="inline-flex items-center gap-2 rounded-lg bg-emerald-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-emerald-700 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Catat Servis Baru
        </a>
    </div>

    {{-- ── Tabel Riwayat Servis ────────────────────────────────── --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        @if($records->isEmpty())
            <div class="p-12 text-center text-sm text-gray-500">
                Belum ada transaksi servis atau perawatan yang dicatat.
                <div class="mt-3">
                    <a href="{{ route('maintenance-records.create') }}" class="text-emerald-600 font-medium hover:underline">
                        Catat servis pertama Anda
                    </a>
                </div>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-left text-xs">
                    <thead class="bg-gray-50">
                        <tr class="text-gray-500 uppercase tracking-wider font-semibold">
                            <th class="py-3 px-4">Tanggal</th>
                            <th class="py-3 px-4">Kendaraan</th>
                            <th class="py-3 px-4">Bengkel</th>
                            <th class="py-3 px-4">Komponen & Pekerjaan</th>
                            <th class="py-3 px-4 text-right">Odometer</th>
                            <th class="py-3 px-4 text-right">Biaya Suku Cadang</th>
                            <th class="py-3 px-4 text-right">Biaya Jasa</th>
                            <th class="py-3 px-4 text-right">Total Biaya</th>
                            <th class="py-3 px-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach ($records as $record)
                            <tr class="hover:bg-gray-50/50">
                                <td class="py-3.5 px-4 font-medium text-gray-900 whitespace-nowrap">
                                    {{ $record->service_at->format('d/m/Y') }}
                                </td>
                                <td class="py-3.5 px-4 font-medium text-gray-800 whitespace-nowrap">
                                    {{ $record->vehicle->name ?? '-' }}
                                </td>
                                <td class="py-3.5 px-4 text-gray-700">
                                    {{ $record->workshop_name ?? '-' }}
                                </td>
                                <td class="py-3.5 px-4 text-gray-700 max-w-xs truncate">
                                    @if($record->items->isNotEmpty())
                                        <div class="flex flex-wrap gap-1">
                                            @foreach ($record->items as $item)
                                                <span class="rounded bg-gray-100 px-1.5 py-0.5 text-2xs text-gray-700">
                                                    {{ $item->item_name }}
                                                </span>
                                            @endforeach
                                        </div>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="py-3.5 px-4 text-right font-mono text-gray-700 whitespace-nowrap">
                                    {{ number_format($record->odometer_km, 1, ',', '.') }} KM
                                </td>
                                <td class="py-3.5 px-4 text-right font-mono text-gray-600 whitespace-nowrap">
                                    Rp {{ number_format($record->total_parts_cost, 0, ',', '.') }}
                                </td>
                                <td class="py-3.5 px-4 text-right font-mono text-gray-600 whitespace-nowrap">
                                    Rp {{ number_format($record->total_labor_cost, 0, ',', '.') }}
                                </td>
                                <td class="py-3.5 px-4 text-right font-bold text-gray-900 font-mono whitespace-nowrap">
                                    Rp {{ number_format($record->total_cost, 0, ',', '.') }}
                                </td>
                                <td class="py-3.5 px-4 text-right whitespace-nowrap">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ route('maintenance-records.show', $record) }}" class="text-indigo-600 hover:text-indigo-900 font-medium">
                                            Detail
                                        </a>
                                        <a href="{{ route('maintenance-records.edit', $record) }}" class="text-gray-500 hover:text-gray-700 font-medium">
                                            Edit
                                        </a>
                                        <form action="{{ route('maintenance-records.destroy', $record) }}" method="POST"
                                              onsubmit="return confirm('Hapus catatan servis ini?');" class="inline">
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

            @if($records->hasPages())
                <div class="p-4 border-t border-gray-100">
                    {{ $records->links() }}
                </div>
            @endif
        @endif
    </div>

</div>
@endsection
