<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fuel_price_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fuel_type_id')->constrained()->cascadeOnDelete();
            $table->string('region', 120)->nullable();
            $table->decimal('price_per_liter', 12, 2);
            $table->enum('source_type', ['manual', 'automatic'])->default('manual');
            $table->dateTime('effective_from');
            $table->dateTime('effective_until')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['fuel_type_id', 'region', 'effective_from', 'effective_until']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fuel_price_settings');
    }
};
