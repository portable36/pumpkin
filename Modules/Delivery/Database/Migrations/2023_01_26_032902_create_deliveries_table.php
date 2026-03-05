<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDeliveriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('deliveries', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('delivery_man_id');
            $table->unsignedBigInteger('order_id');
            $table->date('date')->index();
            $table->decimal('collected_amount', 16, 8);
            $table->text('remark')->nullable();
            $table->timestamps();
        });

        Schema::table('deliveries', function (Blueprint $table) {
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
        Schema::table('deliveries', function (Blueprint $table) {
            $table->dropIndex(['id', 'date']);
            $table->dropForeign(['delivery_man_id', 'order_id']);
        });

        Schema::dropIfExists('deliveries');
    }
}
