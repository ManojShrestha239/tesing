<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tada_expense_authorized_users', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255);
            $table->string('phone', 20)->unique();
            $table->string('email', 255)->unique();
            $table->string('designation', 100)->nullable();
            $table->string('department', 100)->nullable();
            $table->string('status', 50)->default('ACTIVE');
            $table->timestamps();
            $table->softDeletes();
        });

        DB::statement(
            "INSERT INTO tada_expense_authorized_users (name, phone, email, designation, department, status, created_at, updated_at)
             SELECT name, phone, email, designation, department, status, created_at, updated_at
             FROM tada_authorized_users
             WHERE name <> 'As per Itinerary'"
        );

        Schema::table('tada_dealer_expenses', function (Blueprint $table) {
            $table->dropForeign(['expenses_authorized_by']);
        });

        Schema::table('tada_other_expenses', function (Blueprint $table) {
            $table->dropForeign(['expenses_authorized_by']);
        });

        DB::statement(
            "UPDATE tada_dealer_expenses AS expenses
             INNER JOIN tada_authorized_users AS old_users ON old_users.id = expenses.expenses_authorized_by
             INNER JOIN tada_expense_authorized_users AS expense_users ON expense_users.email = old_users.email
             SET expenses.expenses_authorized_by = expense_users.id"
        );
        DB::statement(
            "UPDATE tada_other_expenses AS expenses
             INNER JOIN tada_authorized_users AS old_users ON old_users.id = expenses.expenses_authorized_by
             INNER JOIN tada_expense_authorized_users AS expense_users ON expense_users.email = old_users.email
             SET expenses.expenses_authorized_by = expense_users.id"
        );
        DB::statement(
            "UPDATE tada_dealer_expenses AS expenses
             INNER JOIN tada_authorized_users AS old_users ON old_users.id = expenses.expenses_authorized_by
             SET expenses.expenses_authorized_by = NULL
             WHERE old_users.name = 'As per Itinerary'"
        );
        DB::statement(
            "UPDATE tada_other_expenses AS expenses
             INNER JOIN tada_authorized_users AS old_users ON old_users.id = expenses.expenses_authorized_by
             SET expenses.expenses_authorized_by = NULL
             WHERE old_users.name = 'As per Itinerary'"
        );

        Schema::table('tada_dealer_expenses', function (Blueprint $table) {
            $table->foreign('expenses_authorized_by', 'dealer_expenses_authorizer_fk')
                ->references('id')
                ->on('tada_expense_authorized_users')
                ->restrictOnDelete();
        });

        Schema::table('tada_other_expenses', function (Blueprint $table) {
            $table->foreign('expenses_authorized_by', 'other_expenses_authorizer_fk')
                ->references('id')
                ->on('tada_expense_authorized_users')
                ->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('tada_dealer_expenses', function (Blueprint $table) {
            $table->dropForeign('dealer_expenses_authorizer_fk');
        });

        Schema::table('tada_other_expenses', function (Blueprint $table) {
            $table->dropForeign('other_expenses_authorizer_fk');
        });

        DB::statement(
            "UPDATE tada_dealer_expenses AS expenses
             INNER JOIN tada_expense_authorized_users AS expense_users ON expense_users.id = expenses.expenses_authorized_by
             INNER JOIN tada_authorized_users AS old_users ON old_users.email = expense_users.email
             SET expenses.expenses_authorized_by = old_users.id"
        );
        DB::statement(
            "UPDATE tada_other_expenses AS expenses
             INNER JOIN tada_expense_authorized_users AS expense_users ON expense_users.id = expenses.expenses_authorized_by
             INNER JOIN tada_authorized_users AS old_users ON old_users.email = expense_users.email
             SET expenses.expenses_authorized_by = old_users.id"
        );

        Schema::table('tada_dealer_expenses', function (Blueprint $table) {
            $table->foreign('expenses_authorized_by')
                ->references('id')
                ->on('tada_authorized_users')
                ->restrictOnDelete();
        });

        Schema::table('tada_other_expenses', function (Blueprint $table) {
            $table->foreign('expenses_authorized_by')
                ->references('id')
                ->on('tada_authorized_users')
                ->restrictOnDelete();
        });

        Schema::dropIfExists('tada_expense_authorized_users');
    }
};
