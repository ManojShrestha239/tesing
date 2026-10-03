<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRepeatedDealerVisitsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('daily_visit_dealers', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('repeateschedule_id')->unsigned();
            $table->string('code');
            $table->foreign('repeateschedule_id')->references('id')->on('repeateschedules');
            $table->foreign('code')->references('code')->on('dealers');
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
        Schema::dropIfExists('daily_visit_dealers');
    }
}
