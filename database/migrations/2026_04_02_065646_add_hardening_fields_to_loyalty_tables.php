<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('loyalty_transactions', function (Blueprint $table) {
            $table->decimal('remaining_points', 18, 4)->default(0)->after('points');
            $table->timestamp('expires_at')->nullable()->after('remaining_points');
            $table->boolean('is_expired')->default(false)->after('expires_at');
        });

        Schema::table('vouchers', function (Blueprint $table) {
            $table->decimal('max_discount_amount', 18, 2)->nullable()->after('value');
            $table->boolean('is_active')->default(true)->after('max_discount_amount');
            $table->foreignUuid('membership_tier_id')->nullable()->constrained('membership_tiers')->onDelete('set null')->after('tenant_id');
        });
    }

    public function down(): void
    {
        Schema::table('loyalty_transactions', function (Blueprint $table) {
            $table->dropColumn(['remaining_points', 'expires_at', 'is_expired']);
        });

        Schema::table('vouchers', function (Blueprint $table) {
            $table->dropForeign(['membership_tier_id']);
            $table->dropColumn(['max_discount_amount', 'is_active', 'membership_tier_id']);
        });
    }
};
