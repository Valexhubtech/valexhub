<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Wave\Product;

class HospitalManagementSystemSeeder extends Seeder
{
    public function run(): void
    {
        $product = Product::create([
            'name' => 'Hospital Management System',
            'slug' => 'hospital-management-system',
            'category_id' => 1,
            'type' => 'prebuilt',
            'short_description' => 'A complete hospital management platform for Nigerian healthcare facilities.',
            'description' => 'Manage patient records, appointments, billing, lab results, pharmacy, and staff — all in one place. Deploy instantly and start running your facility digitally today.',
            'low_price' => 17000,
            'high_price' => 350000,
            'features' => [
                'Patient records & history',
                'Appointment scheduling',
                'Billing & invoicing',
                'Laboratory management',
                'Pharmacy integration',
                'Staff & payroll management',
                'Insurance claims tracking',
                'Ward & bed management',
            ],
            'is_active' => true,
            'sort_order' => 2,
        ]);

        // Cloud pricing — ₦17k/month, ₦45k/quarter, ₦160k/year
        DB::table('product_pricing')->insert([
            ['product_id' => $product->id, 'deployment_type' => 'cloud', 'price_type' => 'monthly',   'amount' => 17000,  'is_required' => false, 'is_active' => true, 'sort_order' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['product_id' => $product->id, 'deployment_type' => 'cloud', 'price_type' => 'quarterly',  'amount' => 45000,  'is_required' => false, 'is_active' => true, 'sort_order' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['product_id' => $product->id, 'deployment_type' => 'cloud', 'price_type' => 'yearly',     'amount' => 160000, 'is_required' => false, 'is_active' => true, 'sort_order' => 3, 'created_at' => now(), 'updated_at' => now()],

            // On-prem pricing — License + Setup (required), then monthly maintenance
            ['product_id' => $product->id, 'deployment_type' => 'onprem', 'price_type' => 'license', 'amount' => 300000, 'is_required' => true,  'is_active' => true, 'sort_order' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['product_id' => $product->id, 'deployment_type' => 'onprem', 'price_type' => 'setup',   'amount' => 60000,  'is_required' => true,  'is_active' => true, 'sort_order' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['product_id' => $product->id, 'deployment_type' => 'onprem', 'price_type' => 'monthly',  'amount' => 6000,   'is_required' => false, 'is_active' => true, 'sort_order' => 3, 'created_at' => now(), 'updated_at' => now()],
        ]);

        // Add-ons (price_type is enum: 'onetime' or 'recurring'; billing_cycle: 'monthly'|'quarterly'|'yearly'|null)
        DB::table('product_addons')->insert([
            ['product_id' => $product->id, 'name' => 'SMS & WhatsApp Notifications',    'description' => 'Automated appointment reminders and billing alerts for patients',  'price' => 15000,  'price_type' => 'recurring', 'billing_cycle' => 'monthly', 'deployment_type' => 'both', 'is_active' => true, 'sort_order' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['product_id' => $product->id, 'name' => 'Mobile App (iOS & Android)',       'description' => 'Patient-facing app for appointments, results, and billing',         'price' => 150000, 'price_type' => 'onetime',   'billing_cycle' => null,      'deployment_type' => 'both', 'is_active' => true, 'sort_order' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['product_id' => $product->id, 'name' => 'Laboratory Module',                'description' => 'Advanced lab test ordering, results, and reporting',                 'price' => 25000,  'price_type' => 'onetime',   'billing_cycle' => null,      'deployment_type' => 'both', 'is_active' => true, 'sort_order' => 3, 'created_at' => now(), 'updated_at' => now()],
            ['product_id' => $product->id, 'name' => 'Insurance Claims Integration',     'description' => 'NHIS/HMO claims processing and tracking',                           'price' => 30000,  'price_type' => 'onetime',   'billing_cycle' => null,      'deployment_type' => 'both', 'is_active' => true, 'sort_order' => 4, 'created_at' => now(), 'updated_at' => now()],
            ['product_id' => $product->id, 'name' => 'Staff Training (2 days on-site)', 'description' => 'Hands-on training for your clinical and administrative staff',       'price' => 30000,  'price_type' => 'onetime',   'billing_cycle' => null,      'deployment_type' => 'both', 'is_active' => true, 'sort_order' => 5, 'created_at' => now(), 'updated_at' => now()],
        ]);

        $this->command->info('Hospital Management System seeded successfully.');
    }
}
