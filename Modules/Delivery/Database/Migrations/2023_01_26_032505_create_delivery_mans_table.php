<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDeliveryMansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('delivery_mans', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('user_id');
            $table->string('unique_id')->unique();
            $table->string('license_status', 20)->default('Inactive');
            $table->tinyInteger('is_active');
            $table->timestamps();
        });

        Schema::table('delivery_mans', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('delivery_mans', function (Blueprint $table) {
            $table->dropIndex(['id', 'unique_id']);
            $table->dropForeign(['user_id']);
        });

        Schema::dropIfExists('delivery_mans');
    }
}
