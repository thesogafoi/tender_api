<?php

use App\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255);
            $table->string('mobile', 20);
            $table->enum('type', User::types())->default('CLIENT');
            $table->timestamp('mobile_verified_at')->nullable();
            $table->string('password', 255);
            $table->unsignedInteger('client_detail_id')->nullable();
            $table->boolean('status')->default(1);
            $table->rememberToken();
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
        Schema::dropIfExists('users');
    }
}
