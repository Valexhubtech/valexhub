<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('domain_orders', function (Blueprint $table) {
            $table->id();
            $table->string('instance_id');
            $table->string('domain');
            $table->decimal('price', 10, 2);
            $table->string('currency', 3)->default('NGN');
            $table->enum('state', [
                'pending',
                'purchasing',
                'awaiting_wallet',
                'registered',
                'dns_ready',
                'active',
            ])->default('pending');
            $table->string('go54_reference')->nullable();
            $table->text('last_error')->nullable();
            $table->timestamps();

            $table->index('instance_id');
            $table->index('state');
            $table->index('domain');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('domain_orders');
    }
};
