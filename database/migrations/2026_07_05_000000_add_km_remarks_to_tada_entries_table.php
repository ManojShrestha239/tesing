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
        Schema::table('tada_entries', function (Blueprint $table) {
            if (!Schema::hasColumn('tada_entries', 'km_remarks')) {
                $table->string('km_remarks', 500)->nullable()->after('admin_remarks');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tada_entries', function (Blueprint $table) {
            if (Schema::hasColumn('tada_entries', 'km_remarks')) {
                $table->dropColumn('km_remarks');
            }
        });
    }
};
