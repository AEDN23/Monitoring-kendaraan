<?php

namespace App\Http\Controllers;

use App\Models\FuelTransaction;
use App\Models\FuelType;
use App\Models\Vehicle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class FuelTransactionController extends Controller
{
    // ── Index ────────────────────────────────────────────────────────────────

    public function index(Request $request)
    {
        $vehicles       = $this->cachedUserVehicles();
        $userVehicleIds = $vehicles->pluck('id');

        if ($userVehicleIds->isEmpty()) {
            $fuelTransactions = FuelTransaction::whereRaw('1=0')->paginate(15);
            return view('fuel-transactions.index', compact('fuelTransactions', 'vehicles'));
        }

        $query = FuelTransaction::with(['vehicle', 'fuelType'])
            ->whereIn('vehicle_id', $userVehicleIds)
            ->orderBy('transaction_at', 'desc');

        $selectedVehicleId = $request->has('vehicle_id')
            ? $request->input('vehicle_id')
            : session('active_vehicle_id');

        if ($selectedVehicleId && $selectedVehicleId !== 'all') {
            $query->where('vehicle_id', $selectedVehicleId);
        }

        $fuelTransactions = $query->paginate(15)->withQueryString();

        return view('fuel-transactions.index', compact('fuelTransactions', 'vehicles', 'selectedVehicleId'));
    }

    // ── Create ───────────────────────────────────────────────────────────────

    public function create(Request $request)
    {
        $vehicles  = $this->cachedUserVehicles();
        $fuelTypes = $this->cachedFuelTypes();
        $selectedVehicleId = $request->input('vehicle_id', session('active_vehicle_id'));
        $currentVehicle = $vehicles->firstWhere('id', $selectedVehicleId) ?? $vehicles->first();

        return view('fuel-transactions.create', compact('vehicles', 'fuelTypes', 'selectedVehicleId', 'currentVehicle'));
    }

    // ── Store ────────────────────────────────────────────────────────────────

    public function store(Request $r)
    {
        // Bersihkan titik ribuan dari input jika ada
        if ($r->has('total_amount')) {
            $r->merge([
                'total_amount'    => str_replace('.', '', (string) $r->total_amount),
                'price_per_liter' => str_replace('.', '', (string) $r->price_per_liter),
                'odometer_km'     => str_replace(['.', ','], ['', '.'], (string) $r->odometer_km),
            ]);
        }

        $data = $r->validate($this->rules());

        $this->authorizeVehicle($data['vehicle_id']);

        $data['recorded_at']   = now();
        $data['volume_liter']  = round($data['total_amount'] / $data['price_per_liter'], 3);

        FuelTransaction::create($data);

        // Clear kendaraan cache agar odometer terbaru muncul
        $this->clearVehicleCache();

        return redirect()->route('fuel-transactions.index')
            ->with('success', 'Transaksi BBM berhasil dicatat.');
    }

    // ── Show ─────────────────────────────────────────────────────────────────

    public function show(FuelTransaction $fuelTransaction)
    {
        $this->authorizeOwner($fuelTransaction);

        $fuelTransaction->load(['vehicle', 'fuelType']);

        return view('fuel-transactions.show', compact('fuelTransaction'));
    }

    // ── Edit ─────────────────────────────────────────────────────────────────

    public function edit(FuelTransaction $fuelTransaction)
    {
        $this->authorizeOwner($fuelTransaction);

        $vehicles  = $this->cachedUserVehicles();
        $fuelTypes = $this->cachedFuelTypes();

        return view('fuel-transactions.edit', compact('fuelTransaction', 'vehicles', 'fuelTypes'));
    }

    // ── Update ───────────────────────────────────────────────────────────────

    public function update(Request $r, FuelTransaction $fuelTransaction)
    {
        $this->authorizeOwner($fuelTransaction);

        // Bersihkan titik ribuan dari input jika ada
        if ($r->has('total_amount')) {
            $r->merge([
                'total_amount'    => str_replace('.', '', (string) $r->total_amount),
                'price_per_liter' => str_replace('.', '', (string) $r->price_per_liter),
                'odometer_km'     => str_replace(['.', ','], ['', '.'], (string) $r->odometer_km),
            ]);
        }

        $data = $r->validate($this->rules());

        $this->authorizeVehicle($data['vehicle_id']);

        $data['volume_liter'] = round($data['total_amount'] / $data['price_per_liter'], 3);

        $fuelTransaction->update($data);

        $this->clearVehicleCache();

        return redirect()->route('fuel-transactions.index')
            ->with('success', 'Transaksi BBM berhasil diperbarui.');
    }

    // ── Destroy ──────────────────────────────────────────────────────────────

    public function destroy(FuelTransaction $fuelTransaction)
    {
        $this->authorizeOwner($fuelTransaction);

        $fuelTransaction->delete();

        $this->clearVehicleCache();

        return redirect()->route('fuel-transactions.index')
            ->with('success', 'Transaksi BBM berhasil dihapus.');
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    private function authorizeOwner(FuelTransaction $ft): void
    {
        $userVehicleIds = $this->cachedUserVehicles()->pluck('id');
        if (! $userVehicleIds->contains($ft->vehicle_id)) {
            abort(403);
        }
    }

    private function authorizeVehicle(int $vehicleId): void
    {
        if (! $this->cachedUserVehicles()->contains('id', $vehicleId)) {
            abort(403);
        }
    }

    private function cachedFuelTypes()
    {
        return \App\Models\FuelType::where('is_active', true)->orderBy('name')->get();
    }

    private function rules(): array
    {
        return [
            'vehicle_id'      => ['required', 'exists:vehicles,id'],
            'fuel_type_id'    => ['required', 'exists:fuel_types,id'],
            'transaction_at'  => ['required', 'date'],
            'odometer_km'     => ['required', 'numeric', 'min:0'],
            'total_amount'    => ['required', 'numeric', 'min:0'],
            'price_per_liter' => ['required', 'numeric', 'min:1'],
            'station_name'    => ['nullable', 'string', 'max:150'],
            'notes'           => ['nullable', 'string'],
        ];
    }
}
