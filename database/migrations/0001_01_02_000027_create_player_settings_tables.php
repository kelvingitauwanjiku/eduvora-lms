<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('player_settings', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('name', 255);
            $table->string('slug', 255)->unique();
            $table->text('description')->nullable();
            $table->longText('config')->nullable();
            $table->boolean('auto_play')->default(true);
            $table->boolean('show_controls')->default(true);
            $table->boolean('enable_download')->default(false);
            $table->boolean('enable_fullscreen')->default(true);
            $table->boolean('enable_pip')->default(true);
            $table->boolean('enable_playback_speed')->default(true);
            $table->boolean('enable_quality_selector')->default(true);
            $table->boolean('enable_subtitles')->default(true);
            $table->boolean('enable_keyboard_shortcuts')->default(true);
            $table->boolean('enable_watch_history')->default(true);
            $table->integer('default_playback_speed')->default(1);
            $table->string('default_quality', 10)->default('auto');
            $table->string('player_color', 20)->default('#000000');
            $table->string('logo_url', 500)->nullable();
            $table->string('logo_position', 20)->default('top-right');
            $table->boolean('is_active')->default(true);
            $table->boolean('is_default')->default(false);
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->index('is_active');
        });

        Schema::create('user_player_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('player_setting_id')->nullable()->constrained()->cascadeOnDelete();
            $table->json('preferences')->nullable();
            $table->boolean('auto_play')->default(true);
            $table->boolean('autoplay_next_lesson')->default(true);
            $table->boolean('show_transcript')->default(false);
            $table->boolean('enable_notifications')->default(true);
            $table->string('playback_speed', 10)->default('1x');
            $table->string('video_quality', 20)->default('auto');
            $table->boolean('enable_subtitles')->default(false);
            $table->string('subtitle_language', 10)->default('en');
            $table->integer('volume')->default(100);
            $table->boolean('is_muted')->default(false);
            $table->boolean('skip_intro')->default(false);
            $table->integer('skip_intro_duration')->default(0);
            $table->boolean('skip_outro')->default(false);
            $table->integer('skip_outro_duration')->default(0);
            $table->boolean('continue_watching')->default(true);
            $table->timestamps();

            $table->unique('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_player_settings');
        Schema::dropIfExists('player_settings');
    }
};
