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
        Schema::create('vendor_attributes', function (Blueprint $table) {
            $table->bigInteger('id', true);
            $table->integer('attribute_id')->index('vendor_attributes_attribute_id_foreign_idx');
            $table->bigInteger('vendor_id')->index('vendor_attributes_vendor_id_foreign_idx');
            $table->bigInteger('shop_id')->nullable()->index('vendor_attributes_shop_id_foreign_idx');
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vendor_attributes');
    }
};
