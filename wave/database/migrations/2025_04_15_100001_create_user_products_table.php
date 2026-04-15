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
        Schema::create('user_products', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedInteger('product_id');
            $table->decimal('amount_paid', 10, 2);
            $table->timestamp('purchase_date');
            $table->timestamp('next_renewal_date')->nullable();
            $table->enum('status', ['active', 'inactive', 'cancelled', 'expired'])->default('active');
            $table->unsignedBigInteger('subscription_id')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->foreign('subscription_id')->references('id')->on('subscriptions')->onDelete('set null');
            
            $table->index(['user_id', 'status'], 'idx_user_status');
            $table->index(['product_id', 'status'], 'idx_product_status');
            $table->unique(['user_id', 'product_id'], 'unique_user_product');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_products');
    }
};