<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDeliveryManShippingZonesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('delivery_man_shipping_zones', function (Blueprint $table) {
            $table->unsignedBigInteger('delivery_man_id');
            $table->unsignedInteger('shipping_zone_id');
        });

        Schema::table('delivery_man_shipping_zones', function (Blueprint $table) {
            $table->foreign(['delivery_man_id'])->references(['id'])->on('delivery_mans')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign(['shipping_zone_id'])->references(['id'])->on('shipping_zones')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('delivery_man_shipping_zones', function (Blueprint $table) {
            $table->dropForeign(['delivery_man_id', 'shipping_zone_id']);
        });

        Schema::dropIfExists('delivery_man_shipping_zones');
    }
}
