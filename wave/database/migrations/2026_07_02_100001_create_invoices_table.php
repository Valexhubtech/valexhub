<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('user_product_id')->nullable();
            $table->unsignedBigInteger('deployment_id')->nullable();
            $table->decimal('amount', 12, 2);
            $table->string('currency', 3)->default('NGN');
            $table->enum('status', ['draft', 'sent', 'paid'])->default('draft');
            $table->string('paystack_reference')->nullable()->unique();
            $table->json('line_items');
            $table->string('pdf_path')->nullable();
            $table->timestamp('due_date')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('user_product_id')->references('id')->on('user_products')->onDelete('set null');
            $table->foreign('deployment_id')->references('id')->on('deployments')->onDelete('set null');

            $table->index(['user_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
