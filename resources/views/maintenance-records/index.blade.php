@extends('layouts.app')

@section('title', 'Riwayat Servis & Perawatan')
@section('header', 'Riwayat Servis & Perawatan')

@section('content')
<div class="container-fluid p-0">

    {{-- ── Filter & Header ─────────────────────────────────────── --}}
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
        {{-- Filter Kendaraan --}}
        <form method="GET" action="{{ route('maintenance-records.index') }}" class="d-flex align-items-center gap-2">
            <select name="vehicle_id" onchange="this.form.submit()" class="form-select form-select-sm" style="min-width: 220px;">
                <option value="all" {{ ($selectedVehicleId == 'all') ? 'selected' : '' }}>Semua Kendaraan</option>
                @foreach ($vehicles as $v)
                    <option value="{{ $v->id }}" {{ ($selectedVehicleId == $v->id) ? 'selected' : '' }}>
                        {{ $v->name }} {{ $v->license_plate ? "({$v->license_plate})" : '' }}
                    </option>
                @endforeach
            </select>
            @if(request()->has('vehicle_id') && request('vehicle_id') != session('active_vehicle_id'))
                <a href="{{ route('maintenance-records.index') }}" class="btn btn-sm btn-outline-secondary" title="Kembali ke kendaraan aktif">
                    Reset
                </a>
            @endif
        </form>

        <a href="{{ route('maintenance-records.create', ['vehicle_id' => ($selectedVehicleId && $selectedVehicleId !== 'all') ? $selectedVehicleId : null]) }}" class="btn btn-success d-inline-flex align-items-center gap-2">
            <i class="bi bi-plus-lg"></i>
            <span>Catat Servis Baru</span>
        </a>
    </div>

    {{-- ── Tabel Riwayat Servis ────────────────────────────────── --}}
    <div class="card border-0 shadow-sm rounded-3 overflow-hidden">
        @if($records->isEmpty())
            <div class="p-5 text-center text-secondary">
                <i class="bi bi-tools fs-1 text-muted d-block mb-2"></i>
                <div class="small">Belum ada transaksi servis atau perawatan yang dicatat.</div>
                <div class="mt-3">
                    <a href="{{ route('maintenance-records.create') }}" class="btn btn-sm btn-success">
                        Catat servis pertama Anda
                    </a>
                </div>
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr class="small text-secondary text-uppercase">
                            <th class="ps-3 py-3">Tanggal</th>
                            <th class="py-3">Kendaraan</th>
                            <th class="py-3">Bengkel</th>
                            <th class="py-3">Komponen & Pekerjaan</th>
                            <th class="py-3 text-end">Odometer</th>
                            <th class="py-3 text-end">Suku Cadang</th>
                            <th class="py-3 text-end">Jasa</th>
                            <th class="py-3 text-end">Total Biaya</th>
                            <th class="pe-3 py-3 text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($records as $record)
                            <tr>
                                <td class="ps-3 py-3 fw-semibold text-dark text-nowrap">
                                    {{ $record->service_at->format('d/m/Y') }}
                                </td>
                                <td class="py-3 fw-medium text-dark text-nowrap">
                                    {{ $record->vehicle->name ?? '-' }}
                                </td>
                                <td class="py-3 text-secondary">
                                    {{ $record->workshop_name ?? '-' }}
                                </td>
                                <td class="py-3">
                                    @if($record->items->isNotEmpty())
                                        <div class="d-flex flex-wrap gap-1">
                                            @foreach ($record->items as $item)
                                                <span class="badge bg-light text-secondary border" style="font-size: 0.75rem;">
                                                    {{ $item->item_name }}
                                                </span>
                                            @endforeach
                                        </div>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td class="py-3 text-end font-monospace text-secondary text-nowrap">
                                    {{ number_format($record->odometer_km, 1, ',', '.') }} KM
                                </td>
                                <td class="py-3 text-end font-monospace text-secondary text-nowrap">
                                    Rp {{ number_format($record->total_parts_cost, 0, ',', '.') }}
                                </td>
                                <td class="py-3 text-end font-monospace text-secondary text-nowrap">
                                    Rp {{ number_format($record->total_labor_cost, 0, ',', '.') }}
                                </td>
                                <td class="py-3 text-end font-monospace fw-bold text-dark text-nowrap">
                                    Rp {{ number_format($record->total_cost, 0, ',', '.') }}
                                </td>
                                <td class="pe-3 py-3 text-end text-nowrap">
                                    <div class="d-inline-flex align-items-center gap-2">
                                        <a href="{{ route('maintenance-records.show', $record) }}" class="btn btn-sm btn-light py-1 px-2 text-primary" title="Detail">
                                            Detail
                                        </a>
                                        <a href="{{ route('maintenance-records.edit', $record) }}" class="btn btn-sm btn-light py-1 px-2 text-secondary" title="Edit">
                                            Edit
                                        </a>
                                        <form action="{{ route('maintenance-records.destroy', $record) }}" method="POST"
                                              onsubmit="return confirm('Hapus catatan servis ini?');" class="d-inline">
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

            @if($records->hasPages())
                <div class="p-3 border-top">
                    {{ $records->links('pagination::bootstrap-5') }}
                </div>
            @endif
        @endif
    </div>

</div>
@endsection
