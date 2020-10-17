<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ClientDetailPlanes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('client_detail_planes', function (Blueprint $table) {
            $table->id();
            $table->string('plane_title', 255);
            $table->date('plane_submited');
            $table->date('plane_expired');
            $table->string('plane_cost', 255);
            $table->unsignedInteger('client_detail_id')->index();
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
        Schema::dropIfExists('client_detail_planes');
    }
}
