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
        if (Schema::hasTable('subdealer_stock_logs')) {
            Schema::dropIfExists('subdealer_stock_logs');
        }

        if (Schema::hasTable('subdealer_stocks')) {
            Schema::dropIfExists('subdealer_stocks');
        }

        if (Schema::hasTable('stock_logs') && Schema::hasColumn('stock_logs', 'stock_transaction_id')) {
            Schema::table('stock_logs', function (Blueprint $table) {
                $table->dropForeign(['stock_transaction_id']);
                $table->dropColumn('stock_transaction_id');
            });
        }

        if (Schema::hasTable('stock_transactions')) {
            Schema::dropIfExists('stock_transactions');
        }

        if (Schema::hasTable('stock_logs') && Schema::hasColumn('stock_logs', 'current_stock')) {
            Schema::table('stock_logs', function (Blueprint $table) {
                $table->dropColumn('current_stock');
            });
        }

        if (Schema::hasTable('stock_logs') && Schema::hasColumn('stock_logs', 'to_dealer_code')) {
            Schema::table('stock_logs', function (Blueprint $table) {
                $table->dropColumn('to_dealer_code');
            });
        }

        if (Schema::hasTable('stock_audit_logs')) {
            Schema::dropIfExists('stock_audit_logs');
        }

        if (Schema::hasTable('stock_logs')) {
            Schema::dropIfExists('stock_logs');
        }

        if (Schema::hasTable('stocks')) {
            Schema::dropIfExists('stocks');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasTable('subdealer_stocks')) {
            Schema::create('subdealer_stocks', function (Blueprint $table) {
                $table->id();
                $table->string('subdealer_code');
                $table->foreign('subdealer_code')->references('code')->on('dealers')->onDelete('cascade');
                $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
                $table->foreignId('size_id')->constrained('sizes')->onDelete('cascade');
                $table->unsignedBigInteger('total_quantity')->default(0);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('subdealer_stock_logs')) {
            Schema::create('subdealer_stock_logs', function (Blueprint $table) {
                $table->id();
                $table->foreignId('subdealer_stock_id')->constrained('subdealer_stocks')->onDelete('cascade');
                $table->unsignedBigInteger('in_quantity')->default(0);
                $table->unsignedBigInteger('out_quantity')->default(0);
                $table->unsignedBigInteger('total_quantity')->default(0);
                $table->unsignedBigInteger('changed_by_id');
                $table->string('changed_by_type')->comment('e.g., "App\Models\User" or "App\Models\DealerUserInfo"');
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('stocks')) {
            Schema::create('stocks', function (Blueprint $table) {
                $table->id();
                $table->string('dealer_code');
                $table->unsignedBigInteger('productsize_id');
                $table->decimal('total_quantity', 10, 2)->default(0)->unsigned();
                $table->decimal('available_quantity', 10, 2)->default(0)->unsigned();
                $table->timestamps();

                $table->unique(['dealer_code', 'productsize_id'], 'uniq_dealer_sku');
            });
        }

        if (!Schema::hasTable('stock_logs')) {
            Schema::create('stock_logs', function (Blueprint $table) {
                $table->id();
                $table->string('to_dealer_code')->nullable();
                $table->foreignId('stock_id')
                    ->constrained('stocks')
                    ->cascadeOnDelete();
                $table->date('date');
                $table->decimal('in_qty', 10, 2)->default(0)->unsigned();
                $table->decimal('out_qty', 10, 2)->default(0)->unsigned();
                $table->decimal('current_stock', 10, 2)->default(0)->unsigned();
                $table->foreignId('order_id')
                    ->nullable()
                    ->constrained('orders')
                    ->cascadeOnDelete();
                $table->string('action')->default('create');
                $table->string('remarks')->nullable();
                $table->morphs('submitted_by');
                $table->timestamps();
                $table->index('stock_id');
            });
        }

        if (!Schema::hasTable('stock_transactions')) {
            Schema::create('stock_transactions', function (Blueprint $table) {
                $table->id();
                $table->date('date')->index();
                $table->string('dealer_code')->index();
                $table->string('subdealer_code')->nullable()->index();
                $table->string('transaction_type')->index();
                $table->string('remarks', 500)->nullable();
                $table->morphs('submitted_by');
                $table->timestamps();
            });
        }

        if (Schema::hasTable('stock_logs')) {
            if (!Schema::hasColumn('stock_logs', 'stock_transaction_id')) {
                Schema::table('stock_logs', function (Blueprint $table) {
                    $table->foreignId('stock_transaction_id')
                        ->nullable()
                        ->constrained('stock_transactions')
                        ->cascadeOnDelete();
                });
            }
        }

        if (!Schema::hasTable('stock_audit_logs')) {
            Schema::create('stock_audit_logs', function (Blueprint $table) {
                $table->id();
                $table->foreignId('stock_log_id')
                    ->constrained('stock_logs')
                    ->cascadeOnDelete();
                $table->date('date')->index();
                $table->string('field_name')->index();
                $table->string('old_value')->nullable();
                $table->string('new_value')->nullable();
                $table->string('action')->default('update')->index();
                $table->string('remarks', 500)->nullable();
                $table->morphs('changed_by');
                $table->timestamps();

                $table->index(['stock_log_id', 'date']);
                $table->index(['changed_by_type', 'changed_by_id', 'date']);
            });
        }
    }
};
