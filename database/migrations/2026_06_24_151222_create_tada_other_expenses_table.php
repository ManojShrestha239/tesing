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
        Schema::create('tada_other_expenses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tada_entry_id')->constrained('tada_entries')->onDelete('cascade');
            $table->string('expense_details', 255)->comment('Free text explanation');
            $table->decimal('amount', 10, 2)->default(0.00);
            $table->foreignId('authorized_user_id')->nullable()->constrained('tada_authorized_users')->onDelete('restrict');
            $table->text('remarks', 500)->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tada_other_expenses');
    }
};
