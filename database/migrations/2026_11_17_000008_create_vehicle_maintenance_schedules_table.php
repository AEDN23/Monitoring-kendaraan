<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vehicle_maintenance_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehicle_id')->constrained()->cascadeOnDelete();
            $table->foreignId('maintenance_item_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('interval_km')->nullable();
            $table->unsignedInteger('interval_days')->nullable();
            $table->unsignedInteger('reminder_before_km')->nullable();
            $table->unsignedInteger('reminder_before_days')->nullable();
            $table->foreignId('last_service_record_id')->nullable()->constrained('maintenance_records')->nullOnDelete();
            $table->decimal('last_service_odometer_km', 12, 1)->nullable();
            $table->dateTime('last_service_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->text('notes')->nullable();
            $table->timestamps();

            // Satu jadwal aktif per komponen per kendaraan
            $table->unique(['vehicle_id', 'maintenance_item_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vehicle_maintenance_schedules');
    }
};
