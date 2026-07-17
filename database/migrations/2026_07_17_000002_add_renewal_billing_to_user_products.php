<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::table('user_products', function (Blueprint $table) {
            // What the customer will pay at each renewal (may differ from amount_paid for custom deals)
            $table->decimal('renewal_amount', 10, 2)->nullable()->after('addons_amount');

            // How often they're billed on a recurring plan
            $table->string('billing_cycle')->nullable()->after('renewal_amount'); // monthly|quarterly|yearly
        });
    }

    public function down(): void
    {
        Schema::table('user_products', function (Blueprint $table) {
            $table->dropColumn(['renewal_amount', 'billing_cycle']);
        });
    }
};
