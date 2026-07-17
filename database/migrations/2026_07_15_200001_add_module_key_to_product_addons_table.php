<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::table('product_addons', function (Blueprint $table) {
            // Maps this add-on to a core app module key (e.g. 'booking', 'inventory').
            // Null means the add-on is a service/one-off and does not unlock a module.
            $table->string('module_key', 50)->nullable()->after('name');
        });
    }

    public function down(): void
    {
        Schema::table('product_addons', function (Blueprint $table) {
            $table->dropColumn('module_key');
        });
    }
};
