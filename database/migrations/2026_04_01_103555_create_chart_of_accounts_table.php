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
        Schema::create('chart_of_accounts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('account_category_id')->constrained('account_categories');
            $table->string('account_code', 20)->unique();
            $table->string('account_name');
            $table->string('type'); // asset, liability, equity, revenue, expense
            $table->integer('normal_balance'); // 1 = debit, -1 = credit
            $table->integer('level')->default(1); // 1 = major, 2 = sub, 3 = detail
            $table->uuid('parent_id')->nullable();
            $table->foreignUuid('branch_id')->nullable()->constrained(); // nullable for company-wide accounts
            $table->decimal('opening_balance', 15, 2)->default(0);
            $table->decimal('current_balance', 15, 2)->default(0);
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_cash_account')->default(false);
            $table->boolean('is_bank_account')->default(false);
            $table->string('bank_account_number')->nullable();
            $table->string('bank_name')->nullable();
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['tenant_id', 'account_code']);
            $table->index(['tenant_id', 'type']);
            $table->index(['tenant_id', 'account_category_id']);
            $table->index(['tenant_id', 'parent_id']);
            $table->index(['tenant_id', 'branch_id']);
        });

        // Add self-referencing foreign key after table is created
        Schema::table('chart_of_accounts', function (Blueprint $table) {
            $table->foreign('parent_id')->references('id')->on('chart_of_accounts')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chart_of_accounts');
    }
};
