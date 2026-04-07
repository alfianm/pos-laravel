<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subscription_plans', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('code', 50)->unique();
            $table->string('name', 150);
            $table->text('description')->nullable();
            $table->string('billing_cycle', 20)->default('monthly');
            $table->decimal('price_monthly', 15, 2)->default(0);
            $table->decimal('price_yearly', 15, 2)->default(0);
            $table->jsonb('features')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_public')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->index('code');
            $table->index('is_active');
            $table->index('is_public');
            $table->index('sort_order');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subscription_plans');
    }
};
