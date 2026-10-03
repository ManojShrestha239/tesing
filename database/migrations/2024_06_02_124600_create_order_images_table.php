<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrderImagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_images', function (Blueprint $table) {
            $table->increments('id');
            // $table->integer('order_id')->unsigned();
            $table->unsignedBigInteger('mobileuser_id');
            // $table->integer('mobileuser_id')->unsigned();
            $table->string('dealer_code');
            // $table->string('area_code');
            // $table->date('date');
            $table->string('image');
            $table->string('url');
            $table->string('status');
            $table->boolean('is_form')->default(0);
            // $table->string('status');
            // $table->string('remarks');
            // $table->date('payment_commitment_date');
            // $table->foreign('order_id')->references('id')->on('orders')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('mobileuser_id')->references('id')->on('mobileusers')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('dealer_code')->references('code')->on('dealers')->onUpdate('cascade')->onDelete('cascade');
            // $table->foreign('area_code')->references('areacode')->on('areas')->onUpdate('cascade')->onDelete('cascade');
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
        Schema::dropIfExists('order_images');
    }
}
