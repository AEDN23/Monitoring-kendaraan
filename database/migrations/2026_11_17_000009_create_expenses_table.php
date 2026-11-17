<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tabel pengeluaran umum — fase lanjutan (parkir, pajak, cuci, dll.)
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehicle_id')->constrained()->cascadeOnDelete();
            $table->dateTime('expense_date');
            $table->string('category', 80);
            $table->string('description', 255)->nullable();
            $table->decimal('amount', 14, 2);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['vehicle_id', 'expense_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('expenses');
    }
};
