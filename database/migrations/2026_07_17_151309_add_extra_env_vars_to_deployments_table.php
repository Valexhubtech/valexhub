<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::table('deployments', function (Blueprint $table) {
            $table->text('extra_env_vars')->nullable()->after('credentials_encrypted');
            $table->string('env_inject_mode')->default('alongside')->after('extra_env_vars');
        });
    }

    public function down(): void
    {
        Schema::table('deployments', function (Blueprint $table) {
            $table->dropColumn(['extra_env_vars', 'env_inject_mode']);
        });
    }
};
