<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration
{
    public function up(): void
    {
        Schema::table('coolify_servers', function (Blueprint $table) {
            $table->string('ip_address')->nullable()->after('api_url')
                ->comment('Public IP of the server — used for DNS A record configuration');
        });
    }

    public function down(): void
    {
        Schema::table('coolify_servers', function (Blueprint $table) {
            $table->dropColumn('ip_address');
        });
    }
};
