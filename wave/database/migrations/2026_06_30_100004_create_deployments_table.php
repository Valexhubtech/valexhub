<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('deployments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedInteger('product_id');
            $table->unsignedBigInteger('user_product_id')->nullable();
            $table->enum('deploy_for', ['self', 'client'])->default('self');
            $table->string('client_name')->nullable();
            $table->string('client_email')->nullable();
            $table->string('coolify_app_id')->nullable();
            $table->string('deployment_url')->nullable();
            $table->text('credentials_encrypted')->nullable();
            $table->enum('status', ['pending', 'provisioning', 'active', 'failed'])->default('pending');
            $table->text('failure_reason')->nullable();
            $table->enum('domain_option', ['self_managed', 'requested'])->nullable();
            $table->timestamp('deployed_at')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->foreign('user_product_id')->references('id')->on('user_products')->onDelete('set null');
            $table->index(['user_id', 'status'], 'idx_deployments_user_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('deployments');
    }
};
