<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    protected function createIndexIfNotExists(string $table, string $column): void
    {
        $indexName = "{$table}_{$column}_index";
        $exists = DB::selectOne('
            SELECT 1 FROM pg_indexes 
            WHERE indexname = ? AND tablename = ?
        ', [$indexName, $table]);

        if (! $exists) {
            $columnExists = DB::selectOne('
                SELECT 1 FROM information_schema.columns 
                WHERE table_name = ? AND column_name = ?
            ', [$table, $column]);

            if ($columnExists) {
                DB::statement("CREATE INDEX {$indexName} ON {$table} ({$column})");
            }
        }
    }

    protected function dropIndexIfExists(string $table, string $column): void
    {
        $indexName = "{$table}_{$column}_index";
        $exists = DB::selectOne('
            SELECT 1 FROM pg_indexes 
            WHERE indexname = ? AND tablename = ?
        ', [$indexName, $table]);

        if ($exists) {
            DB::statement("DROP INDEX {$indexName}");
        }
    }

    public function up(): void
    {
        if (DB::getDriverName() !== 'pgsql') {
            return;
        }

        $this->createIndexIfNotExists('branches', 'tenant_id');
        $this->createIndexIfNotExists('branches', 'status');

        $this->createIndexIfNotExists('customers', 'tenant_id');
        $this->createIndexIfNotExists('customers', 'customer_group_id');
        $this->createIndexIfNotExists('customers', 'status');
        $this->createIndexIfNotExists('customers', 'email');

        $this->createIndexIfNotExists('suppliers', 'tenant_id');
        $this->createIndexIfNotExists('suppliers', 'status');

        $this->createIndexIfNotExists('products', 'tenant_id');
        $this->createIndexIfNotExists('products', 'category_id');
        $this->createIndexIfNotExists('products', 'brand_id');
        $this->createIndexIfNotExists('products', 'unit_id');
        $this->createIndexIfNotExists('products', 'is_active');
        $this->createIndexIfNotExists('products', 'sku');

        $this->createIndexIfNotExists('product_variants', 'product_id');
        $this->createIndexIfNotExists('product_variants', 'sku');

        $this->createIndexIfNotExists('product_categories', 'tenant_id');
        $this->createIndexIfNotExists('product_categories', 'parent_id');

        $this->createIndexIfNotExists('brands', 'tenant_id');

        $this->createIndexIfNotExists('units', 'tenant_id');

        $this->createIndexIfNotExists('customer_groups', 'tenant_id');

        $this->createIndexIfNotExists('inventories', 'tenant_id');
        $this->createIndexIfNotExists('inventories', 'branch_id');
        $this->createIndexIfNotExists('inventories', 'product_id');
        $this->createIndexIfNotExists('inventories', 'product_variant_id');

        $this->createIndexIfNotExists('stock_movements', 'tenant_id');
        $this->createIndexIfNotExists('stock_movements', 'branch_id');
        $this->createIndexIfNotExists('stock_movements', 'inventory_id');
        $this->createIndexIfNotExists('stock_movements', 'created_at');

        $this->createIndexIfNotExists('stock_adjustments', 'tenant_id');
        $this->createIndexIfNotExists('stock_adjustments', 'branch_id');
        $this->createIndexIfNotExists('stock_adjustments', 'status');
        $this->createIndexIfNotExists('stock_adjustments', 'created_at');

        $this->createIndexIfNotExists('stock_transfers', 'tenant_id');
        $this->createIndexIfNotExists('stock_transfers', 'source_branch_id');
        $this->createIndexIfNotExists('stock_transfers', 'destination_branch_id');
        $this->createIndexIfNotExists('stock_transfers', 'status');
        $this->createIndexIfNotExists('stock_transfers', 'created_at');

        $this->createIndexIfNotExists('sales', 'tenant_id');
        $this->createIndexIfNotExists('sales', 'branch_id');
        $this->createIndexIfNotExists('sales', 'user_id');
        $this->createIndexIfNotExists('sales', 'customer_id');
        $this->createIndexIfNotExists('sales', 'status');
        $this->createIndexIfNotExists('sales', 'payment_status');
        $this->createIndexIfNotExists('sales', 'sale_date');
        $this->createIndexIfNotExists('sales', 'created_at');

        $this->createIndexIfNotExists('sale_items', 'sale_id');
        $this->createIndexIfNotExists('sale_items', 'product_id');
        $this->createIndexIfNotExists('sale_items', 'product_variant_id');

        $this->createIndexIfNotExists('sale_payments', 'sale_id');

        $this->createIndexIfNotExists('purchase_orders', 'tenant_id');
        $this->createIndexIfNotExists('purchase_orders', 'branch_id');
        $this->createIndexIfNotExists('purchase_orders', 'supplier_id');
        $this->createIndexIfNotExists('purchase_orders', 'status');
        $this->createIndexIfNotExists('purchase_orders', 'created_at');

        $this->createIndexIfNotExists('purchase_order_items', 'purchase_order_id');
        $this->createIndexIfNotExists('purchase_order_items', 'product_id');
        $this->createIndexIfNotExists('purchase_order_items', 'product_variant_id');

        $this->createIndexIfNotExists('purchase_payments', 'purchase_order_id');

        $this->createIndexIfNotExists('expenses', 'tenant_id');
        $this->createIndexIfNotExists('expenses', 'branch_id');
        $this->createIndexIfNotExists('expenses', 'expense_category_id');
        $this->createIndexIfNotExists('expenses', 'date');

        $this->createIndexIfNotExists('expense_categories', 'tenant_id');

        $this->createIndexIfNotExists('leads', 'tenant_id');
        $this->createIndexIfNotExists('leads', 'lead_source_id');
        $this->createIndexIfNotExists('leads', 'lead_stage_id');
        $this->createIndexIfNotExists('leads', 'assigned_to');
        $this->createIndexIfNotExists('leads', 'status');
        $this->createIndexIfNotExists('leads', 'created_at');

        $this->createIndexIfNotExists('follow_ups', 'lead_id');
        $this->createIndexIfNotExists('follow_ups', 'user_id');
        $this->createIndexIfNotExists('follow_ups', 'scheduled_at');

        $this->createIndexIfNotExists('proposals', 'tenant_id');
        $this->createIndexIfNotExists('proposals', 'lead_id');
        $this->createIndexIfNotExists('proposals', 'customer_id');
        $this->createIndexIfNotExists('proposals', 'branch_id');
        $this->createIndexIfNotExists('proposals', 'status');
        $this->createIndexIfNotExists('proposals', 'created_at');

        $this->createIndexIfNotExists('customer_timelines', 'customer_id');
        $this->createIndexIfNotExists('customer_timelines', 'created_at');

        $this->createIndexIfNotExists('cash_register_sessions', 'tenant_id');
        $this->createIndexIfNotExists('cash_register_sessions', 'branch_id');
        $this->createIndexIfNotExists('cash_register_sessions', 'user_id');
        $this->createIndexIfNotExists('cash_register_sessions', 'status');
        $this->createIndexIfNotExists('cash_register_sessions', 'opened_at');

        $this->createIndexIfNotExists('membership_tiers', 'tenant_id');

        $this->createIndexIfNotExists('loyalty_accounts', 'tenant_id');
        $this->createIndexIfNotExists('loyalty_accounts', 'customer_id');
        $this->createIndexIfNotExists('loyalty_accounts', 'membership_tier_id');

        $this->createIndexIfNotExists('loyalty_transactions', 'loyalty_account_id');
        $this->createIndexIfNotExists('loyalty_transactions', 'created_at');

        $this->createIndexIfNotExists('vouchers', 'tenant_id');
        $this->createIndexIfNotExists('vouchers', 'code');
        $this->createIndexIfNotExists('vouchers', 'starts_at');
        $this->createIndexIfNotExists('vouchers', 'ends_at');

        $this->createIndexIfNotExists('marketplace_accounts', 'tenant_id');
        $this->createIndexIfNotExists('marketplace_accounts', 'platform');

        $this->createIndexIfNotExists('marketplace_shops', 'tenant_id');
        $this->createIndexIfNotExists('marketplace_shops', 'marketplace_account_id');
        $this->createIndexIfNotExists('marketplace_shops', 'branch_id');

        $this->createIndexIfNotExists('marketplace_product_maps', 'tenant_id');
        $this->createIndexIfNotExists('marketplace_product_maps', 'product_id');
        $this->createIndexIfNotExists('marketplace_product_maps', 'product_variant_id');
        $this->createIndexIfNotExists('marketplace_product_maps', 'marketplace_shop_id');

        $this->createIndexIfNotExists('marketplace_orders', 'tenant_id');
        $this->createIndexIfNotExists('marketplace_orders', 'branch_id');
        $this->createIndexIfNotExists('marketplace_orders', 'marketplace_shop_id');
        $this->createIndexIfNotExists('marketplace_orders', 'status');
        $this->createIndexIfNotExists('marketplace_orders', 'payment_status');
        $this->createIndexIfNotExists('marketplace_orders', 'order_date');

        $this->createIndexIfNotExists('marketplace_order_items', 'marketplace_order_id');
        $this->createIndexIfNotExists('marketplace_order_items', 'product_id');

        $this->createIndexIfNotExists('marketplace_sync_logs', 'tenant_id');
        $this->createIndexIfNotExists('marketplace_sync_logs', 'marketplace_account_id');
        $this->createIndexIfNotExists('marketplace_sync_logs', 'sync_type');
        $this->createIndexIfNotExists('marketplace_sync_logs', 'status');
        $this->createIndexIfNotExists('marketplace_sync_logs', 'created_at');

        $this->createIndexIfNotExists('audit_logs', 'tenant_id');
        $this->createIndexIfNotExists('audit_logs', 'branch_id');
        $this->createIndexIfNotExists('audit_logs', 'user_id');
        $this->createIndexIfNotExists('audit_logs', 'model_type');
        $this->createIndexIfNotExists('audit_logs', 'model_id');
        $this->createIndexIfNotExists('audit_logs', 'action');
        $this->createIndexIfNotExists('audit_logs', 'created_at');

        $this->createIndexIfNotExists('branch_prices', 'branch_id');
        $this->createIndexIfNotExists('branch_prices', 'product_id');
        $this->createIndexIfNotExists('branch_prices', 'product_variant_id');

        $this->createIndexIfNotExists('users', 'tenant_id');
        $this->createIndexIfNotExists('users', 'active_branch_id');
        $this->createIndexIfNotExists('users', 'email');
    }

    public function down(): void
    {
        if (DB::getDriverName() !== 'pgsql') {
            return;
        }

        $indexes = [
            ['branches', 'tenant_id'],
            ['branches', 'status'],
            ['customers', 'tenant_id'],
            ['customers', 'customer_group_id'],
            ['customers', 'status'],
            ['customers', 'email'],
            ['suppliers', 'tenant_id'],
            ['suppliers', 'status'],
            ['products', 'tenant_id'],
            ['products', 'category_id'],
            ['products', 'brand_id'],
            ['products', 'unit_id'],
            ['products', 'is_active'],
            ['products', 'sku'],
            ['product_variants', 'product_id'],
            ['product_variants', 'sku'],
            ['sales', 'tenant_id'],
            ['sales', 'branch_id'],
            ['sales', 'user_id'],
            ['sales', 'customer_id'],
            ['sales', 'status'],
            ['sales', 'payment_status'],
            ['sales', 'sale_date'],
            ['sales', 'created_at'],
        ];

        foreach ($indexes as [$table, $column]) {
            $this->dropIndexIfExists($table, $column);
        }
    }
};
