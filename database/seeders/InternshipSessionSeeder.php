<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Wave\InternshipSession;

class InternshipSessionSeeder extends Seeder
{
    public function run(): void
    {
        InternshipSession::updateOrCreate(
            ['slug' => '2026-internship-program'],
            [
                'name'                 => '2026 Internship Program',
                'slug'                 => '2026-internship-program',
                'description'          => 'Join ValexHub as an intern and gain hands-on experience building real software that powers Nigerian businesses. We offer structured mentorship, live project exposure, and a direct path to full employment for outstanding interns.',
                'roles'                => [
                    'Frontend Developer',
                    'Backend Developer',
                    'UI/UX Designer',
                    'Business Development',
                    'Digital Marketing',
                    'Product Management',
                    'Technical Support',
                    'Data & Analytics',
                ],
                'application_deadline' => '2026-10-31',
                'is_active'            => true,
            ]
        );

        $this->command->info('Internship session seeded: 2026 Internship Program');
    }
}
