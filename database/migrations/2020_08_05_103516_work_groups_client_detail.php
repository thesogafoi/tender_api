<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class WorkGroupsClientDetail extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('work_groups_client_detail', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('client_detail_id');
            $table->foreign('client_detail_id')
                      ->references('id')
                      ->on('client_details');
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
        Schema::dropIfExists('work_groups_client_detail');
    }
}
