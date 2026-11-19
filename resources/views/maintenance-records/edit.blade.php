@extends('layouts.app')

@section('title', 'Edit Riwayat Servis')
@section('header', 'Edit Riwayat Servis')

@section('content')
<div class="container-fluid p-0" style="max-width: 960px;">
    <div class="d-flex align-items-center gap-3 mb-4">
        <a href="{{ route('maintenance-records.index') }}" class="btn btn-outline-secondary btn-sm rounded-circle d-flex align-items-center justify-content-center" style="width: 34px; height: 34px;">
            <i class="bi bi-arrow-left"></i>
        </a>
        <div>
            <h4 class="fw-bold text-dark mb-0">Edit Riwayat Servis</h4>
            <div class="text-secondary small">Perbarui tanggal, kilometer, maupun rincian item servis</div>
        </div>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger py-2 small mb-4" role="alert">
            <div class="fw-bold mb-1">Periksa kembali data yang diisi:</div>
            <ul class="mb-0 ps-3">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card border-0 shadow-sm rounded-3 p-4">
        <form action="{{ route('maintenance-records.update', $record) }}" method="POST" id="serviceForm">
            @csrf
            @method('PUT')

            {{-- ── Data Umum Servis ─────────────────────────────────── --}}
            <div class="row g-3 pb-4 border-bottom">
                <div class="col-12 col-sm-6 col-lg-3">
                    <label for="vehicle_id" class="form-label small fw-semibold">Kendaraan <span class="text-danger">*</span></label>
                    <select name="vehicle_id" id="vehicle_id" class="form-select form-select-sm" required>
                        @foreach ($vehicles as $v)
                            <option value="{{ $v->id }}" {{ old('vehicle_id', $record->vehicle_id) == $v->id ? 'selected' : '' }}>
                                {{ $v->name }} {{ $v->license_plate ? "({$v->license_plate})" : '' }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-12 col-sm-6 col-lg-3">
                    <label for="service_at" class="form-label small fw-semibold">Tanggal Servis <span class="text-danger">*</span></label>
                    <input type="date" name="service_at" id="service_at"
                           value="{{ old('service_at', $record->service_at ? $record->service_at->format('Y-m-d') : '') }}"
                           class="form-control form-control-sm"
                           required>
                </div>

                <div class="col-12 col-sm-6 col-lg-3">
                    <label for="odometer_km" class="form-label small fw-semibold">Kilometer Servis <span class="text-danger">*</span></label>
                    <div class="input-group input-group-sm">
                        <input type="number" step="any" name="odometer_km" id="odometer_km"
                               value="{{ old('odometer_km', $record->odometer_km) }}"
                               class="form-control font-monospace"
                               required>
                        <span class="input-group-text">KM</span>
                    </div>
                </div>

                <div class="col-12 col-sm-6 col-lg-3">
                    <label for="workshop_name" class="form-label small fw-semibold">Bengkel / Tempat</label>
                    <input type="text" name="workshop_name" id="workshop_name"
                           value="{{ old('workshop_name', $record->workshop_name) }}"
                           class="form-control form-control-sm">
                </div>
            </div>

            {{-- ── Rincian Komponen / Barang & Jasa ─────────────────── --}}
            <div class="mt-4">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
                    <h6 class="fw-bold text-dark mb-0">Rincian Komponen, Barang & Biaya Jasa</h6>
                    <button type="button" id="addItemBtn" class="btn btn-sm btn-outline-primary d-inline-flex align-items-center gap-1">
                        <i class="bi bi-plus-lg"></i>
                        <span>Tambah Baris Item</span>
                    </button>
                </div>

                <div class="table-responsive rounded-3 border">
                    <table class="table table-hover align-middle mb-0" id="itemsTable">
                        <thead class="table-light">
                            <tr class="small text-secondary text-uppercase">
                                <th class="ps-3 py-2" style="min-width: 180px;">Nama Pekerjaan / Barang <span class="text-danger">*</span></th>
                                <th class="py-2" style="min-width: 160px;">Tautkan Komponen</th>
                                <th class="py-2 text-end" style="width: 80px;">Qty</th>
                                <th class="py-2 text-end" style="width: 130px;">Harga Satuan</th>
                                <th class="py-2 text-end" style="width: 130px;">Total Barang</th>
                                <th class="py-2 text-end" style="width: 130px;">Biaya Jasa</th>
                                <th class="pe-3 py-2 text-center" style="width: 40px;"></th>
                            </tr>
                        </thead>
                        <tbody id="itemsContainer">
                            @foreach ($record->items as $idx => $item)
                                <tr class="item-row">
                                    <td class="ps-3 py-2">
                                        <input type="text" name="items[{{ $idx }}][item_name]"
                                               value="{{ old("items.$idx.item_name", $item->item_name) }}"
                                               class="form-control form-control-sm" required>
                                    </td>
                                    <td class="py-2">
                                        <select name="items[{{ $idx }}][maintenance_item_id]" class="form-select form-select-sm item-select">
                                            <option value="">(Tidak ditautkan)</option>
                                            @foreach ($maintenanceItems as $mi)
                                                <option value="{{ $mi->id }}" {{ old("items.$idx.maintenance_item_id", $item->maintenance_item_id) == $mi->id ? 'selected' : '' }}>
                                                    {{ $mi->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td class="py-2">
                                        <input type="number" step="any" name="items[{{ $idx }}][quantity]"
                                               value="{{ old("items.$idx.quantity", (float)$item->quantity) }}" min="0.1"
                                               class="form-control form-control-sm text-end font-monospace item-qty" required>
                                    </td>
                                    <td class="py-2">
                                        <input type="number" step="any" name="items[{{ $idx }}][unit_price]"
                                               value="{{ old("items.$idx.unit_price", (int)$item->unit_price) }}" min="0"
                                               class="form-control form-control-sm text-end font-monospace item-price" required>
                                    </td>
                                    <td class="py-2">
                                        <input type="number" step="any" name="items[{{ $idx }}][parts_cost]"
                                               value="{{ old("items.$idx.parts_cost", (int)$item->parts_cost) }}" min="0" readonly
                                               class="form-control form-control-sm text-end font-monospace bg-light item-parts-cost">
                                    </td>
                                    <td class="py-2">
                                        <input type="number" step="any" name="items[{{ $idx }}][labor_cost]"
                                               value="{{ old("items.$idx.labor_cost", (int)$item->labor_cost) }}" min="0"
                                               class="form-control form-control-sm text-end font-monospace item-labor-cost" required>
                                    </td>
                                    <td class="pe-3 py-2 text-center">
                                        <button type="button" class="btn btn-sm btn-link text-danger p-0 remove-row-btn" title="Hapus baris">
                                            <i class="bi bi-x-circle fs-6"></i>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="table-light fw-semibold small">
                            <tr>
                                <td colspan="4" class="ps-3 py-2 text-end text-secondary">Subtotal:</td>
                                <td class="py-2 text-end font-monospace text-dark" id="totalPartsLabel">Rp 0</td>
                                <td class="py-2 text-end font-monospace text-dark" id="totalLaborLabel">Rp 0</td>
                                <td></td>
                            </tr>
                            <tr class="table-primary fw-bold text-primary">
                                <td colspan="4" class="ps-3 py-2 text-end">Grand Total Biaya Servis:</td>
                                <td colspan="2" class="py-2 text-end font-monospace fs-6" id="grandTotalLabel">Rp 0</td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            {{-- Catatan --}}
            <div class="mt-4">
                <label for="notes" class="form-label small fw-semibold">Catatan Tambahan</label>
                <textarea name="notes" id="notes" rows="2" class="form-control">{{ old('notes', $record->notes) }}</textarea>
            </div>

            <div class="d-flex align-items-center gap-2 mt-4 pt-3 border-top">
                <button type="submit" class="btn btn-primary px-4">
                    Simpan Perubahan
                </button>
                <a href="{{ route('maintenance-records.index') }}" class="btn btn-outline-secondary px-3">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        let rowCount = {{ $record->items->count() }};
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
            const removeBtn = e.target.closest('.remove-row-btn');
            if (removeBtn) {
                const rows = container.querySelectorAll('.item-row');
                if (rows.length > 1) {
                    removeBtn.closest('tr').remove();
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
                <td class="ps-3 py-2">
                    <input type="text" name="items[${index}][item_name]"
                           placeholder="Contoh: Filter Udara Baru"
                           class="form-control form-control-sm" required>
                </td>
                <td class="py-2">
                    <select name="items[${index}][maintenance_item_id]" class="form-select form-select-sm item-select">
                        ${itemsOptions}
                    </select>
                </td>
                <td class="py-2">
                    <input type="number" step="any" name="items[${index}][quantity]" value="1" min="0.1"
                           class="form-control form-control-sm text-end font-monospace item-qty" required>
                </td>
                <td class="py-2">
                    <input type="number" step="any" name="items[${index}][unit_price]" value="0" min="0"
                           class="form-control form-control-sm text-end font-monospace item-price" required>
                </td>
                <td class="py-2">
                    <input type="number" step="any" name="items[${index}][parts_cost]" value="0" min="0" readonly
                           class="form-control form-control-sm text-end font-monospace bg-light item-parts-cost">
                </td>
                <td class="py-2">
                    <input type="number" step="any" name="items[${index}][labor_cost]" value="0" min="0"
                           class="form-control form-control-sm text-end font-monospace item-labor-cost" required>
                </td>
                <td class="pe-3 py-2 text-center">
                    <button type="button" class="btn btn-sm btn-link text-danger p-0 remove-row-btn" title="Hapus baris">
                        <i class="bi bi-x-circle fs-6"></i>
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
