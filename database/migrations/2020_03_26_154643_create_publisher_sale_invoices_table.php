<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePublisherSaleInvoicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('publisher_sale_invoices', function (Blueprint $table) {
            $table->id();
            $table->double('total_sale')->nullable();
            $table->double('after_discount')->nullable();
            $table->double('discount');
            $table->boolean('completed');
            $table->double('amount_paid');
            $table->unsignedInteger('publisher_id');
            $table->string('invoicenumber');
            $table->foreign('publisher_id')->references('id')->on('users');
            $table->unsignedInteger('agent_id');
            $table->foreign('agent_id')->references('id')->on('agents');
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
        Schema::dropIfExists('publisher_sale_invoices');
    }
}
