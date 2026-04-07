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
        // Fix leads table
        Schema::table('leads', function (Blueprint $table) {
            if (!Schema::hasColumn('leads', 'lead_no')) {
                $table->string('lead_no', 50)->nullable()->after('tenant_id');
            }
            if (!Schema::hasColumn('leads', 'converted_at')) {
                $table->timestamp('converted_at')->nullable();
            }
            if (!Schema::hasColumn('leads', 'converted_customer_id')) {
                $table->foreignUuid('converted_customer_id')->nullable()->constrained('customers')->onDelete('set null');
            }
        });

        // Fix lead_stages table
        Schema::table('lead_stages', function (Blueprint $table) {
            if (!Schema::hasColumn('lead_stages', 'sort_order')) {
                // Rename 'order' to 'sort_order' if 'order' exists, otherwise create it
                if (Schema::hasColumn('lead_stages', 'order')) {
                    $table->renameColumn('order', 'sort_order');
                } else {
                    $table->integer('sort_order')->default(0);
                }
            }
        });

        // Fix proposals table
        Schema::table('proposals', function (Blueprint $table) {
            if (!Schema::hasColumn('proposals', 'deleted_at')) {
                $table->softDeletes();
            }
            
            // Rename columns back to what the model expects if they were changed
            if (Schema::hasColumn('proposals', 'date') && !Schema::hasColumn('proposals', 'proposal_date')) {
                $table->renameColumn('date', 'proposal_date');
            }
            
            if (Schema::hasColumn('proposals', 'expiry_date') && !Schema::hasColumn('proposals', 'valid_until')) {
                $table->renameColumn('expiry_date', 'valid_until');
            }
            
            if (Schema::hasColumn('proposals', 'amount') && !Schema::hasColumn('proposals', 'total_amount')) {
                $table->renameColumn('amount', 'total_amount');
            }

            if (!Schema::hasColumn('proposals', 'subtotal')) {
                $table->decimal('subtotal', 18, 2)->default(0);
            }
            if (!Schema::hasColumn('proposals', 'tax_amount')) {
                $table->decimal('tax_amount', 18, 2)->default(0);
            }
            if (!Schema::hasColumn('proposals', 'discount_amount')) {
                $table->decimal('discount_amount', 18, 2)->default(0);
            }
            if (!Schema::hasColumn('proposals', 'created_by')) {
                $table->foreignUuid('created_by')->nullable()->constrained('users')->onDelete('set null');
            }
            if (!Schema::hasColumn('proposals', 'branch_id')) {
                $table->foreignUuid('branch_id')->nullable()->constrained('branches')->onDelete('set null');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('proposals', function (Blueprint $table) {
            // Reverse is not strictly necessary for this fix-all migration but good practice
        });
    }
};
