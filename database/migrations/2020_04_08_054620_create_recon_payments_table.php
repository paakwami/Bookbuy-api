<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReconPaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('recon_payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users');
            $table->unsignedInteger('retail_payment_disection_id');
            $table->foreign('retail_payment_disection_id')->references('id')->on('retail_payment_disections');
            $table->unsignedInteger('agent_sale_invoice_id');
            $table->foreign('agent_sale_invoice_id')->references('id')->on('agent_sale_invoices');
            $table->double('paid_to_agent')->nullable();
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
        Schema::dropIfExists('recon_payments');
    }
}
