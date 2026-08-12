<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('google_auth_nonces', function (Blueprint $table) {
            $table->string('nonce', 64)->primary();
            $table->unsignedBigInteger('deployment_id')->nullable()->index();
            $table->timestamp('expires_at')->index();

            $table->foreign('deployment_id')
                ->references('id')
                ->on('deployments')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('google_auth_nonces');
    }
};
