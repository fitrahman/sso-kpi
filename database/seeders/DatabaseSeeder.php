<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\UserClientRole;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database cleanly.
     */
    public function run(): void
    {
        // 1. Run User Seeder
        $this->call(AdminUserSeeder::class);
        $this->call(PegawaiSSOUserSeeder::class);

        $users = User::all();
    }
}
// 2. Define Client Applications
//         $clients = [
//             [
//                 'id'                      => 3,
//                 'name'                    => 'Sistem 1',
//                 'secret'                  => 'CpOjCpepT7NnLBKXgsnjvVXKCpNRyW2QO5nLbaDn',
//                 'redirect'                => 'http://sistem1.test/auth/sso/callback',
//                 'personal_access_client' => 0,
//                 'password_client'        => 0,
//                 'revoked'                => 0,
//                 'is_maintenance'         => 0,
//                 'is_visible'             => 1,
//                 'description'            => 'Aplikasi Layanan Informasi & Internal Sistem 1',
//                 'display_order'          => 1,
//                 'supported_roles'        => json_encode(['Admin', 'Staff', 'pengguna']),
//                 'created_at'             => now(),
//                 'updated_at'             => now(),
//             ],
//             [
//                 'id'                      => 4,
//                 'name'                    => 'Sistem 2',
//                 'secret'                  => 'Sistem2SecretKey998877665544332211',
//                 'redirect'                => 'http://sistem2.test/auth/sso/callback',
//                 'personal_access_client' => 0,
//                 'password_client'        => 0,
//                 'revoked'                => 0,
//                 'is_maintenance'         => 0,
//                 'is_visible'             => 1,
//                 'description'            => 'Aplikasi Layanan Operasional Sistem 2',
//                 'display_order'          => 2,
//                 'supported_roles'        => json_encode(['Admin', 'Operator', 'pengguna']),
//                 'created_at'             => now(),
//                 'updated_at'             => now(),
//             ],
//             [
//                 'id'                      => 5,
//                 'name'                    => 'Sistem 3',
//                 'secret'                  => 'Sistem3SecretKey112233445566778899',
//                 'redirect'                => 'http://sistem3.test/auth/sso/callback',
//                 'personal_access_client' => 0,
//                 'password_client'        => 0,
//                 'revoked'                => 0,
//                 'is_maintenance'         => 0,
//                 'is_visible'             => 1,
//                 'description'            => 'Aplikasi Layanan Pengaduan & Informasi',
//                 'display_order'          => 3,
//                 'supported_roles'        => json_encode(['Admin', 'Supervisor', 'Operator']),
//                 'created_at'             => now(),
//                 'updated_at'             => now(),
//             ],
//             [
//                 'id'                      => 6,
//                 'name'                    => 'Go Login Web',
//                 'secret'                  => 'Fe2YUzKlSzJZypUeMuryi6zJmzyOU8JNurYkCTyk',
//                 'redirect'                => 'http://localhost:8080/callback',
//                 'personal_access_client' => 0,
//                 'password_client'        => 0,
//                 'revoked'                => 0,
//                 'is_maintenance'         => 0,
//                 'is_visible'             => 1,
//                 'description'            => 'Aplikasi Otentikasi Berbasis Golang',
//                 'display_order'          => 4,
//                 'supported_roles'        => json_encode(['Admin', 'User']),
//                 'created_at'             => now(),
//                 'updated_at'             => now(),
//             ],
//             [
//                 'id'                      => 7,
//                 'name'                    => 'SIMPEG KPI',
//                 'secret'                  => 'SimpegKpiSecretKey998877665544332211',
//                 'redirect'                => 'http://simpeg-kpi-web-main.test/auth/sso/callback',
//                 'personal_access_client' => 0,
//                 'password_client'        => 0,
//                 'revoked'                => 0,
//                 'is_maintenance'         => 0,
//                 'is_visible'             => 1,
//                 'description'            => 'Sistem Informasi Manajemen Kepegawaian KPI',
//                 'display_order'          => 5,
//                 'supported_roles'        => json_encode(['admin', 'atasan', 'pegawai']),
//                 'created_at'             => now(),
//                 'updated_at'             => now(),
//             ],
//         ];

//         foreach ($clients as $clientData) {
//             DB::table('oauth_clients')->updateOrInsert(
//                 ['id' => $clientData['id']],
//                 $clientData
//             );
//         }

//         // 3. Grant Client Access to All Users & Assign Roles
//         foreach ($users as $u) {
//             foreach ([3, 4, 5, 6, 7] as $clientId) {
//                 DB::table('client_user_access')->updateOrInsert(
//                     ['user_id' => $u->id, 'client_id' => $clientId],
//                     [
//                         'status'     => 'approved',
//                         'created_at' => now(),
//                         'updated_at' => now(),
//                     ]
//                 );

//                 // Assign Local Client Role
//                 $role = 'pengguna';
//                 if ($u->role === 'admin') {
//                     $role = ($clientId === 7) ? 'admin' : 'Admin';
//                 } elseif ($u->role === 'Kepegawaian') {
//                     $role = ($clientId === 7) ? 'admin' : 'Admin';
//                 } elseif ($u->role === 'Manajerial') {
//                     $role = ($clientId === 7) ? 'atasan' : 'Supervisor';
//                 }

//                 UserClientRole::updateOrCreate(
//                     ['user_id' => $u->id, 'oauth_client_id' => $clientId],
//                     ['role' => $role]
//                 );
//             }
//         }

//         $this->command->info('✅ Clean Database Seeding Completed Successfully!');
//     }
// }
