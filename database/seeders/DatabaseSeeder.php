<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Nonaktifkan foreign key checks untuk melakukan truncate tabel
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('oauth_clients')->truncate();
        DB::table('oauth_personal_access_clients')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // 1. Panggil seeder pengguna (Admin & Staff)
        $this->call(AdminUserSeeder::class);

        // 2. Buat Password Grant Client (ID 1)
        DB::table('oauth_clients')->insert([
            'id' => 1,
            'user_id' => null,
            'name' => 'Password Grant Client',
            'secret' => Str::random(40),
            'provider' => 'users',
            'redirect' => 'http://localhost',
            'personal_access_client' => 0,
            'password_client' => 1,
            'revoked' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 3. Buat Client Sistem 1 (ID 2) - Harus ID 2 agar cocok dengan Sistem 1 .env
        DB::table('oauth_clients')->insert([
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
        ]);

        // 4. Buat Client Sistem 2 (ID 3) - Harus ID 3 agar cocok dengan Sistem 2 .env
        DB::table('oauth_clients')->insert([
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
        ]);

        // 5. Buat Personal Access Client (ID 4)
        DB::table('oauth_clients')->insert([
            'id' => 4,
            'user_id' => null,
            'name' => 'Personal Access Client',
            'secret' => Str::random(40),
            'provider' => 'users',
            'redirect' => 'http://localhost',
            'personal_access_client' => 1,
            'password_client' => 0,
            'revoked' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('oauth_personal_access_clients')->insert([
            'client_id' => 4,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 6. Buat Auth Code PKCE Client (ID 5)
        DB::table('oauth_clients')->insert([
            'id' => 5,
            'user_id' => null,
            'name' => 'Auth Code PKCE Client',
            'secret' => null,
            'provider' => 'users',
            'redirect' => 'http://localhost/oauth/callback',
            'personal_access_client' => 0,
            'password_client' => 0,
            'revoked' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
