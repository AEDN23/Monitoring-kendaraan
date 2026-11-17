<?php

namespace App\Http\Controllers;

use App\Models\MaintenanceItem;
use App\Models\MaintenanceRecord;
use App\Models\MaintenanceRecordItem;
use App\Models\Vehicle;
use App\Models\VehicleMaintenanceSchedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MaintenanceRecordController extends Controller
{
    // ── Helpers ──────────────────────────────────────────────────────────────

    private function authorizeRecord(MaintenanceRecord $record): void
    {
        abort_unless($record->vehicle->user_id === Auth::id(), 403);
    }

    private function validateRecord(Request $r): array
    {
        return $r->validate([
            'vehicle_id'                    => 'required|exists:vehicles,id',
            'service_at'                    => 'required|date',
            'odometer_km'                   => 'required|numeric|min:0',
            'workshop_name'                 => 'nullable|string|max:150',
            'notes'                         => 'nullable|string',
            'items'                         => 'required|array|min:1',
            'items.*.item_name'             => 'required|string|max:150',
            'items.*.maintenance_item_id'   => 'nullable|exists:maintenance_items,id',
            'items.*.quantity'              => 'required|numeric|min:0.01',
            'items.*.unit_price'            => 'required|numeric|min:0',
            'items.*.parts_cost'            => 'required|numeric|min:0',
            'items.*.labor_cost'            => 'required|numeric|min:0',
        ]);
    }

    private function saveItems(MaintenanceRecord $record, array $items): void
    {
        foreach ($items as $itemData) {
            $record->items()->create([
                'maintenance_item_id' => $itemData['maintenance_item_id'] ?? null,
                'item_name'           => $itemData['item_name'],
                'quantity'            => $itemData['quantity'],
                'unit_price'          => $itemData['unit_price'],
                'parts_cost'          => $itemData['parts_cost'],
                'labor_cost'          => $itemData['labor_cost'],
            ]);
        }
    }

    private function resetSchedules(MaintenanceRecord $record): void
    {
        $record->load('items');
        foreach ($record->items as $item) {
            if ($item->maintenance_item_id) {
                $schedule = VehicleMaintenanceSchedule::where('vehicle_id', $record->vehicle_id)
                    ->where('maintenance_item_id', $item->maintenance_item_id)
                    ->first();
                if ($schedule) {
                    $schedule->resetFromRecord($record);
                }
            }
        }
    }

    // ── Actions ───────────────────────────────────────────────────────────────

    public function index(Request $r)
    {
        $userId = Auth::id();

        $vehicleIds = Vehicle::where('user_id', $userId)->pluck('id');

        $query = MaintenanceRecord::with(['vehicle', 'items.maintenanceItem'])
            ->whereIn('vehicle_id', $vehicleIds)
            ->orderBy('service_at', 'desc');

        if ($r->filled('vehicle_id')) {
            $query->where('vehicle_id', $r->vehicle_id);
        }

        $records = $query->paginate(15)->withQueryString();

        $vehicles = Vehicle::where('user_id', $userId)->orderBy('name')->get();

        return view('maintenance-records.index', compact('records', 'vehicles'));
    }

    public function create(Request $r)
    {
        $vehicles = Vehicle::where('user_id', Auth::id())->orderBy('name')->get();
        $maintenanceItems = MaintenanceItem::where('is_active', true)
            ->orderBy('category')
            ->orderBy('name')
            ->get();

        $selectedVehicleId = $r->input('vehicle_id');

        return view('maintenance-records.create', compact('vehicles', 'maintenanceItems', 'selectedVehicleId'));
    }

    public function store(Request $r)
    {
        $data = $this->validateRecord($r);

        $vehicle = Vehicle::findOrFail($data['vehicle_id']);
        abort_unless($vehicle->user_id === Auth::id(), 403);

        DB::transaction(function () use ($data, $vehicle) {
            $record = MaintenanceRecord::create([
                'vehicle_id'    => $data['vehicle_id'],
                'service_at'    => $data['service_at'],
                'recorded_at'   => now(),
                'odometer_km'   => $data['odometer_km'],
                'workshop_name' => $data['workshop_name'] ?? null,
                'notes'         => $data['notes'] ?? null,
                'total_parts_cost' => 0,
                'total_labor_cost' => 0,
                'total_cost'       => 0,
            ]);

            $this->saveItems($record, $data['items']);

            $record->load('items');
            $record->recalculateTotals();
            $record->save();

            $this->resetSchedules($record);
        });

        return redirect()
            ->route('maintenance-records.index')
            ->with('success', 'Riwayat servis berhasil dicatat.');
    }

    public function show(MaintenanceRecord $mr)
    {
        $mr->load(['vehicle', 'items.maintenanceItem']);
        $this->authorizeRecord($mr);

        return view('maintenance-records.show', ['record' => $mr]);
    }

    public function edit(MaintenanceRecord $mr)
    {
        $mr->load(['vehicle', 'items.maintenanceItem']);
        $this->authorizeRecord($mr);

        $vehicles = Vehicle::where('user_id', Auth::id())->orderBy('name')->get();
        $maintenanceItems = MaintenanceItem::where('is_active', true)
            ->orderBy('category')
            ->orderBy('name')
            ->get();

        return view('maintenance-records.edit', [
            'record'           => $mr,
            'vehicles'         => $vehicles,
            'maintenanceItems' => $maintenanceItems,
        ]);
    }

    public function update(Request $r, MaintenanceRecord $mr)
    {
        $mr->load('vehicle');
        $this->authorizeRecord($mr);

        $data = $this->validateRecord($r);

        $vehicle = Vehicle::findOrFail($data['vehicle_id']);
        abort_unless($vehicle->user_id === Auth::id(), 403);

        DB::transaction(function () use ($data, $mr) {
            $mr->update([
                'vehicle_id'    => $data['vehicle_id'],
                'service_at'    => $data['service_at'],
                'odometer_km'   => $data['odometer_km'],
                'workshop_name' => $data['workshop_name'] ?? null,
                'notes'         => $data['notes'] ?? null,
            ]);

            // Delete old items and re-insert
            $mr->items()->delete();
            $this->saveItems($mr, $data['items']);

            $mr->load('items');
            $mr->recalculateTotals();
            $mr->save();

            $this->resetSchedules($mr);
        });

        return redirect()
            ->route('maintenance-records.show', $mr)
            ->with('success', 'Riwayat servis berhasil diperbarui.');
    }

    public function destroy(MaintenanceRecord $mr)
    {
        $mr->load(['vehicle', 'items']);
        $this->authorizeRecord($mr);

        DB::transaction(function () use ($mr) {
            // For each schedule that was reset by this record, roll back to previous valid service
            foreach ($mr->items as $item) {
                if ($item->maintenance_item_id) {
                    $schedule = VehicleMaintenanceSchedule::where('vehicle_id', $mr->vehicle_id)
                        ->where('maintenance_item_id', $item->maintenance_item_id)
                        ->where('last_service_record_id', $mr->id)
                        ->first();

                    if ($schedule) {
                        // Find the previous valid record for this maintenance_item
                        $prevRecord = MaintenanceRecord::with('items')
                            ->whereHas('items', fn($q) => $q->where('maintenance_item_id', $item->maintenance_item_id))
                            ->where('vehicle_id', $mr->vehicle_id)
                            ->where('id', '!=', $mr->id)
                            ->orderBy('service_at', 'desc')
                            ->first();

                        if ($prevRecord) {
                            $schedule->resetFromRecord($prevRecord);
                        } else {
                            // No previous service — clear the last service fields
                            $schedule->update([
                                'last_service_record_id'   => null,
                                'last_service_odometer_km' => null,
                                'last_service_at'          => null,
                            ]);
                        }
                    }
                }
            }

            $mr->delete();
        });

        return redirect()
            ->route('maintenance-records.index')
            ->with('success', 'Riwayat servis berhasil dihapus.');
    }
}
