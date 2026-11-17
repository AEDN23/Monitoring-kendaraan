@extends('layouts.app')

@section('title', 'Jadwal Perawatan')
@section('header', 'Jadwal Perawatan Berkala')

@section('content')
<div class="space-y-6">

    @if(!$vehicle)
        <div class="rounded-xl border border-gray-200 bg-white p-12 text-center">
            <h3 class="text-base font-semibold text-gray-900">Belum ada kendaraan terdaftar</h3>
            <p class="mt-1 text-sm text-gray-500">Silakan tambahkan kendaraan terlebih dahulu untuk mengatur jadwal perawatan.</p>
            <div class="mt-4">
                <a href="{{ route('vehicles.create') }}" class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700">
                    Tambah Kendaraan
                </a>
            </div>
        </div>
    @else
        {{-- ── Switcher Kendaraan & Tombol Tambah ─────────────────── --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-3">
                <label for="vehicle_select" class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Kendaraan:</label>
                <select id="vehicle_select" onchange="window.location.href='/schedules/' + this.value"
                        class="rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm font-medium shadow-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
                    @foreach ($vehicles as $v)
                        <option value="{{ $v->id }}" {{ $v->id === $vehicle->id ? 'selected' : '' }}>
                            {{ $v->name }} ({{ number_format($v->latestOdometerKm() ?? 0, 0, ',', '.') }} KM)
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-center gap-2">
                <a href="{{ route('maintenance-records.create', ['vehicle_id' => $vehicle->id]) }}"
                   class="rounded-lg border border-emerald-300 bg-emerald-50 px-3 py-2 text-sm font-medium text-emerald-700 hover:bg-emerald-100 transition">
                    + Catat Servis Selesai
                </a>
                <a href="{{ route('schedules.create', $vehicle) }}"
                   class="inline-flex items-center gap-1.5 rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Tambah Jadwal Komponen
                </a>
            </div>
        </div>

        {{-- ── Info Banner Odometer ────────────────────────────────── --}}
        <div class="bg-indigo-50 border border-indigo-100 rounded-xl p-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
            <div>
                <span class="text-xs text-indigo-700 font-semibold uppercase tracking-wider">Status Acuan Odometer</span>
                <p class="text-xs text-indigo-600 mt-0.5">Status jadwal dihitung dari kilometer terkini dibandingkan target interval.</p>
            </div>
            <div class="text-right">
                <span class="text-xs text-indigo-500">Odometer Terkini:</span>
                <span class="font-bold text-indigo-900 font-mono text-base ml-1">{{ number_format($currentKm, 1, ',', '.') }} KM</span>
            </div>
        </div>

        {{-- ── Daftar Jadwal Komponen ──────────────────────────────── --}}
        @if($schedules->isEmpty())
            <div class="rounded-xl border border-dashed border-gray-200 bg-white p-12 text-center">
                <h3 class="text-base font-semibold text-gray-900">Belum ada komponen yang dijadwalkan</h3>
                <p class="mt-1 text-sm text-gray-500">Tambahkan jadwal servis rutin seperti oli mesin, kampas rem, filter udara, dll.</p>
                <div class="mt-4">
                    <a href="{{ route('schedules.create', $vehicle) }}" class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700">
                        Tambah Jadwal Sekarang
                    </a>
                </div>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                @foreach ($schedules as $sched)
                    @php
                        $badge = match($sched->computed_status) {
                            'overdue'          => ['bg' => 'bg-rose-50 text-rose-700 border-rose-200', 'label' => 'Terlambat!'],
                            'soon'             => ['bg' => 'bg-amber-50 text-amber-700 border-amber-200', 'label' => 'Segera Servis'],
                            'needs_inspection' => ['bg' => 'bg-sky-50 text-sky-700 border-sky-200', 'label' => 'Perlu Inspeksi'],
                            default            => ['bg' => 'bg-emerald-50 text-emerald-700 border-emerald-200', 'label' => 'Kondisi Aman'],
                        };
                        $nextKm = $sched->nextDueKm();
                        $nextDate = $sched->nextDueDate();
                    @endphp
                    <div class="bg-white rounded-xl border border-gray-200 p-5 hover:shadow-sm transition flex flex-col justify-between">
                        <div>
                            <div class="flex items-start justify-between">
                                <div>
                                    <span class="text-2xs font-semibold uppercase tracking-wider text-gray-400">
                                        {{ $sched->maintenanceItem->category ?? 'Komponen' }}
                                    </span>
                                    <h3 class="text-base font-bold text-gray-900">{{ $sched->maintenanceItem->name ?? '-' }}</h3>
                                </div>
                                <span class="rounded-full border px-2 py-0.5 text-xs font-semibold {{ $badge['bg'] }}">
                                    {{ $badge['label'] }}
                                </span>
                            </div>

                            {{-- Parameter Interval --}}
                            <div class="mt-4 space-y-2 text-xs">
                                <div class="flex justify-between text-gray-600">
                                    <span>Interval Ditetapkan:</span>
                                    <span class="font-medium text-gray-900">
                                        @if($sched->interval_km) {{ number_format($sched->interval_km, 0, ',', '.') }} KM @endif
                                        @if($sched->interval_km && $sched->interval_days) / @endif
                                        @if($sched->interval_days) {{ $sched->interval_days }} hari @endif
                                        @if(!$sched->interval_km && !$sched->interval_days) Cek Manual @endif
                                    </span>
                                </div>

                                <div class="flex justify-between text-gray-600">
                                    <span>Servis Terakhir:</span>
                                    <span class="font-mono text-gray-800">
                                        @if($sched->last_service_odometer_km !== null)
                                            {{ number_format($sched->last_service_odometer_km, 1, ',', '.') }} KM
                                        @else
                                            Belum tercatat
                                        @endif
                                        @if($sched->last_service_at)
                                            <span class="block text-right font-sans text-gray-400 text-2xs">
                                                {{ $sched->last_service_at->format('d/m/Y') }}
                                            </span>
                                        @endif
                                    </span>
                                </div>

                                <div class="pt-2 border-t border-gray-100 flex justify-between font-semibold">
                                    <span class="text-gray-700">Target Jatuh Tempo:</span>
                                    <span class="font-mono text-indigo-600 text-right">
                                        @if($nextKm)
                                            {{ number_format($nextKm, 1, ',', '.') }} KM
                                        @endif
                                        @if($nextDate)
                                            <span class="block font-sans text-xs text-gray-500">
                                                {{ $nextDate->format('d/m/Y') }}
                                            </span>
                                        @endif
                                        @if(!$nextKm && !$nextDate)
                                            Berdasarkan fisik
                                        @endif
                                    </span>
                                </div>
                            </div>
                        </div>

                        {{-- Action Buttons --}}
                        <div class="mt-5 pt-3 border-t border-gray-100 flex items-center justify-between text-xs">
                            <div class="flex items-center gap-2">
                                <a href="{{ route('schedules.edit', [$vehicle, $sched]) }}" class="text-gray-500 hover:text-gray-700 font-medium">
                                    Edit
                                </a>
                                <form action="{{ route('schedules.destroy', [$vehicle, $sched]) }}" method="POST"
                                      onsubmit="return confirm('Hapus jadwal ini?');" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-rose-600 hover:text-rose-800 font-medium">
                                        Hapus
                                    </button>
                                </form>
                            </div>

                            <a href="{{ route('maintenance-records.create', ['vehicle_id' => $vehicle->id, 'item_id' => $sched->maintenance_item_id]) }}"
                               class="rounded bg-indigo-50 px-2.5 py-1 text-indigo-700 hover:bg-indigo-100 font-medium">
                                + Servis Item Ini
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    @endif

</div>
@endsection
