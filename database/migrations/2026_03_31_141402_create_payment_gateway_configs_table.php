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
        Schema::create('payment_gateway_configs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('provider'); // xendit, midtrans, stripe, etc
            $table->string('name'); // Display Name
            $table->jsonb('config'); // secret_key, public_key, webhook_secret, etc
            $table->boolean('is_active')->default(false);
            $table->boolean('is_test_mode')->default(true);
            $table->timestamps();
            $table->softDeletes();
            
            $table->unique('provider');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_gateway_configs');
    }
};
