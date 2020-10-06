<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ClientDetailFavoriteAdvertise extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('client_detail_favorite_advertise', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('advertise_id');
            $table->foreign('advertise_id')
                      ->references('id')
                      ->on('advertises');
            $table->unsignedBigInteger('client_detail_id');
            $table->foreign('client_detail_id')
                      ->references('id')
                      ->on('client_details');

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
        Schema::dropIfExists('client_detail_favorite_advertise');
    }
}
