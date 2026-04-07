<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('marketplace_accounts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('tenant_id')->constrained('tenants')->onDelete('cascade');
            $table->string('marketplace', 50); // shopee, tokopedia
            $table->string('name', 150);
            $table->text('access_token')->nullable();
            $table->text('refresh_token')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->jsonb('meta')->nullable();
            $table->timestamps();
        });

        Schema::create('marketplace_shops', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('tenant_id')->constrained('tenants')->onDelete('cascade');
            $table->foreignUuid('marketplace_account_id')->constrained('marketplace_accounts')->onDelete('cascade');
            $table->string('shop_id', 100);
            $table->string('name', 150);
            $table->timestamps();
        });

        Schema::create('marketplace_product_maps', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('tenant_id')->constrained('tenants')->onDelete('cascade');
            $table->foreignUuid('product_id')->nullable()->constrained('products')->onDelete('cascade');
            $table->foreignUuid('product_variant_id')->nullable()->constrained('product_variants')->onDelete('cascade');
            $table->string('marketplace', 50);
            $table->string('external_product_id', 100);
            $table->string('external_sku', 150)->nullable();
            $table->timestamps();
        });

        Schema::create('marketplace_orders', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('tenant_id')->constrained('tenants')->onDelete('cascade');
            $table->foreignUuid('marketplace_shop_id')->constrained('marketplace_shops')->onDelete('cascade');
            $table->string('external_order_id', 100);
            $table->string('marketplace', 50);
            $table->timestamp('order_date');
            $table->string('status', 50);
            $table->decimal('total_amount', 18, 2);
            $table->jsonb('raw_data')->nullable();
            $table->timestamps();

            $table->unique(['marketplace', 'external_order_id'], 'mp_orders_unique');
        });

        Schema::create('marketplace_order_items', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('tenant_id')->constrained('tenants')->onDelete('cascade');
            $table->foreignUuid('marketplace_order_id')->constrained('marketplace_orders')->onDelete('cascade');
            $table->string('external_item_id', 100);
            $table->string('name', 200);
            $table->string('sku', 150)->nullable();
            $table->decimal('qty', 18, 4);
            $table->decimal('price', 18, 2);
            $table->timestamps();
        });

        Schema::create('marketplace_sync_logs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('tenant_id')->constrained('tenants')->onDelete('cascade');
            $table->string('marketplace', 50);
            $table->string('type', 50); // product, stock, order
            $table->string('status', 30); // success, failed
            $table->text('message')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('marketplace_sync_logs');
        Schema::dropIfExists('marketplace_order_items');
        Schema::dropIfExists('marketplace_orders');
        Schema::dropIfExists('marketplace_product_maps');
        Schema::dropIfExists('marketplace_shops');
        Schema::dropIfExists('marketplace_accounts');
    }
};
