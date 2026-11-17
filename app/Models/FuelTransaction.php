<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class FuelTransaction extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'vehicle_id',
        'fuel_type_id',
        'transaction_at',
        'recorded_at',
        'odometer_km',
        'total_amount',
        'price_per_liter',
        'volume_liter',
        'price_source',
        'station_name',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'transaction_at'  => 'datetime',
            'recorded_at'     => 'datetime',
            'odometer_km'     => 'decimal:1',
            'total_amount'    => 'decimal:2',
            'price_per_liter' => 'decimal:2',
            'volume_liter'    => 'decimal:3',
        ];
    }

    // ── Relasi ──────────────────────────────────────────

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function fuelType(): BelongsTo
    {
        return $this->belongsTo(FuelType::class);
    }

    // ── Helper: hitung ulang volume liter ──

    public function recalculateVolume(): void
    {
        if ($this->price_per_liter > 0) {
            $this->volume_liter = round($this->total_amount / $this->price_per_liter, 3);
        }
    }
}
