<?php

use App\ClientDetail;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClientDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('client_details', function (Blueprint $table) {
            $table->id();
            $table->string('phone', 11)->nullable();
            $table->enum('type', ClientDetail::types())->nullable();
            $table->string('company_name')->nullable();
            $table->unsignedBigInteger('user_id')->index();
            $table->date('subscription_date')->nullable();
            $table->string('subscription_title')->nullable();
            $table->unsignedInteger('subscription_count')->default(0);
            $table->unsignedInteger('work_groups_changes')->default(0);
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
        Schema::dropIfExists('client_details');
    }
}
