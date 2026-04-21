<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('home_page_sections', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('name', 255);
            $table->string('slug', 255)->unique();
            $table->text('description')->nullable();
            $table->enum('section_type', [
                'hero',
                'featured_courses',
                'categories',
                'trending',
                'latest_courses',
                'top_instructors',
                'testimonials',
                'stats',
                'cta',
                'blog_posts',
                'bootcamps',
                'ebooks',
                'newsletter',
                'faq',
                'partners',
                'custom',
            ])->default('custom');
            $table->json('settings')->nullable();
            $table->json('filters')->nullable();
            $table->integer('limit')->default(8);
            $table->integer('columns')->default(4);
            $table->string('order_by', 50)->default('created_at');
            $table->string('order_dir', 10)->default('desc');
            $table->string('layout', 50)->default('grid')->comment('grid, list, slider, carousel');
            $table->string('style', 50)->nullable();
            $table->string('background_color', 20)->nullable();
            $table->string('text_color', 20)->nullable();
            $table->boolean('show_title')->default(true);
            $table->boolean('show_subtitle')->default(true);
            $table->boolean('show_view_all')->default(true);
            $table->string('view_all_url', 500)->nullable();
            $table->string('view_all_text', 100)->default('View All');
            $table->boolean('is_active')->default(true);
            $table->boolean('is_full_width')->default(false);
            $table->boolean('is_lazy_load')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->index('section_type');
            $table->index('is_active');
            $table->index('sort_order');
        });

        Schema::create('home_page_section_items', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('section_id')->constrained()->cascadeOnDelete();
            $table->string('item_type', 100)->nullable();
            $table->bigInteger('item_id')->nullable();
            $table->string('title', 255)->nullable();
            $table->string('subtitle', 255)->nullable();
            $table->text('description')->nullable();
            $table->string('image', 500)->nullable();
            $table->string('link', 500)->nullable();
            $table->string('link_text', 100)->nullable();
            $table->string('icon', 100)->nullable();
            $table->string('badge', 50)->nullable();
            $table->string('badge_color', 20)->nullable();
            $table->decimal('price', 12, 2)->nullable();
            $table->string('rating', 10)->nullable();
            $table->string('button_text', 100)->nullable();
            $table->string('button_color', 20)->nullable();
            $table->string('target', 20)->default('_self');
            $table->json('metadata')->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index('section_id');
            $table->index('item_type');
        });

        Schema::create('sliders', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('name', 255);
            $table->string('slug', 255)->unique();
            $table->text('description')->nullable();
            $table->enum('type', ['main', 'secondary', 'mini'])->default('main');
            $table->enum('layout', ['full', 'contained', 'boxed'])->default('full');
            $table->integer('height')->default(600);
            $table->boolean('auto_play')->default(true);
            $table->integer('auto_play_interval')->default(5000);
            $table->boolean('show_indicators')->default(true);
            $table->boolean('show_arrows')->default(true);
            $table->boolean('pause_on_hover')->default(true);
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->index('is_active');
        });

        Schema::create('slider_slides', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('slider_id')->constrained()->cascadeOnDelete();
            $table->string('title', 255)->nullable();
            $table->text('subtitle')->nullable();
            $table->longText('description')->nullable();
            $table->string('button_text', 100)->nullable();
            $table->string('button_url', 500)->nullable();
            $table->string('button_target', 20)->default('_self');
            $table->string('button_color', 20)->nullable();
            $table->string('second_button_text', 100)->nullable();
            $table->string('second_button_url', 500)->nullable();
            $table->string('background_type', 20)->default('image')->comment('image, video, gradient');
            $table->string('background_image', 500)->nullable();
            $table->string('background_video', 500)->nullable();
            $table->string('background_video_poster', 500)->nullable();
            $table->string('background_color', 20)->nullable();
            $table->string('background_gradient_start', 20)->nullable();
            $table->string('background_gradient_end', 20)->nullable();
            $table->string('text_color', 20)->default('light');
            $table->string('text_alignment', 20)->default('left');
            $table->string('content_position', 20)->default('center');
            $table->boolean('show_overlay')->default(false);
            $table->string('overlay_color', 20)->nullable();
            $table->string('overlay_opacity', 10)->nullable();
            $table->string('badge', 50)->nullable();
            $table->string('badge_color', 20)->nullable();
            $table->string('thumbnail', 500)->nullable();
            $table->json('custom_css')->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            $table->integer('clicks_count')->default(0);
            $table->integer('views_count')->default(0);
            $table->timestamps();

            $table->index('slider_id');
        });

        Schema::create('promotional_banners', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('title', 255);
            $table->text('subtitle')->nullable();
            $table->string('image', 500)->nullable();
            $table->string('link', 500)->nullable();
            $table->string('button_text', 100)->nullable();
            $table->enum('type', ['banner', 'popup', 'toast', 'sticky'])->default('banner');
            $table->enum('position', ['top', 'bottom', 'center', 'corner'])->default('top');
            $table->string('background_color', 20)->nullable();
            $table->string('text_color', 20)->nullable();
            $table->string('border_color', 20)->nullable();
            $table->boolean('is_dismissible')->default(true);
            $table->boolean('show_close_button')->default(true);
            $table->integer('dismiss_duration')->nullable()->comment('Seconds before auto dismiss');
            $table->boolean('is_active')->default(true);
            $table->boolean('show_on_mobile')->default(true);
            $table->boolean('show_on_desktop')->default(true);
            $table->enum('target', ['all', 'guests', 'students', 'instructors'])->default('all');
            $table->json('target_pages')->nullable();
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            $table->integer('views_count')->default(0);
            $table->integer('clicks_count')->default(0);
            $table->integer('dismiss_count')->default(0);
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->index('is_active');
            $table->index('type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('promotional_banners');
        Schema::dropIfExists('slider_slides');
        Schema::dropIfExists('sliders');
        Schema::dropIfExists('home_page_section_items');
        Schema::dropIfExists('home_page_sections');
    }
};
