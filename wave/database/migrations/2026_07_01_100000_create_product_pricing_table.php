<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('product_pricing', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('product_id');
            $table->enum('deployment_type', ['cloud', 'onprem', 'both'])->default('both');
            $table->enum('price_type', ['setup', 'license', 'monthly', 'quarterly', 'yearly', 'onetime']);
            $table->decimal('amount', 10, 2);
            $table->string('paystack_plan_code')->nullable(); // only for monthly/quarterly/yearly
            $table->boolean('is_required')->default(false);  // for setup/license fees
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->index(['product_id', 'deployment_type', 'is_active'], 'idx_product_pricing');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_pricing');
    }
};
