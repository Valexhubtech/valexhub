<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Wave\Product;

class AdditionalProductsSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedHospitality();
        $this->seedRealEstate();
        $this->seedServiceBusiness();
    }

    // ─────────────────────────────────────────────────────────
    // 1. HOSPITALITY MANAGEMENT SYSTEM
    //    Module-based: low base price, real value comes from
    //    the recurring add-on modules the client selects.
    // ─────────────────────────────────────────────────────────
    private function seedHospitality(): void
    {
        $product = Product::firstOrCreate(
            ['slug' => 'hospitality-management-system'],
            [
                'name'              => 'Hospitality Management System',
                'category_id'       => 1,
                'type'              => 'prebuilt',
                'short_description' => 'A modular hospitality platform for hotels, restaurants, and guest houses.',
                'description'       => 'Start with the core platform and activate only the modules your property needs — room booking, restaurant POS, inventory, and more. Pay for what you use, scale as you grow.',
                'low_price'         => 15000,
                'high_price'        => 400000,
                'features'          => [
                    'Core platform with staff & roles',
                    'Dashboard & reporting',
                    'Guest & customer records',
                    'Billing & receipts',
                    'Select only the modules you need',
                    'Cloud or on-premises deployment',
                ],
                'is_active'  => true,
                'sort_order' => 3,
            ]
        );

        if ($product->pricingOptions()->count() === 0) {
            DB::table('product_pricing')->insert([
                // Cloud — base platform
                ['product_id' => $product->id, 'deployment_type' => 'cloud', 'price_type' => 'monthly',   'amount' => 15000,  'is_required' => false, 'is_active' => true, 'sort_order' => 1, 'created_at' => now(), 'updated_at' => now()],
                ['product_id' => $product->id, 'deployment_type' => 'cloud', 'price_type' => 'quarterly',  'amount' => 40000,  'is_required' => false, 'is_active' => true, 'sort_order' => 2, 'created_at' => now(), 'updated_at' => now()],
                ['product_id' => $product->id, 'deployment_type' => 'cloud', 'price_type' => 'yearly',     'amount' => 145000, 'is_required' => false, 'is_active' => true, 'sort_order' => 3, 'created_at' => now(), 'updated_at' => now()],
                // On-prem — license + setup (required), optional monthly maintenance
                ['product_id' => $product->id, 'deployment_type' => 'onprem', 'price_type' => 'license', 'amount' => 350000, 'is_required' => true,  'is_active' => true, 'sort_order' => 1, 'created_at' => now(), 'updated_at' => now()],
                ['product_id' => $product->id, 'deployment_type' => 'onprem', 'price_type' => 'setup',   'amount' => 70000,  'is_required' => true,  'is_active' => true, 'sort_order' => 2, 'created_at' => now(), 'updated_at' => now()],
                ['product_id' => $product->id, 'deployment_type' => 'onprem', 'price_type' => 'monthly',  'amount' => 8000,   'is_required' => false, 'is_active' => true, 'sort_order' => 3, 'created_at' => now(), 'updated_at' => now()],
            ]);
        }

        if ($product->addons()->count() === 0) {
            // Recurring modules — the main value proposition
            DB::table('product_addons')->insert([
                ['product_id' => $product->id, 'name' => 'Room & Accommodation Booking',   'module_key' => 'booking',   'description' => 'Full reservation engine, availability calendar, check-in/check-out, and room management',  'price' => 10000,  'price_type' => 'recurring', 'billing_cycle' => 'monthly', 'deployment_type' => 'both', 'is_active' => true, 'sort_order' => 1, 'created_at' => now(), 'updated_at' => now()],
                ['product_id' => $product->id, 'name' => 'Restaurant & Bar POS',            'module_key' => 'pos',       'description' => 'Table management, orders, kitchen display, and split billing for your F&B operation',       'price' => 8000,   'price_type' => 'recurring', 'billing_cycle' => 'monthly', 'deployment_type' => 'both', 'is_active' => true, 'sort_order' => 2, 'created_at' => now(), 'updated_at' => now()],
                ['product_id' => $product->id, 'name' => 'Inventory & Procurement',         'module_key' => 'inventory', 'description' => 'Stock control, supplier orders, low-stock alerts, and cost tracking',                       'price' => 7000,   'price_type' => 'recurring', 'billing_cycle' => 'monthly', 'deployment_type' => 'both', 'is_active' => true, 'sort_order' => 3, 'created_at' => now(), 'updated_at' => now()],
                ['product_id' => $product->id, 'name' => 'Guest Self-Service Portal',       'module_key' => null,        'description' => 'Online check-in, service requests, and digital receipts for guests',                        'price' => 6000,   'price_type' => 'recurring', 'billing_cycle' => 'monthly', 'deployment_type' => 'both', 'is_active' => true, 'sort_order' => 4, 'created_at' => now(), 'updated_at' => now()],
                ['product_id' => $product->id, 'name' => 'Housekeeping & Maintenance',      'module_key' => null,        'description' => 'Room status tracking, task assignment, and maintenance request management',                  'price' => 5000,   'price_type' => 'recurring', 'billing_cycle' => 'monthly', 'deployment_type' => 'both', 'is_active' => true, 'sort_order' => 5, 'created_at' => now(), 'updated_at' => now()],
                ['product_id' => $product->id, 'name' => 'Events & Conference Management',  'module_key' => 'booking',   'description' => 'Venue booking, event scheduling, catering orders, and event billing',                       'price' => 10000,  'price_type' => 'recurring', 'billing_cycle' => 'monthly', 'deployment_type' => 'both', 'is_active' => true, 'sort_order' => 6, 'created_at' => now(), 'updated_at' => now()],
                // One-time addons
                ['product_id' => $product->id, 'name' => 'Mobile App (iOS & Android)',      'module_key' => null,        'description' => 'Branded guest app for bookings, service requests, and loyalty rewards',                     'price' => 120000, 'price_type' => 'onetime',   'billing_cycle' => null,      'deployment_type' => 'both', 'is_active' => true, 'sort_order' => 7, 'created_at' => now(), 'updated_at' => now()],
                ['product_id' => $product->id, 'name' => 'Staff Training (2 days on-site)', 'module_key' => null,        'description' => 'Hands-on training for front desk, kitchen, and management staff',                           'price' => 35000,  'price_type' => 'onetime',   'billing_cycle' => null,      'deployment_type' => 'both', 'is_active' => true, 'sort_order' => 8, 'created_at' => now(), 'updated_at' => now()],
            ]);
        }

        $this->command->info('Hospitality Management System seeded.');
    }

    // ─────────────────────────────────────────────────────────
    // 2. REAL ESTATE MANAGEMENT SYSTEM
    // ─────────────────────────────────────────────────────────
    private function seedRealEstate(): void
    {
        $product = Product::firstOrCreate(
            ['slug' => 'real-estate-management-system'],
            [
                'name'              => 'Real Estate Management System',
                'category_id'       => 1,
                'type'              => 'prebuilt',
                'short_description' => 'Manage properties, tenants, rent collection, and maintenance in one platform.',
                'description'       => 'Built for Nigerian property managers and landlords. Track rent payments, manage tenant agreements, handle maintenance requests, and generate financial reports — all in one place.',
                'low_price'         => 18000,
                'high_price'        => 340000,
                'features'          => [
                    'Property & unit management',
                    'Tenant records & agreements',
                    'Rent collection & receipts',
                    'Maintenance request tracking',
                    'Vacancy & occupancy reporting',
                    'Agent & staff management',
                    'Financial reports & statements',
                ],
                'is_active'  => true,
                'sort_order' => 4,
            ]
        );

        if ($product->pricingOptions()->count() === 0) {
            DB::table('product_pricing')->insert([
                ['product_id' => $product->id, 'deployment_type' => 'cloud', 'price_type' => 'monthly',   'amount' => 18000,  'is_required' => false, 'is_active' => true, 'sort_order' => 1, 'created_at' => now(), 'updated_at' => now()],
                ['product_id' => $product->id, 'deployment_type' => 'cloud', 'price_type' => 'quarterly',  'amount' => 48000,  'is_required' => false, 'is_active' => true, 'sort_order' => 2, 'created_at' => now(), 'updated_at' => now()],
                ['product_id' => $product->id, 'deployment_type' => 'cloud', 'price_type' => 'yearly',     'amount' => 170000, 'is_required' => false, 'is_active' => true, 'sort_order' => 3, 'created_at' => now(), 'updated_at' => now()],
                ['product_id' => $product->id, 'deployment_type' => 'onprem', 'price_type' => 'license', 'amount' => 280000, 'is_required' => true,  'is_active' => true, 'sort_order' => 1, 'created_at' => now(), 'updated_at' => now()],
                ['product_id' => $product->id, 'deployment_type' => 'onprem', 'price_type' => 'setup',   'amount' => 55000,  'is_required' => true,  'is_active' => true, 'sort_order' => 2, 'created_at' => now(), 'updated_at' => now()],
                ['product_id' => $product->id, 'deployment_type' => 'onprem', 'price_type' => 'monthly',  'amount' => 5500,   'is_required' => false, 'is_active' => true, 'sort_order' => 3, 'created_at' => now(), 'updated_at' => now()],
            ]);
        }

        if ($product->addons()->count() === 0) {
            DB::table('product_addons')->insert([
                ['product_id' => $product->id, 'name' => 'Tenant Self-Service Portal',      'module_key' => null,            'description' => 'Tenants can pay rent, submit requests, and view statements online',                        'price' => 8000,  'price_type' => 'recurring', 'billing_cycle' => 'monthly', 'deployment_type' => 'both', 'is_active' => true, 'sort_order' => 1, 'created_at' => now(), 'updated_at' => now()],
                ['product_id' => $product->id, 'name' => 'Online Listing Sync',             'module_key' => 'catalog',       'description' => 'Sync vacant properties to external listing platforms automatically',                         'price' => 5000,  'price_type' => 'recurring', 'billing_cycle' => 'monthly', 'deployment_type' => 'both', 'is_active' => true, 'sort_order' => 2, 'created_at' => now(), 'updated_at' => now()],
                ['product_id' => $product->id, 'name' => 'Automated Rent Reminders',        'module_key' => 'notifications', 'description' => 'SMS and WhatsApp reminders for upcoming and overdue rent payments',                         'price' => 5000,  'price_type' => 'recurring', 'billing_cycle' => 'monthly', 'deployment_type' => 'both', 'is_active' => true, 'sort_order' => 3, 'created_at' => now(), 'updated_at' => now()],
                ['product_id' => $product->id, 'name' => 'Agent & Commission Tracking',     'module_key' => 'staff',         'description' => 'Manage agents, assign properties, and auto-calculate commissions',                          'price' => 6000,  'price_type' => 'recurring', 'billing_cycle' => 'monthly', 'deployment_type' => 'both', 'is_active' => true, 'sort_order' => 4, 'created_at' => now(), 'updated_at' => now()],
                ['product_id' => $product->id, 'name' => 'Agent Mobile App',                'module_key' => null,            'description' => 'Mobile app for agents to manage listings, viewings, and leads on the go',                  'price' => 80000, 'price_type' => 'onetime',   'billing_cycle' => null,      'deployment_type' => 'both', 'is_active' => true, 'sort_order' => 5, 'created_at' => now(), 'updated_at' => now()],
                ['product_id' => $product->id, 'name' => 'Staff Training (1 day)',          'module_key' => null,            'description' => 'Onboarding and hands-on training for property managers and agents',                         'price' => 25000, 'price_type' => 'onetime',   'billing_cycle' => null,      'deployment_type' => 'both', 'is_active' => true, 'sort_order' => 6, 'created_at' => now(), 'updated_at' => now()],
            ]);
        }

        $this->command->info('Real Estate Management System seeded.');
    }

    // ─────────────────────────────────────────────────────────
    // 3. SERVICE & RETAIL BUSINESS SUITE
    //    For salons, clinics, spas, gyms, tailors, repair shops,
    //    and any appointment/booking-driven business.
    // ─────────────────────────────────────────────────────────
    private function seedServiceBusiness(): void
    {
        $product = Product::firstOrCreate(
            ['slug' => 'service-business-suite'],
            [
                'name'              => 'Service & Retail Business Suite',
                'category_id'       => 1,
                'type'              => 'prebuilt',
                'short_description' => 'Bookings, POS, inventory, and client management for service and retail businesses.',
                'description'       => 'Built for salons, clinics, gyms, spas, tailors, and any service-based business. Manage appointments, walk-ins, staff shifts, inventory, and payments — all from one dashboard.',
                'low_price'         => 12000,
                'high_price'        => 240000,
                'features'          => [
                    'Appointment & booking management',
                    'Walk-in queue management',
                    'Client records & history',
                    'Staff scheduling & attendance',
                    'Point of Sale (POS)',
                    'Inventory & stock control',
                    'Sales & revenue reports',
                ],
                'is_active'  => true,
                'sort_order' => 5,
            ]
        );

        if ($product->pricingOptions()->count() === 0) {
            DB::table('product_pricing')->insert([
                ['product_id' => $product->id, 'deployment_type' => 'cloud', 'price_type' => 'monthly',   'amount' => 12000,  'is_required' => false, 'is_active' => true, 'sort_order' => 1, 'created_at' => now(), 'updated_at' => now()],
                ['product_id' => $product->id, 'deployment_type' => 'cloud', 'price_type' => 'quarterly',  'amount' => 32000,  'is_required' => false, 'is_active' => true, 'sort_order' => 2, 'created_at' => now(), 'updated_at' => now()],
                ['product_id' => $product->id, 'deployment_type' => 'cloud', 'price_type' => 'yearly',     'amount' => 115000, 'is_required' => false, 'is_active' => true, 'sort_order' => 3, 'created_at' => now(), 'updated_at' => now()],
                ['product_id' => $product->id, 'deployment_type' => 'onprem', 'price_type' => 'license', 'amount' => 200000, 'is_required' => true,  'is_active' => true, 'sort_order' => 1, 'created_at' => now(), 'updated_at' => now()],
                ['product_id' => $product->id, 'deployment_type' => 'onprem', 'price_type' => 'setup',   'amount' => 40000,  'is_required' => true,  'is_active' => true, 'sort_order' => 2, 'created_at' => now(), 'updated_at' => now()],
                ['product_id' => $product->id, 'deployment_type' => 'onprem', 'price_type' => 'monthly',  'amount' => 4000,   'is_required' => false, 'is_active' => true, 'sort_order' => 3, 'created_at' => now(), 'updated_at' => now()],
            ]);
        }

        if ($product->addons()->count() === 0) {
            DB::table('product_addons')->insert([
                ['product_id' => $product->id, 'name' => 'Online Booking Widget',           'module_key' => 'booking',       'description' => 'Let clients book appointments from your website or a shareable link',                      'price' => 5000,   'price_type' => 'recurring', 'billing_cycle' => 'monthly', 'deployment_type' => 'both', 'is_active' => true, 'sort_order' => 1, 'created_at' => now(), 'updated_at' => now()],
                ['product_id' => $product->id, 'name' => 'SMS & WhatsApp Reminders',        'module_key' => 'notifications', 'description' => 'Automated appointment confirmations and reminders to reduce no-shows',                     'price' => 5000,   'price_type' => 'recurring', 'billing_cycle' => 'monthly', 'deployment_type' => 'both', 'is_active' => true, 'sort_order' => 2, 'created_at' => now(), 'updated_at' => now()],
                ['product_id' => $product->id, 'name' => 'Inventory & Stock Management',    'module_key' => 'inventory',     'description' => 'Track products, consumables, and supplies with low-stock alerts',                          'price' => 6000,   'price_type' => 'recurring', 'billing_cycle' => 'monthly', 'deployment_type' => 'both', 'is_active' => true, 'sort_order' => 3, 'created_at' => now(), 'updated_at' => now()],
                ['product_id' => $product->id, 'name' => 'Customer Loyalty & Rewards',      'module_key' => 'contacts',      'description' => 'Points system, loyalty cards, and referral rewards to retain clients',                     'price' => 5000,   'price_type' => 'recurring', 'billing_cycle' => 'monthly', 'deployment_type' => 'both', 'is_active' => true, 'sort_order' => 4, 'created_at' => now(), 'updated_at' => now()],
                ['product_id' => $product->id, 'name' => 'Staff Commission Tracking',       'module_key' => 'staff',         'description' => 'Track services per staff member and auto-calculate commissions',                           'price' => 4000,   'price_type' => 'recurring', 'billing_cycle' => 'monthly', 'deployment_type' => 'both', 'is_active' => true, 'sort_order' => 5, 'created_at' => now(), 'updated_at' => now()],
                ['product_id' => $product->id, 'name' => 'Branded Mobile App',              'module_key' => null,            'description' => 'Custom-branded app for your clients to book, pay, and track loyalty points',              'price' => 100000, 'price_type' => 'onetime',   'billing_cycle' => null,      'deployment_type' => 'both', 'is_active' => true, 'sort_order' => 6, 'created_at' => now(), 'updated_at' => now()],
                ['product_id' => $product->id, 'name' => 'Staff Training (1 day)',          'module_key' => null,            'description' => 'Onboarding session for your team on managing bookings, POS, and reports',                 'price' => 20000,  'price_type' => 'onetime',   'billing_cycle' => null,      'deployment_type' => 'both', 'is_active' => true, 'sort_order' => 7, 'created_at' => now(), 'updated_at' => now()],
            ]);
        }

        $this->command->info('Service & Retail Business Suite seeded.');
    }
}
