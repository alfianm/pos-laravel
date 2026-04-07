<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 7.1 cash_register_sessions
        Schema::create('cash_register_sessions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('tenant_id')->constrained('tenants')->onDelete('cascade');
            $table->foreignUuid('branch_id')->constrained('branches')->onDelete('cascade');
            $table->foreignUuid('user_id')->constrained('users')->onDelete('cascade');
            $table->timestamp('opened_at');
            $table->timestamp('closed_at')->nullable();
            $table->decimal('opening_cash', 18, 2)->default(0);
            $table->decimal('closing_cash', 18, 2)->nullable();
            $table->decimal('total_sale_amount', 18, 2)->default(0);
            $table->decimal('expected_cash', 18, 2)->nullable();
            $table->decimal('actual_cash', 18, 2)->nullable();
            $table->text('notes')->nullable();
            $table->string('status', 30)->default('open');
            $table->timestamps();

            $table->index('status');
        });

        // 7.2 sales
        Schema::create('sales', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('tenant_id')->constrained('tenants')->onDelete('cascade');
            $table->foreignUuid('branch_id')->constrained('branches')->onDelete('cascade');
            $table->foreignUuid('cash_register_session_id')->nullable()->constrained('cash_register_sessions')->onDelete('set null');
            $table->foreignUuid('customer_id')->nullable()->constrained('customers')->onDelete('set null');
            $table->string('sale_no', 50);
            $table->timestamp('sale_date');
            $table->string('status', 30)->default('completed');
            $table->string('payment_status', 30)->default('paid');
            $table->decimal('subtotal', 18, 2)->default(0);
            $table->decimal('discount_amount', 18, 2)->default(0);
            $table->decimal('tax_amount', 18, 2)->default(0);
            $table->decimal('grand_total', 18, 2)->default(0);
            $table->decimal('paid_amount', 18, 2)->default(0);
            $table->decimal('due_amount', 18, 2)->default(0);
            $table->text('notes')->nullable();
            $table->foreignUuid('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();

            $table->unique(['tenant_id', 'sale_no']);
            $table->index('status');
            $table->index('payment_status');
        });

        // 7.3 sale_items
        Schema::create('sale_items', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('tenant_id')->constrained('tenants')->onDelete('cascade');
            $table->foreignUuid('sale_id')->constrained('sales')->onDelete('cascade');
            $table->foreignUuid('product_id')->constrained('products')->onDelete('cascade');
            $table->foreignUuid('product_variant_id')->nullable()->constrained('product_variants')->onDelete('cascade');
            $table->decimal('qty', 18, 4);
            $table->decimal('unit_price', 18, 2);
            $table->decimal('discount_amount', 18, 2)->default(0);
            $table->decimal('tax_amount', 18, 2)->default(0);
            $table->decimal('line_total', 18, 2);
            $table->timestamps();
        });

        // 7.4 sale_payments
        Schema::create('sale_payments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('tenant_id')->constrained('tenants')->onDelete('cascade');
            $table->foreignUuid('sale_id')->constrained('sales')->onDelete('cascade');
            $table->string('payment_no', 50);
            $table->timestamp('payment_date');
            $table->decimal('amount', 18, 2);
            $table->string('payment_method', 50);
            $table->string('reference_no', 100)->nullable();
            $table->text('notes')->nullable();
            $table->foreignUuid('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sale_payments');
        Schema::dropIfExists('sale_items');
        Schema::dropIfExists('sales');
        Schema::dropIfExists('cash_register_sessions');
    }
};
