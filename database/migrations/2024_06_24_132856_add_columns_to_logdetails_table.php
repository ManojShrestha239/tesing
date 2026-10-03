<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('logdetails', function (Blueprint $table) {
            $table->double('previous_due_amount')->nullable()->default(null)->after('previous_status');
            $table->double('previous_lastyearsales')->nullable()->default(null)->after('previous_due_amount');
            $table->string('amount')->nullable()->default(null)->after('previous_lastyearsales')->comment('for edit collection amount');
            $table->string('update_status')->nullable()->default(null)->after('amount')->comment('for edit collection status');
            $table->double('update_due_amount')->nullable()->default(null)->after('update_status');
            $table->double('update_lastyearsales')->nullable()->default(null)->after('update_due_amount');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('logdetails', function (Blueprint $table) {
            //
        });
    }
};
