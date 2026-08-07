<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Wave\Category;
use Wave\Product;

/**
 * Seeds the ValexHub Hospitality Platform product.
 * Deploys the core-hospitality Docker image — a separate vertical from the core-service app.
 * Safe to re-run on production (same upsert strategy as CoreServiceProductSeeder).
 */
class CoreHospitalityProductSeeder extends Seeder
{
    public function run(): void
    {
        $category = Category::firstOrCreate(
            ['slug' => 'cloud-platform'],
            ['name' => 'Cloud Platform', 'order' => 1]
        );

        $product = Product::updateOrCreate(
            ['slug' => 'hospitality-platform'],
            [
                'name'              => 'ValexHub Hospitality Platform',
                'slug'              => 'hospitality-platform',
                'category_id'       => $category->id,
                'type'              => 'prebuilt',
                'short_description' => 'A complete hospitality management platform for hotels, guest houses, and restaurants.',
                'description'       => '<p>ValexHub Hospitality Platform gives your property a fully private, cloud-hosted management system built specifically for the hospitality industry. Manage room reservations, restaurant and bar POS, F&B inventory, guest billing, staff, and housekeeping — all in one place. Night audit reports and revenue dashboards are built in. Your instance runs on its own subdomain and is ready in minutes.</p>',
                'low_price'         => 20000,
                'high_price'        => 195000,
                'features'          => [
                    'Private cloud instance on your own subdomain',
                    'Room reservation & availability management',
                    'Restaurant & bar point of sale',
                    'Night audit & revenue dashboard',
                    'Guest billing & invoicing',
                    'Housekeeping & staff management',
                    'F&B inventory & procurement',
                    'SMS & WhatsApp guest notifications',
                    'Modular — activate only what you need',
                ],
                'coolify_deploy_type'  => 'docker_image',
                'coolify_docker_image' => 'ghcr.io/valexhub-dev-s/core-hospitality:latest',
                'login_path'           => '/login',
                'is_active'            => true,
                'sort_order'           => 2,
            ]
        );

        $this->command->info("Upserted product '{$product->name}' (ID {$product->id}).");

        // Pricing — safe to wipe and re-insert (no customer FK on product_pricing)
        DB::table('product_pricing')->where('product_id', $product->id)->delete();
        DB::table('product_pricing')->insert([
            ['product_id' => $product->id, 'deployment_type' => 'cloud', 'price_type' => 'monthly',   'amount' => 20000,  'is_required' => false, 'is_active' => true, 'sort_order' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['product_id' => $product->id, 'deployment_type' => 'cloud', 'price_type' => 'quarterly', 'amount' => 54000,  'is_required' => false, 'is_active' => true, 'sort_order' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['product_id' => $product->id, 'deployment_type' => 'cloud', 'price_type' => 'yearly',    'amount' => 195000, 'is_required' => false, 'is_active' => true, 'sort_order' => 3, 'created_at' => now(), 'updated_at' => now()],
        ]);

        // Addons — upsert by module_key (never deletes — order_addons cascade FK)
        // Keys match packages/config/src/modules.ts exactly.
        $addons = [
            // ── Core (always included, free) ────────────────────────────────────
            ['module_key' => 'auth',          'name' => 'Authentication & User Management',  'description' => 'Multi-user login, roles, permissions, and session management.',                               'price' => 0,     'sort_order' => 1],
            ['module_key' => 'org',           'name' => 'Organisation Management',           'description' => 'Property profile, branding, departments, and team structure.',                                'price' => 0,     'sort_order' => 2],
            ['module_key' => 'platform',      'name' => 'Platform & System Settings',        'description' => 'Core system configuration, integrations, and platform-level controls.',                      'price' => 0,     'sort_order' => 3],
            ['module_key' => 'location',      'name' => 'Location Management',              'description' => 'Manage multiple property locations or branches from a single account.',                        'price' => 0,     'sort_order' => 4],
            // ── Shared operational modules ───────────────────────────────────────
            ['module_key' => 'dashboard',     'name' => 'Analytics & Revenue Dashboard',    'description' => 'Live revenue reports, occupancy rates, ADR, and performance metrics.',                        'price' => 3000,  'sort_order' => 5],
            ['module_key' => 'contacts',      'name' => 'Guest & Contact Management',       'description' => 'Guest profiles, stay history, preferences, and communication logs.',                          'price' => 3000,  'sort_order' => 6],
            ['module_key' => 'invoicing',     'name' => 'Billing & Invoicing',              'description' => 'Create and send guest invoices, track payments, and accept Paystack online payments.',        'price' => 4000,  'sort_order' => 7],
            ['module_key' => 'inventory',     'name' => 'F&B Inventory & Procurement',      'description' => 'Stock control, supplier orders, low-stock alerts, and cost tracking for food and beverage.',  'price' => 7000,  'sort_order' => 8],
            ['module_key' => 'staff',         'name' => 'Staff Management',                 'description' => 'Staff profiles, shift scheduling, attendance tracking, and commissions.',                     'price' => 5000,  'sort_order' => 9],
            ['module_key' => 'notifications', 'name' => 'SMS & WhatsApp Guest Notifications','description' => 'Automated booking confirmations, check-in reminders, and billing alerts for guests.',        'price' => 4000,  'sort_order' => 10],
            ['module_key' => 'audit_log',     'name' => 'Audit Log & Activity History',     'description' => 'Full audit trail of user actions, record changes, and system events.',                       'price' => 2000,  'sort_order' => 11],
            // ── Hospitality-specific modules ─────────────────────────────────────
            ['module_key' => 'rooms',         'name' => 'Room Management',                  'description' => 'Room setup, types, bed configurations, amenities, and room status board.',                    'price' => 5000,  'sort_order' => 12],
            ['module_key' => 'reservations',  'name' => 'Reservations',                     'description' => 'Full reservation engine, availability calendar, group bookings, and folio assignment.',       'price' => 10000, 'sort_order' => 13],
            ['module_key' => 'front_desk',    'name' => 'Front Desk',                       'description' => 'Check-in, check-out, room assignment, walk-ins, and guest services at the front desk.',      'price' => 8000,  'sort_order' => 14],
            ['module_key' => 'folio',         'name' => 'Guest Folio',                      'description' => 'Itemised guest billing folio — post charges, split bills, and settle accounts on checkout.',  'price' => 5000,  'sort_order' => 15],
            ['module_key' => 'night_audit',   'name' => 'Night Audit',                      'description' => 'End-of-day audit run — posts room charges, closes the day, and generates revenue reports.',   'price' => 4000,  'sort_order' => 16],
            ['module_key' => 'housekeeping',  'name' => 'Housekeeping',                     'description' => 'Room cleaning task assignment, status tracking, and maintenance request management.',          'price' => 4000,  'sort_order' => 17],
            ['module_key' => 'restaurant',    'name' => 'Restaurant & Bar',                 'description' => 'Table management, orders, kitchen display system, and charge-to-room for F&B operations.',   'price' => 8000,  'sort_order' => 18],
            // ── Content / Marketing ──────────────────────────────────────────────
            ['module_key' => 'gallery',       'name' => 'Property Media Gallery',           'description' => 'Showcase your rooms, facilities, and amenities with a rich photo gallery.',                   'price' => 2000,  'sort_order' => 19],
            ['module_key' => 'blog',          'name' => 'Blog & Content Publishing',        'description' => 'Publish news, event announcements, and updates directly from your dashboard.',                'price' => 2000,  'sort_order' => 20],
        ];

        foreach ($addons as $addon) {
            $exists = DB::table('product_addons')
                ->where('product_id', $product->id)
                ->where('module_key', $addon['module_key'])
                ->exists();

            if ($exists) {
                DB::table('product_addons')
                    ->where('product_id', $product->id)
                    ->where('module_key', $addon['module_key'])
                    ->update([
                        'name'        => $addon['name'],
                        'description' => $addon['description'],
                        'price'       => $addon['price'],
                        'sort_order'  => $addon['sort_order'],
                        'updated_at'  => now(),
                    ]);
            } else {
                DB::table('product_addons')->insert([
                    'product_id'      => $product->id,
                    'name'            => $addon['name'],
                    'module_key'      => $addon['module_key'],
                    'description'     => $addon['description'],
                    'price'           => $addon['price'],
                    'price_type'      => 'recurring',
                    'billing_cycle'   => 'monthly',
                    'deployment_type' => 'cloud',
                    'is_active'       => true,
                    'sort_order'      => $addon['sort_order'],
                    'created_at'      => now(),
                    'updated_at'      => now(),
                ]);
            }
        }

        $this->command->info('Addons upserted ('.count($addons).' entries).');
        $this->command->info('Docker image: ghcr.io/valexhub-dev-s/core-hospitality:latest');
    }
}
