<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('products')
            ->whereIn('slug', [
                'school-management-system',
                'hotel-management-system',
                'property-management-system',
                'payroll-hr-management',
                'inventory-pos-system',
            ])
            ->where('type', 'custom')
            ->update(['type' => 'prebuilt']);
    }

    public function down(): void
    {
        DB::table('products')
            ->whereIn('slug', [
                'school-management-system',
                'hotel-management-system',
                'property-management-system',
                'payroll-hr-management',
                'inventory-pos-system',
            ])
            ->update(['type' => 'custom']);
    }
};
