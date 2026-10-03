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
        Schema::table('dealers', function (Blueprint $table) {
            $table->string('grade')->nullable()->default(null);
            $table->string('category')->nullable()->default(null);
            $table->string('documentation_file')->nullable();
            $table->double('discount')->default(0.00);
            $table->integer('series');
            $table->string('type')->nullable()->default(null);
            $table->string('othertext')->nullable()->default(null);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('dealers', function (Blueprint $table) {
            //
        });
    }
};
