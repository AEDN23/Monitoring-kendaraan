<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VehicleMaintenanceSchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'vehicle_id',
        'maintenance_item_id',
        'interval_km',
        'interval_days',
        'reminder_before_km',
        'reminder_before_days',
        'last_service_record_id',
        'last_service_odometer_km',
        'last_service_at',
        'is_active',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'last_service_odometer_km' => 'decimal:1',
            'last_service_at'          => 'datetime',
            'is_active'                => 'boolean',
            'interval_km'              => 'integer',
            'interval_days'            => 'integer',
            'reminder_before_km'       => 'integer',
            'reminder_before_days'     => 'integer',
        ];
    }

    // ── Relasi ──────────────────────────────────────────

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function maintenanceItem(): BelongsTo
    {
        return $this->belongsTo(MaintenanceItem::class);
    }

    public function lastServiceRecord(): BelongsTo
    {
        return $this->belongsTo(MaintenanceRecord::class, 'last_service_record_id');
    }

    // ── Kalkulasi target berikutnya ──────────────────────

    public function nextDueKm(): ?float
    {
        if (! $this->interval_km || $this->last_service_odometer_km === null) {
            return null;
        }

        return (float) $this->last_service_odometer_km + $this->interval_km;
    }

    public function nextDueDate(): ?Carbon
    {
        if (! $this->interval_days || ! $this->last_service_at) {
            return null;
        }

        return $this->last_service_at->copy()->addDays($this->interval_days);
    }

    /**
     * Status: safe | soon | overdue | needs_inspection
     * Dihitung dinamis dari data terbaru, tidak disimpan di DB.
     */
    public function status(float $currentOdometerKm): string
    {
        $trackingMode = $this->maintenanceItem->tracking_mode ?? 'km_or_time';

        if ($trackingMode === 'condition') {
            return 'needs_inspection';
        }

        $overdueKm   = false;
        $overdueDays = false;
        $soonKm      = false;
        $soonDays    = false;

        // Periksa KM
        if ($this->interval_km) {
            $dueKm       = $this->nextDueKm();
            $reminderKm  = $this->reminder_before_km ?? 200;
            $overdueKm   = $currentOdometerKm >= $dueKm;
            $soonKm      = !$overdueKm && ($currentOdometerKm >= ($dueKm - $reminderKm));
        }

        // Periksa waktu
        if ($this->interval_days) {
            $dueDate      = $this->nextDueDate();
            $reminderDays = $this->reminder_before_days ?? 7;
            $overdueDays  = now()->greaterThanOrEqualTo($dueDate);
            $soonDays     = !$overdueDays && now()->greaterThanOrEqualTo($dueDate->copy()->subDays($reminderDays));
        }

        if ($overdueKm || $overdueDays) {
            return 'overdue';
        }

        if ($soonKm || $soonDays) {
            return 'soon';
        }

        return 'safe';
    }

    // ── Reset jadwal setelah perawatan dicatat ──

    public function resetFromRecord(MaintenanceRecord $record): void
    {
        $this->last_service_record_id   = $record->id;
        $this->last_service_odometer_km = $record->odometer_km;
        $this->last_service_at          = $record->service_at;
        $this->save();
    }
}
