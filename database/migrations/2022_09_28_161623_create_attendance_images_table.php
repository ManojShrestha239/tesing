<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAttendanceImagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('attendance_images', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('attendance_id')->unsigned();
            $table->integer('attendancestatus_id')->unsigned();
            $table->integer('mobileuser_id')->unsigned();
            $table->string('image');
            $table->date('date');
            $table->time('time');
            $table->string('status');
            // $table->foreign('mobileuser_id')->references('id')->on('mobileusers')->onDelete('cascade')->onUpdate('cascade');
            // $table->foreign('attendance_id')->references('id')->on('attendances')->onDelete('cascade')->onUpdate('cascade');
            // $table->foreign('attendancestatus_id')->references('id')->on('attendance_statuses')->onDelete('cascade')->onUpdate('cascade');
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
        Schema::dropIfExists('attendance_images');
    }
}
