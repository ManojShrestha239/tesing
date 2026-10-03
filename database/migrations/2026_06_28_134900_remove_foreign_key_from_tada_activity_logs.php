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
        Schema::table('tada_activity_logs', function (Blueprint $table) {
            // Drop foreign key constraint
            $table->dropForeign('tada_activity_logs_action_by_user_id_foreign');
            // Make column nullable
            $table->unsignedBigInteger('action_by_user_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tada_activity_logs', function (Blueprint $table) {
            $table->foreign('action_by_user_id')->references('id')->on('users')->onDelete('restrict');
        });
    }
};
