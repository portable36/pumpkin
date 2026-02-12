<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->enum('type', ['order', 'wishlist', 'review', 'promo', 'system', 'message'])->default('system');
            $table->string('title');
            $table->text('message');
            $table->json('data')->nullable();
            $table->boolean('is_read')->default(false);
            $table->timestamp('read_at')->nullable();
            $table->string('action_url')->nullable();
            $table->timestamps();
            $table->index(['user_id', 'is_read']);
        });

        Schema::create('user_devices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('device_name')->nullable();
            $table->string('device_identifier')->unique();
            $table->enum('device_type', ['mobile', 'tablet', 'desktop'])->default('mobile');
            $table->string('os')->nullable();
            $table->string('os_version')->nullable();
            $table->string('browser')->nullable();
            $table->string('last_ip')->nullable();
            $table->text('last_user_agent')->nullable();
            $table->text('push_token')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamp('last_activity_at')->nullable();
            $table->timestamps();
            $table->index('user_id');
        });

        Schema::create('user_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('ip_address');
            $table->text('user_agent');
            $table->timestamp('last_activity_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
            $table->index('user_id');
        });

        Schema::create('login_attempts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->cascadeOnDelete();
            $table->string('email');
            $table->string('ip_address');
            $table->text('user_agent')->nullable();
            $table->enum('status', ['success', 'failed'])->default('failed');
            $table->string('reason')->nullable();
            $table->timestamp('attempted_at')->nullable();
            $table->timestamps();
            $table->index(['email', 'attempted_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('login_attempts');
        Schema::dropIfExists('user_sessions');
        Schema::dropIfExists('user_devices');
        Schema::dropIfExists('user_notifications');
    }
};
