<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('order_addons', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_product_id');
            $table->unsignedBigInteger('product_addon_id');
            $table->decimal('amount_paid', 10, 2);   // locked at purchase time
            $table->enum('price_type', ['onetime', 'recurring']);
            $table->string('paystack_reference')->nullable();
            $table->timestamps();

            $table->foreign('user_product_id')->references('id')->on('user_products')->onDelete('cascade');
            $table->foreign('product_addon_id')->references('id')->on('product_addons')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_addons');
    }
};
