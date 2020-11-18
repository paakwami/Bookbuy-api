<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReconTransactsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('recon_transacts', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users');
            $table->unsignedInteger('agent_id');
            $table->foreign('agent_id')->references('id')->on('agents');
            $table->double('amount_supplied')->nullable();
            $table->double('payment_received_by_agent')->nullable();
            $table->double('in_debt')->nullable();
            $table->double('payment_to_publisher')->nullable();
            $table->double('in_debt_to_publisher')->nullable();
            $table->string('transactiontype');
            $table->integer('recon_transactable_id');
            $table->string('recon_transactable_type');
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
        Schema::dropIfExists('recon_transacts');
    }
}
