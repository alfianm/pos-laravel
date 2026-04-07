<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 4.1 customer_groups
        Schema::create('customer_groups', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('tenant_id')->constrained('tenants')->onDelete('cascade');
            $table->string('name', 100);
            $table->decimal('discount_percentage', 5, 2)->default(0);
            $table->timestamps();
            $table->softDeletes();
            $table->unique(['tenant_id', 'name']);
        });

        // 4.2 customers
        Schema::create('customers', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('tenant_id')->constrained('tenants')->onDelete('cascade');
            $table->foreignUuid('branch_id')->nullable()->constrained('branches')->onDelete('set null');
            $table->foreignUuid('customer_group_id')->nullable()->constrained('customer_groups')->onDelete('set null');
            $table->string('code', 50);
            $table->string('name', 150);
            $table->string('email', 150)->nullable();
            $table->string('phone', 50)->nullable();
            $table->string('gender', 20)->nullable();
            $table->date('birth_date')->nullable();
            $table->text('address')->nullable();
            $table->string('city', 100)->nullable();
            $table->string('province', 100)->nullable();
            $table->string('postal_code', 20)->nullable();
            $table->string('tax_number', 100)->nullable();
            $table->text('notes')->nullable();
            $table->decimal('total_spent', 18, 2)->default(0);
            $table->integer('total_orders')->default(0);
            $table->timestamp('last_purchase_at')->nullable();
            $table->string('status', 30)->default('active');
            $table->string('source', 50)->nullable();
            $table->jsonb('meta')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['tenant_id', 'code']);
            $table->index('tenant_id');
            $table->index('branch_id');
            $table->index('customer_group_id');
            $table->index('email');
            $table->index('phone');
            $table->index('status');
        });

        // 4.3 suppliers
        Schema::create('suppliers', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('tenant_id')->constrained('tenants')->onDelete('cascade');
            $table->string('code', 50);
            $table->string('name', 150);
            $table->string('email', 150)->nullable();
            $table->string('phone', 50)->nullable();
            $table->string('contact_person', 150)->nullable();
            $table->text('address')->nullable();
            $table->string('city', 100)->nullable();
            $table->string('province', 100)->nullable();
            $table->string('postal_code', 20)->nullable();
            $table->integer('payment_terms_days')->default(0);
            $table->text('notes')->nullable();
            $table->string('status', 30)->default('active');
            $table->jsonb('meta')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['tenant_id', 'code']);
            $table->index('tenant_id');
            $table->index('status');
        });

        // 4.4 brands
        Schema::create('brands', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('tenant_id')->constrained('tenants')->onDelete('cascade');
            $table->string('name', 100);
            $table->timestamps();
            $table->softDeletes();
            $table->unique(['tenant_id', 'name']);
        });

        // 4.5 units
        Schema::create('units', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('tenant_id')->constrained('tenants')->onDelete('cascade');
            $table->string('name', 100);
            $table->string('short_name', 20);
            $table->timestamps();
            $table->softDeletes();
            $table->unique(['tenant_id', 'name']);
            $table->unique(['tenant_id', 'short_name']);
        });

        // 4.6 product_categories
        Schema::create('product_categories', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('tenant_id')->constrained('tenants')->onDelete('cascade');
            $table->uuid('parent_id')->nullable();
            $table->string('name', 100);
            $table->string('slug', 150);
            $table->timestamps();
            
            $table->unique(['tenant_id', 'slug']);
        });

        Schema::table('product_categories', function (Blueprint $table) {
            $table->foreign('parent_id')->references('id')->on('product_categories')->onDelete('set null');
        });

        // 4.7 products
        Schema::create('products', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('tenant_id')->constrained('tenants')->onDelete('cascade');
            $table->foreignUuid('category_id')->nullable()->constrained('product_categories')->onDelete('set null');
            $table->foreignUuid('brand_id')->nullable()->constrained('brands')->onDelete('set null');
            $table->foreignUuid('unit_id')->nullable()->constrained('units')->onDelete('set null');
            $table->string('code', 50);
            $table->string('sku', 100);
            $table->string('barcode', 100)->nullable();
            $table->string('name', 200);
            $table->string('type', 30)->default('single');
            $table->decimal('purchase_price', 18, 2)->default(0);
            $table->decimal('selling_price', 18, 2)->default(0);
            $table->decimal('cost_price', 18, 2)->default(0);
            $table->boolean('track_stock')->default(true);
            $table->boolean('allow_decimal')->default(false);
            $table->boolean('has_expiry')->default(false);
            $table->boolean('is_active')->default(true);
            $table->text('description')->nullable();
            $table->string('image_url', 255)->nullable();
            $table->jsonb('meta')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['tenant_id', 'code']);
            $table->unique(['tenant_id', 'sku']);
            $table->index('tenant_id');
            $table->index('category_id');
            $table->index('is_active');
        });

        // 4.8 product_variants
        Schema::create('product_variants', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('tenant_id')->constrained('tenants')->onDelete('cascade');
            $table->foreignUuid('product_id')->constrained('products')->onDelete('cascade');
            $table->string('name', 150);
            $table->string('sku', 100);
            $table->string('barcode', 100)->nullable();
            $table->decimal('purchase_price', 18, 2)->default(0);
            $table->decimal('selling_price', 18, 2)->default(0);
            $table->decimal('cost_price', 18, 2)->default(0);
            $table->boolean('is_default')->default(false);
            $table->boolean('is_active')->default(true);
            $table->jsonb('attributes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['tenant_id', 'sku']);
            $table->index('product_id');
        });

        // 4.9 branch_prices
        Schema::create('branch_prices', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('tenant_id')->constrained('tenants')->onDelete('cascade');
            $table->foreignUuid('branch_id')->constrained('branches')->onDelete('cascade');
            $table->foreignUuid('product_id')->nullable()->constrained('products')->onDelete('cascade');
            $table->foreignUuid('product_variant_id')->nullable()->constrained('product_variants')->onDelete('cascade');
            $table->decimal('price', 18, 2);
            $table->timestamps();

            $table->index('tenant_id');
            $table->index('branch_id');
            $table->index('product_id');
            $table->index('product_variant_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('branch_prices');
        Schema::dropIfExists('product_variants');
        Schema::dropIfExists('products');
        Schema::dropIfExists('product_categories');
        Schema::dropIfExists('units');
        Schema::dropIfExists('brands');
        Schema::dropIfExists('suppliers');
        Schema::dropIfExists('customers');
        Schema::dropIfExists('customer_groups');
    }
};
