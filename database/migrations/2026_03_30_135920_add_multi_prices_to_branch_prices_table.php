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
        Schema::table('branch_prices', function (Blueprint $table) {
            $table->decimal('retail_price', 18, 2)->after('price')->default(0);
            $table->decimal('wholesale_price', 18, 2)->after('retail_price')->default(0);
            $table->decimal('member_price', 18, 2)->after('wholesale_price')->default(0);
        });

        // Migrate existing price to retail_price
        DB::statement('UPDATE branch_prices SET retail_price = price');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('branch_prices', function (Blueprint $table) {
            $table->dropColumn(['retail_price', 'wholesale_price', 'member_price']);
        });
    }
};
