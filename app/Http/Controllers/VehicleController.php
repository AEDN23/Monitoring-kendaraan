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
        // withLatestOdometer tidak di-cache karena pagination perlu fresh count
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

        $data['user_id']   = auth()->id();
        $data['is_active'] = true;

        $vehicle = Vehicle::create($data);

        // Langsung aktifkan kendaraan yang baru dibuat
        session(['active_vehicle_id' => $vehicle->id]);

        $this->clearVehicleCache();

        return redirect()->route('dashboard')
            ->with('success', "Kendaraan {$vehicle->name} berhasil ditambahkan dan aktif.");
    }

    // ── Show ─────────────────────────────────────────────────────────────────

    public function show(Vehicle $vehicle)
    {
        $this->authorizeOwner($vehicle);

        // Load semua relasi dalam satu panggil — satu koneksi
        $vehicle->loadMax('fuelTransactions as max_fuel_odometer', 'odometer_km');
        $vehicle->loadMax('maintenanceRecords as max_service_odometer', 'odometer_km');
        $vehicle->load([
            'maintenanceSchedules.maintenanceItem',
            'fuelTransactions.fuelType',
            'maintenanceRecords',
        ]);

        $currentOdometer = $vehicle->latestOdometerKm();

        $now = now();

        // Statistik BBM & servis bulan ini — dari relasi yang sudah di-load
        $fuelThisMonth = $vehicle->fuelTransactions
            ->where('transaction_at', '>=', $now->copy()->startOfMonth())
            ->where('transaction_at', '<=', $now->copy()->endOfMonth());

        $fuelStats = (object)[
            'total_cost'  => $fuelThisMonth->sum('total_amount'),
            'total_liter' => $fuelThisMonth->sum('volume_liter'),
        ];

        $serviceThisMonth = $vehicle->maintenanceRecords
            ->where('service_at', '>=', $now->copy()->startOfMonth()->toDateString())
            ->where('service_at', '<=', $now->copy()->endOfMonth()->toDateString());

        $serviceStats = (object)[
            'total_cost'  => $serviceThisMonth->sum('total_cost'),
            'total_count' => $serviceThisMonth->count(),
        ];

        // Jadwal perawatan dengan status — in-memory, zero extra queries
        $schedules = $vehicle->maintenanceSchedules
            ->where('is_active', true)
            ->map(function ($schedule) use ($currentOdometer) {
                $schedule->computed_status    = $schedule->status((float) $currentOdometer);
                $schedule->computed_next_km   = $schedule->nextDueKm();
                $schedule->computed_next_date = $schedule->nextDueDate();
                return $schedule;
            });

        // 5 Transaksi BBM & servis terakhir — dari koleksi yang sudah di-load
        $lastFuelTransactions = $vehicle->fuelTransactions
            ->sortByDesc('transaction_at')
            ->take(5);

        $lastMaintenanceRecords = $vehicle->maintenanceRecords
            ->sortByDesc('service_at')
            ->take(5)
            ->load('items');

        return view('vehicles.show', compact(
            'vehicle',
            'currentOdometer',
            'fuelStats',
            'serviceStats',
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

        $this->clearVehicleCache();

        return redirect()->route('vehicles.index')
            ->with('success', 'Kendaraan berhasil diperbarui.');
    }

    // ── Destroy ──────────────────────────────────────────────────────────────

    public function destroy(Vehicle $vehicle)
    {
        $this->authorizeOwner($vehicle);

        if (session('active_vehicle_id') == $vehicle->id) {
            session()->forget('active_vehicle_id');
        }

        $vehicle->delete();

        $this->clearVehicleCache();

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
