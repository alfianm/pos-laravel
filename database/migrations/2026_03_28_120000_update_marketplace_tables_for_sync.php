<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('marketplace_accounts', function (Blueprint $table) {
            $table->string('external_account_id', 150)->nullable()->after('marketplace');
            $table->text('api_key')->nullable()->after('name');
            $table->text('api_secret')->nullable()->after('api_key');
            $table->string('status', 30)->default('active')->after('expires_at');
            $table->foreignUuid('created_by')->nullable()->after('status')->constrained('users')->nullOnDelete();
        });

        Schema::table('marketplace_shops', function (Blueprint $table) {
            $table->foreignUuid('branch_id')->nullable()->after('marketplace_account_id')->constrained('branches')->nullOnDelete();
            $table->string('marketplace', 50)->after('shop_id');
            $table->string('region_code', 50)->nullable()->after('name');
            $table->string('status', 30)->default('active')->after('region_code');
            $table->jsonb('settings')->nullable()->after('status');
            $table->renameColumn('shop_id', 'external_shop_id');
        });

        Schema::table('marketplace_product_maps', function (Blueprint $table) {
            $table->foreignUuid('marketplace_shop_id')->nullable()->after('tenant_id')->constrained('marketplace_shops')->nullOnDelete();
            $table->string('external_name', 255)->nullable()->after('external_sku');
            $table->boolean('sync_price')->default(true)->after('external_name');
            $table->boolean('sync_stock')->default(true)->after('sync_price');
            $table->boolean('is_active')->default(true)->after('sync_stock');
            $table->timestamp('last_sync_at')->nullable()->after('is_active');
            $table->string('last_sync_status', 30)->nullable()->after('last_sync_at');
            $table->text('last_sync_error')->nullable()->after('last_sync_status');
            $table->jsonb('meta')->nullable()->after('last_sync_error');
        });

        Schema::table('marketplace_orders', function (Blueprint $table) {
            $table->foreignUuid('branch_id')->nullable()->after('tenant_id')->constrained('branches')->nullOnDelete();
            $table->foreignUuid('customer_id')->nullable()->after('marketplace_shop_id')->constrained('customers')->nullOnDelete();
            $table->string('external_order_no', 150)->nullable()->after('external_order_id');
            $table->string('buyer_name', 150)->nullable()->after('status');
            $table->string('buyer_phone', 50)->nullable()->after('buyer_name');
            $table->decimal('subtotal', 18, 2)->default(0)->after('buyer_phone');
            $table->decimal('shipping_amount', 18, 2)->default(0)->after('subtotal');
            $table->decimal('discount_amount', 18, 2)->default(0)->after('shipping_amount');
            $table->renameColumn('total_amount', 'grand_total');
            $table->timestamp('imported_at')->nullable()->after('raw_data');
        });

        Schema::table('marketplace_order_items', function (Blueprint $table) {
            $table->foreignUuid('marketplace_product_map_id')->nullable()->after('tenant_id')->constrained('marketplace_product_maps')->nullOnDelete();
            $table->foreignUuid('product_id')->nullable()->after('marketplace_product_map_id')->constrained('products')->nullOnDelete();
            $table->foreignUuid('product_variant_id')->nullable()->after('product_id')->constrained('product_variants')->nullOnDelete();
            $table->string('external_variant_id', 150)->nullable()->after('external_item_id');
            $table->renameColumn('name', 'name_snapshot');
            $table->renameColumn('sku', 'external_sku');
            $table->renameColumn('price', 'unit_price');
            $table->decimal('line_total', 18, 2)->default(0)->after('qty');
            $table->jsonb('raw_data')->nullable()->after('line_total');
        });

        Schema::table('marketplace_sync_logs', function (Blueprint $table) {
            $table->foreignUuid('branch_id')->nullable()->after('tenant_id')->constrained('branches')->nullOnDelete();
            $table->foreignUuid('marketplace_shop_id')->nullable()->after('branch_id')->constrained('marketplace_shops')->nullOnDelete();
            $table->renameColumn('type', 'sync_type');
            $table->string('direction', 20)->nullable()->after('sync_type');
            $table->string('entity_type', 100)->nullable()->after('direction');
            $table->uuid('entity_id')->nullable()->after('entity_type');
            $table->string('external_entity_id', 150)->nullable()->after('entity_id');
            $table->renameColumn('message', 'error_message');
            $table->jsonb('request_payload')->nullable()->after('error_message');
            $table->jsonb('response_payload')->nullable()->after('request_payload');
            $table->jsonb('payload')->nullable()->after('response_payload');
            $table->timestamp('synced_at')->nullable()->after('payload');
        });
    }

    public function down(): void
    {
        Schema::table('marketplace_accounts', function (Blueprint $table) {
            $table->dropColumn(['external_account_id', 'api_key', 'api_secret', 'status', 'created_by']);
        });

        Schema::table('marketplace_shops', function (Blueprint $table) {
            $table->dropColumn(['branch_id', 'marketplace', 'region_code', 'status', 'settings']);
            $table->renameColumn('external_shop_id', 'shop_id');
        });

        Schema::table('marketplace_product_maps', function (Blueprint $table) {
            $table->dropColumn(['marketplace_shop_id', 'external_name', 'sync_price', 'sync_stock', 'is_active', 'last_sync_at', 'last_sync_status', 'last_sync_error', 'meta']);
        });

        Schema::table('marketplace_orders', function (Blueprint $table) {
            $table->dropColumn(['branch_id', 'customer_id', 'external_order_no', 'buyer_name', 'buyer_phone', 'subtotal', 'shipping_amount', 'discount_amount', 'imported_at']);
            $table->renameColumn('grand_total', 'total_amount');
        });

        Schema::table('marketplace_order_items', function (Blueprint $table) {
            $table->dropColumn(['marketplace_product_map_id', 'product_id', 'product_variant_id', 'external_variant_id', 'raw_data']);
            $table->renameColumn('name_snapshot', 'name');
            $table->renameColumn('external_sku', 'sku');
            $table->renameColumn('unit_price', 'price');
            $table->dropColumn('line_total');
        });

        Schema::table('marketplace_sync_logs', function (Blueprint $table) {
            $table->dropColumn(['branch_id', 'marketplace_shop_id', 'direction', 'entity_type', 'entity_id', 'external_entity_id', 'request_payload', 'response_payload', 'payload', 'synced_at']);
            $table->renameColumn('sync_type', 'type');
            $table->renameColumn('error_message', 'message');
        });
    }
};
