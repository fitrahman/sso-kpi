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
        DB::table('client_user_access')->truncate();
        DB::table('user_client_roles')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // 1. Panggil seeder pengguna (Admin & Staff)
        $this->call(AdminUserSeeder::class);

        // 2. Buat Client Sistem 1 (ID 2)
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

        // 3. Buat Client Sistem 2 (ID 3)
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

        // 4. Buat Client Sistem Go (ID 4)
        DB::table('oauth_clients')->insert([
            'id' => 4,
            'user_id' => null,
            'name' => 'Sistem Go',
            'secret' => 'sistemgo_secret_key_123',
            'provider' => null,
            'redirect' => 'http://sistemgo.test/auth/sso/callback',
            'personal_access_client' => 0,
            'password_client' => 0,
            'revoked' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 5. Buat Klien Sistem Kepegawaian / Sistem 3 (ID 5)
        DB::table('oauth_clients')->insert([
            'id' => 5,
            'user_id' => null,
            'name' => 'Sistem 3',
            'secret' => 'sistem3_secret_key_123',
            'provider' => null,
            'redirect' => 'http://sistem3.test/auth/sso/callback',
            'personal_access_client' => 0,
            'password_client' => 0,
            'revoked' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 6. Buat Data Dummy Hak Akses & Peran Klien
        $humas = \App\Models\User::where('email', 'humas@kpi.com')->first();
        $kepegawaian = \App\Models\User::where('email', 'kepegawaian@kpi.com')->first();
        $manajerial = \App\Models\User::where('email', 'manajerial@kpi.com')->first();

        // Humas: Akses ke Sistem 1 (Atasan) & Sistem 2 (Pegawai)
        if ($humas) {
            $humas->accessedClients()->attach(2, ['status' => 'approved']);
            \App\Models\UserClientRole::create(['user_id' => $humas->id, 'oauth_client_id' => 2, 'role' => 'atasan']);
            
            $humas->accessedClients()->attach(3, ['status' => 'approved']);
            \App\Models\UserClientRole::create(['user_id' => $humas->id, 'oauth_client_id' => 3, 'role' => 'pegawai']);
        }

        // Kepegawaian: Akses ke Sistem 1 (Pegawai), Sistem 2 (Atasan), & Sistem 3 / Sistem Kepegawaian (Admin)
        if ($kepegawaian) {
            $kepegawaian->accessedClients()->attach(2, ['status' => 'approved']);
            \App\Models\UserClientRole::create(['user_id' => $kepegawaian->id, 'oauth_client_id' => 2, 'role' => 'pegawai']);

            $kepegawaian->accessedClients()->attach(3, ['status' => 'approved']);
            \App\Models\UserClientRole::create(['user_id' => $kepegawaian->id, 'oauth_client_id' => 3, 'role' => 'atasan']);

            $kepegawaian->accessedClients()->attach(5, ['status' => 'approved']);
            \App\Models\UserClientRole::create(['user_id' => $kepegawaian->id, 'oauth_client_id' => 5, 'role' => 'admin']);
        }

        // Manajerial: Akses ke semua Sistem (Pegawai)
        if ($manajerial) {
            foreach ([2, 3, 4, 5] as $cId) {
                $manajerial->accessedClients()->attach($cId, ['status' => 'approved']);
                \App\Models\UserClientRole::create(['user_id' => $manajerial->id, 'oauth_client_id' => $cId, 'role' => 'pegawai']);
            }
        }
    }
}
