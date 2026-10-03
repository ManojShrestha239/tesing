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
        Schema::create('tada_dealer_visits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tada_entry_id')->constrained('tada_entries')->onDelete('cascade');
            $table->string('dealer_code', 100)->nullable()->comment('FK to dealers table. Null if unlisted.');
            $table->string('unlisted_dealer_name', 255)->nullable()->comment('Populated only if visit_type is unlisted');
            $table->boolean('is_physical_visit')->default(true);
            $table->string('visit_type', 50)->comment('online, offline, unlisted');
            $table->text('remarks', 500)->nullable();

            $table->timestamps();

            $table->index('dealer_code', 'idx_tada_visit_dealer');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tada_dealer_visits');
    }
};
