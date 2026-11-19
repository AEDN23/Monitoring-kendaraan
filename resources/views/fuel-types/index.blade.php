@extends('layouts.app')

@section('title', 'Master Jenis BBM')
@section('header', 'Pengaturan Jenis BBM')

@section('content')
<div class="container-fluid p-0" style="max-width: 800px;">

    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="fw-bold text-dark mb-0">Master Jenis BBM</h4>
            <div class="text-secondary small">Daftar bahan bakar yang dapat dipilih saat transaksi pengisian BBM</div>
        </div>
        <a href="{{ route('fuel-types.create') }}" class="btn btn-primary d-inline-flex align-items-center gap-2">
            <i class="bi bi-plus-lg"></i>
            <span>Tambah Jenis BBM</span>
        </a>
    </div>

    <div class="card border-0 shadow-sm rounded-3 overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr class="small text-secondary text-uppercase">
                        <th class="ps-3 py-3">Nama BBM</th>
                        <th class="py-3">Status</th>
                        <th class="pe-3 py-3 text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($fuelTypes as $ft)
                        <tr>
                            <td class="ps-3 py-3 fw-semibold text-dark">
                                {{ $ft->name }}
                            </td>
                            <td class="py-3">
                                @if($ft->is_active)
                                    <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-2 py-1">Aktif</span>
                                @else
                                    <span class="badge bg-secondary bg-opacity-10 text-secondary px-2 py-1">Nonaktif</span>
                                @endif
                            </td>
                            <td class="pe-3 py-3 text-end">
                                <div class="d-inline-flex align-items-center gap-2">
                                    <a href="{{ route('fuel-types.edit', $ft) }}" class="btn btn-sm btn-light py-1 px-2 text-primary">
                                        Edit
                                    </a>
                                    <form action="{{ route('fuel-types.destroy', $ft) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-light py-1 px-2 text-secondary">
                                            {{ $ft->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if($fuelTypes->hasPages())
            <div class="p-3 border-top">
                {{ $fuelTypes->links('pagination::bootstrap-5') }}
            </div>
        @endif
    </div>

</div>
@endsection
