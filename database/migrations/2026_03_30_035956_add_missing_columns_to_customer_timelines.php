<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('customer_timelines', function (Blueprint $table) {
            // Add branch_id if not exists
            if (! Schema::hasColumn('customer_timelines', 'branch_id')) {
                $table->foreignUuid('branch_id')->nullable()->constrained('branches')->onDelete('cascade');
                $table->index('branch_id');
            }

            // Add type if not exists
            if (! Schema::hasColumn('customer_timelines', 'type')) {
                $table->string('type', 50)->nullable();
                $table->index('type');
            }

            // Add title if not exists
            if (! Schema::hasColumn('customer_timelines', 'title')) {
                $table->string('title', 200)->nullable();
            }

            // Add metadata if not exists
            if (! Schema::hasColumn('customer_timelines', 'metadata')) {
                $table->jsonb('metadata')->nullable();
            }

            // Add occurred_at if not exists
            if (! Schema::hasColumn('customer_timelines', 'occurred_at')) {
                $table->timestamp('occurred_at')->nullable();
                $table->index('occurred_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('customer_timelines', function (Blueprint $table) {
            if (Schema::hasColumn('customer_timelines', 'branch_id')) {
                $table->dropForeign(['branch_id']);
                $table->dropColumn('branch_id');
            }
            if (Schema::hasColumn('customer_timelines', 'type')) {
                $table->dropColumn('type');
            }
            if (Schema::hasColumn('customer_timelines', 'title')) {
                $table->dropColumn('title');
            }
            if (Schema::hasColumn('customer_timelines', 'metadata')) {
                $table->dropColumn('metadata');
            }
            if (Schema::hasColumn('customer_timelines', 'occurred_at')) {
                $table->dropColumn('occurred_at');
            }
        });
    }
};
