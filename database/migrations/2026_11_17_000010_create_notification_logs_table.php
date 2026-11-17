<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tabel log notifikasi — fase notifikasi
        Schema::create('notification_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('vehicle_id')->constrained()->cascadeOnDelete();
            $table->foreignId('schedule_id')->nullable()->constrained('vehicle_maintenance_schedules')->nullOnDelete();
            $table->string('notification_type', 60);
            $table->string('title', 180);
            $table->text('message');
            $table->dateTime('triggered_at');
            $table->dateTime('read_at')->nullable();
            $table->string('delivery_channel', 40)->nullable(); // web, email, push
            $table->timestamp('created_at')->nullable();

            $table->index(['user_id', 'read_at']);
            $table->index(['schedule_id', 'triggered_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notification_logs');
    }
};
