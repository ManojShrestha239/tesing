<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDealersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dealers', function (Blueprint $table) {
            $table->string('code')->primary();
            $table->string('name');
            $table->string('address');
            $table->string('latitude')->nullable();
            $table->string('longitude')->nullable();
            $table->string('mobile')->unique();
            $table->string('email')->nullable();//->unique();
            $table->string('phone')->nullable();
            $table->string('state')->nullable();
            $table->string('area_code');
            $table->foreign('area_code')->references('areacode')->on('areas');
            $table->string('subarea');
            $table->integer('order_limit')->unsigned()->nullable();
            $table->string('status')->default("1");
            $table->string('active_status')->default("Active");
            $table->string('ledger_file')->nullable();
            $table->float('due_amount')->default(0);
            $table->float('cheque_in_hand')->default(0);
            $table->integer('cheque_nos')->default(0);
            $table->string('ledger_update_date')->nullable();
            $table->string('created_by')->nullable();
            $table->string('pan_vat_no')->nullable();
            $table->string('proprietor_name')->nullable();
            $table->boolean('sync')->default(false);
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
        Schema::dropIfExists('dealers');
    }
}
