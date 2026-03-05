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
        Schema::create('customer_addresses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained('customers')->onDelete('cascade');

            $table->bigInteger('vendor_id')->nullable()
                ->index('customer_addresses_vendor_id_foreign_idx');

            $table->foreign('vendor_id')->references('id')
                ->on('vendors')->onUpdate('cascade')->onDelete('cascade');

            $table->string('company_name')->nullable();
            $table->string('type_of_place', 20)->default('home')->comment('home/office');
            $table->string('address_1', 191);
            $table->string('address_2', 191)->nullable();
            $table->string('city', 100)->index();
            $table->string('state', 100)->nullable()->index();
            $table->string('zip', 191)->nullable()->index();
            $table->string('country', 191)->index();
            $table->mediumInteger('is_default')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_addresses');
    }
};
