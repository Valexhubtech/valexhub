<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::table('coolify_servers', function (Blueprint $table) {
            $table->renameColumn('api_key', 'api_token');
        });
    }

    public function down(): void
    {
        Schema::table('coolify_servers', function (Blueprint $table) {
            $table->renameColumn('api_token', 'api_key');
        });
    }
};
