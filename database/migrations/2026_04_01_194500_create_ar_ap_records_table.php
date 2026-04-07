<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('ar_ap_records', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('tenant_id')->constrained('tenants');
            $table->foreignUuid('branch_id')->constrained('branches');
            $table->enum('type', ['ar', 'ap']); // AR = Accounts Receivable, AP = Accounts Payable
            $table->foreignUuid('entity_id'); // customer_id for AR, supplier_id for AP
            $table->string('entity_type'); // App\Models\Customer or App\Models\Supplier
            $table->foreignUuid('transaction_id'); // sale_id or purchase_order_id
            $table->string('transaction_type'); // App\Models\Sale or App\Models\PurchaseOrder
            $table->string('reference_number'); // invoice_number or po_number
            $table->date('transaction_date');
            $table->date('due_date');
            $table->decimal('total_amount', 15, 2);
            $table->decimal('paid_amount', 15, 2)->default(0);
            $table->decimal('balance_amount', 15, 2);
            $table->enum('status', ['outstanding', 'partial', 'paid', 'overdue'])->default('outstanding');
            $table->integer('days_overdue')->default(0);
            $table->text('notes')->nullable();
            $table->foreignUuid('created_by')->constrained('users');
            $table->timestamps();
            $table->softDeletes();

            // Indexes for reporting
            $table->index(['tenant_id', 'type', 'status']);
            $table->index(['tenant_id', 'entity_id', 'entity_type']);
            $table->index(['tenant_id', 'due_date']);
            $table->index(['tenant_id', 'type', 'status', 'due_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ar_ap_records');
    }
};