<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('email_domains', function (Blueprint $table) {
            $table->id();
            $table->string('instance_id');
            $table->string('domain');
            $table->boolean('is_shared')->default(false);
            $table->enum('stage', ['default_only', 'custom_pending', 'custom_active'])->default('default_only');
            $table->enum('status', ['pending', 'manual', 'verifying', 'stalled', 'active'])->default('pending');
            $table->json('records')->nullable();
            $table->text('plume_api_key')->nullable(); // stored encrypted
            $table->timestamp('stalled_at')->nullable();
            $table->timestamps();

            $table->unique(['instance_id', 'domain']);
            $table->index('instance_id');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('email_domains');
    }
};
