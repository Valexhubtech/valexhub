<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $driver = DB::getDriverName();

        if ($driver === 'sqlite') {
            return; // SQLite has no ENUM — status is TEXT, all values already valid
        }

        if ($driver === 'pgsql') {
            $this->alterPostgres(
                'deployments',
                "status IN ('pending','provisioning','active','failed','suspended','terminated')"
            );
            $this->alterPostgres(
                'user_products',
                "status IN ('active','inactive','cancelled','expired','suspended','terminated')"
            );
        } else {
            DB::statement("ALTER TABLE deployments MODIFY COLUMN status ENUM('pending','provisioning','active','failed','suspended','terminated') NOT NULL DEFAULT 'pending'");
            DB::statement("ALTER TABLE user_products MODIFY COLUMN status ENUM('active','inactive','cancelled','expired','suspended','terminated') NOT NULL DEFAULT 'inactive'");
        }
    }

    public function down(): void
    {
        $driver = DB::getDriverName();

        if ($driver === 'sqlite') {
            return;
        }

        if ($driver === 'pgsql') {
            $this->alterPostgres(
                'deployments',
                "status IN ('pending','provisioning','active','failed')"
            );
            $this->alterPostgres(
                'user_products',
                "status IN ('active','inactive','cancelled','expired')"
            );
        } else {
            DB::statement("ALTER TABLE deployments MODIFY COLUMN status ENUM('pending','provisioning','active','failed') NOT NULL DEFAULT 'pending'");
            DB::statement("ALTER TABLE user_products MODIFY COLUMN status ENUM('active','inactive','cancelled','expired') NOT NULL DEFAULT 'inactive'");
        }
    }

    private function alterPostgres(string $table, string $check): void
    {
        // Drop any existing CHECK constraints on the status column for this table
        $constraints = DB::select("
            SELECT tc.constraint_name
            FROM information_schema.table_constraints tc
            JOIN information_schema.check_constraints cc
              ON tc.constraint_name = cc.constraint_name
            WHERE tc.table_name = ?
              AND tc.constraint_type = 'CHECK'
              AND cc.check_clause ILIKE '%status%'
        ", [$table]);

        foreach ($constraints as $row) {
            DB::statement("ALTER TABLE \"{$table}\" DROP CONSTRAINT IF EXISTS \"{$row->constraint_name}\"");
        }

        $constraintName = "{$table}_status_check";
        DB::statement("ALTER TABLE \"{$table}\" ADD CONSTRAINT \"{$constraintName}\" CHECK ({$check})");
    }
};
