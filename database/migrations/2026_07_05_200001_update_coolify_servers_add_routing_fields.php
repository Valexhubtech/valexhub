<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::table('coolify_servers', function (Blueprint $table) {
            $table->string('coolify_server_uuid')->nullable()->after('api_key');
            $table->string('coolify_project_uuid')->nullable()->after('coolify_server_uuid');
            $table->string('coolify_environment_name')->default('production')->after('coolify_project_uuid');
        });
    }

    public function down(): void
    {
        Schema::table('coolify_servers', function (Blueprint $table) {
            $table->dropColumn(['coolify_server_uuid', 'coolify_project_uuid', 'coolify_environment_name']);
        });
    }
};
