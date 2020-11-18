<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePublisherDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('publisher_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->double('sold_to_agents')->nullable();
            $table->double('agents_balance')->nullable();
            $table->double('sold_by_agents')->nullable();
            $table->double('received_from_agents')->nullable();
            $table->double('received_but_with_agents')->nullable();
            $table->double('indebt_with_retailers')->nullable();
            $table->string('Year');
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
        Schema::dropIfExists('publisher_details');
    }
}
