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
        Schema::create('promo_rules', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('voucher_id')->constrained()->cascadeOnDelete();
            
            $table->string('type'); // buy_x_get_y, min_qty, bundle
            $table->json('rule_data'); // e.g. {"buy_qty": 2, "get_qty": 1, "target_sku": "..."}
            
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('promo_rules');
    }
};
