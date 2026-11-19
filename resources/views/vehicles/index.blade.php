@extends('layouts.app')

@section('title', 'Kendaraan Saya')
@section('header', 'Kendaraan Saya')

@section('content')
<div class="container-fluid p-0">
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
        <div>
            <h4 class="fw-bold text-dark mb-0">Kendaraan Saya</h4>
            <div class="text-secondary small">Kelola semua kendaraan Anda</div>
        </div>
        <a href="{{ route('vehicles.create') }}" class="btn btn-primary d-inline-flex align-items-center gap-2">
            <i class="bi bi-plus-lg"></i>
            <span>Tambah Kendaraan</span>
        </a>
    </div>

    @if($vehicles->isEmpty())
        <div class="card border-0 shadow-sm rounded-3 p-5 text-center">
            <div class="d-inline-flex align-items-center justify-content-center bg-primary bg-opacity-10 text-primary rounded-circle mx-auto mb-3" style="width: 60px; height: 60px;">
                <i class="bi bi-truck fs-2"></i>
            </div>
            <h5 class="fw-bold text-dark mb-1">Belum ada kendaraan</h5>
            <p class="text-secondary small mb-3">Tambahkan kendaraan pertama Anda untuk mulai memantau BBM dan perawatan.</p>
            <div>
                <a href="{{ route('vehicles.create') }}" class="btn btn-primary btn-sm">
                    Tambah Kendaraan
                </a>
            </div>
        </div>
    @else
        <div class="card border-0 shadow-sm rounded-3 overflow-hidden">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr class="small text-secondary text-uppercase">
                            <th class="ps-3 py-3">Nama Kendaraan</th>
                            <th class="py-3">Jenis</th>
                            <th class="py-3">Merek / Model</th>
                            <th class="py-3">Plat</th>
                            <th class="py-3 text-end">KM Terkini</th>
                            <th class="py-3">Status</th>
                            <th class="pe-3 py-3 text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($vehicles as $vehicle)
                        <tr>
                            <td class="ps-3 py-3 fw-semibold text-dark">
                                {{ $vehicle->name }}
                            </td>
                            <td class="py-3">
                                @php
                                    $typeBadge = match($vehicle->vehicle_type) {
                                        'motorcycle' => ['label' => 'Motor',  'class' => 'bg-warning bg-opacity-10 text-dark border border-warning border-opacity-25'],
                                        'car'        => ['label' => 'Mobil',  'class' => 'bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25'],
                                        default      => ['label' => 'Lainnya','class' => 'bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25'],
                                    };
                                @endphp
                                <span class="badge {{ $typeBadge['class'] }} px-2 py-1">
                                    {{ $typeBadge['label'] }}
                                </span>
                            </td>
                            <td class="py-3 text-secondary">
                                {{ implode(' ', array_filter([$vehicle->brand, $vehicle->model])) ?: '—' }}
                            </td>
                            <td class="py-3">
                                @if($vehicle->license_plate)
                                    <span class="badge bg-light text-secondary font-monospace border">
                                        {{ $vehicle->license_plate }}
                                    </span>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td class="py-3 text-end font-monospace fw-semibold text-dark">
                                {{ number_format($vehicle->latestOdometerKm(), 0, ',', '.') }} km
                            </td>
                            <td class="py-3">
                                @if($vehicle->is_active)
                                    <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-2 py-1">Aktif</span>
                                @else
                                    <span class="badge bg-secondary bg-opacity-10 text-secondary px-2 py-1">Nonaktif</span>
                                @endif
                            </td>
                            <td class="pe-3 py-3 text-end">
                                <div class="d-inline-flex align-items-center gap-1">
                                    <a href="{{ route('vehicles.show', $vehicle) }}" class="btn btn-sm btn-light py-1 px-2 text-primary" title="Lihat">
                                        Lihat
                                    </a>
                                    <a href="{{ route('vehicles.edit', $vehicle) }}" class="btn btn-sm btn-light py-1 px-2 text-secondary" title="Edit">
                                        Edit
                                    </a>
                                    <form action="{{ route('vehicles.destroy', $vehicle) }}" method="POST"
                                          onsubmit="return confirm('Hapus kendaraan ini?')" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-light py-1 px-2 text-danger" title="Hapus">
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
            <div class="p-3 border-top">
                {{ $vehicles->links('pagination::bootstrap-5') }}
            </div>
            @endif
        </div>
    @endif
</div>
@endsection
