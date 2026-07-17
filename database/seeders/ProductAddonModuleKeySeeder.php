<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Backfills module_key on existing product_addon rows.
 * Safe to run multiple times — uses name-based matching, only updates where module_key IS NULL.
 */
class ProductAddonModuleKeySeeder extends Seeder
{
    public function run(): void
    {
        $map = [
            // School Management
            'SMS & WhatsApp Notifications' => 'notifications',
            'Online Fee Payment Gateway' => 'invoicing',

            // Hotel Management (ProductSeeder + AdditionalProductsSeeder)
            'Online Booking Engine' => 'booking',
            'Restaurant & Bar POS' => 'pos',
            'Events & Conference Booking' => 'booking',

            // Property Management
            'Automated Rent Reminders' => 'notifications',
            'Online Rent Collection' => 'invoicing',
            'Agent & Commission Tracking' => 'staff',

            // Payroll & HR
            'Biometric Attendance Integration' => 'staff',
            'Employee Self-Service Portal' => 'staff',

            // Inventory & POS
            'E-commerce Integration' => 'catalog',
            'Customer Loyalty Program' => 'contacts',
            'WhatsApp Receipt & Promotions' => 'notifications',
            'Advanced Analytics Dashboard' => 'dashboard',

            // AdditionalProductsSeeder — Hospitality Management System
            'Room & Accommodation Booking' => 'booking',
            'Inventory & Procurement' => 'inventory',
            'Events & Conference Management' => 'booking',

            // AdditionalProductsSeeder — Real Estate Management System
            'Online Listing Sync' => 'catalog',

            // AdditionalProductsSeeder — Service & Retail Business Suite
            'Online Booking Widget' => 'booking',
            'SMS & WhatsApp Reminders' => 'notifications',
            'Inventory & Stock Management' => 'inventory',
            'Customer Loyalty & Rewards' => 'contacts',
            'Staff Commission Tracking' => 'staff',
        ];

        foreach ($map as $name => $moduleKey) {
            DB::table('product_addons')
                ->where('name', $name)
                ->whereNull('module_key')
                ->update(['module_key' => $moduleKey]);
        }

        $this->command->info('Product addon module keys backfilled.');
    }
}
