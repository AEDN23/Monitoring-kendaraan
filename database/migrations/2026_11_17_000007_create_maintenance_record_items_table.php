<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('maintenance_record_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('maintenance_record_id')->constrained()->cascadeOnDelete();
            $table->foreignId('maintenance_item_id')->nullable()->constrained()->nullOnDelete();
            $table->string('item_name', 150);                // snapshot nama barang/pekerjaan
            $table->decimal('quantity', 10, 2)->default(1);
            $table->decimal('unit_price', 14, 2)->default(0);
            $table->decimal('parts_cost', 14, 2)->default(0);
            $table->decimal('labor_cost', 14, 2)->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('maintenance_record_id');
            $table->index('maintenance_item_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('maintenance_record_items');
    }
};
