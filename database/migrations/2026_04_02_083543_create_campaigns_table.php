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
        Schema::create('campaigns', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('tenant_id')->constrained()->cascadeOnDelete();
            
            $table->string('name');
            $table->enum('type', ['voucher', 'broadcast', 'loyalty_bonus'])->default('voucher');
            
            // Targeting
            $table->string('target_segment')->nullable(); // e.g. "Champions", "At Risk"
            $table->json('target_filters')->nullable(); // For custom criteria
            
            // Content & Benefit
            $table->foreignUuid('voucher_id')->nullable()->constrained('vouchers')->nullOnDelete();
            $table->decimal('bonus_points', 12, 2)->default(0);
            $table->text('message')->nullable();
            
            // Schedule & Status
            $table->enum('status', ['draft', 'scheduled', 'running', 'completed', 'cancelled'])->default('draft');
            $table->dateTime('starts_at')->nullable();
            $table->dateTime('ends_at')->nullable();
            
            // Analytics cache
            $table->integer('reach_count')->default(0);
            $table->integer('conversion_count')->default(0);
            $table->decimal('revenue_generated', 15, 2)->default(0);

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('campaigns');
    }
};
