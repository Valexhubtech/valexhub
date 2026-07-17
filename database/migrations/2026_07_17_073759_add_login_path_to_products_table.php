<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // Path appended to deployment_url for the login page.
            // e.g. "/dashboard" → https://app.sslip.io/dashboard
            // Each product can differ: hospital app = "/dashboard", school = "/admin/login"
            $table->string('login_path')->default('/dashboard');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('login_path');
        });
    }
};
