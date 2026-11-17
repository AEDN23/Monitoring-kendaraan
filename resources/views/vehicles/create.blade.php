@extends('layouts.app')

@section('title', 'Tambah Kendaraan')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('vehicles.index') }}"
           class="text-gray-400 hover:text-gray-600 transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Tambah Kendaraan</h1>
            <p class="text-sm text-gray-500 mt-0.5">Daftarkan kendaraan baru Anda</p>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <form action="{{ route('vehicles.store') }}" method="POST">
            @csrf
            @include('vehicles._form')

            <div class="flex items-center gap-3 mt-6 pt-6 border-t border-gray-100">
                <button type="submit"
                        class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium px-6 py-2.5 rounded-lg transition">
                    Simpan Kendaraan
                </button>
                <a href="{{ route('vehicles.index') }}"
                   class="text-gray-600 hover:text-gray-800 text-sm font-medium px-4 py-2.5 rounded-lg border border-gray-200 hover:border-gray-300 transition">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
