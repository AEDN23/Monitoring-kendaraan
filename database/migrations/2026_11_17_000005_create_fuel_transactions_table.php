<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fuel_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehicle_id')->constrained()->cascadeOnDelete();
            $table->foreignId('fuel_type_id')->constrained()->restrictOnDelete();
            $table->dateTime('transaction_at');              // tanggal/waktu pengisian sebenarnya
            $table->timestamp('recorded_at');                // waktu data dicatat ke aplikasi
            $table->decimal('odometer_km', 12, 1);
            $table->decimal('total_amount', 14, 2);
            $table->decimal('price_per_liter', 12, 2);
            $table->decimal('volume_liter', 10, 3);
            $table->enum('price_source', ['manual', 'configured'])->default('manual');
            $table->string('station_name', 150)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['vehicle_id', 'transaction_at']);
            $table->index(['vehicle_id', 'odometer_km']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fuel_transactions');
    }
};
