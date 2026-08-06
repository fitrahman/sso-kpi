<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\UserClientRole;
use Laravel\Passport\Passport;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class UserApiTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_returns_unauthorized_when_not_logged_in()
    {
        $response = $this->getJson('/api/v1/user?client_id=1');

        $response->assertStatus(401);
    }

    /** @test */
    public function it_returns_role_none_when_client_role_not_found()
    {
        $user = User::factory()->create(['role' => 'pengguna']);
        
        Passport::actingAs($user);

        $response = $this->getJson('/api/v1/user?client_id=999');

        $response->assertStatus(200)
            ->assertJson([
                'id'    => $user->id,
                'name'  => $user->name,
                'email' => $user->email,
                'role'  => 'pengguna',
            ]);
    }

    /** @test */
    public function it_returns_correct_role_when_client_role_is_found()
    {
        $user = User::factory()->create();
        
        // Insert client manually to bypass any missing factory errors
        DB::table('oauth_clients')->insert([
            'id' => 2,
            'name' => 'Sistem 1',
            'secret' => 'secret_key_123',
            'redirect' => 'http://localhost',
            'personal_access_client' => 0,
            'password_client' => 0,
            'revoked' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Create the role
        UserClientRole::create([
            'user_id' => $user->id,
            'oauth_client_id' => 2,
            'role' => 'pengguna',
        ]);

        Passport::actingAs($user);

        $response = $this->getJson('/api/v1/user?client_id=2');

        $response->assertStatus(200)
            ->assertJson([
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => 'pengguna',
            ]);
    }

    /** @test */
    public function it_returns_role_admin_for_global_admin_users_bypassing_database_lookup()
    {
        $admin = User::factory()->create([
            'role' => 'admin'
        ]);

        Passport::actingAs($admin);

        $response = $this->getJson('/api/v1/user?client_id=2');

        $response->assertStatus(200)
            ->assertJson([
                'id' => $admin->id,
                'name' => $admin->name,
                'email' => $admin->email,
                'role' => 'admin',
            ]);
    }

    /** @test */
    public function global_admin_user_with_assigned_local_role_returns_assigned_local_role()
    {
        $admin = User::factory()->create(['role' => 'admin']);

        DB::table('oauth_clients')->insert([
            'id'                     => 4,
            'name'                   => 'SIMPEG KPI',
            'secret'                 => 'secret',
            'redirect'               => 'http://localhost',
            'personal_access_client' => 0,
            'password_client'        => 0,
            'revoked'                => 0,
            'created_at'             => now(),
            'updated_at'             => now(),
        ]);

        UserClientRole::create([
            'user_id'         => $admin->id,
            'oauth_client_id' => 4,
            'role'            => 'atasan',
        ]);

        Passport::actingAs($admin);

        $response = $this->getJson('/api/v1/user?client_id=4');

        $response->assertStatus(200)
            ->assertJson([
                'id'    => $admin->id,
                'name'  => $admin->name,
                'email' => $admin->email,
                'role'  => 'atasan',
            ]);
    }

    /** @test */
    public function authenticated_user_can_get_and_sync_client_supported_roles()
    {
        $user = User::factory()->create();
        Passport::actingAs($user);

        DB::table('oauth_clients')->insert([
            'id'                     => 10,
            'name'                   => 'Testing App',
            'secret'                 => 'secret',
            'redirect'               => 'http://localhost',
            'personal_access_client' => 0,
            'password_client'        => 0,
            'revoked'                => 0,
            'supported_roles'        => json_encode(['Admin', 'Staff']),
            'created_at'             => now(),
            'updated_at'             => now(),
        ]);

        // Test GET /api/v1/client-roles
        $getRes = $this->getJson('/api/v1/client-roles?client_id=10');
        $getRes->assertStatus(200)
            ->assertJson([
                'success'         => true,
                'client_id'       => 10,
                'supported_roles' => ['Admin', 'Staff'],
            ]);

        // Test POST /api/v1/client-roles/sync
        $postRes = $this->postJson('/api/v1/client-roles/sync', [
            'client_id'     => 10,
            'client_secret' => 'secret',
            'roles'         => ['Admin', 'Supervisor', 'Operator', 'Staff'],
        ]);

        $postRes->assertStatus(200)
            ->assertJson([
                'success'         => true,
                'client_id'       => 10,
                'supported_roles' => ['Admin', 'Supervisor', 'Operator', 'Staff'],
            ]);
    }
}
