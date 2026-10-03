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
        Schema::table('orderitems', function (Blueprint $table) {
            $table->double('height')->nullable()->default(null)->comment('ft');
            $table->double('width')->nullable()->default(null)->comment('ft');
            $table->string('size')->nullable()->default(null)->comment('ft');
            $table->double('area')->nullable()->default(null)->comment('sq ft');
            $table->double('weight')->nullable()->default(null);
            $table->double('discount')->default(0.00)->comment('ft');
            $table->double('vat')->default(0.00)->comment('ft');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orderitems', function (Blueprint $table) {
            //
        });
    }
};
