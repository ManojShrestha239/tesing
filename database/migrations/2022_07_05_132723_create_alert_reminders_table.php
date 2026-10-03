<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAlertRemindersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('alert_reminders', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedBigInteger('mobileuser_id');
            $table->string('dealer_code');
            $table->string('date');
            $table->text('remarks');
            $table->string('status')->nullable();
            $table->string('flag')->default('Pending');
            $table->timestamps();
            $table->foreign('mobileuser_id')->references('id')->on('mobileusers')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('dealer_code')->references('code')->on('dealers')->onDelete('cascade')->onUpdate('cascade');
        });
        Schema::create('reminder_purposes', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('alertreminder_id');
            $table->unsignedBigInteger('purpose_id');
            $table->foreign('alertreminder_id')->references('id')->on('alert_reminders')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('purpose_id')->references('id')->on('purposes')->onDelete('cascade')->onUpdate('cascade');
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
        Schema::dropIfExists('alert_reminders');
    }
}
