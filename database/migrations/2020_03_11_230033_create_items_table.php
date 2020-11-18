<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('items', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->double('price');
            $table->double('sale');
            $table->integer('edition');
            $table->boolean('status');
            $table->string('book_image')->nullable();
            $table->unsignedInteger('series_id');
            $table->foreign('series_id')->references('id')->on('series')->onDelete('cascade');
            $table->unsignedInteger('learner_stage_id');
            $table->foreign('learner_stage_id')->references('id')->on('learner_stages')->onDelete('cascade');
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
        Schema::dropIfExists('items');
    }
}
