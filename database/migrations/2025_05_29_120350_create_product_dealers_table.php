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
        Schema::create('product_dealers', function (Blueprint $table) {
            $table->id();
            // $table->foreignId('dealer_id')->constrained('dealers', 'code')->onDelete('cascade');
            $table->string('dealer_id');
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->integer('size_id');

            $table->integer('assign_qty');
            $table->date('date');
            $table->timestamps();

            // Define foreign key constraint explicitly
            $table->foreign('dealer_id')
                ->references('code')
                ->on('dealers')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_dealers');
    }
};
