<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class MaintenanceRecord extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'vehicle_id',
        'maintenance_item_id',
        'service_at',
        'recorded_at',
        'odometer_km',
        'workshop_name',
        'notes',
        'total_parts_cost',
        'total_labor_cost',
        'total_cost',
    ];

    protected function casts(): array
    {
        return [
            'service_at'       => 'datetime',
            'recorded_at'      => 'datetime',
            'odometer_km'      => 'decimal:1',
            'total_parts_cost' => 'decimal:2',
            'total_labor_cost' => 'decimal:2',
            'total_cost'       => 'decimal:2',
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

    public function items(): HasMany
    {
        return $this->hasMany(MaintenanceRecordItem::class);
    }

    public function schedules(): HasMany
    {
        return $this->hasMany(VehicleMaintenanceSchedule::class, 'last_service_record_id');
    }

    // ── Helper: hitung ulang total dari item ──

    public function recalculateTotals(): void
    {
        $this->total_parts_cost = $this->items->sum('parts_cost');
        $this->total_labor_cost = $this->items->sum('labor_cost');
        $this->total_cost       = $this->total_parts_cost + $this->total_labor_cost;
    }
}
