<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClassgroupLearnerstageTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('classgroup_learner_stage', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('classgroup_id');
            $table->foreign('classgroup_id')->references('id')->on('classgroups')->onDelete('cascade');
            $table->unsignedInteger('learner_stage_id');
            $table->foreign('learner_stage_id')->references('id')->on('learner_stages')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('classgroup_learner_stage');
    }
}
