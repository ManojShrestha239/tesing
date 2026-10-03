<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDenominationpointsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('denominationpoints', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('collection_id')->unsigned();
            $table->bigInteger('denomination_id')->unsigned();
            $table->integer('denomination');
            $table->integer('quantity');
            $table->integer('total');
            $table->foreign('collection_id')->references('id')->on('payments');
            $table->foreign('denomination_id')->references('id')->on('denominations');
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
        Schema::dropIfExists('denominationpoints');
    }
}
