<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('stock_transfers', function (Blueprint $table) {
            $table->date('transfer_date')->after('status')->nullable();
            $table->date('received_date')->after('received_at')->nullable();
        });

        // Populate transfer_date from created_at for existing records
        DB::statement('UPDATE stock_transfers SET transfer_date = CAST(created_at AS date)');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stock_transfers', function (Blueprint $table) {
            $table->dropColumn(['transfer_date', 'received_date']);
        });
    }
};
