<?php
    use function Laravel\Folio\{name};
    name('products.school');
?>

<x-layouts.marketing
    :seo="[
        'title' => 'School Management System - ValexHub',
        'description' => 'Complete school management software with student records, fee management, attendance tracking, and detailed reporting for private schools in Nigeria.',
    ]"
>
    <x-container>
        <div class="py-16">
            <!-- Header -->
            <div class="text-center mb-16">
                <div class="flex items-center justify-center w-16 h-16 mx-auto mb-6 bg-zinc-100 rounded-xl">
                    <x-phosphor-graduation-cap class="w-8 h-8 text-zinc-600" />
                </div>
                <h1 class="text-4xl font-bold text-zinc-900 mb-4">School Management System</h1>
                <p class="text-xl text-zinc-500 max-w-3xl mx-auto">Complete digital solution for managing students, fees, attendance, and academic records with powerful reporting tools designed specifically for Nigerian private schools.</p>
            </div>

            <!-- Features Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 mb-16">
                <div class="p-6 border border-zinc-200 rounded-lg">
                    <h3 class="font-semibold text-zinc-900 mb-2">Student Management</h3>
                    <p class="text-sm text-zinc-500">Complete student profiles, registration, class assignment, and academic history tracking.</p>
                </div>
                <div class="p-6 border border-zinc-200 rounded-lg">
                    <h3 class="font-semibold text-zinc-900 mb-2">Fee Management</h3>
                    <p class="text-sm text-zinc-500">Automated fee calculation, payment tracking, receipts, and outstanding balance alerts.</p>
                </div>
                <div class="p-6 border border-zinc-200 rounded-lg">
                    <h3 class="font-semibold text-zinc-900 mb-2">Attendance Tracking</h3>
                    <p class="text-sm text-zinc-500">Daily attendance capture, absence tracking, and automated parent notifications via SMS/WhatsApp.</p>
                </div>
                <div class="p-6 border border-zinc-200 rounded-lg">
                    <h3 class="font-semibold text-zinc-900 mb-2">Results & Grading</h3>
                    <p class="text-sm text-zinc-500">Test scores, continuous assessment, term reports, and transcript generation.</p>
                </div>
                <div class="p-6 border border-zinc-200 rounded-lg">
                    <h3 class="font-semibold text-zinc-900 mb-2">Parent Portal</h3>
                    <p class="text-sm text-zinc-500">Parents can view child's attendance, results, fees, and receive school announcements.</p>
                </div>
                <div class="p-6 border border-zinc-200 rounded-lg">
                    <h3 class="font-semibold text-zinc-900 mb-2">Financial Reports</h3>
                    <p class="text-sm text-zinc-500">Revenue tracking, fee collection reports, expense management, and profit analysis.</p>
                </div>
            </div>

            <!-- Benefits Section -->
            <div class="bg-zinc-50 rounded-xl p-8 mb-16">
                <h2 class="text-2xl font-bold text-zinc-900 mb-6 text-center">Why Schools Choose Our System</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div>
                        <h3 class="font-semibold text-zinc-900 mb-2">💰 Recover Lost Revenue</h3>
                        <p class="text-sm text-zinc-500 mb-4">Reduce fee defaults by 60% with automated reminders and payment tracking.</p>
                        
                        <h3 class="font-semibold text-zinc-900 mb-2">⏱️ Save 15+ Hours Weekly</h3>
                        <p class="text-sm text-zinc-500 mb-4">Automated reports, attendance, and fee calculations reduce manual work.</p>
                    </div>
                    <div>
                        <h3 class="font-semibold text-zinc-900 mb-2">📱 Works Offline</h3>
                        <p class="text-sm text-zinc-500 mb-4">Continue operations even when internet is down. Data syncs when reconnected.</p>
                        
                        <h3 class="font-semibold text-zinc-900 mb-2">🇳🇬 Built for Nigeria</h3>
                        <p class="text-sm text-zinc-500">Supports Nigerian curriculum, Naira currency, and local payment methods.</p>
                    </div>
                </div>
            </div>

            <!-- Pricing -->
            <div class="text-center mb-16">
                <h2 class="text-2xl font-bold text-zinc-900 mb-4">Simple, Transparent Pricing</h2>
                <div class="max-w-md mx-auto p-6 border border-zinc-200 rounded-lg">
                    <h3 class="text-lg font-semibold text-zinc-900 mb-2">Complete School System</h3>
                    <div class="text-3xl font-bold text-zinc-900 mb-2">₦150,000 <span class="text-sm font-normal text-zinc-500">setup</span></div>
                    <div class="text-zinc-500 mb-4">+ ₦25,000/month hosting & support</div>
                    <ul class="text-sm text-zinc-500 text-left mb-6">
                        <li>✓ Complete installation & training</li>
                        <li>✓ Up to 1,000 students</li>
                        <li>✓ Unlimited staff accounts</li>
                        <li>✓ Parent portal access</li>
                        <li>✓ WhatsApp/SMS notifications</li>
                        <li>✓ 24/7 support</li>
                    </ul>
                </div>
            </div>

            <!-- CTA -->
            <div class="text-center">
                <h2 class="text-2xl font-bold text-zinc-900 mb-4">Ready to Transform Your School?</h2>
                <p class="text-zinc-500 mb-8 max-w-2xl mx-auto">Join 200+ Nigerian schools already using our system to improve efficiency and increase revenue.</p>
                <div class="flex flex-col gap-3 justify-center items-center md:flex-row">
                    <a href="https://wa.me/2348012345678?text=Hi%2C%20I%27d%20like%20to%20schedule%20a%20demo%20for%20the%20School%20Management%20System" target="_blank" class="px-8 py-3 text-white bg-zinc-900 rounded-md hover:bg-zinc-800">Book Free Demo</a>
                    <a href="{{ route('products') }}" class="px-8 py-3 text-zinc-700 border border-zinc-300 rounded-md hover:bg-zinc-50">View All Products</a>
                </div>
            </div>
        </div>
    </x-container>
</x-layouts.marketing>