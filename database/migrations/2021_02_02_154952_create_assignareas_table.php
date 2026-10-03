<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAssignareasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('assignareas', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('subarea_id')->unsigned();
            $table->string('areacode');
            $table->foreign('subarea_id')->references('id')->on('subareas');
            $table->foreign('areacode')->references('areacode')->on('areas');
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
        Schema::dropIfExists('assignareas');
    }
}
