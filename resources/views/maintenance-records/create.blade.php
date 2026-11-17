@extends('layouts.app')

@section('title', 'Catat Servis / Perawatan')
@section('header', 'Catat Servis & Perawatan')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('maintenance-records.index') }}" class="text-gray-400 hover:text-gray-600 transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Catat Riwayat Servis</h1>
            <p class="text-sm text-gray-500 mt-0.5">Satu kunjungan servis dapat mencakup beberapa pekerjaan & penggantian suku cadang</p>
        </div>
    </div>

    @if ($errors->any())
        <div class="mb-6 rounded-lg bg-rose-50 border border-rose-200 p-4 text-xs text-rose-800">
            <p class="font-bold mb-1">Periksa kembali data yang diisi:</p>
            <ul class="list-disc pl-4 space-y-0.5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <form action="{{ route('maintenance-records.store') }}" method="POST" id="serviceForm">
            @csrf

            {{-- ── Data Umum Servis ─────────────────────────────────── --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 pb-6 border-b border-gray-100">
                <div>
                    <label for="vehicle_id" class="block text-xs font-semibold text-gray-700 uppercase tracking-wider">Kendaraan <span class="text-rose-500">*</span></label>
                    <select name="vehicle_id" id="vehicle_id"
                            class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500"
                            required>
                        <option value="">Pilih Kendaraan</option>
                        @foreach ($vehicles as $v)
                            <option value="{{ $v->id }}" {{ old('vehicle_id', $selectedVehicleId) == $v->id ? 'selected' : '' }}>
                                {{ $v->name }} {{ $v->license_plate ? "({$v->license_plate})" : '' }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="service_at" class="block text-xs font-semibold text-gray-700 uppercase tracking-wider">Tanggal Servis <span class="text-rose-500">*</span></label>
                    <input type="date" name="service_at" id="service_at"
                           value="{{ old('service_at', date('Y-m-d')) }}"
                           class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500"
                           required>
                </div>

                <div>
                    <label for="odometer_km" class="block text-xs font-semibold text-gray-700 uppercase tracking-wider">Kilometer Servis <span class="text-rose-500">*</span></label>
                    <input type="number" step="0.1" name="odometer_km" id="odometer_km"
                           value="{{ old('odometer_km') }}"
                           placeholder="KM saat di bengkel"
                           class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm font-mono shadow-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500"
                           required>
                </div>

                <div>
                    <label for="workshop_name" class="block text-xs font-semibold text-gray-700 uppercase tracking-wider">Nama Bengkel / Tempat</label>
                    <input type="text" name="workshop_name" id="workshop_name"
                           value="{{ old('workshop_name') }}"
                           placeholder="Contoh: Ahass Dago, Bengkel Mandiri"
                           class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
                </div>
            </div>

            {{-- ── Rincian Komponen / Barang & Jasa (Multi-item Dinamis) ── --}}
            <div class="mt-6">
                <div class="flex items-center justify-between mb-3">
                    <div>
                        <h2 class="text-sm font-bold text-gray-900">Rincian Komponen, Barang & Biaya Jasa</h2>
                        <p class="text-xs text-gray-500">Setiap item yang terhubung dengan master komponen akan mereset jadwal perawatannya secara otomatis.</p>
                    </div>
                    <button type="button" id="addItemBtn"
                            class="inline-flex items-center gap-1.5 rounded-lg bg-indigo-50 px-3 py-1.5 text-xs font-semibold text-indigo-700 hover:bg-indigo-100 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Tambah Baris Item
                    </button>
                </div>

                <div class="overflow-x-auto rounded-lg border border-gray-200">
                    <table class="min-w-full divide-y divide-gray-200 text-left text-xs" id="itemsTable">
                        <thead class="bg-gray-50">
                            <tr class="text-gray-500 uppercase tracking-wider font-semibold">
                                <th class="py-2.5 px-3 min-w-[180px]">Nama Pekerjaan / Barang <span class="text-rose-500">*</span></th>
                                <th class="py-2.5 px-3 min-w-[160px]">Tautkan Komponen</th>
                                <th class="py-2.5 px-3 w-20 text-right">Qty</th>
                                <th class="py-2.5 px-3 w-28 text-right">Harga Satuan</th>
                                <th class="py-2.5 px-3 w-28 text-right">Total Barang</th>
                                <th class="py-2.5 px-3 w-28 text-right">Biaya Jasa</th>
                                <th class="py-2.5 px-3 w-10 text-center"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100" id="itemsContainer">
                            {{-- Baris pertama default --}}
                            <tr class="item-row">
                                <td class="p-2">
                                    <input type="text" name="items[0][item_name]"
                                           placeholder="Contoh: Ganti Oli Mesin MPX1"
                                           class="w-full rounded border border-gray-300 px-2 py-1.5 text-xs focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500" required>
                                </td>
                                <td class="p-2">
                                    <select name="items[0][maintenance_item_id]" class="item-select w-full rounded border border-gray-300 px-2 py-1.5 text-xs focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
                                        <option value="">(Tidak ditautkan)</option>
                                        @foreach ($maintenanceItems as $mi)
                                            <option value="{{ $mi->id }}">{{ $mi->name }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td class="p-2">
                                    <input type="number" step="0.1" name="items[0][quantity]" value="1" min="0.1"
                                           class="item-qty w-full rounded border border-gray-300 px-2 py-1.5 text-xs text-right font-mono focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500" required>
                                </td>
                                <td class="p-2">
                                    <input type="number" step="500" name="items[0][unit_price]" value="0" min="0"
                                           class="item-price w-full rounded border border-gray-300 px-2 py-1.5 text-xs text-right font-mono focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500" required>
                                </td>
                                <td class="p-2">
                                    <input type="number" step="500" name="items[0][parts_cost]" value="0" min="0" readonly
                                           class="item-parts-cost w-full rounded border border-gray-200 bg-gray-50 px-2 py-1.5 text-xs text-right font-mono text-gray-700">
                                </td>
                                <td class="p-2">
                                    <input type="number" step="500" name="items[0][labor_cost]" value="0" min="0"
                                           class="item-labor-cost w-full rounded border border-gray-300 px-2 py-1.5 text-xs text-right font-mono focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500" required>
                                </td>
                                <td class="p-2 text-center">
                                    <button type="button" class="remove-row-btn text-gray-400 hover:text-rose-600 transition" title="Hapus baris">
                                        &times;
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                        <tfoot class="bg-gray-50 font-semibold text-xs border-t border-gray-200">
                            <tr>
                                <td colspan="4" class="py-2.5 px-3 text-right text-gray-700">Subtotal:</td>
                                <td class="py-2.5 px-3 text-right font-mono text-gray-900" id="totalPartsLabel">Rp 0</td>
                                <td class="py-2.5 px-3 text-right font-mono text-gray-900" id="totalLaborLabel">Rp 0</td>
                                <td></td>
                            </tr>
                            <tr class="bg-indigo-50/60 font-bold text-sm text-indigo-950">
                                <td colspan="4" class="py-3 px-3 text-right">Grand Total Biaya Servis:</td>
                                <td colspan="2" class="py-3 px-3 text-right font-mono text-base text-indigo-700" id="grandTotalLabel">Rp 0</td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            {{-- Catatan --}}
            <div class="mt-6">
                <label for="notes" class="block text-xs font-semibold text-gray-700 uppercase tracking-wider">Catatan Tambahan</label>
                <textarea name="notes" id="notes" rows="2"
                          placeholder="Catatan dari mekanik, garansi pekerjaan, dll."
                          class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">{{ old('notes') }}</textarea>
            </div>

            <div class="flex items-center gap-3 mt-6 pt-6 border-t border-gray-100">
                <button type="submit"
                        class="bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium px-6 py-2.5 rounded-lg transition">
                    Simpan Riwayat Servis
                </button>
                <a href="{{ route('maintenance-records.index') }}"
                   class="text-gray-600 hover:text-gray-800 text-sm font-medium px-4 py-2.5 rounded-lg border border-gray-200 hover:border-gray-300 transition">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        let rowCount = 1;
        const container = document.getElementById('itemsContainer');
        const addBtn    = document.getElementById('addItemBtn');

        const itemsOptions = `
            <option value="">(Tidak ditautkan)</option>
            @foreach ($maintenanceItems as $mi)
                <option value="{{ $mi->id }}">{{ $mi->name }}</option>
            @endforeach
        `;

        function calculateTotals() {
            let totalParts = 0;
            let totalLabor = 0;

            document.querySelectorAll('.item-row').forEach(row => {
                const qty       = parseFloat(row.querySelector('.item-qty').value) || 0;
                const unitPrice = parseFloat(row.querySelector('.item-price').value) || 0;
                const partsCostInput = row.querySelector('.item-parts-cost');
                const laborCostInput = row.querySelector('.item-labor-cost');

                const partsCost = Math.round(qty * unitPrice);
                partsCostInput.value = partsCost;

                const laborCost = parseFloat(laborCostInput.value) || 0;

                totalParts += partsCost;
                totalLabor += laborCost;
            });

            document.getElementById('totalPartsLabel').textContent = 'Rp ' + totalParts.toLocaleString('id-ID');
            document.getElementById('totalLaborLabel').textContent = 'Rp ' + totalLabor.toLocaleString('id-ID');
            document.getElementById('grandTotalLabel').textContent = 'Rp ' + (totalParts + totalLabor).toLocaleString('id-ID');
        }

        container.addEventListener('input', function (e) {
            if (e.target.matches('.item-qty, .item-price, .item-labor-cost')) {
                calculateTotals();
            }
        });

        container.addEventListener('click', function (e) {
            if (e.target.matches('.remove-row-btn')) {
                const rows = container.querySelectorAll('.item-row');
                if (rows.length > 1) {
                    e.target.closest('tr').remove();
                    calculateTotals();
                } else {
                    alert('Minimal harus ada 1 item pekerjaan/suku cadang.');
                }
            }
        });

        addBtn.addEventListener('click', function () {
            const index = rowCount++;
            const tr = document.createElement('tr');
            tr.className = 'item-row';
            tr.innerHTML = `
                <td class="p-2">
                    <input type="text" name="items[${index}][item_name]"
                           placeholder="Contoh: Kampas Rem Depan"
                           class="w-full rounded border border-gray-300 px-2 py-1.5 text-xs focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500" required>
                </td>
                <td class="p-2">
                    <select name="items[${index}][maintenance_item_id]" class="item-select w-full rounded border border-gray-300 px-2 py-1.5 text-xs focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
                        ${itemsOptions}
                    </select>
                </td>
                <td class="p-2">
                    <input type="number" step="0.1" name="items[${index}][quantity]" value="1" min="0.1"
                           class="item-qty w-full rounded border border-gray-300 px-2 py-1.5 text-xs text-right font-mono focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500" required>
                </td>
                <td class="p-2">
                    <input type="number" step="500" name="items[${index}][unit_price]" value="0" min="0"
                           class="item-price w-full rounded border border-gray-300 px-2 py-1.5 text-xs text-right font-mono focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500" required>
                </td>
                <td class="p-2">
                    <input type="number" step="500" name="items[${index}][parts_cost]" value="0" min="0" readonly
                           class="item-parts-cost w-full rounded border border-gray-200 bg-gray-50 px-2 py-1.5 text-xs text-right font-mono text-gray-700">
                </td>
                <td class="p-2">
                    <input type="number" step="500" name="items[${index}][labor_cost]" value="0" min="0"
                           class="item-labor-cost w-full rounded border border-gray-300 px-2 py-1.5 text-xs text-right font-mono focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500" required>
                </td>
                <td class="p-2 text-center">
                    <button type="button" class="remove-row-btn text-gray-400 hover:text-rose-600 transition" title="Hapus baris">
                        &times;
                    </button>
                </td>
            `;
            container.appendChild(tr);
            calculateTotals();
        });

        calculateTotals();
    });
</script>
@endsection
