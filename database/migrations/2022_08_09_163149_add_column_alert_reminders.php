<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnAlertReminders extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('alert_reminders', function (Blueprint $table) {
            //
            $table->string('dealer_code')->nullable()->change();
            $table
                ->string('dealer_name')
                ->nullable()
                ->after('dealer_code');
            $table
                ->string('dealer_address')
                ->nullable()
                ->after('dealer_name');
            $table
                ->string('dealer_contact')
                ->nullable()
                ->after('dealer_address');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('alert_reminders', function (Blueprint $table) {
            //
        });
    }
}
