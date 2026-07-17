<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->string('coolify_deploy_type')->nullable()->after('type');
            $table->string('coolify_docker_image')->nullable()->after('coolify_deploy_type');
            $table->string('coolify_git_repo')->nullable()->after('coolify_docker_image');
            $table->string('coolify_git_branch')->default('main')->after('coolify_git_repo');
            $table->json('coolify_env_template')->nullable()->after('coolify_git_branch');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn([
                'coolify_deploy_type',
                'coolify_docker_image',
                'coolify_git_repo',
                'coolify_git_branch',
                'coolify_env_template',
            ]);
        });
    }
};
