<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::table('domain_purchases', function (Blueprint $table) {
            // Array of nameserver hostnames e.g. ["ns1.hostinger.com", "ns2.hostinger.com"]
            $table->json('nameservers')->nullable()->after('dns_status');

            // Array of DNS record objects: [{type, name, value, ttl}]
            $table->json('dns_records')->nullable()->after('nameservers');

            // Raw registrar response payload for debugging / future use
            $table->json('registrar_data')->nullable()->after('dns_records');
        });
    }

    public function down(): void
    {
        Schema::table('domain_purchases', function (Blueprint $table) {
            $table->dropColumn(['nameservers', 'dns_records', 'registrar_data']);
        });
    }
};
