@extends('layouts.app')

@section('title', 'Master Komponen Perawatan')
@section('header', 'Master Komponen Perawatan')

@section('content')
<div class="container-fluid p-0">

    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
        <div>
            <h4 class="fw-bold text-dark mb-0">Master Komponen Perawatan</h4>
            <div class="text-secondary small">Daftar standar komponen servis, interval bawaan pabrikan, dan metode pemantauan</div>
        </div>
        <a href="{{ route('maintenance-items.create') }}" class="btn btn-primary d-inline-flex align-items-center gap-2">
            <i class="bi bi-plus-lg"></i>
            <span>Tambah Komponen</span>
        </a>
    </div>

    <div class="card border-0 shadow-sm rounded-3 overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr class="small text-secondary text-uppercase">
                        <th class="ps-3 py-3">Nama Komponen</th>
                        <th class="py-3">Kategori</th>
                        <th class="py-3">Mode Pantau</th>
                        <th class="py-3">Interval Default</th>
                        <th class="py-3">Untuk Kendaraan</th>
                        <th class="py-3">Status</th>
                        <th class="pe-3 py-3 text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($items as $item)
                        @php
                            $modeBadge = match($item->tracking_mode) {
                                'km'         => ['class' => 'bg-primary bg-opacity-10 text-primary', 'label' => 'Berdasarkan KM'],
                                'time'       => ['class' => 'bg-purple bg-opacity-10 text-dark', 'label' => 'Berdasarkan Waktu'],
                                'condition'  => ['class' => 'bg-warning bg-opacity-10 text-dark', 'label' => 'Cek Kondisi'],
                                default      => ['class' => 'bg-info bg-opacity-10 text-dark', 'label' => 'KM / Waktu'],
                            };
                            $appliesLabel = match($item->applies_to) {
                                'motorcycle' => 'Motor Saja',
                                'car'        => 'Mobil Saja',
                                'both'       => 'Motor & Mobil',
                                default      => 'Lainnya',
                            };
                        @endphp
                        <tr>
                            <td class="ps-3 py-3">
                                <div class="fw-semibold text-dark">{{ $item->name }}</div>
                                @if($item->description)
                                    <div class="text-secondary small" style="font-size: 0.75rem;">{{ $item->description }}</div>
                                @endif
                            </td>
                            <td class="py-3 text-secondary">
                                {{ $item->category ?? '-' }}
                            </td>
                            <td class="py-3">
                                <span class="badge {{ $modeBadge['class'] }} px-2 py-1">
                                    {{ $modeBadge['label'] }}
                                </span>
                            </td>
                            <td class="py-3 font-monospace text-secondary small">
                                @if($item->default_interval_km) {{ number_format($item->default_interval_km, 0, ',', '.') }} KM @endif
                                @if($item->default_interval_km && $item->default_interval_days) / @endif
                                @if($item->default_interval_days) {{ $item->default_interval_days }} hari @endif
                                @if(!$item->default_interval_km && !$item->default_interval_days) - @endif
                            </td>
                            <td class="py-3 text-secondary small">
                                {{ $appliesLabel }}
                            </td>
                            <td class="py-3">
                                @if($item->is_active)
                                    <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-2 py-1">Aktif</span>
                                @else
                                    <span class="badge bg-secondary bg-opacity-10 text-secondary px-2 py-1">Nonaktif</span>
                                @endif
                            </td>
                            <td class="pe-3 py-3 text-end">
                                <div class="d-inline-flex align-items-center gap-2">
                                    <a href="{{ route('maintenance-items.edit', $item) }}" class="btn btn-sm btn-light py-1 px-2 text-primary">
                                        Edit
                                    </a>
                                    <form action="{{ route('maintenance-items.destroy', $item) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-light py-1 px-2 text-secondary">
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
            <div class="p-3 border-top">
                {{ $items->links('pagination::bootstrap-5') }}
            </div>
        @endif
    </div>

</div>
@endsection
