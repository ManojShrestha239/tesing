<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMessagesTable extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('message_groups')) {
            Schema::create('message_groups', function (Blueprint $table) {
                $table->id();
                $table->string('group_name');
                $table->string('status');
                $table->integer('created_by');
                $table->timestamps();
                $table->index('created_by');
            });
        }

        if (!Schema::hasTable('messages')) {
            Schema::create('messages', function (Blueprint $table) {
                $table->id();
                $table->bigInteger('from')->nullable();
                $table->bigInteger('to')->nullable();
                $table->text('message');
                $table->unsignedInteger('admin')->nullable();
                $table->tinyInteger('is_read');
                $table->string('groupid')->nullable();
                $table->string('type')->default('individual');
                $table->timestamps();

                $table->index('from');
                $table->index('to');
                $table->index('groupid');
            });
        }

        if (!Schema::hasTable('message_group_users')) {
            Schema::create('message_group_users', function (Blueprint $table) {
                $table->id();
                $table->unsignedInteger('user_id')->nullable();
                $table->unsignedBigInteger('message_group_id');
                $table->unsignedInteger('admin_user')->nullable();
                $table->string('user_type');
                $table->string('status');
                $table->timestamps();

                $table->index('user_id');
                $table->index('message_group_id');
                $table->foreign('message_group_id')->references('id')->on('message_groups')->onDelete('cascade');
            });
        }

        if (!Schema::hasTable('message_images')) {
            Schema::create('message_images', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('chat_id');
                $table->string('image');
                $table->timestamps();

                $table->index('chat_id');
                $table->foreign('chat_id')->references('id')->on('messages')->onDelete('cascade');
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('message_images');
        Schema::dropIfExists('message_group_users');
        Schema::dropIfExists('messages');
        Schema::dropIfExists('message_groups');
    }
}
