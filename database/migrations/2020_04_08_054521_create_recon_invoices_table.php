<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReconInvoicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('recon_invoices', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('agent_sale_invoice_id');
            $table->foreign('agent_sale_invoice_id')->references('id')->on('agent_sale_invoices');
            $table->unsignedInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users');
            $table->double('to_be_paid_to_publisher')->nullable();
            $table->double('to_be_paid_to_agent')->nullable();
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
        Schema::dropIfExists('recon_invoices');
    }
}
