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
        if (!Schema::hasTable('stock_transactions')) {
            Schema::create('stock_transactions', function (Blueprint $table) {
                $table->id();

                $table->foreignId('dealer_stock_id')
                    ->constrained('dealer_stocks')
                    ->onDelete('cascade')
                    ->onUpdate('cascade');

                $table->string('retailer_code')->nullable();
                $table->foreign('retailer_code')
                    ->references('code')
                    ->on('dealers')
                    ->onDelete('set null')
                    ->onUpdate('cascade');

                $table->integer('quantity')->comment('Positive = in, negative = out');
                $table->string('reason')->comment('e.g., restock, sale, adjustment');

                $table->string('transaction_type')
                    ->default('edit')
                    ->comment('Type of stock movement are in, out, or edit');

                $table->nullableMorphs('created_by');

                $table->timestamps();
                $table->softDeletes();

                $table->index('dealer_stock_id');
                $table->index('retailer_code');
                $table->index('transaction_type');
                $table->index('created_at');
                $table->index('deleted_at');
                $table->index(['dealer_stock_id', 'created_at']);

                $table->comment('Individual stock movements with polymorphic creator tracking');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasTable('stock_transactions')) {
            Schema::dropIfExists('stock_transactions');
        }
    }
};
