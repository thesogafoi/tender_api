<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AdvertiseWorkGroup extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('advertise_work_group', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('advertise_id');
            $table->foreign('advertise_id')
                      ->references('id')
                      ->on('advertises');
            $table->unsignedBigInteger('work_group_id');
            $table->foreign('work_group_id')
                      ->references('id')
                      ->on('work_groups');

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
        Schema::dropIfExists('advertise_work_group');
    }
}
