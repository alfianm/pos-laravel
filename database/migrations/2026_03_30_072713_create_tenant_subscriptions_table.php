<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tenant_subscriptions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('tenant_id')->constrained('tenants')->onDelete('cascade');
            $table->foreignUuid('subscription_plan_id')->constrained('subscription_plans');
            $table->string('status', 30)->default('active');
            $table->string('billing_cycle', 20)->default('monthly');
            $table->decimal('amount', 15, 2);
            $table->date('starts_at');
            $table->date('ends_at');
            $table->date('trial_ends_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->timestamp('downgraded_at')->nullable();
            $table->foreignUuid('downgraded_to_plan_id')->nullable()->constrained('subscription_plans');
            $table->jsonb('metadata')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('tenant_id');
            $table->index('subscription_plan_id');
            $table->index('status');
            $table->index('starts_at');
            $table->index('ends_at');
            $table->index(['tenant_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tenant_subscriptions');
    }
};
