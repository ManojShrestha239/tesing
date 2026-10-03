<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDealerUserInfoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dealer_user_info', function (Blueprint $table) {
            $table->id();
            $table->string('full_name');
            $table->string('address');
            $table->string('phone');
            $table->string('user_code');
            $table->string('password');
            $table->string('user_pass_o');
            $table->string('dealer_id');
            $table->integer('user_role');
            $table->integer('status');
            $table->integer('created_by')->nullable();
            $table->text('token')->nullable();
            $table->string('fcm_id')->nullable();
            $table->string('device_id')->nullable();
            $table->foreign('dealer_id')->references('code')->on('dealers');
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
        Schema::dropIfExists('dealer_user_info');
    }
}
