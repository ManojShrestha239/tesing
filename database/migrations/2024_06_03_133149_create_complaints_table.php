<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('complaints')) {
            Schema::create('complaints', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('issuetype_id')->index();
                $table->text('issue_desc');
                $table->string('user_type', 255);
                $table->string('status', 255)->index();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('complaint_admin_users')) {
            Schema::create('complaint_admin_users', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('complaint_id')->index();
                $table->unsignedBigInteger('user_id')->index();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('complaint_dealer_users')) {
            Schema::create('complaint_dealer_users', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('complaint_id')->index();
                $table->string('dealer_code', 255)->index();
                $table->unsignedBigInteger('dealeruser_id')->index();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('complaint_images')) {
            Schema::create('complaint_images', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('complaint_id')->index();
                $table->string('image', 255);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('complaint_replies')) {
            Schema::create('complaint_replies', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('complaint_id')->index();
                $table->text('comment');
                $table->string('user_type', 255)->comment('dealer,admin');
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('complaint_reply_admin_users')) {
            Schema::create('complaint_reply_admin_users', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('complaintreply_id')->index();
                $table->unsignedBigInteger('user_id')->index();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('complaint_reply_dealer_users')) {
            Schema::create('complaint_reply_dealer_users', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('complaintreply_id')->index();
                $table->string('dealer_code', 255)->index();
                $table->unsignedBigInteger('dealeruser_id')->index();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('complaint_reply_images')) {
            Schema::create('complaint_reply_images', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('complaintreply_id')->index();
                $table->string('image', 255);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('product_complaints')) {
            Schema::create('product_complaints', function (Blueprint $table) {
                $table->id();
                $table->string('customer_name', 255);
                $table->string('customer_email', 255)->nullable();
                $table->string('customer_mobile', 255);
                $table->string('customer_address', 255);
                $table->string('product_batch', 255);
                $table->string('product_name', 255);
                $table->string('product_size', 255)->nullable();
                $table->string('complaint_date', 255);
                $table->string('color_code', 255);
                $table->string('dealer_code', 255)->index();
                $table->unsignedBigInteger('dealeruser_id')->index();
                $table->unsignedBigInteger('complaint_id')->index();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('product_complaints');
        Schema::dropIfExists('complaint_reply_images');
        Schema::dropIfExists('complaint_reply_dealer_users');
        Schema::dropIfExists('complaint_reply_admin_users');
        Schema::dropIfExists('complaint_replies');
        Schema::dropIfExists('complaint_images');
        Schema::dropIfExists('complaint_dealer_users');
        Schema::dropIfExists('complaint_admin_users');
        Schema::dropIfExists('complaints');
    }
};
