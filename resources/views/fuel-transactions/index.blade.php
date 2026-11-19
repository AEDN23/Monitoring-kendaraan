@extends('layouts.app')

@section('title', 'Riwayat Pengisian BBM')
@section('header', 'Riwayat Pengisian BBM')

@section('content')
<div class="container-fluid p-0">

    {{-- ── Filter & Aksi Header ────────────────────────────────── --}}
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
        {{-- Filter Kendaraan --}}
        <form method="GET" action="{{ route('fuel-transactions.index') }}" class="d-flex align-items-center gap-2">
            <select name="vehicle_id" onchange="this.form.submit()" class="form-select form-select-sm" style="min-width: 220px;">
                <option value="">Semua Kendaraan</option>
                @foreach ($vehicles as $v)
                    <option value="{{ $v->id }}" {{ request('vehicle_id') == $v->id ? 'selected' : '' }}>
                        {{ $v->name }} {{ $v->license_plate ? "({$v->license_plate})" : '' }}
                    </option>
                @endforeach
            </select>
            @if(request('vehicle_id'))
                <a href="{{ route('fuel-transactions.index') }}" class="btn btn-sm btn-outline-secondary">
                    Reset
                </a>
            @endif
        </form>

        <a href="{{ route('fuel-transactions.create', ['vehicle_id' => request('vehicle_id')]) }}" class="btn btn-primary d-inline-flex align-items-center gap-2">
            <i class="bi bi-plus-lg"></i>
            <span>Catat Pengisian BBM</span>
        </a>
    </div>

    {{-- ── Tabel Transaksi BBM ─────────────────────────────────── --}}
    <div class="card border-0 shadow-sm rounded-3 overflow-hidden">
        @if($fuelTransactions->isEmpty())
            <div class="p-5 text-center text-secondary">
                <i class="bi bi-fuel-pump fs-1 text-muted d-block mb-2"></i>
                <div class="small">Belum ada transaksi pengisian BBM yang dicatat.</div>
                <div class="mt-3">
                    <a href="{{ route('fuel-transactions.create') }}" class="btn btn-sm btn-primary">
                        Catat transaksi pertama Anda
                    </a>
                </div>
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr class="small text-secondary text-uppercase">
                            <th class="ps-3 py-3">Tanggal Transaksi</th>
                            <th class="py-3">Kendaraan</th>
                            <th class="py-3">BBM</th>
                            <th class="py-3 text-end">Odometer</th>
                            <th class="py-3 text-end">Volume</th>
                            <th class="py-3 text-end">Harga / Liter</th>
                            <th class="py-3 text-end">Total Biaya</th>
                            <th class="pe-3 py-3 text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($fuelTransactions as $ft)
                            <tr>
                                <td class="ps-3 py-3">
                                    <div class="fw-semibold text-dark">{{ $ft->transaction_at->format('d/m/Y') }}</div>
                                    <div class="text-secondary small font-monospace">{{ $ft->transaction_at->format('H:i') }}</div>
                                </td>
                                <td class="py-3 fw-medium text-dark">
                                    {{ $ft->vehicle->name ?? '-' }}
                                </td>
                                <td class="py-3">
                                    <span class="badge bg-warning bg-opacity-10 text-dark border border-warning border-opacity-25 px-2 py-1">
                                        {{ $ft->fuelType->name ?? '-' }}
                                    </span>
                                    @if($ft->station_name)
                                        <div class="text-secondary small mt-0.5">{{ $ft->station_name }}</div>
                                    @endif
                                </td>
                                <td class="py-3 text-end font-monospace text-secondary">
                                    {{ number_format($ft->odometer_km, 1, ',', '.') }} KM
                                </td>
                                <td class="py-3 text-end font-monospace fw-semibold text-dark">
                                    {{ number_format($ft->volume_liter, 3, ',', '.') }} L
                                </td>
                                <td class="py-3 text-end text-secondary font-monospace">
                                    Rp {{ number_format($ft->price_per_liter, 0, ',', '.') }}
                                </td>
                                <td class="py-3 text-end fw-bold text-dark font-monospace">
                                    Rp {{ number_format($ft->total_amount, 0, ',', '.') }}
                                </td>
                                <td class="pe-3 py-3 text-end">
                                    <div class="d-inline-flex align-items-center gap-2">
                                        <a href="{{ route('fuel-transactions.show', $ft) }}" class="btn btn-sm btn-light py-1 px-2 text-primary" title="Detail">
                                            Detail
                                        </a>
                                        <a href="{{ route('fuel-transactions.edit', $ft) }}" class="btn btn-sm btn-light py-1 px-2 text-secondary" title="Edit">
                                            Edit
                                        </a>
                                        <form action="{{ route('fuel-transactions.destroy', $ft) }}" method="POST"
                                              onsubmit="return confirm('Hapus transaksi BBM ini?');" class="d-inline">
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

            @if($fuelTransactions->hasPages())
                <div class="p-3 border-top">
                    {{ $fuelTransactions->links('pagination::bootstrap-5') }}
                </div>
            @endif
        @endif
    </div>

</div>
@endsection
