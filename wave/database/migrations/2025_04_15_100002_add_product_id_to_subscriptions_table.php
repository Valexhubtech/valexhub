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
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->unsignedInteger('product_id')->nullable()->after('plan_id');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('set null');
            $table->index('product_id', 'idx_product');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->dropForeign(['product_id']);
            $table->dropIndex('idx_product');
            $table->dropColumn('product_id');
        });
    }
};