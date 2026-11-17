<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('name', 100);
            $table->enum('vehicle_type', ['motorcycle', 'car', 'other']);
            $table->string('brand', 100)->nullable();
            $table->string('model', 100)->nullable();
            $table->smallInteger('production_year')->unsigned()->nullable();
            $table->string('license_plate', 20)->nullable();
            $table->decimal('initial_odometer_km', 12, 1)->default(0);
            $table->date('initial_odometer_date');
            $table->text('notes')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['user_id', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vehicles');
    }
};
