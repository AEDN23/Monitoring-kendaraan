<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('maintenance_items', function (Blueprint $table) {
            $table->id();
            $table->string('name', 120);
            $table->string('category', 80)->nullable();
            $table->unsignedInteger('default_interval_km')->nullable();
            $table->unsignedInteger('default_interval_days')->nullable();
            $table->enum('tracking_mode', ['km', 'time', 'km_or_time', 'condition'])->default('km_or_time');
            $table->enum('applies_to', ['motorcycle', 'car', 'both', 'other'])->default('both');
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('maintenance_items');
    }
};
