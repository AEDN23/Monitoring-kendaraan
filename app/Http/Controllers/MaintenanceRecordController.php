<?php

namespace App\Http\Controllers;

use App\Models\MaintenanceItem;
use App\Models\MaintenanceRecord;
use App\Models\MaintenanceRecordItem;
use App\Models\Vehicle;
use App\Models\VehicleMaintenanceSchedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class MaintenanceRecordController extends Controller
{
    // ── Helpers ──────────────────────────────────────────────────────────────

    private function authorizeRecord(MaintenanceRecord $record): void
    {
        $userVehicleIds = $this->cachedUserVehicles()->pluck('id');
        abort_unless($userVehicleIds->contains($record->vehicle_id), 403);
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

    private function cachedMaintenanceItems()
    {
        return MaintenanceItem::where('is_active', true)
            ->orderBy('category')
            ->orderBy('name')
            ->get();
    }

    // ── Actions ───────────────────────────────────────────────────────────────

    public function index(Request $r)
    {
        $vehicles   = $this->cachedUserVehicles();
        $vehicleIds = $vehicles->pluck('id');

        if ($vehicleIds->isEmpty()) {
            $records = MaintenanceRecord::whereRaw('1=0')->paginate(15);
            return view('maintenance-records.index', compact('records', 'vehicles'));
        }

        $query = MaintenanceRecord::with(['vehicle', 'items.maintenanceItem'])
            ->whereIn('vehicle_id', $vehicleIds)
            ->orderBy('service_at', 'desc');

        $selectedVehicleId = $r->has('vehicle_id')
            ? $r->input('vehicle_id')
            : session('active_vehicle_id');

        if ($selectedVehicleId && $selectedVehicleId !== 'all') {
            $query->where('vehicle_id', $selectedVehicleId);
        }

        $records = $query->paginate(15)->withQueryString();

        return view('maintenance-records.index', compact('records', 'vehicles', 'selectedVehicleId'));
    }

    public function create(Request $r)
    {
        $vehicles         = $this->cachedUserVehicles();
        $maintenanceItems = $this->cachedMaintenanceItems();
        $selectedVehicleId = $r->input('vehicle_id', session('active_vehicle_id'));

        return view('maintenance-records.create', compact('vehicles', 'maintenanceItems', 'selectedVehicleId'));
    }

    public function store(Request $r)
    {
        $data = $this->validateRecord($r);

        $userVehicleIds = $this->cachedUserVehicles()->pluck('id');
        abort_unless($userVehicleIds->contains($data['vehicle_id']), 403);

        DB::transaction(function () use ($data) {
            $record = MaintenanceRecord::create([
                'vehicle_id'       => $data['vehicle_id'],
                'service_at'       => $data['service_at'],
                'recorded_at'      => now(),
                'odometer_km'      => $data['odometer_km'],
                'workshop_name'    => $data['workshop_name'] ?? null,
                'notes'            => $data['notes'] ?? null,
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

        $this->clearVehicleCache();

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

        $vehicles         = $this->cachedUserVehicles();
        $maintenanceItems = $this->cachedMaintenanceItems();

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

        $userVehicleIds = $this->cachedUserVehicles()->pluck('id');
        abort_unless($userVehicleIds->contains($data['vehicle_id']), 403);

        DB::transaction(function () use ($data, $mr) {
            $mr->update([
                'vehicle_id'    => $data['vehicle_id'],
                'service_at'    => $data['service_at'],
                'odometer_km'   => $data['odometer_km'],
                'workshop_name' => $data['workshop_name'] ?? null,
                'notes'         => $data['notes'] ?? null,
            ]);

            $mr->items()->delete();
            $this->saveItems($mr, $data['items']);

            $mr->load('items');
            $mr->recalculateTotals();
            $mr->save();

            $this->resetSchedules($mr);
        });

        $this->clearVehicleCache();

        return redirect()
            ->route('maintenance-records.show', $mr)
            ->with('success', 'Riwayat servis berhasil diperbarui.');
    }

    public function destroy(MaintenanceRecord $mr)
    {
        $mr->load(['vehicle', 'items']);
        $this->authorizeRecord($mr);

        DB::transaction(function () use ($mr) {
            foreach ($mr->items as $item) {
                if ($item->maintenance_item_id) {
                    $schedule = VehicleMaintenanceSchedule::where('vehicle_id', $mr->vehicle_id)
                        ->where('maintenance_item_id', $item->maintenance_item_id)
                        ->where('last_service_record_id', $mr->id)
                        ->first();

                    if ($schedule) {
                        $prevRecord = MaintenanceRecord::with('items')
                            ->whereHas('items', fn($q) => $q->where('maintenance_item_id', $item->maintenance_item_id))
                            ->where('vehicle_id', $mr->vehicle_id)
                            ->where('id', '!=', $mr->id)
                            ->orderBy('service_at', 'desc')
                            ->first();

                        if ($prevRecord) {
                            $schedule->resetFromRecord($prevRecord);
                        } else {
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

        $this->clearVehicleCache();

        return redirect()
            ->route('maintenance-records.index')
            ->with('success', 'Riwayat servis berhasil dihapus.');
    }
}
