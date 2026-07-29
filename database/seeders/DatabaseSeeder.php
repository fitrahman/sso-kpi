<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Panggil seeder bawaan untuk User dan Passport
        $this->call([
            AdminUserSeeder::class,
            PassportClientSeeder::class,
        ]);

        // Seed data Client untuk Sistem 1 dan Sistem 2 secara default
        DB::table('oauth_clients')->insertOrIgnore([
            [
                'id' => 2,
                'user_id' => null,
                'name' => 'Sistem 1',
                'secret' => 'FFkmMi3JaJ1UlHABGXGqxQZg0KKyHRT0oqrREFG2',
                'provider' => null,
                'redirect' => 'http://sistem1.test/auth/sso/callback',
                'personal_access_client' => 0,
                'password_client' => 0,
                'revoked' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 3,
                'user_id' => null,
                'name' => 'Sistem 2',
                'secret' => 'sistem2_secret_key_123',
                'provider' => null,
                'redirect' => 'http://sistem2.test/auth/sso/callback',
                'personal_access_client' => 0,
                'password_client' => 0,
                'revoked' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }
}
