@extends('layouts.app')

@section('title', 'Detail Riwayat Servis')
@section('header', 'Detail Riwayat Servis')

@section('content')
<div class="container-fluid p-0" style="max-width: 860px;">
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
        <div class="d-flex align-items-center gap-3">
            <a href="{{ route('maintenance-records.index') }}" class="btn btn-outline-secondary btn-sm rounded-circle d-flex align-items-center justify-content-center" style="width: 34px; height: 34px;">
                <i class="bi bi-arrow-left"></i>
            </a>
            <div>
                <h4 class="fw-bold text-dark mb-0">Detail Servis Kendaraan</h4>
                <div class="text-secondary small">{{ $record->service_at->format('d F Y') }} &bull; {{ $record->vehicle->name ?? '-' }}</div>
            </div>
        </div>
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('maintenance-records.edit', $record) }}" class="btn btn-sm btn-outline-secondary">
                <i class="bi bi-pencil me-1"></i> Edit
            </a>
            <form action="{{ route('maintenance-records.destroy', $record) }}" method="POST"
                  onsubmit="return confirm('Hapus catatan servis ini?');" class="d-inline">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-sm btn-outline-danger">
                    <i class="bi bi-trash me-1"></i> Hapus
                </button>
            </form>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-3 overflow-hidden mb-4">
        {{-- Card Highlight Total Biaya --}}
        <div class="p-4 text-white d-flex align-items-center justify-content-between bg-success">
            <div>
                <div class="text-uppercase small text-white-50" style="font-size: 0.75rem;">Total Biaya Servis</div>
                <div class="fs-2 fw-bold font-monospace mt-1">Rp {{ number_format($record->total_cost, 0, ',', '.') }}</div>
                <div class="d-flex gap-3 mt-1 small text-white-50">
                    <span>Suku Cadang: Rp {{ number_format($record->total_parts_cost, 0, ',', '.') }}</span>
                    <span>&bull;</span>
                    <span>Jasa: Rp {{ number_format($record->total_labor_cost, 0, ',', '.') }}</span>
                </div>
            </div>
            <div class="text-end">
                <div class="text-uppercase small text-white-50" style="font-size: 0.75rem;">Odometer Tercatat</div>
                <div class="fs-3 fw-bold font-monospace mt-1">{{ number_format($record->odometer_km, 1, ',', '.') }} KM</div>
            </div>
        </div>

        {{-- Detail Header Info --}}
        <div class="p-4 border-bottom bg-light">
            <div class="row g-3 small">
                <div class="col-6 col-md-3">
                    <span class="text-secondary fw-semibold">Kendaraan:</span>
                    <div class="fw-bold text-dark mt-1">
                        <a href="{{ route('vehicles.show', $record->vehicle) }}" class="text-decoration-none">
                            {{ $record->vehicle->name ?? '-' }}
                        </a>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <span class="text-secondary fw-semibold">Bengkel / Tempat:</span>
                    <div class="fw-bold text-dark mt-1">{{ $record->workshop_name ?? '-' }}</div>
                </div>
                <div class="col-6 col-md-3">
                    <span class="text-secondary fw-semibold">Tanggal Servis:</span>
                    <div class="fw-bold text-dark mt-1">{{ $record->service_at->format('d/m/Y') }}</div>
                </div>
                <div class="col-6 col-md-3">
                    <span class="text-secondary fw-semibold">Waktu Input:</span>
                    <div class="text-muted mt-1">{{ $record->recorded_at ? $record->recorded_at->format('d/m/Y H:i') : '-' }}</div>
                </div>
            </div>
        </div>

        {{-- Tabel Items --}}
        <div class="p-4">
            <h6 class="fw-bold text-dark mb-3">Rincian Pekerjaan & Suku Cadang</h6>
            <div class="table-responsive rounded-3 border">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr class="small text-secondary text-uppercase">
                            <th class="ps-3 py-2">Item / Pekerjaan</th>
                            <th class="py-2">Komponen Terkait</th>
                            <th class="py-2 text-end">Qty</th>
                            <th class="py-2 text-end">Harga Satuan</th>
                            <th class="py-2 text-end">Total Barang</th>
                            <th class="py-2 text-end">Biaya Jasa</th>
                            <th class="pe-3 py-2 text-end">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($record->items as $item)
                            <tr>
                                <td class="ps-3 py-2 fw-semibold text-dark">{{ $item->item_name }}</td>
                                <td class="py-2">
                                    @if($item->maintenanceItem)
                                        <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25">
                                            {{ $item->maintenanceItem->name }}
                                        </span>
                                    @else
                                        <span class="text-muted small">-</span>
                                    @endif
                                </td>
                                <td class="py-2 text-end font-monospace text-secondary">{{ (float)$item->quantity }}</td>
                                <td class="py-2 text-end font-monospace text-secondary">Rp {{ number_format($item->unit_price, 0, ',', '.') }}</td>
                                <td class="py-2 text-end font-monospace text-secondary">Rp {{ number_format($item->parts_cost, 0, ',', '.') }}</td>
                                <td class="py-2 text-end font-monospace text-secondary">Rp {{ number_format($item->labor_cost, 0, ',', '.') }}</td>
                                <td class="pe-3 py-2 text-end font-monospace fw-bold text-dark">
                                    Rp {{ number_format($item->totalCost(), 0, ',', '.') }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="table-light fw-bold small">
                        <tr>
                            <td colspan="4" class="ps-3 py-2 text-end text-secondary">Total:</td>
                            <td class="py-2 text-end font-monospace text-dark">Rp {{ number_format($record->total_parts_cost, 0, ',', '.') }}</td>
                            <td class="py-2 text-end font-monospace text-dark">Rp {{ number_format($record->total_labor_cost, 0, ',', '.') }}</td>
                            <td class="pe-3 py-2 text-end font-monospace text-success fs-6">Rp {{ number_format($record->total_cost, 0, ',', '.') }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            @if($record->notes)
                <div class="mt-3 alert alert-light py-2 px-3 small border mb-0">
                    <span class="fw-semibold text-secondary">Catatan Servis:</span> {{ $record->notes }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
