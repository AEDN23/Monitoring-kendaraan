<?php

namespace App\Http\Controllers;

use App\Models\MaintenanceItem;
use App\Models\Vehicle;
use App\Models\VehicleMaintenanceSchedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VehicleMaintenanceScheduleController extends Controller
{
    // ── Helpers ──────────────────────────────────────────────────────────────

    private function authorizeVehicle(Vehicle $vehicle): void
    {
        abort_unless($vehicle->user_id === Auth::id(), 403);
    }

    private function validateSchedule(Request $r, Vehicle $vehicle): array
    {
        $data = $r->validate([
            'maintenance_item_id'      => 'required|exists:maintenance_items,id',
            'interval_km'              => 'nullable|integer|min:100',
            'interval_days'            => 'nullable|integer|min:1',
            'reminder_before_km'       => 'nullable|integer|min:0',
            'reminder_before_days'     => 'nullable|integer|min:0',
            'last_service_odometer_km' => 'nullable|numeric|min:0',
            'last_service_at'          => 'nullable|date',
            'notes'                    => 'nullable|string',
            'is_active'                => 'sometimes|boolean',
        ]);

        // Check if tracking_mode is condition — skip interval requirement
        $item = MaintenanceItem::find($data['maintenance_item_id']);
        if ($item && $item->tracking_mode !== 'condition') {
            if (empty($data['interval_km']) && empty($data['interval_days'])) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'interval_km' => 'Minimal salah satu interval KM atau interval hari harus diisi.',
                ]);
            }
        }

        // Cast checkbox
        $data['is_active'] = $r->boolean('is_active');

        return $data;
    }

    // ── Actions ───────────────────────────────────────────────────────────────

    public function index(?Vehicle $vehicle = null)
    {
        $user = Auth::user();
        $vehicles = $user->vehicles()
            ->where('is_active', true)
            ->withLatestOdometer()
            ->orderBy('name')
            ->get();

        if (! $vehicle || ! $vehicle->exists) {
            $vehicle = $vehicles->first();
        } else {
            // Reuse the instance that already has max odometer loaded
            $vehicle = $vehicles->firstWhere('id', $vehicle->id) ?? $vehicle;
        }

        if (! $vehicle) {
            return view('schedules.index', [
                'vehicle'   => null,
                'vehicles'  => $vehicles,
                'schedules' => collect(),
                'currentKm' => 0,
            ]);
        }

        $this->authorizeVehicle($vehicle);

        $schedules = $vehicle->maintenanceSchedules()
            ->with('maintenanceItem')
            ->get();

        $currentKm = $vehicle->latestOdometerKm();

        $schedules = $schedules->map(function ($schedule) use ($currentKm) {
            $schedule->computed_status = $schedule->status((float) $currentKm);
            return $schedule;
        });

        return view('schedules.index', compact('vehicle', 'vehicles', 'schedules', 'currentKm'));
    }

    public function create(Vehicle $vehicle)
    {
        $this->authorizeVehicle($vehicle);

        // Items already registered for this vehicle
        $usedItemIds = $vehicle->maintenanceSchedules()->pluck('maintenance_item_id');

        $maintenanceItems = MaintenanceItem::where('is_active', true)
            ->whereNotIn('id', $usedItemIds)
            ->orderBy('category')
            ->orderBy('name')
            ->get();

        return view('schedules.create', compact('vehicle', 'maintenanceItems'));
    }

    public function store(Request $r, Vehicle $vehicle)
    {
        $this->authorizeVehicle($vehicle);

        $data = $this->validateSchedule($r, $vehicle);
        $data['vehicle_id'] = $vehicle->id;

        VehicleMaintenanceSchedule::create($data);

        return redirect()
            ->route('schedules.index', $vehicle)
            ->with('success', 'Jadwal perawatan berhasil ditambahkan.');
    }

    public function edit(Vehicle $vehicle, VehicleMaintenanceSchedule $schedule)
    {
        $this->authorizeVehicle($vehicle);
        abort_unless($schedule->vehicle_id === $vehicle->id, 404);

        $maintenanceItems = MaintenanceItem::where('is_active', true)
            ->orderBy('category')
            ->orderBy('name')
            ->get();

        return view('schedules.edit', compact('vehicle', 'schedule', 'maintenanceItems'));
    }

    public function update(Request $r, Vehicle $vehicle, VehicleMaintenanceSchedule $schedule)
    {
        $this->authorizeVehicle($vehicle);
        abort_unless($schedule->vehicle_id === $vehicle->id, 404);

        $data = $this->validateSchedule($r, $vehicle);
        $schedule->update($data);

        return redirect()
            ->route('schedules.index', $vehicle)
            ->with('success', 'Jadwal perawatan berhasil diperbarui.');
    }

    public function destroy(Vehicle $vehicle, VehicleMaintenanceSchedule $schedule)
    {
        $this->authorizeVehicle($vehicle);
        abort_unless($schedule->vehicle_id === $vehicle->id, 404);

        $schedule->delete();

        return redirect()
            ->route('schedules.index', $vehicle)
            ->with('success', 'Jadwal perawatan berhasil dihapus.');
    }
}
