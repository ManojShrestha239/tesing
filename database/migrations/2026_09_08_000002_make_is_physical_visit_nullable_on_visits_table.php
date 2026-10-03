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
        if (Schema::hasColumn('visits', 'is_physical_visit')) {
            Schema::table('visits', function (Blueprint $table) {
                $table->boolean('is_physical_visit')->nullable()->default(null)->change();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('visits', 'is_physical_visit')) {
            Schema::table('visits', function (Blueprint $table) {
                $table->boolean('is_physical_visit')->nullable(false)->default(true)->change();
            });
        }
    }
};
