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
        Schema::create('productmanuf_inventories', function (Blueprint $table) {
            $table->id();
            $table->integer('batch_number');
            $table->bigInteger('product_id')->unsigned();
            $table->integer('size_id');
            $table->float('total_qty');
            $table->date('date');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('productmanuf_inventories');
    }
};
