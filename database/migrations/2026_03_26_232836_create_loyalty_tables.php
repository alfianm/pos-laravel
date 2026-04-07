<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('membership_tiers', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('tenant_id')->constrained('tenants')->onDelete('cascade');
            $table->string('name', 100);
            $table->decimal('min_spending', 18, 2)->default(0);
            $table->decimal('point_multiplier', 5, 2)->default(1);
            $table->timestamps();
        });

        Schema::create('loyalty_accounts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('tenant_id')->constrained('tenants')->onDelete('cascade');
            $table->foreignUuid('customer_id')->constrained('customers')->onDelete('cascade');
            $table->foreignUuid('membership_tier_id')->nullable()->constrained('membership_tiers')->onDelete('set null');
            $table->decimal('points_balance', 18, 4)->default(0);
            $table->timestamps();

            $table->unique(['tenant_id', 'customer_id']);
        });

        Schema::create('loyalty_transactions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('tenant_id')->constrained('tenants')->onDelete('cascade');
            $table->foreignUuid('loyalty_account_id')->constrained('loyalty_accounts')->onDelete('cascade');
            $table->string('type', 30); // earn, redeem, adjust
            $table->decimal('points', 18, 4);
            $table->string('reference_type', 100)->nullable();
            $table->uuid('reference_id')->nullable();
            $table->timestamps();
        });

        Schema::create('vouchers', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('tenant_id')->constrained('tenants')->onDelete('cascade');
            $table->string('code', 50);
            $table->string('type', 30); // fixed, percentage
            $table->decimal('value', 18, 2);
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            $table->integer('usage_limit')->nullable();
            $table->integer('used_count')->default(0);
            $table->decimal('min_order_amount', 18, 2)->default(0);
            $table->timestamps();

            $table->unique(['tenant_id', 'code']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vouchers');
        Schema::dropIfExists('loyalty_transactions');
        Schema::dropIfExists('loyalty_accounts');
        Schema::dropIfExists('membership_tiers');
    }
};
