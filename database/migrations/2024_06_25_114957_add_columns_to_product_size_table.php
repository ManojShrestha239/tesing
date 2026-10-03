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
        Schema::table('product_size', function (Blueprint $table) {
            $table->unsignedInteger('subproduct_id')->after('product_id');
            $table->string('sub_product')->nullable()->default(null)->after('subproduct_id');
            $table->string('SQFT')->nullable()->default(null);
            $table->string('SQMT')->nullable()->default(null);
            $table->string('weight_per_sq_ft')->nullable()->default(null);
            $table->string('rate')->nullable()->default(null);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_size', function (Blueprint $table) {
            //
        });
    }
};
