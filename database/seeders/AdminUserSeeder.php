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
