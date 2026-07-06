<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration
{
    public function up(): void
    {
        Schema::create('coolify_servers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('api_url');
            $table->text('api_key');   // stored encrypted
            $table->unsignedInteger('max_deployments')->default(50);
            $table->decimal('monthly_cost', 10, 2)->default(0);
            $table->enum('status', ['active', 'offline', 'maintenance'])->default('active');
            $table->unsignedInteger('sort_order')->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::table('deployments', function (Blueprint $table) {
            $table->unsignedBigInteger('coolify_server_id')->nullable()->after('coolify_app_id');
            $table->foreign('coolify_server_id')->references('id')->on('coolify_servers')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('deployments', function (Blueprint $table) {
            $table->dropForeign(['coolify_server_id']);
            $table->dropColumn('coolify_server_id');
        });
        Schema::dropIfExists('coolify_servers');
    }
};
