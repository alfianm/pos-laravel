<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('tenant_id')->constrained('tenants')->onDelete('cascade');
            $table->foreignUuid('tenant_subscription_id')->nullable()->constrained('tenant_subscriptions');
            $table->foreignUuid('payment_method_id')->nullable()->constrained('payment_methods');
            $table->string('invoice_number', 100)->nullable();
            $table->string('transaction_id', 255)->nullable();
            $table->string('reference_number', 255)->nullable();
            $table->string('status', 30)->default('pending');
            $table->string('type', 50)->default('subscription');
            $table->decimal('amount', 15, 2);
            $table->decimal('fee', 15, 2)->default(0);
            $table->string('currency', 10)->default('IDR');
            $table->jsonb('payment_details')->nullable();
            $table->text('notes')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('failed_at')->nullable();
            $table->string('failure_reason', 255)->nullable();
            $table->foreignUuid('recorded_by')->nullable()->constrained('users');
            $table->timestamps();
            $table->softDeletes();

            $table->index('tenant_id');
            $table->index('tenant_subscription_id');
            $table->index('payment_method_id');
            $table->index('status');
            $table->index('type');
            $table->index('invoice_number');
            $table->index('transaction_id');
            $table->index('reference_number');
            $table->index('paid_at');
            $table->index(['tenant_id', 'status']);
            $table->index(['tenant_id', 'type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
