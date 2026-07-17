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
        Schema::table('user_products', function (Blueprint $table) {
            $table->dropUnique('unique_user_product');
        });
    }

    public function down(): void
    {
        Schema::table('user_products', function (Blueprint $table) {
            $table->unique(['user_id', 'product_id'], 'unique_user_product');
        });
    }
};
