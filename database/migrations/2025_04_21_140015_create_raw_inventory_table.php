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
        Schema::create('raw_inventory', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('pm_id')->nullable(); // Product Manufacture
            $table->unsignedBigInteger('r_id');              // Raw Material

            $table->float('total_qty');
            $table->float('withheld');
            $table->float('available');

            $table->string('status')->nullable();
            $table->integer('flag')->nullable();
            $table->date('date');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('raw_inventory');
    }
};
