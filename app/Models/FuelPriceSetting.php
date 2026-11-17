<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FuelPriceSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'fuel_type_id',
        'region',
        'price_per_liter',
        'source_type',
        'effective_from',
        'effective_until',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'price_per_liter' => 'decimal:2',
            'effective_from'  => 'datetime',
            'effective_until' => 'datetime',
        ];
    }

    public function fuelType(): BelongsTo
    {
        return $this->belongsTo(FuelType::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // ── Helper: harga aktif untuk jenis BBM tertentu pada tanggal tertentu ──

    public static function activePrice(int $fuelTypeId, string $region = null, ?string $at = null): ?self
    {
        $at = $at ?? now();

        return static::where('fuel_type_id', $fuelTypeId)
            ->when($region, fn ($q) => $q->where('region', $region))
            ->where('effective_from', '<=', $at)
            ->where(fn ($q) => $q->whereNull('effective_until')->orWhere('effective_until', '>=', $at))
            ->orderBy('effective_from', 'desc')
            ->first();
    }
}
