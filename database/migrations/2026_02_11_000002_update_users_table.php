<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone')->nullable()->after('email');
            $table->timestamp('phone_verified_at')->nullable()->after('email_verified_at');
            $table->timestamp('last_login_at')->nullable();
            $table->string('last_login_ip')->nullable();
            $table->boolean('is_active')->default(true);
            $table->foreignId('vendor_id')->nullable()->constrained('vendors')->cascadeOnDelete();
            $table->string('google_id')->nullable()->unique();
            $table->string('facebook_id')->nullable()->unique();
            $table->string('avatar')->nullable();
            $table->text('bio')->nullable();
            $table->string('timezone')->default('UTC');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'phone',
                'phone_verified_at',
                'last_login_at',
                'last_login_ip',
                'is_active',
                'vendor_id',
                'google_id',
                'facebook_id',
                'avatar',
                'bio',
                'timezone',
            ]);
        });
    }
};
