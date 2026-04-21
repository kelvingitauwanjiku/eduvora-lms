<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('languages', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('native_name', 100)->nullable();
            $table->string('code', 10)->unique();
            $table->string('locale', 10)->nullable();
            $table->string('direction', 10)->default('ltr');
            $table->string('flag', 10)->nullable();
            $table->boolean('is_default')->default(false);
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->index('code');
            $table->index('is_active');
        });

        Schema::create('language_phrases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('language_id')->constrained()->cascadeOnDelete();
            $table->string('group', 100)->default('messages');
            $table->string('key', 255);
            $table->text('value')->nullable();
            $table->text('default_value')->nullable();
            $table->boolean('is_translated')->default(false);
            $table->timestamps();

            $table->unique(['language_id', 'group', 'key']);
            $table->index('group');
            $table->index('is_translated');
        });

        Schema::create('translations', function (Blueprint $table) {
            $table->id();
            $table->string('locale', 10);
            $table->string('group', 100)->default('messages');
            $table->string('key', 255);
            $table->text('value')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->unique(['locale', 'group', 'key']);
            $table->index('locale');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('translations');
        Schema::dropIfExists('language_phrases');
        Schema::dropIfExists('languages');
    }
};
