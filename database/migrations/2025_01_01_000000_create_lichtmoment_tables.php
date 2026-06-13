<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Users table - only create if not exists (Laravel default may already have it)
        if (!Schema::hasTable('users')) {
            Schema::create('users', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('email')->unique();
                $table->timestamp('email_verified_at')->nullable();
                $table->string('password');
                $table->rememberToken();
                $table->timestamps();
            });
        }

        // Add admin user if not exists
        if (!\Illuminate\Support\Facades\DB::table('users')->where('email', 'admin@lichtmoment.de')->exists()) {
            \Illuminate\Support\Facades\DB::table('users')->insert([
                'name' => 'Admin',
                'email' => 'admin@lichtmoment.de',
                'password' => \Illuminate\Support\Facades\Hash::make('wasd1234'),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        if (!Schema::hasTable('projects')) {
            Schema::create('projects', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->text('description')->default('');
                $table->string('slug')->unique();
                $table->string('cover_image')->default('');
                $table->boolean('download_enabled')->default(true);
                $table->string('password_hash')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('folders')) {
            Schema::create('folders', function (Blueprint $table) {
                $table->id();
                $table->foreignId('project_id')->constrained()->cascadeOnDelete();
                $table->string('name');
                $table->foreignId('parent_id')->nullable()->constrained('folders')->cascadeOnDelete();
                $table->integer('sort_order')->default(0);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('photos')) {
            Schema::create('photos', function (Blueprint $table) {
                $table->id();
                $table->foreignId('project_id')->nullable()->constrained()->nullOnDelete();
                $table->foreignId('folder_id')->nullable()->constrained()->nullOnDelete();
                $table->string('filename');
                $table->string('original_name');
                $table->integer('file_size')->default(0);
                $table->integer('sort_order')->default(0);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('share_links')) {
            Schema::create('share_links', function (Blueprint $table) {
                $table->id();
                $table->foreignId('project_id')->constrained()->cascadeOnDelete();
                $table->string('token', 64)->unique();
                $table->string('password_hash')->nullable();
                $table->boolean('download_enabled')->default(true);
                $table->timestamp('expires_at')->nullable();
                $table->integer('access_count')->default(0);
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('share_links');
        Schema::dropIfExists('photos');
        Schema::dropIfExists('folders');
        Schema::dropIfExists('projects');
    }
};
