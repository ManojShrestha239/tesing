
<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSessionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('visits', function (Blueprint $table) {
            $table->id();
            $table->string('dealer_code')->nullable();
            $table->string('dealer_name')->nullable();
            $table->string('dealer_address')->nullable();
            $table->string('contact')->nullable();
            $table->bigInteger('user_id')->unsigned();
            $table->string('areacode')->nullable();
            $table->string('purpose_id');
            $table->time('start_time');
            $table->time('end_time');
            $table->string('date')->nullable();
            $table->string('status');
            $table->foreign('dealer_code')->references('code')->on('dealers')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('user_id')->references('id')->on('mobileusers')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('areacode')->references('areacode')->on('areas')->onDelete('cascade')->onUpdate('cascade');
            $table->longText('remarks')->nullable();
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
        Schema::dropIfExists('sessions');
    }
}
