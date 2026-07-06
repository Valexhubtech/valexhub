<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::table('user_products', function (Blueprint $table) {
            $table->enum('deployment_type', ['cloud', 'onprem'])->default('cloud')->after('subscription_id');
            $table->unsignedBigInteger('product_pricing_id')->nullable()->after('deployment_type');
            $table->decimal('setup_amount', 10, 2)->default(0)->after('product_pricing_id'); // one-time setup/license paid
            $table->decimal('addons_amount', 10, 2)->default(0)->after('setup_amount');      // one-time addons paid

            $table->foreign('product_pricing_id')->references('id')->on('product_pricing')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('user_products', function (Blueprint $table) {
            $table->dropForeign(['product_pricing_id']);
            $table->dropColumn(['deployment_type', 'product_pricing_id', 'setup_amount', 'addons_amount']);
        });
    }
};
