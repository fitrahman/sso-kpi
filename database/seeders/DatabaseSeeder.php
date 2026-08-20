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

        $users = User::all();
    }
}
// 2. Define Client Applications
//         $clients = [
//             [
//                 'id'                      => 1,
//                 'name'                    => 'nama apk',
//                 'secret'                  => '',
//                 'redirect'                => 'http://nama-apk.test/auth/sso/callback',
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
