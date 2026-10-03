<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('dealer_stocks')) {
            Schema::create('dealer_stocks', function (Blueprint $table) {
                $table->id();

                $table->string('dealer_code');
                $table->foreign('dealer_code')
                    ->references('code')
                    ->on('dealers')
                    ->onDelete('restrict')
                    ->onUpdate('cascade');

                $table->foreignId('productsize_id')
                    ->constrained('product_size')
                    ->onDelete('restrict')
                    ->onUpdate('cascade');

                $table->unsignedInteger('available_quantity')->default(0);
                $table->unsignedInteger('total_quantity')->default(0);
                $table->boolean('is_active')->default(true);
                $table->timestamps();
                $table->softDeletes();

                // === INDEXES ===
                $table->unique(['dealer_code', 'productsize_id'], 'dealer_stocks_dealer_product_unique');
                $table->index('dealer_code');
                $table->index('productsize_id');
                $table->index('is_active');
                $table->index('deleted_at');

                $table->comment('Stores current stock levels per dealer and product size');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('dealer_stocks')) {
            Schema::dropIfExists('dealer_stocks');
        }
    }
};
