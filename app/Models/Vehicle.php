<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Vehicle extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'name',
        'vehicle_type',
        'brand',
        'model',
        'production_year',
        'license_plate',
        'initial_odometer_km',
        'initial_odometer_date',
        'notes',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'initial_odometer_km'   => 'decimal:1',
            'initial_odometer_date' => 'date',
            'is_active'             => 'boolean',
            'production_year'       => 'integer',
        ];
    }

    // ── Relasi ──────────────────────────────────────────

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function fuelTransactions(): HasMany
    {
        return $this->hasMany(FuelTransaction::class);
    }

    public function maintenanceSchedules(): HasMany
    {
        return $this->hasMany(VehicleMaintenanceSchedule::class);
    }

    public function maintenanceRecords(): HasMany
    {
        return $this->hasMany(MaintenanceRecord::class);
    }

    public function expenses(): HasMany
    {
        return $this->hasMany(Expense::class);
    }

    public function notificationLogs(): HasMany
    {
        return $this->hasMany(NotificationLog::class);
    }

    // ── Helper: odometer terbaru berdasarkan tanggal transaksi ──

    public function latestOdometerKm(): ?float
    {
        $latestFuel = $this->fuelTransactions()
            ->orderBy('transaction_at', 'desc')
            ->orderBy('id', 'desc')
            ->value('odometer_km');

        $latestService = $this->maintenanceRecords()
            ->orderBy('service_at', 'desc')
            ->orderBy('id', 'desc')
            ->value('odometer_km');

        return max(
            $latestFuel ?? $this->initial_odometer_km,
            $latestService ?? $this->initial_odometer_km,
        );
    }
}
