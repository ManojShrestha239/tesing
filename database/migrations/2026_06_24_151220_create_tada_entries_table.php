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
        Schema::create('tada_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('mobileusers')->onDelete('restrict');
            $table->string('entry_date', 50);
            $table->string('route', 255);
            $table->string('visit_category', 100)->comment('local, up_country, ex_up_country');
            $table->string('stay_at', 255)->nullable()->comment('Null if visit_category is local');
            $table->boolean('is_combined_visit')->default(false);
            $table->string('combined_with', 255)->nullable()->comment('Required if is_combined_visit is true');
            $table->decimal('starting_km', 12, 2)->nullable();
            $table->decimal('ending_km', 12, 2)->nullable();
            $table->string('status', 50)->default('DRAFT')->comment('DRAFT, SUBMITTED, APPROVED, REJECTED');
            $table->foreignId('authorized_user_id')->nullable()->constrained('tada_authorized_users')->onDelete('restrict');
            $table->string('remarks', 500)->nullable();

            $table->timestamps();
            $table->softDeletes();

            // Custom Indexes
            $table->index(['user_id', 'entry_date'], 'idx_tada_user_date');
            $table->index('status', 'idx_tada_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tada_entries');
    }
};
