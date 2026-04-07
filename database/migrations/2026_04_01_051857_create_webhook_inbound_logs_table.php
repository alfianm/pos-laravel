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
        Schema::create('webhook_inbound_logs', function (Blueprint $blueprint) {
            $blueprint->uuid('id')->primary();
            $blueprint->foreignUuid('tenant_id')->constrained()->cascadeOnDelete();
            $blueprint->string('source')->index(); // 'marketplace-shopee', 'marketplace-tokopedia'
            $blueprint->string('event_type')->index(); // 'stock-sync'
            $blueprint->jsonb('payload');
            $blueprint->integer('status_code');
            $blueprint->jsonb('response_payload')->nullable();
            $blueprint->string('ip_address')->nullable();
            $blueprint->string('user_agent')->nullable();
            $blueprint->timestamps();

            $blueprint->index(['tenant_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('webhook_inbound_logs');
    }
};
