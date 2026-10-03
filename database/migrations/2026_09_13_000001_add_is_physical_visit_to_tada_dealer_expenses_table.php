<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('tada_dealer_expenses', 'is_physical_visit')) {
            Schema::table('tada_dealer_expenses', function (Blueprint $table) {
                $table->boolean('is_physical_visit')->nullable()->after('amount');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('tada_dealer_expenses', 'is_physical_visit')) {
            Schema::table('tada_dealer_expenses', function (Blueprint $table) {
                $table->dropColumn('is_physical_visit');
            });
        }
    }
};
