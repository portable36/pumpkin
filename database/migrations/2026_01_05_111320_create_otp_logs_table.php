<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('otp_logs', function (Blueprint $table) {
            $table->id();
            $table->string('type')->comment('Type of OTP: registration, password_reset, login, etc.');
            $table->string('channel')->comment('Channel used: email, sms, etc.');
            $table->string('provider')->nullable()->comment('Provider used: mail, fast2sms, bulksmsbd, etc.');
            $table->text('browser_agent')->nullable()->comment('User browser agent string');
            $table->unsignedBigInteger('user_id')->nullable()->index()->comment('User ID if available');
            $table->string('email')->nullable()->index()->comment('Email address where OTP was sent');
            $table->string('phone')->nullable()->index()->comment('Phone number where OTP was sent');
            $table->string('otp', 10)->nullable()->comment('OTP code');
            $table->string('status')->default('sent')->index()->comment('Status: sent, failed, verified, expired');
            $table->string('ip_address', 45)->nullable()->comment('IP address of the user');
            $table->text('error_message')->nullable()->comment('Error message if sending failed');
            $table->timestamps();
            
            // Indexes for better query performance
            $table->index(['type', 'status']);
            $table->index(['channel', 'provider']);
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('otp_logs');
    }
};
