<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('online_status', ['online', 'away', 'dnd', 'offline'])->default('offline')->after('status');
            $table->timestamp('last_seen_at')->nullable()->after('online_status');
            $table->boolean('is_public_profile')->default(true)->after('last_seen_at');
            $table->string('status_emoji', 10)->nullable()->after('is_public_profile');
            $table->string('status_message', 255)->nullable()->after('status_emoji');
            $table->timestamp('status_expires_at')->nullable()->after('status_message');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'online_status',
                'last_seen_at',
                'is_public_profile',
                'status_emoji',
                'status_message',
                'status_expires_at',
            ]);
        });
    }
};