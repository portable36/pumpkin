<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDeliveryCommissionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('delivery_commissions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('delivery_man_id');
            $table->unsignedBigInteger('order_id');
            $table->unsignedBigInteger('order_details_id');
            $table->date('date')->index();
            $table->decimal('amount', 16, 8);
            $table->dateTime('approval_time', 5)->nullable()->index();
            $table->string('status', 12)->default('Pending')->index();
            $table->text('remark')->nullable();
            $table->timestamps();
        });
        Schema::table('delivery_commissions', function (Blueprint $table) {
            $table->foreign('delivery_man_id')->references('id')->on('delivery_mans')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('order_id')->references('id')->on('orders')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('delivery_commissions');
    }
}
