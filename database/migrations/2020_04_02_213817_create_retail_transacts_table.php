<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRetailTransactsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('retail_transacts', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('agent_id');
            $table->foreign('agent_id')->references('id')->on('agents');
            $table->unsignedInteger('retail_id');
            $table->foreign('retail_id')->references('id')->on('retails');
            $table->integer('pbalance');
            $table->integer('cbalance');
            $table->integer('amount');
            $table->string('transacttype');
            $table->integer('retail_transactable_id');
            $table->string('retail_transactable_type');
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
        Schema::dropIfExists('retail_transacts');
    }
}
