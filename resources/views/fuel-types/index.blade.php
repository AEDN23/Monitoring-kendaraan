@extends('layouts.app')

@section('title', 'Master Jenis BBM')
@section('header', 'Pengaturan Jenis BBM')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">

    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-bold text-gray-800">Master Jenis BBM</h1>
            <p class="text-xs text-gray-500 mt-0.5">Daftar bahan bakar yang dapat dipilih saat transaksi pengisian BBM</p>
        </div>
        <a href="{{ route('fuel-types.create') }}"
           class="inline-flex items-center gap-1.5 rounded-lg bg-indigo-600 px-3 py-2 text-xs font-semibold text-white shadow-sm hover:bg-indigo-700 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Tambah Jenis BBM
        </a>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200 text-left text-xs">
            <thead class="bg-gray-50">
                <tr class="text-gray-500 uppercase tracking-wider font-semibold">
                    <th class="py-3 px-4">Nama BBM</th>
                    <th class="py-3 px-4">Status</th>
                    <th class="py-3 px-4 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach ($fuelTypes as $ft)
                    <tr class="hover:bg-gray-50/50">
                        <td class="py-3.5 px-4 font-semibold text-gray-900">
                            {{ $ft->name }}
                        </td>
                        <td class="py-3.5 px-4">
                            @if($ft->is_active)
                                <span class="rounded-full bg-emerald-50 px-2.5 py-0.5 text-xs font-medium text-emerald-700">Aktif</span>
                            @else
                                <span class="rounded-full bg-gray-100 px-2.5 py-0.5 text-xs font-medium text-gray-600">Nonaktif</span>
                            @endif
                        </td>
                        <td class="py-3.5 px-4 text-right">
                            <div class="flex items-center justify-end gap-3">
                                <a href="{{ route('fuel-types.edit', $ft) }}" class="text-indigo-600 hover:text-indigo-900 font-medium">
                                    Edit
                                </a>
                                <form action="{{ route('fuel-types.destroy', $ft) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-gray-500 hover:text-gray-700 font-medium">
                                        {{ $ft->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        @if($fuelTypes->hasPages())
            <div class="p-4 border-t border-gray-100">
                {{ $fuelTypes->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
