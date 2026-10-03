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
        Schema::table('orders', function (Blueprint $table) {
            $table->string('delivery_time')->nullable()->default(null);
            $table->string('delivery_cost')->nullable()->default(null);
            $table->double('due')->nullable()->default(0.00);
            $table->string('last_collection_date')->nullable()->default(null);
            $table->double('last_collection')->nullable()->default(0.00);
            $table->double('discount')->default(0.00);
            $table->double('vat')->default(0.00);
            $table->tinyInteger('is_need_correction')->default(0);
            $table->string('risk_zone')->nullable()->default(null);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            //
        });
    }
};
