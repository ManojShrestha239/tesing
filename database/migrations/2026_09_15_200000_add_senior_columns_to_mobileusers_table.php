<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSeniorColumnsToMobileusersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('mobileusers', function (Blueprint $table) {
            $table->boolean('is_senior')->default(false)->after('is_tracking_enabled');
            $table->boolean('is_super_senior')->default(false)->after('is_senior');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('mobileusers', function (Blueprint $table) {
            $table->dropColumn(['is_senior', 'is_super_senior']);
        });
    }
}
