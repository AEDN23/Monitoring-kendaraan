<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class VehicleController extends Controller
{
    // ── Index ────────────────────────────────────────────────────────────────

    public function index()
    {
        $vehicles = auth()->user()
            ->vehicles()
            ->withLatestOdometer()
            ->orderBy('name')
            ->paginate(10);

        return view('vehicles.index', compact('vehicles'));
    }

    // ── Create ───────────────────────────────────────────────────────────────

    public function create()
    {
        return view('vehicles.create');
    }

    // ── Store ────────────────────────────────────────────────────────────────

    public function store(Request $r)
    {
        $data = $r->validate($this->rules());

        $data['user_id']    = auth()->id();
        $data['is_active']  = true;

        Vehicle::create($data);

        return redirect()->route('vehicles.index')
            ->with('success', 'Kendaraan berhasil ditambahkan.');
    }

    // ── Show ─────────────────────────────────────────────────────────────────

    public function show(Vehicle $vehicle)
    {
        $this->authorizeOwner($vehicle);

        $vehicle->load([
            'maintenanceSchedules.maintenanceItem',
            'fuelTransactions.fuelType',
            'maintenanceRecords.maintenanceItem',
        ]);

        $currentOdometer = $vehicle->latestOdometerKm();

        // Statistik BBM bulan ini
        $fuelThisMonth = $vehicle->fuelTransactions()
            ->whereMonth('transaction_at', now()->month)
            ->whereYear('transaction_at', now()->year)
            ->selectRaw('SUM(total_amount) as total_cost, SUM(volume_liter) as total_liter')
            ->first();

        // Statistik servis bulan ini
        $serviceThisMonth = $vehicle->maintenanceRecords()
            ->whereMonth('service_at', now()->month)
            ->whereYear('service_at', now()->year)
            ->selectRaw('SUM(total_cost) as total_cost, COUNT(*) as total_count')
            ->first();

        // Jadwal perawatan dengan status
        $schedules = $vehicle->maintenanceSchedules()
            ->with('maintenanceItem')
            ->where('is_active', true)
            ->get()
            ->map(function ($schedule) use ($currentOdometer) {
                $schedule->computed_status   = $schedule->status((float) $currentOdometer);
                $schedule->computed_next_km  = $schedule->nextDueKm();
                $schedule->computed_next_date = $schedule->nextDueDate();
                return $schedule;
            });

        // 5 Transaksi BBM terakhir
        $lastFuelTransactions = $vehicle->fuelTransactions()
            ->with('fuelType')
            ->orderBy('transaction_at', 'desc')
            ->orderBy('id', 'desc')
            ->limit(5)
            ->get();

        // 5 Servis terakhir
        $lastMaintenanceRecords = $vehicle->maintenanceRecords()
            ->with('maintenanceItem', 'items')
            ->orderBy('service_at', 'desc')
            ->orderBy('id', 'desc')
            ->limit(5)
            ->get();

        return view('vehicles.show', compact(
            'vehicle',
            'currentOdometer',
            'fuelThisMonth',
            'serviceThisMonth',
            'schedules',
            'lastFuelTransactions',
            'lastMaintenanceRecords',
        ));
    }

    // ── Edit ─────────────────────────────────────────────────────────────────

    public function edit(Vehicle $vehicle)
    {
        $this->authorizeOwner($vehicle);

        return view('vehicles.edit', compact('vehicle'));
    }

    // ── Update ───────────────────────────────────────────────────────────────

    public function update(Request $r, Vehicle $vehicle)
    {
        $this->authorizeOwner($vehicle);

        $data = $r->validate($this->rules());

        $vehicle->update($data);

        return redirect()->route('vehicles.index')
            ->with('success', 'Kendaraan berhasil diperbarui.');
    }

    // ── Destroy ──────────────────────────────────────────────────────────────

    public function destroy(Vehicle $vehicle)
    {
        $this->authorizeOwner($vehicle);

        $vehicle->delete();

        return redirect()->route('vehicles.index')
            ->with('success', 'Kendaraan berhasil dihapus.');
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    private function authorizeOwner(Vehicle $vehicle): void
    {
        if ($vehicle->user_id !== auth()->id()) {
            abort(403);
        }
    }

    private function rules(): array
    {
        return [
            'name'                  => ['required', 'string', 'max:100'],
            'vehicle_type'          => ['required', 'in:motorcycle,car,other'],
            'brand'                 => ['nullable', 'string', 'max:100'],
            'model'                 => ['nullable', 'string', 'max:100'],
            'production_year'       => ['nullable', 'integer', 'min:1900', 'max:' . (date('Y') + 1)],
            'license_plate'         => ['nullable', 'string', 'max:20'],
            'initial_odometer_km'   => ['required', 'numeric', 'min:0'],
            'initial_odometer_date' => ['required', 'date'],
            'notes'                 => ['nullable', 'string'],
        ];
    }
}
