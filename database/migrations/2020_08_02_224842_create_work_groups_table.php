<?php

use App\WorkGroup;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWorkGroupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('work_groups', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('parent_id')->index()->nullable();
            $table->enum('type', WorkGroup::types())->default(WorkGroup::types()[0]);
            $table->string('title');
            $table->string('image')->nullable();
            $table->boolean('status')->default(1);
            $table->unsignedTinyInteger('priorty')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('work_groups', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
        Schema::dropIfExists('work_groups');
    }
}
