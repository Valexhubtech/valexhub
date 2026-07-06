<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration
{
    public function up(): void
    {
        Schema::create('domain_purchases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('deployment_id')->nullable()->constrained('deployments')->nullOnDelete();
            $table->string('domain');                          // full domain e.g. "mybusiness.com"
            $table->string('tld', 30);                        // e.g. "com" or "com.ng"
            $table->string('hostinger_item_id')->nullable();  // catalog item ID for registration
            $table->unsignedBigInteger('domain_price_kobo')->default(0);
            $table->unsignedBigInteger('setup_fee_kobo')->default(0);
            $table->unsignedBigInteger('total_kobo')->default(0);
            $table->string('paystack_reference')->nullable()->unique();
            $table->enum('payment_status', ['pending', 'paid', 'failed'])->default('pending');
            $table->enum('registration_status', ['pending', 'processing', 'registered', 'failed'])->default('pending');
            $table->enum('dns_status', ['pending', 'configured', 'failed'])->default('pending');
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('domain_purchases');
    }
};
