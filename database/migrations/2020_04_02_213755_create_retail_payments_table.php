<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRetailPaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('retail_payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('agent_id');
            $table->foreign('agent_id')->references('id')->on('agents');
            $table->unsignedInteger('retail_id');
            $table->foreign('retail_id')->references('id')->on('retails');
            $table->unsignedInteger('payment_method_id');
            $table->foreign('payment_method_id')->references('id')->on('payment_methods');
            $table->integer('amount');
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
        Schema::dropIfExists('retail_payments');
    }
}
