<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run migrations inside a transaction when supported by the DB.
     * This helps ensure a rollback if any part of the migration fails.
     */
    public $withinTransaction = true;

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Create v2_site_construction_stages table first since v2_site_registrations will reference it
        Schema::create('v2_site_construction_stages', function (Blueprint $table) {
            $table->id();
            $table->string('name', 60)->unique();
            $table->string('material_icon', 60)->nullable();
            $table->unsignedTinyInteger('sort_order');
            $table->unsignedTinyInteger('recommended_followup_days')->default(20);
        });

        // Seed the 6 stages visible in the UI only Material Icons
        DB::table('v2_site_construction_stages')->insert([
            ['id' => 1, 'name' => 'Foundation', 'material_icon' => 'foundation', 'sort_order' => 1, 'recommended_followup_days' => 30],
            ['id' => 2, 'name' => 'Structure', 'material_icon' => 'apartment', 'sort_order' => 2, 'recommended_followup_days' => 25],
            ['id' => 3, 'name' => 'Plastering', 'material_icon' => 'handyman', 'sort_order' => 3, 'recommended_followup_days' => 20],
            ['id' => 4, 'name' => 'Putty Stage', 'material_icon' => 'format_paint', 'sort_order' => 4, 'recommended_followup_days' => 15],
            ['id' => 5, 'name' => 'Painting Stage', 'material_icon' => 'brush', 'sort_order' => 5, 'recommended_followup_days' => 10],
            ['id' => 6, 'name' => 'Completed', 'material_icon' => 'done_all', 'sort_order' => 6, 'recommended_followup_days' => 0],
        ]);

        // Create v2_site_types table to categorize different types of construction sites (e.g., Residential, Commercial, etc.)
        Schema::create('v2_site_types', function (Blueprint $table) {
            $table->id();
            $table->string('name', 60)->unique();
            $table->unsignedTinyInteger('sort_order');
        });

        // Seed the 6 stages visible in the UI only Material Icons
        DB::table('v2_site_types')->insert([
            ['id' => 1, 'name' => 'Residential', 'sort_order' => 1],
            ['id' => 2, 'name' => 'Commercial', 'sort_order' => 2],
            ['id' => 3, 'name' => 'Apartment', 'sort_order' => 3],
            ['id' => 4, 'name' => 'Renovation', 'sort_order' => 4]
        ]);

        // Seed some common site types
        Schema::create('v2_site_registrations', function (Blueprint $table) {
            $table->id();
            $table->string('site_registration_no', 60)->unique();
            $table->string('name', 120);
            $table->string('date', 11);

            // dealers table uses `code` (string) as PK, so create a matching string column
            $table->string('dealer_code', 191);

            $table->foreign('dealer_code')
                ->references('code')
                ->on('dealers')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->foreignId('registered_by_user_id')
                ->nullable()
                ->constrained('mobileusers')
                ->restrictOnDelete();

            $table->foreignId('web_created_by_id')
                ->nullable()
                ->constrained('users')
                ->restrictOnDelete();

            $table->foreignId('site_type_id')
                ->constrained('v2_site_types')
                ->restrictOnDelete();

            $table->foreignId('current_stage_id')
                ->constrained('v2_site_construction_stages')
                ->restrictOnDelete();

            $table->foreignId('contractor_id')
                ->constrained('contractors')
                ->restrictOnDelete();

            // Location
            $table->text('address')->nullable();
            $table->decimal('lat', 10, 7)->nullable();
            $table->decimal('lng', 10, 7)->nullable();

            // Construction
            $table->string('floors', 20)->nullable();
            $table->unsignedInteger('approx_area_sqft')->nullable();

            $table->string('preferred_brand', 80)->nullable();
            $table->string('competitor_brand_observed', 80)->nullable();

            // Analytics
            $table->unsignedInteger('estimated_paint_litres')->nullable();

            $table->string('status', 60)->default('active')->comment('active, converted, lost, inactive');

            $table->string('next_followup_at')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index(['registered_by_user_id', 'status']);
            $table->index(['current_stage_id']);
            $table->index(['next_followup_at', 'status']);
            $table->index(['lat', 'lng']);

            $table->fullText(['name', 'address']);
        });

        // Create v2_site_contacts table to store contact information for each site (e.g., owner, contractor, architect, etc.)
        Schema::create('v2_site_contacts', function (Blueprint $table) {
            $table->id();

            $table->foreignId('site_id')
                ->constrained('v2_site_registrations')
                ->cascadeOnDelete();

            $table->string('name', 100);

            $table->string('phone', 20)->nullable();

            $table->string('role', 60)->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index(['site_id']);
            $table->index(['phone']);
        });

        // Create v2_site_photos table to store photos for each site, linked to v2_site_registrations
        Schema::create('v2_site_photos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('site_id')
                ->constrained('v2_site_registrations')
                ->cascadeOnDelete();

            $table->string('file_path');

            $table->string('photo_type', 20)->comment('site_photo, progress_photo');

            $table->timestamps();
            $table->softDeletes();

            $table->index(['site_id']);
        });

        // Create v2_site_stage_logs table to track the history of stage changes for each site, linked to v2_site_registrations and v2_site_construction_stages
        Schema::create('v2_site_stage_logs', function (Blueprint $table) {
            $table->id();

            $table->foreignId('site_id')
                ->constrained('v2_site_registrations')
                ->cascadeOnDelete();

            // Use a single foreignId for `stage_id` to match the referenced table's `id` type
            $table->foreignId('stage_id')
                ->constrained('v2_site_construction_stages')
                ->restrictOnDelete();

            $table->foreignId('changed_by_user_id')
                ->nullable()
                ->constrained('mobileusers')
                ->restrictOnDelete();

            $table->foreignId('web_changed_by_id')
                ->nullable()
                ->constrained('users')
                ->restrictOnDelete();

            $table->string('stage_date');

            $table->string('remarks', 500)->nullable();

            $table->timestamp('created_at')->useCurrent();

            $table->index(['site_id', 'stage_id']);
            $table->index(['site_id', 'created_at']);
            $table->index(['stage_date']);
        });

        // Create v2_site_follow_ups table to manage follow-up actions for each site, linked to v2_site_registrations and users
        Schema::create('v2_site_follow_ups', function (Blueprint $table) {
            $table->id();

            $table->foreignId('site_id')
                ->constrained('v2_site_registrations')
                ->cascadeOnDelete();

            $table->foreignId('assigned_to_user_id')
                ->constrained('mobileusers')
                ->restrictOnDelete();

            $table->foreignId('created_by_user_id')
                ->nullable()
                ->constrained('mobileusers')
                ->restrictOnDelete();

            $table->foreignId('web_changed_by_id')
                ->nullable()
                ->constrained('users')
                ->restrictOnDelete();

            $table->string('follow_up_type')->default('visit')->comment('visit, call, demo, sampling, others');

            $table->string('follow_up_date');

            $table->string('status')->default('pending');

            $table->boolean('auto_reminder_enabled')
                ->default(true);

            //Address
            $table->decimal('lat', 10, 7)->nullable();
            $table->decimal('lng', 10, 7)->nullable();

            $table->text('notes')->nullable();

            $table->timestamp('completed_at')->nullable();

            $table->foreignId('rescheduled_to_id')
                ->nullable()
                ->constrained('v2_site_follow_ups')
                ->nullOnDelete();

            $table->timestamps();
            $table->softDeletes();

            $table->index(
                ['status', 'follow_up_date'],
                'fu_user_status_date'
            );

            $table->index(
                ['site_id', 'status'],
                'fu_site_status'
            );

            $table->index(
                ['follow_up_date', 'status'],
                'fu_date_status'
            );

            $table->index('auto_reminder_enabled');
        });

        // Create v2_site_activity_logs table to store a comprehensive timeline of all significant events for each site, linked to v2_site_registrations and users
        Schema::create('v2_site_activity_logs', function (Blueprint $table) {
            $table->id();

            $table->foreignId('site_id')
                ->constrained('v2_site_registrations')
                ->cascadeOnDelete();

            $table->foreignId('causer_id')
                ->nullable()
                ->constrained('mobileusers')
                ->restrictOnDelete();

            $table->foreignId('web_causer_id')
                ->nullable()
                ->constrained('users')
                ->restrictOnDelete();

            $table->string('event', 80);

            $table->string('description', 300);

            $table->json('properties')->nullable();

            $table->timestamp('created_at')->useCurrent();

            $table->index(
                ['site_id', 'created_at'],
                'al_site_timeline'
            );

            $table->index(
                ['causer_id', 'created_at'],
                'al_user_activity'
            );

            $table->index(['site_id', 'event']);
        });

        // Create v2_site_notes table to store general notes for each site, linked to v2_site_registrations and users
        Schema::create('v2_site_notes', function (Blueprint $table) {
            $table->id();

            $table->foreignId('site_id')
                ->constrained('v2_site_registrations')
                ->cascadeOnDelete();

            $table->foreignId('user_id')
                ->nullable()
                ->constrained('mobileusers')
                ->restrictOnDelete();

            $table->foreignId('web_user_id')
                ->nullable()
                ->constrained('users')
                ->restrictOnDelete();

            $table->text('note');

            $table->timestamps();
        });

        // Create v2_site_products table to store information about different paint products and their coverage, which can be linked to site registrations for analytics and recommendations
        Schema::create('v2_site_products', function (Blueprint $table) {
            $table->id();

            $table->string('name', 100);
            $table->string('types', 50)->comment('top coat, under coat, putty, others');
            $table->decimal('coverage', 10, 2)->nullable();
        });

        // Seed the 6 stages visible in the UI only Material Icons
        DB::table('v2_site_products')->insert([
            ['id' => 1, 'name' => 'Residential', 'types' => 'top', 'coverage' => 350],
            ['id' => 2, 'name' => 'Commercial', 'types' => 'top', 'coverage' => 300],
            ['id' => 3, 'name' => 'Apartment', 'types' => 'under', 'coverage' => 320],
            ['id' => 4, 'name' => 'Renovation', 'types' => 'under', 'coverage' => 280],
            ['id' => 5, 'name' => 'Premium Putty', 'types' => 'putty', 'coverage' => 100],
            ['id' => 6, 'name' => 'Standard Putty', 'types' => 'putty', 'coverage' => 80],
        ]);

        // Create pivot table for many-to-many relationship between site registrations and products (if needed for tracking which products are used/recommended for each site)
        Schema::create('v2_site_assgin_product', function (Blueprint $table) {
            $table->foreignId('site_id')
                ->constrained('v2_site_registrations')
                ->cascadeOnDelete();

            $table->foreignId('site_product_id')
                ->constrained('v2_site_products')
                ->cascadeOnDelete();

            $table->decimal('quantity', 10, 2);

            $table->primary(['site_id', 'site_product_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('v2_site_assgin_product');
        Schema::dropIfExists('v2_site_products');
        Schema::dropIfExists('v2_site_notes');
        Schema::dropIfExists('v2_site_activity_logs');
        Schema::dropIfExists('v2_site_follow_ups');
        Schema::dropIfExists('v2_site_stage_logs');
        Schema::dropIfExists('v2_site_photos');
        Schema::dropIfExists('v2_site_contacts');
        Schema::dropIfExists('v2_site_registrations');
        Schema::dropIfExists('v2_site_types');
        Schema::dropIfExists('v2_site_construction_stages');
    }
};
