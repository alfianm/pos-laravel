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
            $table->integer('recency_score')->default(0)->after('status');
            $table->integer('frequency_score')->default(0)->after('recency_score');
            $table->integer('monetary_score')->default(0)->after('frequency_score');
            $table->string('rfm_segment', 50)->nullable()->after('monetary_score');
        });
    }

    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn(['recency_score', 'frequency_score', 'monetary_score', 'rfm_segment']);
        });
    }
};
