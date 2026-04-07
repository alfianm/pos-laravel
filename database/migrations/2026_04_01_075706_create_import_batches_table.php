<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('import_batches', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('tenant_id')->constrained()->onDelete('cascade');
            $table->foreignUuid('user_id')->constrained()->onDelete('cascade');
            $table->string('import_type'); // products, customers, suppliers, etc
            $table->string('file_path')->nullable();
            $table->string('original_filename')->nullable();
            $table->string('status')->default('pending'); // pending, processing, completed, failed, partial
            $table->integer('total_rows')->default(0);
            $table->integer('processed_rows')->default(0);
            $table->integer('success_count')->default(0);
            $table->integer('error_count')->default(0);
            $table->jsonb('errors_log')->nullable();
            $table->jsonb('mapping_config')->nullable();
            $table->jsonb('meta')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['tenant_id', 'status']);
            $table->index(['tenant_id', 'import_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('import_batches');
    }
};
