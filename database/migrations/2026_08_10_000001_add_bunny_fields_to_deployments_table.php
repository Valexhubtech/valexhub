<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('deployments', function (Blueprint $table) {
            $table->unsignedInteger('bunny_library_id')->nullable()->after('central_api_key');
            $table->text('bunny_api_key_encrypted')->nullable()->after('bunny_library_id');
        });
    }

    public function down(): void
    {
        Schema::table('deployments', function (Blueprint $table) {
            $table->dropColumn(['bunny_library_id', 'bunny_api_key_encrypted']);
        });
    }
};
