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
        if (Schema::hasTable('mobileusers')) {
            if (!Schema::hasColumn('mobileusers', 'is_tracking_enabled')) {
                Schema::table('mobileusers', function (Blueprint $table) {
                    $table->boolean('is_tracking_enabled')->default(true)->after('status');
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('mobileusers') && Schema::hasColumn('mobileusers', 'is_tracking_enabled')) {
            Schema::table('mobileusers', function (Blueprint $table) {
                $table->dropColumn('is_tracking_enabled');
            });
        }
    }
};
