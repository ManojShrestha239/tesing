<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSchedulePurposesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('schedule_purposes', function (Blueprint $table) {
            $table->id();;
            $table->bigInteger('visitschedule_id')->unsigned();
            $table->bigInteger('purpose_id')->unsigned();
            $table->string('status');
            $table->foreign('visitschedule_id')->references('id')->on('schedulevisits');
            $table->foreign('purpose_id')->references('id')->on('purposes');
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
        Schema::dropIfExists('schedule_purposes');
    }
}
