<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tada_dealer_expenses', function (Blueprint $table) {
            $table->foreignId('expenses_authorized_by')
                ->nullable()
                ->after('amount')
                ->constrained('tada_authorized_users')
                ->onDelete('restrict');
        });

        Schema::table('tada_other_expenses', function (Blueprint $table) {
            $table->foreignId('expenses_authorized_by')
                ->nullable()
                ->after('amount')
                ->constrained('tada_authorized_users')
                ->onDelete('restrict');
        });
    }

    public function down(): void
    {
        Schema::table('tada_dealer_expenses', function (Blueprint $table) {
            $table->dropForeign(['expenses_authorized_by']);
            $table->dropColumn('expenses_authorized_by');
        });

        Schema::table('tada_other_expenses', function (Blueprint $table) {
            $table->dropForeign(['expenses_authorized_by']);
            $table->dropColumn('expenses_authorized_by');
        });
    }
};
