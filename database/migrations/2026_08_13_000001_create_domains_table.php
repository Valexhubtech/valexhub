<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('domains', function (Blueprint $table) {
            $table->id();
            $table->string('domain')->unique();
            $table->string('owner')->default('us'); // us | instance_id | external
            $table->string('registrar')->default('elsewhere'); // go54 | elsewhere
            $table->string('dns_host')->default('external'); // desec | external
            $table->boolean('managed')->default(false);
            $table->timestamps();

            $table->index('owner');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('domains');
    }
};
