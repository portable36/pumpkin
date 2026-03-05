<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->id();

            $table->bigInteger('vendor_id')->nullable()
                ->index('customers_vendor_id_foreign_idx');

            $table->foreign('vendor_id')->references('id')
                ->on('vendors')->onUpdate('cascade')->onDelete('cascade');

            $table->string('name')->index();
            $table->string('email')->index()->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('phone', 15)->nullable();
            $table->string('gender', 10)->nullable();
            $table->tinyText('address')->nullable();
            $table->date('birthday')->nullable();
            $table->string('sso_account_id')->nullable();
            $table->string('sso_service')->nullable();
            $table->rememberToken();
            $table->string('status', 15)->default('Pending')->index();
            $table->string('activation_code')->nullable();
            $table->string('activation_otp')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
