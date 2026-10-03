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
        Schema::create('tada_authorized_users', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255);
            $table->string('phone', 20)->unique();
            $table->string('email', 255)->unique();
            $table->string('designation', 100)->nullable();
            $table->string('department', 100)->nullable();
            $table->string('status', 50)->default('ACTIVE')->comment('ACTIVE, INACTIVE');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tada_authorized_users');
    }
};
