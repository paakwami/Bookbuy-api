<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRetailPaymentDisectionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('retail_payment_disections', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('retail_payment_id');
            $table->foreign('retail_payment_id')->references('id')->on('retail_payments');
            $table->unsignedInteger('agent_sale_invoice_id');
            $table->foreign('agent_sale_invoice_id')->references('id')->on('agent_sale_invoices');
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
        Schema::dropIfExists('retail_payment_disections');
    }
}
