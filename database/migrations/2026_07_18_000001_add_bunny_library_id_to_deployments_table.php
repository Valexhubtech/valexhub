<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::table('deployments', function (Blueprint $table) {
            $table->unsignedBigInteger('bunny_library_id')->nullable()->after('custom_domain');
        });
    }

    public function down(): void
    {
        Schema::table('deployments', function (Blueprint $table) {
            $table->dropColumn('bunny_library_id');
        });
    }
};
