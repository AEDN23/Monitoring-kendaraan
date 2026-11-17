<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NotificationLog extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'vehicle_id',
        'schedule_id',
        'notification_type',
        'title',
        'message',
        'triggered_at',
        'read_at',
        'delivery_channel',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'triggered_at' => 'datetime',
            'read_at'      => 'datetime',
            'created_at'   => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function schedule(): BelongsTo
    {
        return $this->belongsTo(VehicleMaintenanceSchedule::class, 'schedule_id');
    }

    public function markAsRead(): void
    {
        $this->read_at = now();
        $this->save();
    }
}
