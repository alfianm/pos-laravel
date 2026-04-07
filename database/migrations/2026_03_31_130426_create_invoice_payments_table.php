<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('invoice_payments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('tenant_id')->constrained();
            $table->foreignUuid('branch_id')->constrained();
            $table->foreignUuid('invoice_id')->constrained();
            $table->string('payment_number', 50)->unique();
            $table->date('payment_date');
            $table->decimal('amount', 15, 2);
            $table->string('method', 30); // cash, transfer, credit_card, debit_card, ewallet, etc
            $table->string('reference_number', 100)->nullable();
            $table->string('bank_name', 100)->nullable();
            $table->string('account_number', 100)->nullable();
            $table->string('account_name', 100)->nullable();
            $table->text('notes')->nullable();
            $table->string('status', 20)->default('completed'); // completed, pending, failed, refunded
            $table->foreignUuid('processed_by')->constrained('users');
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['tenant_id', 'branch_id']);
            $table->index('invoice_id');
            $table->index('payment_date');
            $table->index('method');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoice_payments');
    }
};
