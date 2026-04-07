<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('tenant_id')->nullable()->constrained('tenants')->onDelete('cascade');
            $table->foreignUuid('branch_id')->nullable()->constrained('branches')->onDelete('cascade');
            $table->string('group', 100);
            $table->string('key', 150);
            $table->jsonb('value')->nullable();
            $table->timestamps();

            $table->unique(['tenant_id', 'branch_id', 'group', 'key']);
            $table->index('tenant_id');
            $table->index('branch_id');
            $table->index('group');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
