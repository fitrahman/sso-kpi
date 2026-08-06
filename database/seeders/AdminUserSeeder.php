<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        try {
            $testUsers = [
                [
                    'name' => 'Admin User',
                    'email' => 'admin@kpi.com',
                    'role' => 'admin',
                ],
                [
                    'name' => 'Humas User',
                    'email' => 'humas@kpi.com',
                    'role' => 'Humas',
                ],
                [
                    'name' => 'Kepegawaian User',
                    'email' => 'kepegawaian@kpi.com',
                    'role' => 'Kepegawaian',
                ],
                [
                    'name' => 'Manajerial User',
                    'email' => 'manajerial@kpi.com',
                    'role' => 'Manajerial',
                ],
                [
                    'name' => 'Hukum User',
                    'email' => 'hukum@kpi.com',
                    'role' => 'Hukum',
                ],
                [
                    'name' => 'Visualisasi Data User',
                    'email' => 'visualisasi@kpi.com',
                    'role' => 'Visualisasi Data',
                ],
                [
                    'name' => 'Pengawasan Siaran User',
                    'email' => 'pengawasan@kpi.com',
                    'role' => 'Pengawasan Siaran',
                ],
            ];

            foreach ($testUsers as $u) {
                User::updateOrCreate(
                    ['email' => $u['email']],
                    [
                        'name' => $u['name'],
                        'password' => Hash::make('admin123'),
                        'role' => $u['role'],
                        'status' => 'approved',
                        'email_verified_at' => now(),
                    ]
                );
            }

            $this->command->info('✅ Test users representing all global roles seeded successfully!');

        } catch (\Exception $e) {
            $this->command->error('❌ Error creating users: '.$e->getMessage());
        }
    }
}
