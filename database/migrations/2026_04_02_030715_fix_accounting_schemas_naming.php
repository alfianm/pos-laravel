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
        // Fix chart_of_accounts table
        Schema::table('chart_of_accounts', function (Blueprint $table) {
            if (Schema::hasColumn('chart_of_accounts', 'code') && !Schema::hasColumn('chart_of_accounts', 'account_code')) {
                $table->renameColumn('code', 'account_code');
            }
            if (Schema::hasColumn('chart_of_accounts', 'name') && !Schema::hasColumn('chart_of_accounts', 'account_name')) {
                $table->renameColumn('name', 'account_name');
            }
            if (Schema::hasColumn('chart_of_accounts', 'category_id') && !Schema::hasColumn('chart_of_accounts', 'account_category_id')) {
                $table->renameColumn('category_id', 'account_category_id');
            }
        });

        // Fix journal_entries table
        Schema::table('journal_entries', function (Blueprint $table) {
            if (!Schema::hasColumn('journal_entries', 'reference_number')) {
                $table->string('reference_number')->nullable()->after('journal_number');
            }
            if (Schema::hasColumn('journal_entries', 'reversal_of_id') && !Schema::hasColumn('journal_entries', 'reversed_entry_id')) {
                $table->renameColumn('reversal_of_id', 'reversed_entry_id');
            }
            if (!Schema::hasColumn('journal_entries', 'posted_by')) {
                $table->uuid('posted_by')->nullable()->after('posted_at');
                $table->foreign('posted_by')->references('id')->on('users')->nullOnDelete();
            }
            if (!Schema::hasColumn('journal_entries', 'attachment')) {
                $table->string('attachment')->nullable()->after('status');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('chart_of_accounts', function (Blueprint $table) {
            if (Schema::hasColumn('chart_of_accounts', 'account_code')) {
                $table->renameColumn('account_code', 'code');
            }
            if (Schema::hasColumn('chart_of_accounts', 'account_name')) {
                $table->renameColumn('account_name', 'name');
            }
            if (Schema::hasColumn('chart_of_accounts', 'account_category_id')) {
                $table->renameColumn('account_category_id', 'category_id');
            }
        });

        Schema::table('journal_entries', function (Blueprint $table) {
            if (Schema::hasColumn('journal_entries', 'reference_number')) {
                $table->dropColumn('reference_number');
            }
            if (Schema::hasColumn('journal_entries', 'reversed_entry_id')) {
                $table->renameColumn('reversed_entry_id', 'reversal_of_id');
            }
            if (Schema::hasColumn('journal_entries', 'posted_by')) {
                $table->dropForeign(['posted_by']);
                $table->dropColumn('posted_by');
            }
            if (Schema::hasColumn('journal_entries', 'attachment')) {
                $table->dropColumn('attachment');
            }
        });
    }
};
