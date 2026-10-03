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
        Schema::table('orders', function (Blueprint $table) {
            if (!Schema::hasColumn('orders', 'lat_lng')) {
                $table->string('lat_lng')->nullable();
            }
            if (!Schema::hasColumn('orders', 'location')) {
                $table->string('location')->nullable()->after('lat_lng');
            }
        });

        Schema::table('payments', function (Blueprint $table) {
            if (!Schema::hasColumn('payments', 'lat_lng')) {
                $table->string('lat_lng')->nullable();
            }
            if (!Schema::hasColumn('payments', 'location')) {
                $table->string('location')->nullable()->after('lat_lng');
            }
        });

        Schema::table('visits', function (Blueprint $table) {
            if (!Schema::hasColumn('visits', 'lat_lng')) {
                $table->string('lat_lng')->nullable();
            }
            if (!Schema::hasColumn('visits', 'location')) {
                $table->string('location')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if (Schema::hasColumn('orders', 'lat_lng')) {
                $table->dropColumn('lat_lng');
            }
            if (Schema::hasColumn('orders', 'location')) {
                $table->dropColumn('location');
            }
        });

        Schema::table('payments', function (Blueprint $table) {
            if (Schema::hasColumn('payments', 'lat_lng')) {
                $table->dropColumn('lat_lng');
            }
            if (Schema::hasColumn('payments', 'location')) {
                $table->dropColumn('location');
            }
        });

        Schema::table('visits', function (Blueprint $table) {
            if (Schema::hasColumn('visits', 'lat_lng')) {
                $table->dropColumn('lat_lng');
            }
            if (Schema::hasColumn('visits', 'location')) {
                $table->dropColumn('location');
            }
        });
    }
};
