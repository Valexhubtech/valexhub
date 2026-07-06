<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Wave\Category;
use Wave\Product;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $education   = Category::firstOrCreate(['slug' => 'education'],   ['name' => 'Education',        'order' => 3]);
        $hospitality = Category::firstOrCreate(['slug' => 'hospitality'],  ['name' => 'Hospitality',      'order' => 4]);
        $realEstate  = Category::firstOrCreate(['slug' => 'real-estate'],  ['name' => 'Real Estate',      'order' => 5]);
        $hrFinance   = Category::firstOrCreate(['slug' => 'hr-finance'],   ['name' => 'HR & Finance',     'order' => 6]);
        $retail      = Category::firstOrCreate(['slug' => 'retail'],       ['name' => 'Retail & Logistics', 'order' => 7]);

        $this->seedSchoolManagement($education->id);
        $this->seedHotelManagement($hospitality->id);
        $this->seedPropertyManagement($realEstate->id);
        $this->seedPayrollHR($hrFinance->id);
        $this->seedInventoryPOS($retail->id);
    }

    private function seedSchoolManagement(int $categoryId): void
    {
        $product = Product::updateOrCreate(
            ['slug' => 'school-management-system'],
            [
                'name'              => 'School Management System',
                'category_id'       => $categoryId,
                'type'              => 'prebuilt',
                'short_description' => 'A complete academic management platform for primary, secondary, and tertiary institutions.',
                'description'       => '<p>ValexHub School Management System is built to handle the full operational lifecycle of a Nigerian educational institution — from student enrollment and fee collection to timetabling, attendance, grading, and parent communication.</p><p>Whether you run a nursery school, secondary school, or a polytechnic, this platform eliminates paperwork and puts every piece of academic data at your fingertips. Parents get real-time access to their children\'s progress, and your admin staff spends less time on repetitive tasks.</p><p>All data is stored securely in the cloud with automatic backups. Staff can work from any device, and the system scales as your institution grows.</p>',
                'low_price'         => 80000,
                'high_price'        => 500000,
                'features'          => [
                    'Student enrollment & records management',
                    'School fee collection & receipt generation',
                    'Attendance tracking (teachers & students)',
                    'Subject & class timetable management',
                    'Continuous Assessment & exam grading',
                    'Auto-generated report cards & transcripts',
                    'Parent & guardian communication portal',
                    'Staff records & payroll management',
                    'Library management module',
                    'SMS & email notifications',
                ],
                'is_active'  => true,
                'sort_order' => 10,
                'seo_title'       => 'School Management System Nigeria — ValexHub',
                'seo_description' => 'A complete school management software for Nigerian institutions. Manage students, fees, attendance, grades, timetables, and parent communication in one platform.',
                'seo_keywords'    => 'school management system Nigeria, school software, student records, fee collection software, academic management',
            ]
        );

        if ($product->wasRecentlyCreated || $product->pricingOptions()->count() === 0) {
            DB::table('product_pricing')->where('product_id', $product->id)->delete();
            DB::table('product_pricing')->insert([
                ['product_id' => $product->id, 'deployment_type' => 'cloud',  'price_type' => 'monthly',   'amount' => 25000,  'is_required' => false, 'is_active' => true, 'sort_order' => 1, 'created_at' => now(), 'updated_at' => now()],
                ['product_id' => $product->id, 'deployment_type' => 'cloud',  'price_type' => 'quarterly', 'amount' => 65000,  'is_required' => false, 'is_active' => true, 'sort_order' => 2, 'created_at' => now(), 'updated_at' => now()],
                ['product_id' => $product->id, 'deployment_type' => 'cloud',  'price_type' => 'yearly',    'amount' => 230000, 'is_required' => false, 'is_active' => true, 'sort_order' => 3, 'created_at' => now(), 'updated_at' => now()],
                ['product_id' => $product->id, 'deployment_type' => 'onprem', 'price_type' => 'license',   'amount' => 400000, 'is_required' => true,  'is_active' => true, 'sort_order' => 1, 'created_at' => now(), 'updated_at' => now()],
                ['product_id' => $product->id, 'deployment_type' => 'onprem', 'price_type' => 'setup',     'amount' => 80000,  'is_required' => true,  'is_active' => true, 'sort_order' => 2, 'created_at' => now(), 'updated_at' => now()],
                ['product_id' => $product->id, 'deployment_type' => 'onprem', 'price_type' => 'monthly',   'amount' => 8000,   'is_required' => false, 'is_active' => true, 'sort_order' => 3, 'created_at' => now(), 'updated_at' => now()],
            ]);
        }

        if ($product->wasRecentlyCreated || $product->addons()->count() === 0) {
            DB::table('product_addons')->where('product_id', $product->id)->delete();
            DB::table('product_addons')->insert([
                ['product_id' => $product->id, 'name' => 'Parent & Student Mobile App',   'description' => 'Branded iOS & Android app for parents to track attendance, fees, and results in real time',            'price' => 120000, 'price_type' => 'onetime',   'billing_cycle' => null,      'deployment_type' => 'both', 'is_active' => true, 'sort_order' => 1, 'created_at' => now(), 'updated_at' => now()],
                ['product_id' => $product->id, 'name' => 'SMS & WhatsApp Notifications',  'description' => 'Automated alerts for school fees, results, attendance, and announcements',                              'price' => 8000,   'price_type' => 'recurring', 'billing_cycle' => 'monthly', 'deployment_type' => 'both', 'is_active' => true, 'sort_order' => 2, 'created_at' => now(), 'updated_at' => now()],
                ['product_id' => $product->id, 'name' => 'Online Fee Payment Gateway',    'description' => 'Allow parents to pay school fees directly via Paystack or bank transfer from the parent portal',        'price' => 10000,  'price_type' => 'recurring', 'billing_cycle' => 'monthly', 'deployment_type' => 'both', 'is_active' => true, 'sort_order' => 3, 'created_at' => now(), 'updated_at' => now()],
                ['product_id' => $product->id, 'name' => 'CBT Exam Module',               'description' => 'Computer-Based Testing (CBT) for internal exams and assessments with auto-grading',                    'price' => 40000,  'price_type' => 'onetime',   'billing_cycle' => null,      'deployment_type' => 'both', 'is_active' => true, 'sort_order' => 4, 'created_at' => now(), 'updated_at' => now()],
                ['product_id' => $product->id, 'name' => 'Library Management Module',     'description' => 'Book catalog, borrowing records, fines, and student library cards',                                     'price' => 25000,  'price_type' => 'onetime',   'billing_cycle' => null,      'deployment_type' => 'both', 'is_active' => true, 'sort_order' => 5, 'created_at' => now(), 'updated_at' => now()],
                ['product_id' => $product->id, 'name' => 'Staff Training (2 days)',       'description' => 'Comprehensive on-site training for admin staff, teachers, and management',                              'price' => 40000,  'price_type' => 'onetime',   'billing_cycle' => null,      'deployment_type' => 'both', 'is_active' => true, 'sort_order' => 6, 'created_at' => now(), 'updated_at' => now()],
            ]);
        }

        $this->command->info('School Management System seeded.');
    }

    private function seedHotelManagement(int $categoryId): void
    {
        $product = Product::updateOrCreate(
            ['slug' => 'hotel-management-system'],
            [
                'name'              => 'Hotel Management System',
                'category_id'       => $categoryId,
                'type'              => 'prebuilt',
                'short_description' => 'End-to-end hotel operations software for Nigerian hotels, lodges, and guest houses.',
                'description'       => '<p>ValexHub Hotel Management System gives your property a fully digital front desk — from the moment a guest makes a reservation to the moment they check out. Manage room availability, housekeeping schedules, restaurant orders, billing, and staff, all from one unified platform.</p><p>Designed specifically for Nigerian hospitality businesses, the system supports Naira billing, local payment methods including Paystack, and operates reliably even during internet disruptions. Whether you run a boutique guest house or a multi-floor hotel, this software scales to match your operation.</p><p>Eliminate manual room booking conflicts, lost receipts, and inefficient housekeeping workflows. Your front desk team will have everything they need on one screen.</p>',
                'low_price'         => 100000,
                'high_price'        => 600000,
                'features'          => [
                    'Room booking & reservation management',
                    'Front desk check-in & check-out',
                    'Room availability & occupancy calendar',
                    'Housekeeping & room status tracking',
                    'Restaurant & bar POS integration',
                    'Guest billing & receipt generation',
                    'Naira billing with Paystack integration',
                    'Staff management & shift scheduling',
                    'Inventory & procurement tracking',
                    'Occupancy & revenue reporting',
                ],
                'is_active'  => true,
                'sort_order' => 20,
                'seo_title'       => 'Hotel Management System Nigeria — ValexHub',
                'seo_description' => 'Complete hotel management software for Nigerian hotels, guest houses, and lodges. Manage reservations, housekeeping, billing, and restaurant POS in one platform.',
                'seo_keywords'    => 'hotel management system Nigeria, hotel software, reservation system, guest house management, hospitality software Nigeria',
            ]
        );

        if ($product->wasRecentlyCreated || $product->pricingOptions()->count() === 0) {
            DB::table('product_pricing')->where('product_id', $product->id)->delete();
            DB::table('product_pricing')->insert([
                ['product_id' => $product->id, 'deployment_type' => 'cloud',  'price_type' => 'monthly',   'amount' => 30000,  'is_required' => false, 'is_active' => true, 'sort_order' => 1, 'created_at' => now(), 'updated_at' => now()],
                ['product_id' => $product->id, 'deployment_type' => 'cloud',  'price_type' => 'quarterly', 'amount' => 80000,  'is_required' => false, 'is_active' => true, 'sort_order' => 2, 'created_at' => now(), 'updated_at' => now()],
                ['product_id' => $product->id, 'deployment_type' => 'cloud',  'price_type' => 'yearly',    'amount' => 290000, 'is_required' => false, 'is_active' => true, 'sort_order' => 3, 'created_at' => now(), 'updated_at' => now()],
                ['product_id' => $product->id, 'deployment_type' => 'onprem', 'price_type' => 'license',   'amount' => 500000, 'is_required' => true,  'is_active' => true, 'sort_order' => 1, 'created_at' => now(), 'updated_at' => now()],
                ['product_id' => $product->id, 'deployment_type' => 'onprem', 'price_type' => 'setup',     'amount' => 90000,  'is_required' => true,  'is_active' => true, 'sort_order' => 2, 'created_at' => now(), 'updated_at' => now()],
                ['product_id' => $product->id, 'deployment_type' => 'onprem', 'price_type' => 'monthly',   'amount' => 10000,  'is_required' => false, 'is_active' => true, 'sort_order' => 3, 'created_at' => now(), 'updated_at' => now()],
            ]);
        }

        if ($product->wasRecentlyCreated || $product->addons()->count() === 0) {
            DB::table('product_addons')->where('product_id', $product->id)->delete();
            DB::table('product_addons')->insert([
                ['product_id' => $product->id, 'name' => 'Online Booking Engine',          'description' => 'Let guests book rooms directly from your website with real-time availability',                          'price' => 15000,  'price_type' => 'recurring', 'billing_cycle' => 'monthly', 'deployment_type' => 'both', 'is_active' => true, 'sort_order' => 1, 'created_at' => now(), 'updated_at' => now()],
                ['product_id' => $product->id, 'name' => 'Restaurant & Bar POS',           'description' => 'Table management, order tracking, kitchen display, and integrated billing with room accounts',          'price' => 12000,  'price_type' => 'recurring', 'billing_cycle' => 'monthly', 'deployment_type' => 'both', 'is_active' => true, 'sort_order' => 2, 'created_at' => now(), 'updated_at' => now()],
                ['product_id' => $product->id, 'name' => 'Guest Self-Service Portal',      'description' => 'Digital check-in, service requests, and room-to-guest messaging',                                       'price' => 8000,   'price_type' => 'recurring', 'billing_cycle' => 'monthly', 'deployment_type' => 'both', 'is_active' => true, 'sort_order' => 3, 'created_at' => now(), 'updated_at' => now()],
                ['product_id' => $product->id, 'name' => 'Events & Conference Booking',    'description' => 'Manage hall bookings, corporate events, catering orders, and event billing',                            'price' => 10000,  'price_type' => 'recurring', 'billing_cycle' => 'monthly', 'deployment_type' => 'both', 'is_active' => true, 'sort_order' => 4, 'created_at' => now(), 'updated_at' => now()],
                ['product_id' => $product->id, 'name' => 'Branded Guest Mobile App',       'description' => 'Custom iOS & Android app for your guests to book, pay, and request services',                          'price' => 150000, 'price_type' => 'onetime',   'billing_cycle' => null,      'deployment_type' => 'both', 'is_active' => true, 'sort_order' => 5, 'created_at' => now(), 'updated_at' => now()],
                ['product_id' => $product->id, 'name' => 'Staff Training (2 days)',        'description' => 'On-site training for front desk, housekeeping, and management staff',                                   'price' => 40000,  'price_type' => 'onetime',   'billing_cycle' => null,      'deployment_type' => 'both', 'is_active' => true, 'sort_order' => 6, 'created_at' => now(), 'updated_at' => now()],
            ]);
        }

        $this->command->info('Hotel Management System seeded.');
    }

    private function seedPropertyManagement(int $categoryId): void
    {
        $product = Product::updateOrCreate(
            ['slug' => 'property-management-system'],
            [
                'name'              => 'Property Management System',
                'category_id'       => $categoryId,
                'type'              => 'prebuilt',
                'short_description' => 'Manage rental properties, tenants, rent collection, and maintenance from one dashboard.',
                'description'       => '<p>ValexHub Property Management System is the all-in-one platform for Nigerian landlords, property managers, and real estate agencies. Stop managing tenants from WhatsApp and spreadsheets — bring everything into a system that was built for how property business works in Nigeria.</p><p>Track every property and unit, manage tenant agreements and renewals, collect rent with automated reminders via WhatsApp and SMS, and handle maintenance requests with a structured workflow. Generate owner statements, track arrears, and produce financial reports that make tax season and investor reporting effortless.</p><p>From a single residential property to a portfolio of commercial buildings, this system grows with you.</p>',
                'low_price'         => 60000,
                'high_price'        => 400000,
                'features'          => [
                    'Property & unit portfolio management',
                    'Tenant records & lease agreement tracking',
                    'Automated rent collection & receipts',
                    'WhatsApp & SMS rent reminders',
                    'Maintenance request & vendor management',
                    'Vacancy tracking & listing management',
                    'Agent & commission management',
                    'Owner financial statements',
                    'Arrears & overdue rent reporting',
                    'Document storage (leases, IDs, receipts)',
                ],
                'is_active'  => true,
                'sort_order' => 30,
                'seo_title'       => 'Property Management System Nigeria — ValexHub',
                'seo_description' => 'Property management software for Nigerian landlords and agents. Track tenants, collect rent, manage maintenance, and generate financial reports — all in one place.',
                'seo_keywords'    => 'property management system Nigeria, landlord software, tenant management, rent collection software, real estate software Nigeria',
            ]
        );

        if ($product->wasRecentlyCreated || $product->pricingOptions()->count() === 0) {
            DB::table('product_pricing')->where('product_id', $product->id)->delete();
            DB::table('product_pricing')->insert([
                ['product_id' => $product->id, 'deployment_type' => 'cloud',  'price_type' => 'monthly',   'amount' => 18000,  'is_required' => false, 'is_active' => true, 'sort_order' => 1, 'created_at' => now(), 'updated_at' => now()],
                ['product_id' => $product->id, 'deployment_type' => 'cloud',  'price_type' => 'quarterly', 'amount' => 48000,  'is_required' => false, 'is_active' => true, 'sort_order' => 2, 'created_at' => now(), 'updated_at' => now()],
                ['product_id' => $product->id, 'deployment_type' => 'cloud',  'price_type' => 'yearly',    'amount' => 170000, 'is_required' => false, 'is_active' => true, 'sort_order' => 3, 'created_at' => now(), 'updated_at' => now()],
                ['product_id' => $product->id, 'deployment_type' => 'onprem', 'price_type' => 'license',   'amount' => 320000, 'is_required' => true,  'is_active' => true, 'sort_order' => 1, 'created_at' => now(), 'updated_at' => now()],
                ['product_id' => $product->id, 'deployment_type' => 'onprem', 'price_type' => 'setup',     'amount' => 60000,  'is_required' => true,  'is_active' => true, 'sort_order' => 2, 'created_at' => now(), 'updated_at' => now()],
                ['product_id' => $product->id, 'deployment_type' => 'onprem', 'price_type' => 'monthly',   'amount' => 6000,   'is_required' => false, 'is_active' => true, 'sort_order' => 3, 'created_at' => now(), 'updated_at' => now()],
            ]);
        }

        if ($product->wasRecentlyCreated || $product->addons()->count() === 0) {
            DB::table('product_addons')->where('product_id', $product->id)->delete();
            DB::table('product_addons')->insert([
                ['product_id' => $product->id, 'name' => 'Tenant Self-Service Portal',    'description' => 'Tenants can pay rent, view their lease, raise maintenance requests, and download receipts online',     'price' => 8000,   'price_type' => 'recurring', 'billing_cycle' => 'monthly', 'deployment_type' => 'both', 'is_active' => true, 'sort_order' => 1, 'created_at' => now(), 'updated_at' => now()],
                ['product_id' => $product->id, 'name' => 'Automated Rent Reminders',      'description' => 'SMS and WhatsApp messages sent automatically before and after rent due dates',                           'price' => 5000,   'price_type' => 'recurring', 'billing_cycle' => 'monthly', 'deployment_type' => 'both', 'is_active' => true, 'sort_order' => 2, 'created_at' => now(), 'updated_at' => now()],
                ['product_id' => $product->id, 'name' => 'Online Rent Collection',        'description' => 'Accept rent payments via Paystack directly into your property account with auto-reconciliation',         'price' => 7000,   'price_type' => 'recurring', 'billing_cycle' => 'monthly', 'deployment_type' => 'both', 'is_active' => true, 'sort_order' => 3, 'created_at' => now(), 'updated_at' => now()],
                ['product_id' => $product->id, 'name' => 'Agent & Commission Tracking',   'description' => 'Assign agents to properties, track viewings, and auto-calculate commissions on each lease signed',      'price' => 6000,   'price_type' => 'recurring', 'billing_cycle' => 'monthly', 'deployment_type' => 'both', 'is_active' => true, 'sort_order' => 4, 'created_at' => now(), 'updated_at' => now()],
                ['product_id' => $product->id, 'name' => 'Property Listing Website',      'description' => 'A branded property listing site that syncs your vacancies in real time',                                'price' => 80000,  'price_type' => 'onetime',   'billing_cycle' => null,      'deployment_type' => 'both', 'is_active' => true, 'sort_order' => 5, 'created_at' => now(), 'updated_at' => now()],
                ['product_id' => $product->id, 'name' => 'Staff Training (1 day)',        'description' => 'Hands-on training for property managers, agents, and admin staff',                                       'price' => 25000,  'price_type' => 'onetime',   'billing_cycle' => null,      'deployment_type' => 'both', 'is_active' => true, 'sort_order' => 6, 'created_at' => now(), 'updated_at' => now()],
            ]);
        }

        $this->command->info('Property Management System seeded.');
    }

    private function seedPayrollHR(int $categoryId): void
    {
        $product = Product::updateOrCreate(
            ['slug' => 'payroll-hr-management'],
            [
                'name'              => 'Payroll & HR Management',
                'category_id'       => $categoryId,
                'type'              => 'prebuilt',
                'short_description' => 'End-to-end HR and payroll solution built for Nigerian businesses and tax compliance.',
                'description'       => '<p>ValexHub Payroll & HR Management handles everything from employee onboarding to monthly salary processing — fully compliant with Nigerian tax regulations including PAYE, pension (PFA), NHF, and NSITF deductions.</p><p>Store all employee records digitally, track attendance and leave balances, manage performance reviews, and generate payslips at the click of a button. The system calculates gross pay, statutory deductions, and net pay automatically — removing the risk of manual errors that can cost you in audits and employee disputes.</p><p>Whether you manage 10 or 1,000 employees, this system ensures your HR process is professional, accurate, and audit-ready at all times.</p>',
                'low_price'         => 50000,
                'high_price'        => 350000,
                'features'          => [
                    'Employee records & digital HR files',
                    'Monthly payroll processing & payslips',
                    'PAYE, pension (PFA), NHF & NSITF calculations',
                    'Attendance tracking (clock-in / clock-out)',
                    'Leave management & approval workflow',
                    'Performance review & appraisal module',
                    'Bank transfer file generation',
                    'Staff loan & advance tracking',
                    'Disciplinary & grievance records',
                    'HR analytics & workforce reporting',
                ],
                'is_active'  => true,
                'sort_order' => 40,
                'seo_title'       => 'Payroll & HR Management Software Nigeria — ValexHub',
                'seo_description' => 'Nigerian payroll and HR management software with PAYE, pension, NHF, and NSITF compliance. Process salaries, track attendance, and manage your workforce digitally.',
                'seo_keywords'    => 'payroll software Nigeria, HR management system Nigeria, PAYE calculation Nigeria, employee management software, salary processing Nigeria',
            ]
        );

        if ($product->wasRecentlyCreated || $product->pricingOptions()->count() === 0) {
            DB::table('product_pricing')->where('product_id', $product->id)->delete();
            DB::table('product_pricing')->insert([
                ['product_id' => $product->id, 'deployment_type' => 'cloud',  'price_type' => 'monthly',   'amount' => 20000,  'is_required' => false, 'is_active' => true, 'sort_order' => 1, 'created_at' => now(), 'updated_at' => now()],
                ['product_id' => $product->id, 'deployment_type' => 'cloud',  'price_type' => 'quarterly', 'amount' => 54000,  'is_required' => false, 'is_active' => true, 'sort_order' => 2, 'created_at' => now(), 'updated_at' => now()],
                ['product_id' => $product->id, 'deployment_type' => 'cloud',  'price_type' => 'yearly',    'amount' => 195000, 'is_required' => false, 'is_active' => true, 'sort_order' => 3, 'created_at' => now(), 'updated_at' => now()],
                ['product_id' => $product->id, 'deployment_type' => 'onprem', 'price_type' => 'license',   'amount' => 280000, 'is_required' => true,  'is_active' => true, 'sort_order' => 1, 'created_at' => now(), 'updated_at' => now()],
                ['product_id' => $product->id, 'deployment_type' => 'onprem', 'price_type' => 'setup',     'amount' => 55000,  'is_required' => true,  'is_active' => true, 'sort_order' => 2, 'created_at' => now(), 'updated_at' => now()],
                ['product_id' => $product->id, 'deployment_type' => 'onprem', 'price_type' => 'monthly',   'amount' => 7000,   'is_required' => false, 'is_active' => true, 'sort_order' => 3, 'created_at' => now(), 'updated_at' => now()],
            ]);
        }

        if ($product->wasRecentlyCreated || $product->addons()->count() === 0) {
            DB::table('product_addons')->where('product_id', $product->id)->delete();
            DB::table('product_addons')->insert([
                ['product_id' => $product->id, 'name' => 'Biometric Attendance Integration', 'description' => 'Sync fingerprint or face recognition attendance devices with the HR system automatically',             'price' => 35000,  'price_type' => 'onetime',   'billing_cycle' => null,      'deployment_type' => 'both', 'is_active' => true, 'sort_order' => 1, 'created_at' => now(), 'updated_at' => now()],
                ['product_id' => $product->id, 'name' => 'Employee Self-Service Portal',     'description' => 'Employees can view payslips, apply for leave, and update their records without involving HR',           'price' => 8000,   'price_type' => 'recurring', 'billing_cycle' => 'monthly', 'deployment_type' => 'both', 'is_active' => true, 'sort_order' => 2, 'created_at' => now(), 'updated_at' => now()],
                ['product_id' => $product->id, 'name' => 'Training & Development Module',    'description' => 'Track employee training, certifications, and development plans',                                        'price' => 6000,   'price_type' => 'recurring', 'billing_cycle' => 'monthly', 'deployment_type' => 'both', 'is_active' => true, 'sort_order' => 3, 'created_at' => now(), 'updated_at' => now()],
                ['product_id' => $product->id, 'name' => 'Recruitment & Onboarding Module',  'description' => 'Manage job applications, interviews, offers, and digital onboarding for new hires',                    'price' => 8000,   'price_type' => 'recurring', 'billing_cycle' => 'monthly', 'deployment_type' => 'both', 'is_active' => true, 'sort_order' => 4, 'created_at' => now(), 'updated_at' => now()],
                ['product_id' => $product->id, 'name' => 'Staff Training (1 day)',           'description' => 'On-site payroll and HR system training for your finance and HR teams',                                  'price' => 25000,  'price_type' => 'onetime',   'billing_cycle' => null,      'deployment_type' => 'both', 'is_active' => true, 'sort_order' => 5, 'created_at' => now(), 'updated_at' => now()],
            ]);
        }

        $this->command->info('Payroll & HR Management seeded.');
    }

    private function seedInventoryPOS(int $categoryId): void
    {
        $product = Product::updateOrCreate(
            ['slug' => 'inventory-pos-system'],
            [
                'name'              => 'Inventory & Point of Sale',
                'category_id'       => $categoryId,
                'type'              => 'prebuilt',
                'short_description' => 'Fast, reliable POS and stock management for Nigerian retail stores and multi-branch businesses.',
                'description'       => '<p>ValexHub Inventory & Point of Sale is built for the pace of Nigerian retail. Process sales quickly at the counter, track stock across multiple branches, manage suppliers, and get the financial reports you need to make smart buying decisions.</p><p>The POS works offline during internet outages and syncs automatically when connectivity is restored — so your business never stops. Scan barcodes, apply discounts, manage multiple payment methods (cash, POS card, bank transfer), and print receipts instantly.</p><p>From a single shop to a chain of stores, this system gives you full visibility into what you\'re selling, what\'s running low, and where your money is going.</p>',
                'low_price'         => 40000,
                'high_price'        => 300000,
                'features'          => [
                    'Fast POS checkout with barcode scanning',
                    'Multi-payment: cash, card, bank transfer',
                    'Offline mode (syncs when back online)',
                    'Real-time stock tracking & alerts',
                    'Multi-branch & multi-warehouse support',
                    'Supplier management & purchase orders',
                    'Sales, profit & inventory reporting',
                    'Customer records & purchase history',
                    'Discount, promo & coupon management',
                    'User roles & cashier access control',
                ],
                'is_active'  => true,
                'sort_order' => 50,
                'seo_title'       => 'Inventory & POS System Nigeria — ValexHub',
                'seo_description' => 'Point of sale and inventory management software for Nigerian retail businesses. Track stock, process sales, manage suppliers, and run reports across multiple branches.',
                'seo_keywords'    => 'POS system Nigeria, inventory management Nigeria, point of sale software Nigeria, retail software Nigeria, stock management system',
            ]
        );

        if ($product->wasRecentlyCreated || $product->pricingOptions()->count() === 0) {
            DB::table('product_pricing')->where('product_id', $product->id)->delete();
            DB::table('product_pricing')->insert([
                ['product_id' => $product->id, 'deployment_type' => 'cloud',  'price_type' => 'monthly',   'amount' => 15000,  'is_required' => false, 'is_active' => true, 'sort_order' => 1, 'created_at' => now(), 'updated_at' => now()],
                ['product_id' => $product->id, 'deployment_type' => 'cloud',  'price_type' => 'quarterly', 'amount' => 40000,  'is_required' => false, 'is_active' => true, 'sort_order' => 2, 'created_at' => now(), 'updated_at' => now()],
                ['product_id' => $product->id, 'deployment_type' => 'cloud',  'price_type' => 'yearly',    'amount' => 145000, 'is_required' => false, 'is_active' => true, 'sort_order' => 3, 'created_at' => now(), 'updated_at' => now()],
                ['product_id' => $product->id, 'deployment_type' => 'onprem', 'price_type' => 'license',   'amount' => 240000, 'is_required' => true,  'is_active' => true, 'sort_order' => 1, 'created_at' => now(), 'updated_at' => now()],
                ['product_id' => $product->id, 'deployment_type' => 'onprem', 'price_type' => 'setup',     'amount' => 50000,  'is_required' => true,  'is_active' => true, 'sort_order' => 2, 'created_at' => now(), 'updated_at' => now()],
                ['product_id' => $product->id, 'deployment_type' => 'onprem', 'price_type' => 'monthly',   'amount' => 5000,   'is_required' => false, 'is_active' => true, 'sort_order' => 3, 'created_at' => now(), 'updated_at' => now()],
            ]);
        }

        if ($product->wasRecentlyCreated || $product->addons()->count() === 0) {
            DB::table('product_addons')->where('product_id', $product->id)->delete();
            DB::table('product_addons')->insert([
                ['product_id' => $product->id, 'name' => 'E-commerce Integration',          'description' => 'Sync your in-store inventory with your online store — orders and stock update in real time',          'price' => 12000,  'price_type' => 'recurring', 'billing_cycle' => 'monthly', 'deployment_type' => 'both', 'is_active' => true, 'sort_order' => 1, 'created_at' => now(), 'updated_at' => now()],
                ['product_id' => $product->id, 'name' => 'Customer Loyalty Program',        'description' => 'Points and reward system to drive repeat purchases and customer retention',                             'price' => 6000,   'price_type' => 'recurring', 'billing_cycle' => 'monthly', 'deployment_type' => 'both', 'is_active' => true, 'sort_order' => 2, 'created_at' => now(), 'updated_at' => now()],
                ['product_id' => $product->id, 'name' => 'WhatsApp Receipt & Promotions',   'description' => 'Send digital receipts and promotional offers to customers via WhatsApp after each purchase',           'price' => 5000,   'price_type' => 'recurring', 'billing_cycle' => 'monthly', 'deployment_type' => 'both', 'is_active' => true, 'sort_order' => 3, 'created_at' => now(), 'updated_at' => now()],
                ['product_id' => $product->id, 'name' => 'Advanced Analytics Dashboard',    'description' => 'Deep sales analytics: top products, slow movers, profit margins, and peak hours by branch',           'price' => 8000,   'price_type' => 'recurring', 'billing_cycle' => 'monthly', 'deployment_type' => 'both', 'is_active' => true, 'sort_order' => 4, 'created_at' => now(), 'updated_at' => now()],
                ['product_id' => $product->id, 'name' => 'Barcode Label Printer Setup',     'description' => 'Hardware configuration and label template setup for your product barcodes',                            'price' => 30000,  'price_type' => 'onetime',   'billing_cycle' => null,      'deployment_type' => 'both', 'is_active' => true, 'sort_order' => 5, 'created_at' => now(), 'updated_at' => now()],
                ['product_id' => $product->id, 'name' => 'Staff Training (1 day)',          'description' => 'On-site POS and inventory training for cashiers, stock managers, and supervisors',                     'price' => 20000,  'price_type' => 'onetime',   'billing_cycle' => null,      'deployment_type' => 'both', 'is_active' => true, 'sort_order' => 6, 'created_at' => now(), 'updated_at' => now()],
            ]);
        }

        $this->command->info('Inventory & Point of Sale seeded.');
    }
}
