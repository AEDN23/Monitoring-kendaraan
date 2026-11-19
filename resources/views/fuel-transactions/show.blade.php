@extends('layouts.app')

@section('title', 'Detail Transaksi BBM')
@section('header', 'Detail Transaksi BBM')

@section('content')
<div class="container-fluid p-0" style="max-width: 760px;">
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
        <div class="d-flex align-items-center gap-3">
            <a href="{{ route('fuel-transactions.index') }}" class="btn btn-outline-secondary btn-sm rounded-circle d-flex align-items-center justify-content-center" style="width: 34px; height: 34px;">
                <i class="bi bi-arrow-left"></i>
            </a>
            <div>
                <h4 class="fw-bold text-dark mb-0">Detail Pengisian BBM</h4>
                <div class="text-secondary small">Transaksi tanggal {{ $fuelTransaction->transaction_at->format('d F Y, H:i') }}</div>
            </div>
        </div>
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('fuel-transactions.edit', $fuelTransaction) }}" class="btn btn-sm btn-outline-secondary">
                <i class="bi bi-pencil me-1"></i> Edit
            </a>
            <form action="{{ route('fuel-transactions.destroy', $fuelTransaction) }}" method="POST"
                  onsubmit="return confirm('Hapus transaksi BBM ini?');" class="d-inline">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-sm btn-outline-danger">
                    <i class="bi bi-trash me-1"></i> Hapus
                </button>
            </form>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-3 overflow-hidden">
        {{-- Card Highlight Total --}}
        <div class="p-4 text-white d-flex align-items-center justify-content-between" style="background-color: var(--sidebar-bg);">
            <div>
                <div class="text-uppercase small text-white-50" style="font-size: 0.75rem;">Total Pembayaran</div>
                <div class="fs-2 fw-bold font-monospace mt-1">Rp {{ number_format($fuelTransaction->total_amount, 0, ',', '.') }}</div>
            </div>
            <div class="text-end">
                <div class="text-uppercase small text-white-50" style="font-size: 0.75rem;">Volume Didapat</div>
                <div class="fs-3 fw-bold font-monospace mt-1">{{ number_format($fuelTransaction->volume_liter, 3, ',', '.') }} L</div>
            </div>
        </div>

        {{-- Detail Informasi --}}
        <ul class="list-group list-group-flush small">
            <li class="list-group-item d-flex justify-content-between py-3">
                <span class="text-secondary">Kendaraan</span>
                <a href="{{ route('vehicles.show', $fuelTransaction->vehicle) }}" class="fw-semibold text-decoration-none">
                    {{ $fuelTransaction->vehicle->name ?? '-' }}
                </a>
            </li>
            <li class="list-group-item d-flex justify-content-between py-3">
                <span class="text-secondary">Jenis BBM</span>
                <span class="fw-semibold text-dark">{{ $fuelTransaction->fuelType->name ?? '-' }}</span>
            </li>
            <li class="list-group-item d-flex justify-content-between py-3">
                <span class="text-secondary">Harga Satuan Snapshot</span>
                <span class="font-monospace text-dark">Rp {{ number_format($fuelTransaction->price_per_liter, 0, ',', '.') }} / Liter</span>
            </li>
            <li class="list-group-item d-flex justify-content-between py-3">
                <span class="text-secondary">Odometer Tercatat</span>
                <span class="font-monospace fw-semibold text-dark">{{ number_format($fuelTransaction->odometer_km, 1, ',', '.') }} KM</span>
            </li>
            <li class="list-group-item d-flex justify-content-between py-3">
                <span class="text-secondary">Tanggal Transaksi Aktual</span>
                <span class="text-dark">{{ $fuelTransaction->transaction_at->format('d/m/Y H:i') }}</span>
            </li>
            <li class="list-group-item d-flex justify-content-between py-3">
                <span class="text-secondary">Waktu Input ke Aplikasi</span>
                <span class="text-muted">{{ $fuelTransaction->recorded_at ? $fuelTransaction->recorded_at->format('d/m/Y H:i') : '-' }}</span>
            </li>
            @if($fuelTransaction->station_name)
                <li class="list-group-item d-flex justify-content-between py-3">
                    <span class="text-secondary">Lokasi / SPBU</span>
                    <span class="text-dark">{{ $fuelTransaction->station_name }}</span>
                </li>
            @endif
            @if($fuelTransaction->notes)
                <li class="list-group-item py-3">
                    <span class="text-secondary d-block mb-1">Catatan:</span>
                    <div class="bg-light p-2 rounded text-dark">{{ $fuelTransaction->notes }}</div>
                </li>
            @endif
        </ul>
    </div>
</div>
@endsection
