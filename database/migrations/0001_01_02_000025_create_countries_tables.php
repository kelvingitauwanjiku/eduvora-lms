<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('countries', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('name', 255);
            $table->string('iso2', 2)->nullable();
            $table->string('iso3', 3)->nullable();
            $table->string('numeric_code', 3)->nullable();
            $table->string('phone_code', 10)->nullable();
            $table->string('capital', 255)->nullable();
            $table->string('currency', 10)->nullable();
            $table->string('currency_name', 100)->nullable();
            $table->string('currency_symbol', 10)->nullable();
            $table->string('tld', 10)->nullable();
            $table->string('native', 255)->nullable();
            $table->string('region', 100)->nullable();
            $table->string('subregion', 100)->nullable();
            $table->text('timezones')->nullable();
            $table->text('translations')->nullable();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->string('emoji', 10)->nullable();
            $table->string('emojiU', 20)->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_featured')->default(false);
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->index('iso2');
            $table->index('iso3');
            $table->index('is_active');
            $table->index('region');
        });

        Schema::create('states', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('country_id')->constrained()->cascadeOnDelete();
            $table->string('name', 255);
            $table->string('iso2', 10)->nullable();
            $table->string('state_code', 10)->nullable();
            $table->string('type', 50)->nullable();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index('country_id');
        });

        Schema::create('cities', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('country_id')->constrained()->cascadeOnDelete();
            $table->foreignId('state_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('name', 255);
            $table->string('latitude', 20)->nullable();
            $table->string('longitude', 20)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index('country_id');
            $table->index('state_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cities');
        Schema::dropIfExists('states');
        Schema::dropIfExists('countries');
    }
};
