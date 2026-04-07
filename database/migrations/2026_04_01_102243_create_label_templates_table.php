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
        Schema::create('label_templates', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('tenant_id')->constrained('tenants');
            $table->string('name');
            $table->string('type', 50)->index(); // product_label, shelf_label, address_label
            $table->string('paper_size', 20)->default('A4');
            $table->decimal('width_mm', 6, 2);
            $table->decimal('height_mm', 6, 2);
            $table->integer('labels_per_row')->default(1);
            $table->integer('labels_per_column')->default(1);
            $table->decimal('margin_top_mm', 6, 2)->default(10);
            $table->decimal('margin_left_mm', 6, 2)->default(10);
            $table->decimal('label_spacing_horizontal_mm', 6, 2)->default(0);
            $table->decimal('label_spacing_vertical_mm', 6, 2)->default(0);
            $table->json('fields')->nullable(); // JSON template fields
            $table->text('html_template')->nullable();
            $table->boolean('is_default')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['tenant_id', 'type', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('label_templates');
    }
};
