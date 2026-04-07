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
        Schema::create('account_balances', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('branch_id')->nullable()->constrained();
            $table->foreignUuid('account_id')->constrained('chart_of_accounts');
            $table->date('balance_date');
            $table->decimal('opening_balance', 15, 2)->default(0);
            $table->decimal('closing_balance', 15, 2)->default(0);
            $table->decimal('debit_movement', 15, 2)->default(0);
            $table->decimal('credit_movement', 15, 2)->default(0);
            $table->string('period_month', 7); // YYYY-MM
            $table->timestamps();

            $table->unique(['tenant_id', 'branch_id', 'account_id', 'period_month'], 'unique_account_balance');
            $table->index(['tenant_id', 'period_month']);
            $table->index(['tenant_id', 'account_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('account_balances');
    }
};
