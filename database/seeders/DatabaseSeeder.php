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
        // 1. Run AdminUserSeeder to create test accounts
        $this->call(AdminUserSeeder::class);

        // 2. Seed OAuth Clients
        DB::table('oauth_clients')->insert([
            [
                'id' => 1,
                'user_id' => null,
                'name' => 'Go Client',
                'secret' => 'L239JIbr2tFWDDfNHxpfEQfeuh1aLSt56FkUdwTm',
                'provider' => null,
                'redirect' => 'http://localhost:8080/callback',
                'personal_access_client' => 0,
                'password_client' => 0,
                'revoked' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ],
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
