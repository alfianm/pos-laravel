<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tenant_quotas', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('tenant_id')->constrained('tenants')->onDelete('cascade');
            $table->string('quota_type', 50);
            $table->integer('limit_value')->default(0);
            $table->integer('used_value')->default(0);
            $table->integer('alert_threshold')->default(80);
            $table->timestamp('last_calculated_at')->nullable();
            $table->jsonb('metadata')->nullable();
            $table->timestamps();

            $table->unique(['tenant_id', 'quota_type']);
            $table->index('tenant_id');
            $table->index('quota_type');
            $table->index(['tenant_id', 'quota_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tenant_quotas');
    }
};
