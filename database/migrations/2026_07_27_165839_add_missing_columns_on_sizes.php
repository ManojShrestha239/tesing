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
        Schema::table('sizes', function (Blueprint $table) {
            if (!Schema::hasColumn('sizes', 'order_index')) {
                $table->integer('order_index')->nullable()->after('status');
            }
            if (!Schema::hasColumn('sizes', 'flag')) {
                $table->string('flag', 255)->default('normal')->after('order_index');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sizes', function (Blueprint $table) {
            // Drop the columns if they exist (safe for rollback)
            if (Schema::hasColumn('sizes', 'order_index')) {
                $table->dropColumn('order_index');
            }
            if (Schema::hasColumn('sizes', 'flag')) {
                $table->dropColumn('flag');
            }
        });
    }
};
