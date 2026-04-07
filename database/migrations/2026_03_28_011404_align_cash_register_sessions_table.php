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
        Schema::table('cash_register_sessions', function (Blueprint $table) {
            // Rename columns to match model and code
            if (Schema::hasColumn('cash_register_sessions', 'opening_cash') && !Schema::hasColumn('cash_register_sessions', 'opening_balance')) {
                $table->renameColumn('opening_cash', 'opening_balance');
            }
            if (Schema::hasColumn('cash_register_sessions', 'closing_cash') && !Schema::hasColumn('cash_register_sessions', 'closing_balance')) {
                $table->renameColumn('closing_cash', 'closing_balance');
            }
            if (Schema::hasColumn('cash_register_sessions', 'total_sale_amount') && !Schema::hasColumn('cash_register_sessions', 'total_cash_sales')) {
                $table->renameColumn('total_sale_amount', 'total_cash_sales');
            }
            if (Schema::hasColumn('cash_register_sessions', 'actual_cash') && !Schema::hasColumn('cash_register_sessions', 'total_cash_submitted')) {
                $table->renameColumn('actual_cash', 'total_cash_submitted');
            }

            // Add missing columns if they don't exist
            if (!Schema::hasColumn('cash_register_sessions', 'total_non_cash_sales')) {
                $table->decimal('total_non_cash_sales', 18, 2)->default(0)->after('total_cash_sales');
            }
            if (!Schema::hasColumn('cash_register_sessions', 'total_cash_submitted')) {
                 if (!Schema::hasColumn('cash_register_sessions', 'actual_cash')) {
                    $table->decimal('total_cash_submitted', 18, 2)->default(0)->after('total_non_cash_sales');
                 }
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cash_register_sessions', function (Blueprint $table) {
            // Reversing might be complex so we'll just keep it simple
        });
    }
};
