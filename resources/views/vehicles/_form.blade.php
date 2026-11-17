<div class="space-y-4">
    {{-- Nama Kendaraan & Tipe --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
            <label for="name" class="block text-sm font-medium text-gray-700">Nama Panggilan Kendaraan <span class="text-rose-500">*</span></label>
            <input type="text" name="name" id="name"
                   value="{{ old('name', $vehicle->name ?? '') }}"
                   placeholder="Contoh: Vario Harian, Mobil Keluarga"
                   class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 @error('name') border-rose-500 @enderror"
                   required>
            @error('name')
                <p class="mt-1 text-xs text-rose-500">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="vehicle_type" class="block text-sm font-medium text-gray-700">Jenis Kendaraan <span class="text-rose-500">*</span></label>
            <select name="vehicle_type" id="vehicle_type"
                    class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 @error('vehicle_type') border-rose-500 @enderror"
                    required>
                <option value="">Pilih Jenis</option>
                <option value="motorcycle" {{ old('vehicle_type', $vehicle->vehicle_type ?? '') === 'motorcycle' ? 'selected' : '' }}>Sepeda Motor</option>
                <option value="car" {{ old('vehicle_type', $vehicle->vehicle_type ?? '') === 'car' ? 'selected' : '' }}>Mobil</option>
                <option value="other" {{ old('vehicle_type', $vehicle->vehicle_type ?? '') === 'other' ? 'selected' : '' }}>Lainnya</option>
            </select>
            @error('vehicle_type')
                <p class="mt-1 text-xs text-rose-500">{{ $message }}</p>
            @enderror
        </div>
    </div>

    {{-- Merek & Model --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
            <label for="brand" class="block text-sm font-medium text-gray-700">Merek</label>
            <input type="text" name="brand" id="brand"
                   value="{{ old('brand', $vehicle->brand ?? '') }}"
                   placeholder="Contoh: Honda, Yamaha, Toyota"
                   class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
        </div>

        <div>
            <label for="model" class="block text-sm font-medium text-gray-700">Model / Varian</label>
            <input type="text" name="model" id="model"
                   value="{{ old('model', $vehicle->model ?? '') }}"
                   placeholder="Contoh: Vario 160 CBS, Avanza G"
                   class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
        </div>
    </div>

    {{-- Nomor Polisi & Tahun Produksi --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
            <label for="license_plate" class="block text-sm font-medium text-gray-700">Nomor Polisi (Plat)</label>
            <input type="text" name="license_plate" id="license_plate"
                   value="{{ old('license_plate', $vehicle->license_plate ?? '') }}"
                   placeholder="Contoh: B 1234 XYZ"
                   class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm font-mono uppercase shadow-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
        </div>

        <div>
            <label for="production_year" class="block text-sm font-medium text-gray-700">Tahun Produksi</label>
            <input type="number" name="production_year" id="production_year"
                   value="{{ old('production_year', $vehicle->production_year ?? '') }}"
                   placeholder="Contoh: 2022"
                   min="1950" max="{{ date('Y') + 1 }}"
                   class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
        </div>
    </div>

    {{-- Odometer Awal & Tanggal Berlaku --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
            <label for="initial_odometer_km" class="block text-sm font-medium text-gray-700">Kilometer Awal <span class="text-rose-500">*</span></label>
            <div class="relative mt-1">
                <input type="number" step="0.1" name="initial_odometer_km" id="initial_odometer_km"
                       value="{{ old('initial_odometer_km', $vehicle->initial_odometer_km ?? '0') }}"
                       min="0"
                       class="block w-full rounded-lg border border-gray-300 pr-10 px-3 py-2 text-sm font-mono shadow-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 @error('initial_odometer_km') border-rose-500 @enderror"
                       required>
                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3">
                    <span class="text-xs text-gray-400 font-semibold">KM</span>
                </div>
            </div>
            @error('initial_odometer_km')
                <p class="mt-1 text-xs text-rose-500">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="initial_odometer_date" class="block text-sm font-medium text-gray-700">Tanggal KM Awal Berlaku <span class="text-rose-500">*</span></label>
            <input type="date" name="initial_odometer_date" id="initial_odometer_date"
                   value="{{ old('initial_odometer_date', isset($vehicle) && $vehicle->initial_odometer_date ? $vehicle->initial_odometer_date->format('Y-m-d') : date('Y-m-d')) }}"
                   class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 @error('initial_odometer_date') border-rose-500 @enderror"
                   required>
            @error('initial_odometer_date')
                <p class="mt-1 text-xs text-rose-500">{{ $message }}</p>
            @enderror
        </div>
    </div>

    {{-- Catatan --}}
    <div>
        <label for="notes" class="block text-sm font-medium text-gray-700">Catatan Tambahan</label>
        <textarea name="notes" id="notes" rows="3"
                  placeholder="Contoh: Beli second bulan lalu, oli terakhir diganti pemilik sebelumnya di 12.000 KM"
                  class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">{{ old('notes', $vehicle->notes ?? '') }}</textarea>
    </div>
</div>
