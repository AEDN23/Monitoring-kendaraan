@extends('layouts.app')

@section('title', 'Kendaraan Saya')

@section('content')
<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Kendaraan Saya</h1>
        <p class="text-sm text-gray-500 mt-1">Kelola semua kendaraan Anda</p>
    </div>
    <a href="{{ route('vehicles.create') }}"
       class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        Tambah Kendaraan
    </a>
</div>

@if($vehicles->isEmpty())
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-12 text-center">
        <div class="flex justify-center mb-4">
            <svg class="w-16 h-16 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                      d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                      d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414A1 1 0 0121 11.414V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"/>
            </svg>
        </div>
        <h3 class="text-lg font-semibold text-gray-700 mb-2">Belum ada kendaraan</h3>
        <p class="text-gray-500 mb-6">Tambahkan kendaraan pertama Anda untuk mulai memantau BBM dan perawatan.</p>
        <a href="{{ route('vehicles.create') }}"
           class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium px-5 py-2.5 rounded-lg transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Tambah Kendaraan
        </a>
    </div>
@else
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="text-left px-5 py-3.5 font-semibold text-gray-600">Nama Kendaraan</th>
                        <th class="text-left px-5 py-3.5 font-semibold text-gray-600">Jenis</th>
                        <th class="text-left px-5 py-3.5 font-semibold text-gray-600">Merek / Model</th>
                        <th class="text-left px-5 py-3.5 font-semibold text-gray-600">Plat</th>
                        <th class="text-right px-5 py-3.5 font-semibold text-gray-600">KM Terkini</th>
                        <th class="text-left px-5 py-3.5 font-semibold text-gray-600">Status</th>
                        <th class="text-center px-5 py-3.5 font-semibold text-gray-600">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($vehicles as $vehicle)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-5 py-4 font-medium text-gray-800">
                            {{ $vehicle->name }}
                        </td>
                        <td class="px-5 py-4">
                            @php
                                $typeBadge = match($vehicle->vehicle_type) {
                                    'motorcycle' => ['label' => 'Motor',  'class' => 'bg-orange-100 text-orange-700'],
                                    'car'        => ['label' => 'Mobil',  'class' => 'bg-blue-100 text-blue-700'],
                                    default      => ['label' => 'Lainnya','class' => 'bg-gray-100 text-gray-700'],
                                };
                            @endphp
                            <span class="inline-block px-2.5 py-0.5 rounded-full text-xs font-medium {{ $typeBadge['class'] }}">
                                {{ $typeBadge['label'] }}
                            </span>
                        </td>
                        <td class="px-5 py-4 text-gray-600">
                            {{ implode(' ', array_filter([$vehicle->brand, $vehicle->model])) ?: '—' }}
                        </td>
                        <td class="px-5 py-4 text-gray-600">
                            {{ $vehicle->license_plate ?: '—' }}
                        </td>
                        <td class="px-5 py-4 text-right text-gray-700 font-mono">
                            {{ number_format($vehicle->latestOdometerKm(), 0, ',', '.') }} km
                        </td>
                        <td class="px-5 py-4">
                            @if($vehicle->is_active)
                                <span class="inline-block px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-700">Aktif</span>
                            @else
                                <span class="inline-block px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-500">Nonaktif</span>
                            @endif
                        </td>
                        <td class="px-5 py-4">
                            <div class="flex items-center justify-center gap-2">
                                <a href="{{ route('vehicles.show', $vehicle) }}"
                                   class="inline-flex items-center gap-1 text-xs font-medium text-indigo-600 hover:text-indigo-800 bg-indigo-50 hover:bg-indigo-100 px-2.5 py-1.5 rounded-lg transition">
                                    Lihat
                                </a>
                                <a href="{{ route('vehicles.edit', $vehicle) }}"
                                   class="inline-flex items-center gap-1 text-xs font-medium text-amber-600 hover:text-amber-800 bg-amber-50 hover:bg-amber-100 px-2.5 py-1.5 rounded-lg transition">
                                    Edit
                                </a>
                                <form action="{{ route('vehicles.destroy', $vehicle) }}" method="POST"
                                      onsubmit="return confirm('Hapus kendaraan ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="inline-flex items-center gap-1 text-xs font-medium text-red-600 hover:text-red-800 bg-red-50 hover:bg-red-100 px-2.5 py-1.5 rounded-lg transition">
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

        @if($vehicles->hasPages())
        <div class="px-5 py-4 border-t border-gray-100">
            {{ $vehicles->links() }}
        </div>
        @endif
    </div>
@endif
@endsection
