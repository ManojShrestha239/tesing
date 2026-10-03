<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLogdetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('logdetails', function (Blueprint $table) {
            $table->id();
            $table->integer('ordercoll_id')->unsigned();
            $table->string('flag');
            $table->bigInteger('user_id')->unsigned();
            $table->integer('tint_user')->nullable();
            $table->string('cheque_no')->nullable();
            $table->date('date');
            $table->integer('admin')->nullable();
            $table->integer('mo_user')->unsigned();
            $table->string('status');
            $table->string('previous_status');
            $table->text('remarks')->nullable();
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
        Schema::dropIfExists('logdetails');
    }
}
