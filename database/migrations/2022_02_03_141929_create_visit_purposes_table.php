<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVisitPurposesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('visit_purposes', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id')->unsigned();
            $table->string('dealer_code')->nullable();
            $table->bigInteger('visit_id')->unsigned();
            $table->string('areacode')->nullable();
            $table->bigInteger("purpose_id")->unsigned();
            $table->string('date');
            $table->string('status');
            $table->foreign('user_id')->references('id')->on('mobileusers');
            $table->foreign('dealer_code')->references('code')->on('dealers');
            $table->foreign('visit_id')->references('id')->on('visits');
            $table->foreign('purpose_id')->references('id')->on('purposes');
            $table->foreign('areacode')->references('areacode')->on('areas');
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
        Schema::dropIfExists('visit_purposes');
    }
}
