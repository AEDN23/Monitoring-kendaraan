<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MaintenanceItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'category',
        'default_interval_km',
        'default_interval_days',
        'tracking_mode',
        'applies_to',
        'description',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active'            => 'boolean',
            'default_interval_km'  => 'integer',
            'default_interval_days'=> 'integer',
        ];
    }

    public function maintenanceRecords(): HasMany
    {
        return $this->hasMany(MaintenanceRecord::class);
    }

    public function maintenanceRecordItems(): HasMany
    {
        return $this->hasMany(MaintenanceRecordItem::class);
    }

    public function vehicleMaintenanceSchedules(): HasMany
    {
        return $this->hasMany(VehicleMaintenanceSchedule::class);
    }
}
