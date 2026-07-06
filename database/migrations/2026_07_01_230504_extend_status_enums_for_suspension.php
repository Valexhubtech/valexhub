<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE deployments MODIFY COLUMN status ENUM('pending','provisioning','active','failed','suspended','terminated') NOT NULL DEFAULT 'pending'");
        DB::statement("ALTER TABLE user_products MODIFY COLUMN status ENUM('active','inactive','cancelled','expired','suspended','terminated') NOT NULL DEFAULT 'inactive'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE deployments MODIFY COLUMN status ENUM('pending','provisioning','active','failed') NOT NULL DEFAULT 'pending'");
        DB::statement("ALTER TABLE user_products MODIFY COLUMN status ENUM('active','inactive','cancelled','expired') NOT NULL DEFAULT 'inactive'");
    }
};
