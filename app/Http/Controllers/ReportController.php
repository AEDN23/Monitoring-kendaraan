<?php

namespace App\Http\Controllers;

use App\Models\FuelTransaction;
use App\Models\MaintenanceRecord;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function index(Request $request): View
    {
        $vehicles = $this->cachedUserVehicles(activeOnly: true);

        $vehicleId  = $request->has('vehicle_id')
            ? $request->input('vehicle_id')
            : session('active_vehicle_id');

        $month      = (int) ($request->input('month') ?? now()->month);
        $year       = (int) ($request->input('year') ?? now()->year);

        $vehicleIds = ($vehicleId && $vehicleId !== 'all')
            ? [$vehicleId]
            : $vehicles->pluck('id')->toArray();

        if (empty($vehicleIds)) {
            return view('reports.index', [
                'vehicles'           => $vehicles,
                'vehicleId'          => $vehicleId,
                'month'              => $month,
                'year'               => $year,
                'fuelTransactions'   => collect(),
                'fuelStats'          => ['total_amount' => 0, 'total_liter' => 0, 'total_fill' => 0],
                'maintenanceRecords' => collect(),
                'serviceStats'       => ['total_cost' => 0, 'total_parts' => 0, 'total_labor' => 0, 'total_visits' => 0],
                'grandTotal'         => 0,
            ]);
        }

        $fuelTransactions = FuelTransaction::with(['vehicle', 'fuelType'])
            ->whereIn('vehicle_id', $vehicleIds)
            ->whereYear('transaction_at', $year)
            ->whereMonth('transaction_at', $month)
            ->orderBy('transaction_at', 'desc')
            ->get();

        $fuelStats = [
            'total_amount' => $fuelTransactions->sum('total_amount'),
            'total_liter'  => $fuelTransactions->sum('volume_liter'),
            'total_fill'   => $fuelTransactions->count(),
        ];

        $maintenanceRecords = MaintenanceRecord::with(['vehicle', 'items'])
            ->whereIn('vehicle_id', $vehicleIds)
            ->whereYear('service_at', $year)
            ->whereMonth('service_at', $month)
            ->orderBy('service_at', 'desc')
            ->get();

        $serviceStats = [
            'total_cost'   => $maintenanceRecords->sum('total_cost'),
            'total_parts'  => $maintenanceRecords->sum('total_parts_cost'),
            'total_labor'  => $maintenanceRecords->sum('total_labor_cost'),
            'total_visits' => $maintenanceRecords->count(),
        ];

        $grandTotal = $fuelStats['total_amount'] + $serviceStats['total_cost'];

        return view('reports.index', compact(
            'vehicles',
            'vehicleId',
            'month',
            'year',
            'fuelTransactions',
            'fuelStats',
            'maintenanceRecords',
            'serviceStats',
            'grandTotal'
        ));
    }
}
