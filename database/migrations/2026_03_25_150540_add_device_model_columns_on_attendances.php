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
        if (
            Schema::hasTable('attendances') &&
            !Schema::hasColumn('attendances', 'device_model')
        ) {

            Schema::table('attendances', function (Blueprint $table) {
                $table->string('device_model')->nullable()->after('visit_method');
            });
        }

        if (
            Schema::hasTable('attendance_statuses') &&
            !Schema::hasColumn('attendance_statuses', 'device_model')
        ) {

            Schema::table('attendance_statuses', function (Blueprint $table) {
                $table->string('device_model')->nullable()->after('visit_method');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        if (
            Schema::hasTable('attendances') &&
            Schema::hasColumn('attendances', 'device_model')
        ) {

            Schema::table('attendances', function (Blueprint $table) {
                $table->dropColumn('device_model');
            });
        }

        if (
            Schema::hasTable('attendance_statuses') &&
            !Schema::hasColumn('attendance_statuses', 'device_model')
        ) {

            Schema::table('attendance_statuses', function (Blueprint $table) {
                $table->string('device_model')->nullable()->after('visit_method');
            });
        }
    }
};
