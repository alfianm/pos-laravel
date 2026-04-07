<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('barcode_label_templates', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('tenant_id')->constrained('tenants');
            $table->string('name');
            $table->string('code', 50)->unique(); // internal code like: barcode_3x2_cm
            $table->string('barcode_type', 20)->default('CODE128'); // CODE128, QR_CODE, EAN13
            $table->decimal('width_mm', 6, 2);
            $table->decimal('height_mm', 6, 2);
            $table->boolean('show_product_name')->default(true);
            $table->boolean('show_price')->default(false);
            $table->boolean('show_sku')->default(true);
            $table->boolean('show_branch_code')->default(false);
            $table->string('font_family', 50)->default('Arial');
            $table->integer('font_size')->default(10);
            $table->text('custom_css')->nullable();
            $table->boolean('is_default')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['tenant_id', 'code', 'is_active']);
            $table->index(['tenant_id', 'barcode_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('barcode_label_templates');
    }
};
