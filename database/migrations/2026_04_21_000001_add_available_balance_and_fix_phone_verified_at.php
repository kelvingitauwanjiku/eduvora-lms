<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->decimal('available_balance', 12, 2)->default(0)->after('balance');
        });

        // Change phone_verified_at from string to timestamp using raw SQL (no doctrine/dbal required)
        DB::statement('ALTER TABLE users MODIFY phone_verified_at TIMESTAMP NULL DEFAULT NULL');
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('available_balance');
        });

        // Revert phone_verified_at back to string
        DB::statement('ALTER TABLE users MODIFY phone_verified_at VARCHAR(255) NULL');
    }
};
