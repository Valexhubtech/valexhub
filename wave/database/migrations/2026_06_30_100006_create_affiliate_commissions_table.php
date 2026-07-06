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
        Schema::create('affiliate_commissions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('affiliate_id');
            $table->unsignedBigInteger('referred_user_id');
            $table->unsignedBigInteger('subscription_id');
            $table->unsignedBigInteger('payment_transaction_id');
            $table->unsignedInteger('billing_month_number');
            $table->decimal('plan_monthly_price', 10, 2);
            $table->decimal('commission_rate', 5, 2);
            $table->decimal('commission_amount', 10, 2);
            $table->enum('status', ['accrued', 'claimed'])->default('accrued');
            $table->timestamp('accrued_at');
            $table->timestamp('claimed_at')->nullable();
            $table->timestamps();

            $table->foreign('affiliate_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('referred_user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('subscription_id')->references('id')->on('subscriptions')->onDelete('cascade');
            $table->foreign('payment_transaction_id')->references('id')->on('payment_transactions')->onDelete('cascade');

            $table->unique(['subscription_id', 'billing_month_number'], 'uniq_subscription_month');
            $table->index(['affiliate_id', 'status'], 'idx_affiliate_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('affiliate_commissions');
    }
};
