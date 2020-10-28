<?php

use App\Advertise;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAdvertisesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('advertises', function (Blueprint $table) {
            $table->id();
            $table->string('tender_code', 255)->nullable();
            $table->string('title', '1000');
            $table->unsignedTinyInteger('status')->default(0);
            $table->unsignedBigInteger('adinviter_id')->nullable();
            $table->string('adinviter_title')->nullable();
            $table->enum('type', Advertise::types())->default(Advertise::types()[0]);
            $table->string('invitation_code', 255)->nullable();
            $table->date('receipt_date')->nullable();
            $table->date('submit_date')->nullable();
            $table->date('start_date')->nullable();
            $table->date('free_date')->nullable();
            $table->date('invitation_date')->nullable();
            $table->text('description')->nullable();
            $table->text('resource')->nullable();
            $table->boolean('is_nerve_center')->default(0);
            $table->string('image')->nullable();
            $table->string('link')->nullable();
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
        Schema::dropIfExists('advertises');
    }
}
