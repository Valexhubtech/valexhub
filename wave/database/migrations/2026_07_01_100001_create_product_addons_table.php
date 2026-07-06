<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('product_addons', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('product_id');
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2);
            $table->enum('price_type', ['onetime', 'recurring'])->default('onetime');
            $table->enum('billing_cycle', ['monthly', 'quarterly', 'yearly'])->nullable(); // only for recurring
            $table->string('paystack_plan_code')->nullable(); // only for recurring addons
            $table->enum('deployment_type', ['cloud', 'onprem', 'both'])->default('both');
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->index(['product_id', 'is_active'], 'idx_product_addons');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_addons');
    }
};
