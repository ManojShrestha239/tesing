<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSchedulevisitsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('schedulevisits', function (Blueprint $table) {
            $table->id();
            $table->string('dealer_code');
            $table->bigInteger('user_id')->unsigned();
            $table->string('purpose')->nullable();
            $table->string('purpose_visit_id')->nullable();
            $table->longText('purpose_id');
            $table->date('date');
            $table->integer('is_visit');
            $table->string('remarks')->nullable();
            $table->foreign('dealer_code')->references('code')->on('dealers');
            $table->foreign('user_id')->references('id')->on('mobileusers');
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
        Schema::dropIfExists('schedulevisits');
    }
}
