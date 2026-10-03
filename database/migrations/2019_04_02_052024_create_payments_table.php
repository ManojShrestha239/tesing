<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->string('payment_type');
            $table->string('dealer_code');
            $table->foreign('dealer_code')->references('code')->on('dealers')->onDelete('cascade')->onUpdate('cascade');
            $table->bigInteger('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('mobileusers')->onDelete('cascade')->onUpdate('cascade');
            $table->string('areacode');
            $table->foreign('areacode')->references('areacode')->on('areas')->onDelete('cascade')->onUpdate('cascade');
            $table->string('collection_date')->nullable();
            $table->float('amount')->unsigned();
            $table->float('discount')->unsigned();
            $table->integer('token')->default(0);
            $table->float('TDS')->unsigned();
            $table->string('payment_date')->nullable();
            $table->string('cheque_num')->nullable();
            $table->string('receipt_num')->nullable();
            $table->bigInteger('bank_id')->unsigned()->nullable();
            $table->foreign('bank_id')->references('id')->on('banks')->onDelete('cascade')->onUpdate('cascade');
            $table->text('mRemarks')->nullable();
            $table->text('remarks')->nullable();
            $table->string('status')->nullable();
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
        Schema::dropIfExists('payments');
    }
}
