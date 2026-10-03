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
        Schema::create('unlisted_dealers', function (Blueprint $table) {
            $table->id();
            $table->string('name', 191);
            $table->string('address', 191);
            $table->string('contact', 191)->unique();
            $table->string('area_code');
            $table->foreign('area_code')->references('areacode')->on('areas');
            $table->string('subarea');
            $table->string('lat', 191);
            $table->string('lng', 191);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('unlisted_dealers');
    }
};
