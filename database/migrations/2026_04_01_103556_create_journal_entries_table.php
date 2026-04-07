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
        Schema::create('journal_entries', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('branch_id')->constrained();
            $table->string('journal_number', 50)->unique();
            $table->string('reference_number')->nullable();
            $table->date('entry_date');
            $table->string('reference_type')->nullable(); // sale, purchase, adjustment, etc
            $table->uuid('reference_id')->nullable();
            $table->uuid('reversed_entry_id')->nullable();
            $table->text('description')->nullable();
            $table->text('notes')->nullable();
            $table->string('source_document')->nullable();
            $table->uuid('created_by')->nullable();
            $table->uuid('approved_by')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->boolean('is_posted')->default(false);
            $table->timestamp('posted_at')->nullable();
            $table->uuid('posted_by')->nullable();
            $table->boolean('is_reversing_entry')->default(false);
            $table->boolean('is_reversed')->default(false);
            $table->decimal('total_debit', 15, 2)->default(0);
            $table->decimal('total_credit', 15, 2)->default(0);
            $table->boolean('is_balanced')->default(false);
            $table->string('status', 20)->default('draft'); // draft, posted, reversed
            $table->string('attachment')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['tenant_id', 'journal_number']);
            $table->index(['tenant_id', 'reference_number']);
            $table->index(['tenant_id', 'entry_date']);
            $table->index(['tenant_id', 'status']);
            $table->index(['tenant_id', 'reference_type', 'reference_id']);
            $table->index(['tenant_id', 'is_posted']);
        });

        // Add self-referencing foreign key after table is created
        Schema::table('journal_entries', function (Blueprint $table) {
            $table->foreign('reversed_entry_id')->references('id')->on('journal_entries')->nullOnDelete();
            $table->foreign('created_by')->references('id')->on('users')->nullOnDelete();
            $table->foreign('approved_by')->references('id')->on('users')->nullOnDelete();
            $table->foreign('posted_by')->references('id')->on('users')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('journal_entries');
    }
};
