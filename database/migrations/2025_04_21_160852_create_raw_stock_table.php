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
        Schema::create('raw_stock', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('r_id');
            $table->float('total_qty');
            $table->float('withdraw')->default(0);
            $table->float('available');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('raw_stock');
    }
};
