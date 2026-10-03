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
        Schema::create('tada_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tada_entry_id')->constrained('tada_entries')->onDelete('cascade');
            $table->foreignId('tada_dealer_expense_id')->nullable()->constrained('tada_dealer_expenses')->onDelete('restrict');
            $table->foreignId('tada_other_expense_id')->nullable()->constrained('tada_other_expenses')->onDelete('restrict');
            $table->string('receipt_image_path', 512)->nullable()->comment('S3/Storage URL for the receipt');

            $table->timestamps();

            $table->index('tada_entry_id', 'idx_tada_image_entry');
            $table->index('tada_dealer_expense_id', 'idx_tada_image_dealer_exp');
            $table->index('tada_other_expense_id', 'idx_tada_image_other_exp');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tada_images');
    }
};
