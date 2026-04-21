<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_player_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('course_id')->nullable()->constrained()->cascadeOnDelete();
            $table->boolean('autoplay')->default(true);
            $table->decimal('playback_speed', 3, 1)->default(1.0);
            $table->string('quality')->default('auto');
            $table->string('subtitles')->default('auto');
            $table->integer('subtitles_size')->default(16);
            $table->integer('volume')->default(80);
            $table->boolean('muted')->default(false);
            $table->boolean('loop')->default(false);
            $table->boolean('show_transcript')->default(false);
            $table->boolean('show_chapters')->default(true);
            $table->boolean('keyboard_shortcuts')->default(true);
            $table->boolean('continue_watching')->default(true);
            $table->integer('skip_intro')->default(0);
            $table->integer('skip_outro')->default(0);
            $table->boolean('play_next')->default(false);
            $table->boolean('remember_position')->default(true);
            $table->boolean('mini_player')->default(false);
            $table->boolean('theater_mode')->default(false);
            $table->boolean('picture_in_picture')->default(true);
            $table->boolean('download_enabled')->default(false);
            $table->boolean('analytics_enabled')->default(true);
            $table->integer('last_position')->nullable();
            $table->json('completed_lessons')->nullable();
            $table->timestamps();
            
            $table->unique(['user_id']);
            $table->unique(['user_id', 'course_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_player_settings');
    }
};