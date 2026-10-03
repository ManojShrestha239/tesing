<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('dealer_code');
            $table->bigInteger('user_id')->unsigned()->nullable();
            $table->string('areacode');
            $table->bigInteger('tint_user')->unsigned()->nullable();
            $table->string('date')->nullable();
            $table->float('total_price')->default(0);
            $table->string('status');
            $table->string('remarks')->nullable();
            $table->string('custom_unit_remarks')->nullable();
            $table->string('user_type')->nullable();
            $table->foreign('dealer_code')->references('code')->on('dealers')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('user_id')->references('id')->on('mobileusers')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('areacode')->references('areacode')->on('areas')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('tint_user')->references('id')->on('dealer_user_info')->onDelete('cascade')->onUpdate('cascade');
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
        Schema::dropIfExists('orders');
    }
}
