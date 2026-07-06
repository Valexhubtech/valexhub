<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('plans', function (Blueprint $table) {
            $table->string('paystack_plan_code_monthly')->nullable()->after('onetime_price_id');
            $table->string('paystack_plan_code_yearly')->nullable()->after('paystack_plan_code_monthly');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('plans', function (Blueprint $table) {
            $table->dropColumn(['paystack_plan_code_monthly', 'paystack_plan_code_yearly']);
        });
    }
};
