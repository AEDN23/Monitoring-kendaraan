<?php

namespace App\Http\Controllers;

use App\Models\FuelTransaction;
use App\Models\MaintenanceRecord;
use App\Models\Vehicle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Semua kendaraan milik user (aktif)
        $vehicles = $user->vehicles()
            ->where('is_active', true)
            ->withLatestOdometer()
            ->with(['maintenanceSchedules.maintenanceItem'])
            ->get();

        // ── Statistik ────────────────────────────────────────
        $totalVehicles = $vehicles->count();

        $totalFuelThisMonth = FuelTransaction::whereIn('vehicle_id', $vehicles->pluck('id'))
            ->whereYear('transaction_at', now()->year)
            ->whereMonth('transaction_at', now()->month)
            ->sum('total_amount');

        $totalServiceThisMonth = MaintenanceRecord::whereIn('vehicle_id', $vehicles->pluck('id'))
            ->whereYear('service_at', now()->year)
            ->whereMonth('service_at', now()->month)
            ->sum('total_cost');

        // ── Status perawatan per kendaraan ───────────────────
        $vehicleStatuses = $vehicles->map(function (Vehicle $vehicle) {
            $odometer = $vehicle->latestOdometerKm() ?? 0;
            $schedules = $vehicle->maintenanceSchedules->where('is_active', true);

            // Hitung status worst-case per kendaraan
            $worstStatus = 'safe';
            $statusPriority = ['overdue' => 3, 'soon' => 2, 'needs_inspection' => 1, 'safe' => 0];

            $scheduleDetails = $schedules->map(function ($schedule) use ($odometer) {
                return [
                    'name'   => $schedule->maintenanceItem->name ?? '-',
                    'status' => $schedule->status($odometer),
                ];
            });

            foreach ($scheduleDetails as $detail) {
                if (($statusPriority[$detail['status']] ?? 0) > ($statusPriority[$worstStatus] ?? 0)) {
                    $worstStatus = $detail['status'];
                }
            }

            return [
                'vehicle'         => $vehicle,
                'odometer'        => $odometer,
                'overall_status'  => $worstStatus,
                'schedule_detail' => $scheduleDetails,
            ];
        });

        return view('dashboard', compact(
            'vehicles',
            'totalVehicles',
            'totalFuelThisMonth',
            'totalServiceThisMonth',
            'vehicleStatuses',
        ));
    }
}
