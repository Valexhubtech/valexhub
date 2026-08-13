<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dns_changes', function (Blueprint $table) {
            $table->id();
            $table->string('domain');
            $table->string('actor'); // 'system' | user id
            $table->enum('action', ['create', 'update', 'delete']);
            $table->string('record_type');
            $table->string('subname');
            $table->json('before')->nullable();
            $table->json('after')->nullable();
            $table->timestamps();

            $table->index('domain');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dns_changes');
    }
};
