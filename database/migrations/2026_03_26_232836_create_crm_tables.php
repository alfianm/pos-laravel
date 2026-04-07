<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lead_sources', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('tenant_id')->constrained('tenants')->onDelete('cascade');
            $table->string('name', 100);
            $table->timestamps();
        });

        Schema::create('lead_stages', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('tenant_id')->constrained('tenants')->onDelete('cascade');
            $table->string('name', 100);
            $table->integer('order')->default(0);
            $table->timestamps();
        });

        Schema::create('leads', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('tenant_id')->constrained('tenants')->onDelete('cascade');
            $table->foreignUuid('lead_source_id')->nullable()->constrained('lead_sources')->onDelete('set null');
            $table->foreignUuid('lead_stage_id')->nullable()->constrained('lead_stages')->onDelete('set null');
            $table->string('name', 150);
            $table->string('company', 150)->nullable();
            $table->string('email', 150)->nullable();
            $table->string('phone', 50)->nullable();
            $table->text('address')->nullable();
            $table->string('status', 30)->default('active');
            $table->foreignUuid('assigned_to')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('follow_ups', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('tenant_id')->constrained('tenants')->onDelete('cascade');
            $table->string('followable_type', 150); // Lead or Customer
            $table->uuid('followable_id');
            $table->string('type', 50); // call, email, chat, meet
            $table->timestamp('scheduled_at');
            $table->timestamp('completed_at')->nullable();
            $table->text('notes')->nullable();
            $table->string('status', 30)->default('pending');
            $table->foreignUuid('performed_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });

        Schema::create('customer_timelines', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('tenant_id')->constrained('tenants')->onDelete('cascade');
            $table->foreignUuid('customer_id')->constrained('customers')->onDelete('cascade');
            $table->string('event_type', 100);
            $table->uuid('reference_id')->nullable();
            $table->string('reference_type', 150)->nullable();
            $table->text('description')->nullable();
            $table->jsonb('meta')->nullable();
            $table->timestamps();
        });

        Schema::create('proposals', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('tenant_id')->constrained('tenants')->onDelete('cascade');
            $table->foreignUuid('customer_id')->nullable()->constrained('customers')->onDelete('set null');
            $table->foreignUuid('lead_id')->nullable()->constrained('leads')->onDelete('set null');
            $table->string('proposal_no', 50);
            $table->date('date');
            $table->date('expiry_date')->nullable();
            $table->decimal('amount', 18, 2);
            $table->string('status', 30)->default('draft');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('proposals');
        Schema::dropIfExists('customer_timelines');
        Schema::dropIfExists('follow_ups');
        Schema::dropIfExists('leads');
        Schema::dropIfExists('lead_stages');
        Schema::dropIfExists('lead_sources');
    }
};
