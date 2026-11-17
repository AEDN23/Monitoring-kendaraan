<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('maintenance_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehicle_id')->constrained()->cascadeOnDelete();
            $table->foreignId('maintenance_item_id')->nullable()->constrained()->nullOnDelete();
            $table->dateTime('service_at');                  // tanggal/waktu perawatan sebenarnya
            $table->timestamp('recorded_at');                // waktu input ke aplikasi
            $table->decimal('odometer_km', 12, 1);
            $table->string('workshop_name', 150)->nullable();
            $table->text('notes')->nullable();
            $table->decimal('total_parts_cost', 14, 2)->default(0);
            $table->decimal('total_labor_cost', 14, 2)->default(0);
            $table->decimal('total_cost', 14, 2)->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['vehicle_id', 'service_at']);
            $table->index(['vehicle_id', 'odometer_km']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('maintenance_records');
    }
};
