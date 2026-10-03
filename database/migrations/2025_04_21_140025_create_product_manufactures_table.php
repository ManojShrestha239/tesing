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
        Schema::create('product_manufactures', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('p_id');

            $table->float('total_qty');
            $table->float('withdraw_qty');
            $table->float('available_qty');
            $table->date('date');
            $table->string('status')->nullable();
            $table->integer('flag')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_manufactures');
    }
};
