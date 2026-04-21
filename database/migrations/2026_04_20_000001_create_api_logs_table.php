<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('api_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('method', 10);
            $table->string('path');
            $table->string('route_name')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->json('request_headers')->nullable();
            $table->json('request_body')->nullable();
            $table->integer('response_status')->nullable();
            $table->json('response_body')->nullable();
            $table->decimal('duration', 10, 2)->nullable();
            $table->decimal('memory', 10, 2)->nullable();
            $table->string('endpoint')->nullable();
            $table->string('action')->nullable();
            $table->enum('status', ['success', 'error', 'request'])->default('success');
            $table->json('context')->nullable();
            $table->timestamps();
            
            $table->index('user_id');
            $table->index('ip_address');
            $table->index('endpoint');
            $table->index('action');
            $table->index('status');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('api_logs');
    }
};