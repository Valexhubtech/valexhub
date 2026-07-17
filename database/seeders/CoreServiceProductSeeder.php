<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Wave\Product;

/**
 * Wipes all existing products, pricing and add-ons, then seeds a single
 * test product backed by the Docker image published to GitHub Container Registry.
 *
 * Module keys come directly from the core's MODULE_KEYS list:
 *   auth, org, catalog, contacts, inventory, pos, invoicing, dashboard,
 *   notifications, audit_log, platform, staff, booking, digital_products, video_courses
 */
class CoreServiceProductSeeder extends Seeder
{
    public function run(): void
    {
        // ── Wipe existing products (cascades to pricing + addons) ────────────
        DB::table('order_addons')->delete();
        DB::table('product_addons')->delete();
        DB::table('product_pricing')->delete();
        DB::table('products')->delete();

        $this->command->info('Cleared all existing products, pricing and add-ons.');

        // ── Seed the one test product ────────────────────────────────────────
        $product = Product::create([
            'name'               => 'ValexHub Core Platform',
            'slug'               => 'core-platform',
            'category_id'        => null,
            'type'               => 'prebuilt',
            'short_description'  => 'The full ValexHub business platform — deploy the modules your business needs.',
            'description'        => '<p>ValexHub Core Platform is a modular business management system. Select only the modules your business needs — POS, inventory, bookings, invoicing, staff management, and more — and deploy your own private instance in minutes.</p>',
            'low_price'          => 15000,
            'high_price'         => 145000,
            'features'           => [
                'Modular — enable only what you need',
                'Private instance on your own subdomain',
                'Booking & appointment management',
                'Point of Sale (POS)',
                'Inventory & stock control',
                'Staff & HR management',
                'Invoicing & payments',
                'Customer & contacts management',
                'SMS & WhatsApp notifications',
                'Analytics dashboard',
            ],
            'coolify_deploy_type'   => 'docker_image',
            'coolify_docker_image'  => 'ghcr.io/valexhub-dev-s/core-service:latest',
            'is_active'             => true,
            'sort_order'            => 1,
        ]);

        // ── Cloud pricing ────────────────────────────────────────────────────
        DB::table('product_pricing')->insert([
            ['product_id' => $product->id, 'deployment_type' => 'cloud', 'price_type' => 'monthly',   'amount' => 15000,  'is_required' => false, 'is_active' => true, 'sort_order' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['product_id' => $product->id, 'deployment_type' => 'cloud', 'price_type' => 'quarterly', 'amount' => 40000,  'is_required' => false, 'is_active' => true, 'sort_order' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['product_id' => $product->id, 'deployment_type' => 'cloud', 'price_type' => 'yearly',    'amount' => 145000, 'is_required' => false, 'is_active' => true, 'sort_order' => 3, 'created_at' => now(), 'updated_at' => now()],
        ]);

        // ── Add-ons — one per MODULE_KEY from the core ───────────────────────
        // Keys: auth, org, catalog, contacts, inventory, pos, invoicing,
        //       dashboard, notifications, audit_log, platform, staff,
        //       booking, digital_products, video_courses
        $addons = [
            ['module_key' => 'auth',             'name' => 'Authentication & User Management',  'description' => 'Multi-user login, roles, permissions, and session management.',                             'price' => 0,     'price_type' => 'recurring', 'billing_cycle' => 'monthly', 'sort_order' => 1],
            ['module_key' => 'org',              'name' => 'Organisation Management',           'description' => 'Organisation profile, branding, and team structure.',                                       'price' => 0,     'price_type' => 'recurring', 'billing_cycle' => 'monthly', 'sort_order' => 2],
            ['module_key' => 'platform',         'name' => 'Platform & System Settings',        'description' => 'Core system configuration, integrations, and platform-level controls.',                    'price' => 0,     'price_type' => 'recurring', 'billing_cycle' => 'monthly', 'sort_order' => 3],
            ['module_key' => 'dashboard',        'name' => 'Analytics Dashboard',               'description' => 'Revenue, sales, and performance reports across your business.',                            'price' => 3000,  'price_type' => 'recurring', 'billing_cycle' => 'monthly', 'sort_order' => 4],
            ['module_key' => 'contacts',         'name' => 'Customers & Contacts',              'description' => 'Customer profiles, purchase history, loyalty tracking, and communication logs.',           'price' => 3000,  'price_type' => 'recurring', 'billing_cycle' => 'monthly', 'sort_order' => 5],
            ['module_key' => 'catalog',          'name' => 'Product Catalogue',                 'description' => 'Manage your products, services, categories, and pricing lists.',                          'price' => 3000,  'price_type' => 'recurring', 'billing_cycle' => 'monthly', 'sort_order' => 6],
            ['module_key' => 'inventory',        'name' => 'Inventory & Stock Management',      'description' => 'Real-time stock tracking, low-stock alerts, supplier orders, and multi-location support.', 'price' => 5000,  'price_type' => 'recurring', 'billing_cycle' => 'monthly', 'sort_order' => 7],
            ['module_key' => 'pos',              'name' => 'Point of Sale (POS)',               'description' => 'Fast checkout, cash and card payments, receipts, and end-of-day reconciliation.',         'price' => 5000,  'price_type' => 'recurring', 'billing_cycle' => 'monthly', 'sort_order' => 8],
            ['module_key' => 'invoicing',        'name' => 'Invoicing & Payments',              'description' => 'Create and send invoices, track payments, and accept Paystack online payments.',           'price' => 4000,  'price_type' => 'recurring', 'billing_cycle' => 'monthly', 'sort_order' => 9],
            ['module_key' => 'staff',            'name' => 'Staff & HR Management',             'description' => 'Staff profiles, attendance, schedules, commissions, and payroll.',                        'price' => 5000,  'price_type' => 'recurring', 'billing_cycle' => 'monthly', 'sort_order' => 10],
            ['module_key' => 'booking',          'name' => 'Booking & Appointments',            'description' => 'Online and walk-in booking, availability calendar, and automated reminders.',             'price' => 5000,  'price_type' => 'recurring', 'billing_cycle' => 'monthly', 'sort_order' => 11],
            ['module_key' => 'notifications',    'name' => 'SMS & WhatsApp Notifications',      'description' => 'Automated appointment reminders, payment confirmations, and promotional messages.',        'price' => 4000,  'price_type' => 'recurring', 'billing_cycle' => 'monthly', 'sort_order' => 12],
            ['module_key' => 'audit_log',        'name' => 'Audit Log & Activity History',      'description' => 'Full audit trail of user actions, changes, and system events.',                           'price' => 2000,  'price_type' => 'recurring', 'billing_cycle' => 'monthly', 'sort_order' => 13],
            ['module_key' => 'digital_products', 'name' => 'Digital Products & Downloads',      'description' => 'Sell e-books, templates, and digital files with secure download delivery.',               'price' => 4000,  'price_type' => 'recurring', 'billing_cycle' => 'monthly', 'sort_order' => 14],
            ['module_key' => 'video_courses',    'name' => 'Video Courses & E-Learning',        'description' => 'Host and sell online courses with video lessons, quizzes, and student progress tracking.', 'price' => 5000,  'price_type' => 'recurring', 'billing_cycle' => 'monthly', 'sort_order' => 15],
        ];

        foreach ($addons as $addon) {
            DB::table('product_addons')->insert([
                'product_id'     => $product->id,
                'name'           => $addon['name'],
                'module_key'     => $addon['module_key'],
                'description'    => $addon['description'],
                'price'          => $addon['price'],
                'price_type'     => $addon['price_type'],
                'billing_cycle'  => $addon['billing_cycle'],
                'deployment_type'=> 'cloud',
                'is_active'      => true,
                'sort_order'     => $addon['sort_order'],
                'created_at'     => now(),
                'updated_at'     => now(),
            ]);
        }

        $this->command->info('ValexHub Core Platform seeded with ' . count($addons) . ' module add-ons.');
        $this->command->info('Docker image: ghcr.io/valexhub-dev-s/core-service:latest');
    }
}
