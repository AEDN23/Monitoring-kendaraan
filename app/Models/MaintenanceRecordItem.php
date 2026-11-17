<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MaintenanceRecordItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'maintenance_record_id',
        'maintenance_item_id',
        'item_name',
        'quantity',
        'unit_price',
        'parts_cost',
        'labor_cost',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'quantity'   => 'decimal:2',
            'unit_price' => 'decimal:2',
            'parts_cost' => 'decimal:2',
            'labor_cost' => 'decimal:2',
        ];
    }

    public function maintenanceRecord(): BelongsTo
    {
        return $this->belongsTo(MaintenanceRecord::class);
    }

    public function maintenanceItem(): BelongsTo
    {
        return $this->belongsTo(MaintenanceItem::class);
    }

    // ── Helper: hitung parts_cost dari quantity * unit_price ──

    public function recalculatePartsCost(): void
    {
        $this->parts_cost = round($this->quantity * $this->unit_price, 2);
    }

    public function totalCost(): float
    {
        return (float) $this->parts_cost + (float) $this->labor_cost;
    }
}
