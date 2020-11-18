<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAgentSaleInvoicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('agent_sale_invoices', function (Blueprint $table) {
            $table->id();
            $table->double('total_sale')->nullable();
            $table->double('after_discount')->nullable();
            $table->double('amount_paid')->nullable();
            $table->boolean('completed');
            $table->double('discount');
            $table->string('invoicenumber');
            $table->unsignedInteger('agent_id');
            $table->foreign('agent_id')->references('id')->on('agents');
            $table->unsignedInteger('retail_id');
            $table->foreign('retail_id')->references('id')->on('retails');
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
        Schema::dropIfExists('agent_sale_invoices');
    }
}
