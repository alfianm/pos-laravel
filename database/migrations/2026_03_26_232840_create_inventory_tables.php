<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 5.1 inventories
        Schema::create('inventories', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('tenant_id')->constrained('tenants')->onDelete('cascade');
            $table->foreignUuid('branch_id')->constrained('branches')->onDelete('cascade');
            $table->foreignUuid('product_id')->constrained('products')->onDelete('cascade');
            $table->foreignUuid('product_variant_id')->nullable()->constrained('product_variants')->onDelete('cascade');
            $table->decimal('qty_on_hand', 18, 4)->default(0);
            $table->decimal('qty_reserved', 18, 4)->default(0);
            $table->decimal('qty_available', 18, 4)->default(0);
            $table->decimal('avg_cost', 18, 4)->default(0);
            $table->decimal('reorder_level', 18, 4)->default(0);
            $table->timestamps();

            $table->unique(['branch_id', 'product_id', 'product_variant_id'], 'inventories_unique');
            $table->index('tenant_id');
            $table->index('branch_id');
        });

        // 5.2 stock_movements
        Schema::create('stock_movements', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('tenant_id')->constrained('tenants')->onDelete('cascade');
            $table->foreignUuid('branch_id')->constrained('branches')->onDelete('cascade');
            $table->foreignUuid('product_id')->constrained('products')->onDelete('cascade');
            $table->foreignUuid('product_variant_id')->nullable()->constrained('product_variants')->onDelete('cascade');
            $table->string('reference_type', 100);
            $table->uuid('reference_id')->nullable();
            $table->string('movement_type', 50);
            $table->decimal('qty', 18, 4);
            $table->decimal('before_qty', 18, 4);
            $table->decimal('after_qty', 18, 4);
            $table->decimal('unit_cost', 18, 4)->default(0);
            $table->text('notes')->nullable();
            $table->foreignUuid('performed_by')->nullable()->constrained('users')->onDelete('set null');
            $table->jsonb('meta')->nullable();
            $table->timestamps();

            $table->index('tenant_id');
            $table->index('branch_id');
            $table->index(['reference_type', 'reference_id']);
            $table->index('created_at');
        });

        // 5.3 stock_adjustments
        Schema::create('stock_adjustments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('tenant_id')->constrained('tenants')->onDelete('cascade');
            $table->foreignUuid('branch_id')->constrained('branches')->onDelete('cascade');
            $table->string('adjustment_no', 50);
            $table->string('reason', 100);
            $table->string('status', 30)->default('draft');
            $table->text('notes')->nullable();
            $table->foreignUuid('performed_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignUuid('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();

            $table->unique(['tenant_id', 'adjustment_no']);
            $table->index('status');
        });

        // 5.4 stock_adjustment_items
        Schema::create('stock_adjustment_items', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('tenant_id')->constrained('tenants')->onDelete('cascade');
            $table->foreignUuid('stock_adjustment_id')->constrained('stock_adjustments')->onDelete('cascade');
            $table->foreignUuid('product_id')->constrained('products')->onDelete('cascade');
            $table->foreignUuid('product_variant_id')->nullable()->constrained('product_variants')->onDelete('cascade');
            $table->decimal('before_qty', 18, 4);
            $table->decimal('adjusted_qty', 18, 4);
            $table->decimal('after_qty', 18, 4);
            $table->decimal('unit_cost', 18, 4)->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // 5.5 stock_transfers
        Schema::create('stock_transfers', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('tenant_id')->constrained('tenants')->onDelete('cascade');
            $table->foreignUuid('from_branch_id')->constrained('branches')->onDelete('cascade');
            $table->foreignUuid('to_branch_id')->constrained('branches')->onDelete('cascade');
            $table->string('transfer_no', 50);
            $table->string('status', 30)->default('draft');
            $table->foreignUuid('requested_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignUuid('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('received_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['tenant_id', 'transfer_no']);
        });

        // 5.6 stock_transfer_items
        Schema::create('stock_transfer_items', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('tenant_id')->constrained('tenants')->onDelete('cascade');
            $table->foreignUuid('stock_transfer_id')->constrained('stock_transfers')->onDelete('cascade');
            $table->foreignUuid('product_id')->constrained('products')->onDelete('cascade');
            $table->foreignUuid('product_variant_id')->nullable()->constrained('product_variants')->onDelete('cascade');
            $table->decimal('qty', 18, 4);
            $table->decimal('received_qty', 18, 4)->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_transfer_items');
        Schema::dropIfExists('stock_transfers');
        Schema::dropIfExists('stock_adjustment_items');
        Schema::dropIfExists('stock_adjustments');
        Schema::dropIfExists('stock_movements');
        Schema::dropIfExists('inventories');
    }
};
