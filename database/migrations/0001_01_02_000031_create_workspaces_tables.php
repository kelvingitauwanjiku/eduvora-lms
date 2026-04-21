<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('workspaces', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('name', 255);
            $table->string('slug', 255)->unique();
            $table->text('description')->nullable();
            $table->string('icon', 500)->nullable();
            $table->string('cover_image', 500)->nullable();
            $table->foreignId('owner_id')->constrained('users')->cascadeOnDelete();
            $table->string('timezone', 50)->default('UTC');
            $table->enum('visibility', ['private', 'team', 'public'])->default('private');
            $table->boolean('allow_guest')->default(false);
            $table->json('settings')->nullable();
            $table->integer('members_count')->default(0);
            $table->integer('pages_count')->default(0);
            $table->integer('files_count')->default(0);
            $table->bigInteger('storage_used')->default(0)->comment('bytes');
            $table->bigInteger('storage_limit')->default(10737418240)->comment('10GB');
            $table->boolean('is_active')->default(true);
            $table->boolean('is_featured')->default(false);
            $table->timestamps();
            $table->softDeletes();

            $table->index('owner_id');
            $table->index('visibility');
        });

        Schema::create('workspace_members', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('workspace_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('role', 50)->default('member')->comment('owner, admin, editor, member, guest');
            $table->json('permissions')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamp('joined_at')->nullable();
            $table->timestamps();

            $table->unique(['workspace_id', 'user_id']);
            $table->index('user_id');
        });

        Schema::create('workspace_invites', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('workspace_id')->constrained()->cascadeOnDelete();
            $table->foreignId('invited_by')->constrained('users')->cascadeOnDelete();
            $table->string('email')->unique();
            $table->string('role', 50)->default('member');
            $table->string('token', 100)->unique();
            $table->string('message', 500)->nullable();
            $table->enum('status', ['pending', 'accepted', 'expired'])->default('pending');
            $table->timestamp('expires_at');
            $table->timestamp('accepted_at')->nullable();
            $table->timestamps();

            $table->index('workspace_id');
            $table->index('email');
        });

        Schema::create('workspace_pages', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('workspace_id')->constrained()->cascadeOnDelete();
            $table->foreignId('parent_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('title', 500);
            $table->string('icon', 20)->nullable();
            $table->text('content')->nullable();
            $table->string('cover', 500)->nullable();
            $table->text('properties')->nullable();
            $table->text('schema')->nullable();
            $table->boolean('is_favorite')->default(false);
            $table->boolean('is_template')->default(false);
            $table->boolean('is_published')->default(true);
            $table->integer('sort_order')->default(0);
            $table->integer('views_count')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->index('workspace_id');
            $table->index('parent_id');
            $table->index('is_template');
        });

        Schema::create('workspace_blocks', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('page_id')->constrained('workspace_pages')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('type', 100)->comment('text, heading, image, code, embed, etc');
            $table->json('content')->nullable();
            $table->json('properties')->nullable();
            $table->string('parent_block_id')->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_deleted')->default(false);
            $table->timestamps();

            $table->index('page_id');
            $table->index('type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('workspace_blocks');
        Schema::dropIfExists('workspace_pages');
        Schema::dropIfExists('workspace_invites');
        Schema::dropIfExists('workspace_members');
        Schema::dropIfExists('workspaces');
    }
};