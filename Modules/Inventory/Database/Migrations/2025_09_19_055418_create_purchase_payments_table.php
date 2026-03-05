<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePurchasePaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('purchase_payments', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('purchase_id')->index('purchase_payments_purchase_id_foreign_idx');
            $table->bigInteger('vendor_id')->index('purchase_payments_vendor_id_foreign_idx');
            $table->bigInteger('supplier_id')->index('purchase_payments_supplier_id_foreign_idx');
            $table->date('payment_date');
            $table->decimal('amount', 16, 8);
            $table->string('payment_method')->nullable();
            $table->string('description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('purchase_payments');
    }
}
