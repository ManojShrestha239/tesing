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
        if (!Schema::hasTable('device_logs')) {
            Schema::create('device_logs', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('mobileuser_id');
                $table->date('date');
                $table->json('device_info');
                $table->foreign('mobileuser_id')->references('id')->on('mobileusers')->onDelete('cascade');
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasTable('device_logs')) {
            Schema::dropIfExists('device_logs');
        }
    }
};
