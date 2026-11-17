<?php

namespace App\Http\Controllers;

use App\Models\FuelTransaction;
use App\Models\FuelType;
use App\Models\Vehicle;
use Illuminate\Http\Request;

class FuelTransactionController extends Controller
{
    // ── Index ────────────────────────────────────────────────────────────────

    public function index(Request $request)
    {
        $userVehicleIds = auth()->user()->vehicles()->pluck('id');

        $query = FuelTransaction::with(['vehicle', 'fuelType'])
            ->whereIn('vehicle_id', $userVehicleIds)
            ->orderBy('transaction_at', 'desc');

        if ($request->filled('vehicle_id')) {
            $query->where('vehicle_id', $request->vehicle_id);
        }

        $fuelTransactions = $query->paginate(15)->withQueryString();

        $vehicles = auth()->user()->vehicles()->orderBy('name')->get();

        return view('fuel-transactions.index', compact('fuelTransactions', 'vehicles'));
    }

    // ── Create ───────────────────────────────────────────────────────────────

    public function create()
    {
        $vehicles  = auth()->user()->vehicles()->orderBy('name')->get();
        $fuelTypes = FuelType::where('is_active', true)->orderBy('name')->get();

        return view('fuel-transactions.create', compact('vehicles', 'fuelTypes'));
    }

    // ── Store ────────────────────────────────────────────────────────────────

    public function store(Request $r)
    {
        $data = $r->validate($this->rules());

        // Pastikan kendaraan milik user
        $this->authorizeVehicle($data['vehicle_id']);

        $data['recorded_at']   = now();
        $data['volume_liter']  = round($data['total_amount'] / $data['price_per_liter'], 3);

        FuelTransaction::create($data);

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

        $vehicles  = auth()->user()->vehicles()->orderBy('name')->get();
        $fuelTypes = FuelType::where('is_active', true)->orderBy('name')->get();

        return view('fuel-transactions.edit', compact('fuelTransaction', 'vehicles', 'fuelTypes'));
    }

    // ── Update ───────────────────────────────────────────────────────────────

    public function update(Request $r, FuelTransaction $fuelTransaction)
    {
        $this->authorizeOwner($fuelTransaction);

        $data = $r->validate($this->rules());

        $this->authorizeVehicle($data['vehicle_id']);

        $data['volume_liter'] = round($data['total_amount'] / $data['price_per_liter'], 3);

        $fuelTransaction->update($data);

        return redirect()->route('fuel-transactions.index')
            ->with('success', 'Transaksi BBM berhasil diperbarui.');
    }

    // ── Destroy ──────────────────────────────────────────────────────────────

    public function destroy(FuelTransaction $fuelTransaction)
    {
        $this->authorizeOwner($fuelTransaction);

        $fuelTransaction->delete();

        return redirect()->route('fuel-transactions.index')
            ->with('success', 'Transaksi BBM berhasil dihapus.');
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    private function authorizeOwner(FuelTransaction $ft): void
    {
        $userVehicleIds = auth()->user()->vehicles()->pluck('id');
        if (! $userVehicleIds->contains($ft->vehicle_id)) {
            abort(403);
        }
    }

    private function authorizeVehicle(int $vehicleId): void
    {
        $userVehicleIds = auth()->user()->vehicles()->pluck('id');
        if (! $userVehicleIds->contains($vehicleId)) {
            abort(403);
        }
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
