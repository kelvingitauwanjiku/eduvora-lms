<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_devices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('device_id')->unique();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->string('platform', 50)->nullable();
            $table->string('browser', 50)->nullable();
            $table->string('device_type', 20)->default('desktop');
            $table->json('location')->nullable();
            $table->boolean('is_current')->default(false);
            $table->boolean('is_banned')->default(false);
            $table->timestamp('last_active_at')->nullable();
            $table->timestamps();
            
            $table->index('user_id');
            $table->index('device_id');
            $table->index('is_current');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_devices');
    }
};