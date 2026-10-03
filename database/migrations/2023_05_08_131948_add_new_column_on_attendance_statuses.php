<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddNewColumnOnAttendanceStatuses extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('attendance_statuses', function (Blueprint $table) {
           
            $table->decimal('km')->nullable();
            $table->string('visit_method')->nullable();
            $table->string('remarks')->nullable();
        });

        Schema::table('attendances', function (Blueprint $table) {
            $table->string('visit_method')->nullable();
            $table->string('remarks')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
