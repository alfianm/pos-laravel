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
        Schema::table('customers', function (Blueprint $table) {
            if (Schema::hasColumn('customers', 'last_purchase_at') && !Schema::hasColumn('customers', 'last_purchase_date')) {
                $table->renameColumn('last_purchase_at', 'last_purchase_date');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            if (Schema::hasColumn('customers', 'last_purchase_date') && !Schema::hasColumn('customers', 'last_purchase_at')) {
                $table->renameColumn('last_purchase_date', 'last_purchase_at');
            }
        });
    }
};
