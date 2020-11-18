<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAgentPaymentDisectionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('agent_payment_disections', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('agent_payment_id');
            $table->foreign('agent_payment_id')->references('id')->on('agent_payments');
            $table->unsignedInteger('publisher_sale_invoice_id');
            $table->foreign('publisher_sale_invoice_id')->references('id')->on('publisher_sale_invoices');
            $table->double('amount');
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
        Schema::dropIfExists('agent_payment_disections');
    }
}
