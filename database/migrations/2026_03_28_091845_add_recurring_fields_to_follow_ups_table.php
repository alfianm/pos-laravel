<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('follow_ups', function (Blueprint $table) {
            $table->boolean('is_recurring')->default(false)->after('status');
            $table->string('recurrence_type', 20)->nullable()->after('is_recurring');
            $table->integer('recurrence_interval')->nullable()->after('recurrence_type');
            $table->date('recurrence_end_date')->nullable()->after('recurrence_interval');
            $table->integer('reminder_minutes_before')->nullable()->after('recurrence_end_date');
            $table->timestamp('reminder_sent_at')->nullable()->after('reminder_minutes_before');
            $table->uuid('parent_follow_up_id')->nullable()->after('reminder_sent_at');
        });
    }

    public function down(): void
    {
        Schema::table('follow_ups', function (Blueprint $table) {
            $table->dropColumn([
                'is_recurring',
                'recurrence_type',
                'recurrence_interval',
                'recurrence_end_date',
                'reminder_minutes_before',
                'reminder_sent_at',
                'parent_follow_up_id',
            ]);
        });
    }
};
