<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('products')) {
            return;
        }

        if (!Schema::hasColumn('products', 'name')) {
            return;
        }

        // Drop old unique constraint if it exists
        $constraintExists = $this->hasUniqueConstraint('products', 'products_name_unique');

        if ($constraintExists) {
            Schema::table('products', function (Blueprint $table) {
                $table->dropUnique('products_name_unique');
            });
        }

        // Add composite unique constraint if both columns exist
        if (Schema::hasColumn('products', 'category_id')) {
            $compositeConstraintExists = $this->hasUniqueConstraint('products', 'products_name_category_id_unique');

            if (!$compositeConstraintExists) {
                Schema::table('products', function (Blueprint $table) {
                    $table->unique(['name', 'category_id'], 'products_name_category_id_unique');
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasTable('products')) {
            return;
        }

        if (!Schema::hasColumn('products', 'name')) {
            return;
        }

        // Drop composite unique constraint if it exists
        $compositeConstraintExists = $this->hasUniqueConstraint('products', 'products_name_category_id_unique');

        if ($compositeConstraintExists) {
            Schema::table('products', function (Blueprint $table) {
                $table->dropUnique('products_name_category_id_unique');
            });
        }

        // Recreate old unique constraint if category_id doesn't exist
        if (!Schema::hasColumn('products', 'category_id')) {
            $constraintExists = $this->hasUniqueConstraint('products', 'products_name_unique');

            if (!$constraintExists) {
                Schema::table('products', function (Blueprint $table) {
                    $table->unique('name', 'products_name_unique');
                });
            }
        }
    }

    private function hasUniqueConstraint(string $table, string $constraintName): bool
    {
        try {
            $constraints = DB::select(
                "SELECT CONSTRAINT_NAME FROM INFORMATION_SCHEMA.TABLE_CONSTRAINTS
                 WHERE TABLE_NAME = ? AND CONSTRAINT_NAME = ? AND CONSTRAINT_TYPE = 'UNIQUE'",
                [$table, $constraintName]
            );

            return !empty($constraints);
        } catch (\Exception $e) {
            return false;
        }
    }
};
