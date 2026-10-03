<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAttendanceStatusesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('attendance_statuses', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('attendance_id')->unsigned();
            $table->bigInteger('mobileuser_id')->unsigned();
            $table->string('date');
            $table->string('time');
            $table->string('address')->nullable();
            $table->string('status');
            $table->timestamps();
            $table->foreign('attendance_id')->references('id')->on('attendances');
            $table->foreign('mobileuser_id')->references('id')->on('mobileusers');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('attendance_statuses');
    }
}
