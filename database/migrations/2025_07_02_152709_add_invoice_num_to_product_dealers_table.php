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
        if (Schema::hasTable('product_dealers') && !Schema::hasColumn('product_dealers', 'invoice_num')) {
            Schema::table('product_dealers', function (Blueprint $table) {
                $table->string('invoice_num')->nullable()->after('size_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('product_dealers') && Schema::hasColumn('product_dealers', 'invoice_num')) {
            Schema::table('product_dealers', function (Blueprint $table) {
                $table->dropColumn('invoice_num');
            });
        }
    }
};
