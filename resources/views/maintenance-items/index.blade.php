@extends('layouts.app')

@section('title', 'Master Komponen Perawatan')
@section('header', 'Master Komponen Perawatan')

@section('content')
<div class="space-y-6">

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold text-gray-800">Master Komponen Perawatan</h1>
            <p class="text-xs text-gray-500 mt-0.5">Daftar standar komponen servis, interval bawaan pabrikan, dan metode pemantauan</p>
        </div>
        <a href="{{ route('maintenance-items.create') }}"
           class="inline-flex items-center gap-1.5 rounded-lg bg-indigo-600 px-3 py-2 text-xs font-semibold text-white shadow-sm hover:bg-indigo-700 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Tambah Komponen
        </a>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-left text-xs">
                <thead class="bg-gray-50">
                    <tr class="text-gray-500 uppercase tracking-wider font-semibold">
                        <th class="py-3 px-4">Nama Komponen</th>
                        <th class="py-3 px-4">Kategori</th>
                        <th class="py-3 px-4">Mode Pantau</th>
                        <th class="py-3 px-4">Interval Default</th>
                        <th class="py-3 px-4">Untuk Kendaraan</th>
                        <th class="py-3 px-4">Status</th>
                        <th class="py-3 px-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach ($items as $item)
                        @php
                            $modeBadge = match($item->tracking_mode) {
                                'km'         => ['bg' => 'bg-indigo-50 text-indigo-700', 'label' => 'Berdasarkan KM'],
                                'time'       => ['bg' => 'bg-purple-50 text-purple-700', 'label' => 'Berdasarkan Waktu'],
                                'condition'  => ['bg' => 'bg-amber-50 text-amber-700', 'label' => 'Cek Kondisi'],
                                default      => ['bg' => 'bg-blue-50 text-blue-700', 'label' => 'KM / Waktu'],
                            };
                            $appliesLabel = match($item->applies_to) {
                                'motorcycle' => 'Motor Saja',
                                'car'        => 'Mobil Saja',
                                'both'       => 'Motor & Mobil',
                                default      => 'Lainnya',
                            };
                        @endphp
                        <tr class="hover:bg-gray-50/50">
                            <td class="py-3.5 px-4 font-semibold text-gray-900">
                                {{ $item->name }}
                                @if($item->description)
                                    <span class="block text-gray-400 font-normal text-2xs mt-0.5">{{ $item->description }}</span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 text-gray-600">
                                {{ $item->category ?? '-' }}
                            </td>
                            <td class="py-3.5 px-4">
                                <span class="rounded px-2 py-0.5 text-2xs font-semibold {{ $modeBadge['bg'] }}">
                                    {{ $modeBadge['label'] }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4 text-gray-700 font-mono">
                                @if($item->default_interval_km) {{ number_format($item->default_interval_km, 0, ',', '.') }} KM @endif
                                @if($item->default_interval_km && $item->default_interval_days) / @endif
                                @if($item->default_interval_days) {{ $item->default_interval_days }} hari @endif
                                @if(!$item->default_interval_km && !$item->default_interval_days) - @endif
                            </td>
                            <td class="py-3.5 px-4 text-gray-600">
                                {{ $appliesLabel }}
                            </td>
                            <td class="py-3.5 px-4">
                                @if($item->is_active)
                                    <span class="rounded-full bg-emerald-50 px-2 py-0.5 text-2xs font-medium text-emerald-700">Aktif</span>
                                @else
                                    <span class="rounded-full bg-gray-100 px-2 py-0.5 text-2xs font-medium text-gray-600">Nonaktif</span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 text-right">
                                <div class="flex items-center justify-end gap-3">
                                    <a href="{{ route('maintenance-items.edit', $item) }}" class="text-indigo-600 hover:text-indigo-900 font-medium">
                                        Edit
                                    </a>
                                    <form action="{{ route('maintenance-items.destroy', $item) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-gray-500 hover:text-gray-700 font-medium">
                                            {{ $item->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if($items->hasPages())
            <div class="p-4 border-t border-gray-100">
                {{ $items->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
