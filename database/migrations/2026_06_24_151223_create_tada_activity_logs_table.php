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
        Schema::create('tada_activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tada_entry_id')->constrained('tada_entries')->onDelete('cascade');
            $table->foreignId('action_by_user_id')->constrained('users')->onDelete('restrict');
            $table->string('action', 50)->comment('e.g., CREATED, UPDATED, STATUS_CHANGED, DELETED');
            $table->json('old_values')->nullable()->comment('State before change');
            $table->json('new_values')->nullable()->comment('State after change');
            $table->string('ip_address', 45)->nullable();

            $table->timestamp('created_at')->useCurrent();

            $table->index('action', 'idx_tada_log_action');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tada_activity_logs');
    }
};
