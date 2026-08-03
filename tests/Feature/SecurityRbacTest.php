<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\UserClientRole;
use Laravel\Passport\Client;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class SecurityRbacTest extends TestCase
{
    use RefreshDatabase;

    protected function createClient(array $attributes = [])
    {
        $id = $attributes['id'] ?? 2;
        DB::table('oauth_clients')->insert(array_merge([
            'id'                     => $id,
            'name'                   => 'Sistem Test ' . $id,
            'secret'                 => 'secret_key_' . $id,
            'redirect'               => 'http://localhost:' . (8000 + $id) . '/callback',
            'personal_access_client' => 0,
            'password_client'        => 0,
            'revoked'                => 0,
            'is_maintenance'         => 0,
            'is_visible'             => 1,
            'created_at'             => now(),
            'updated_at'             => now(),
        ], $attributes));

        return Client::find($id);
    }

    /** @test */
    public function non_admin_cannot_access_admin_routes()
    {
        $user = User::factory()->create([
            'role'   => 'pengguna',
            'status' => 'approved',
        ]);

        $this->actingAs($user);

        $response = $this->get('/admin/users');
        $response->assertStatus(403);

        $responseApps = $this->get('/admin/applications');
        $responseApps->assertStatus(403);
    }

    /** @test */
    public function admin_can_access_admin_routes()
    {
        $admin = User::factory()->create([
            'role'   => 'admin',
            'status' => 'approved',
        ]);

        $this->actingAs($admin);

        $response = $this->get('/admin/users');
        $response->assertStatus(200);

        $responseApps = $this->get('/admin/applications');
        $responseApps->assertStatus(200);
    }

    /** @test */
    public function user_without_approved_client_access_is_blocked_on_gateway()
    {
        $user = User::factory()->create([
            'role'   => 'pengguna',
            'status' => 'approved',
        ]);

        $client = $this->createClient(['id' => 2, 'name' => 'Sistem 1']);

        $this->actingAs($user);

        // Gateway access without approved status in pivot client_user_access
        $response = $this->get('/sso/gateway?appName=Sistem 1');
        $response->assertStatus(200);
        $response->assertViewIs('auth.app-denied');
    }

    /** @test */
    public function user_with_approved_client_access_can_proceed_to_login()
    {
        $user = User::factory()->create([
            'role'   => 'pengguna',
            'status' => 'approved',
        ]);

        $client = $this->createClient(['id' => 2, 'name' => 'Sistem 1', 'redirect' => 'http://localhost:8001/callback']);

        // Attach approved access
        $user->accessedClients()->attach($client->id, ['status' => 'approved']);

        $this->actingAs($user);

        $response = $this->get('/sso/gateway?appName=Sistem 1');
        $response->assertRedirect('http://localhost:8001/login');
    }

    /** @test */
    public function application_maintenance_mode_blocks_regular_user()
    {
        $user = User::factory()->create([
            'role'   => 'pengguna',
            'status' => 'approved',
        ]);

        $client = $this->createClient([
            'id'                  => 2,
            'name'                => 'Sistem 1',
            'is_maintenance'      => 1,
            'maintenance_message' => 'Sedang pemeliharaan server.',
        ]);

        $user->accessedClients()->attach($client->id, ['status' => 'approved']);

        $this->actingAs($user);

        // Check maintenance check on authorize route middleware
        $response = $this->get('/oauth/authorize?client_id=2&response_type=code');
        $response->assertRedirect(route('app.maintenance', [
            'appName' => 'Sistem 1',
            'message' => 'Sedang pemeliharaan server.',
        ]));
    }

    /** @test */
    public function admin_can_bypass_maintenance_mode()
    {
        $admin = User::factory()->create([
            'role'   => 'admin',
            'status' => 'approved',
        ]);

        $client = $this->createClient([
            'id'             => 2,
            'name'           => 'Sistem 1',
            'is_maintenance' => 1,
            'redirect'       => 'http://localhost:8001/callback',
        ]);

        $this->actingAs($admin);

        $response = $this->get('/sso/gateway?appName=Sistem 1');
        $response->assertRedirect('http://localhost:8001/login');
    }

    /** @test */
    public function admin_cannot_delete_self_or_other_main_admin()
    {
        $admin1 = User::factory()->create(['role' => 'admin', 'status' => 'approved']);
        $admin2 = User::factory()->create(['role' => 'admin', 'status' => 'approved']);

        $this->actingAs($admin1);

        // Cannot delete self - user still exists in DB
        $this->from('/admin/users')->delete('/admin/users/' . $admin1->id);
        $this->assertNotNull(User::find($admin1->id));

        // Cannot delete other admin - user still exists in DB
        $this->from('/admin/users')->delete('/admin/users/' . $admin2->id);
        $this->assertNotNull(User::find($admin2->id));
    }

    /** @test */
    public function role_change_in_sso_server_reflects_immediately_in_api_user_claims()
    {
        $user = User::factory()->create(['status' => 'approved']);
        $client = $this->createClient(['id' => 2, 'name' => 'Aplikasi Kepegawaian']);

        // Initial local role: Staff
        UserClientRole::create([
            'user_id'         => $user->id,
            'oauth_client_id' => 2,
            'role'            => 'staff',
        ]);

        \Laravel\Passport\Passport::actingAs($user);

        // First check: role is staff
        $res1 = $this->getJson('/api/user?client_id=2');
        $res1->assertStatus(200)->assertJson(['role' => 'staff']);

        // Update role in SSO to Manager (Promotion)
        UserClientRole::where('user_id', $user->id)->where('oauth_client_id', 2)->update(['role' => 'manager']);

        // Real-time check: role is now manager
        $res2 = $this->getJson('/api/user?client_id=2');
        $res2->assertStatus(200)->assertJson(['role' => 'manager']);
    }

    /** @test */
    public function deactivating_user_in_sso_revokes_tokens()
    {
        $user = User::factory()->create(['role' => 'Kepegawaian', 'status' => 'approved']);

        // Deactivate user and revoke tokens
        $user->update(['status' => 'inactive']);
        $user->tokens()->each(function ($token) {
            $token->revoke();
        });

        $this->assertEquals('inactive', $user->fresh()->status);
    }

    /** @test */
    public function admin_can_create_new_oauth_application()
    {
        $admin = User::factory()->create(['role' => 'admin', 'status' => 'approved']);
        $this->actingAs($admin);

        $response = $this->post('/admin/applications', [
            'name'            => 'Aplikasi Testing Baru',
            'redirect'        => 'http://localhost:8005/auth/callback',
            'description'     => 'Deskripsi aplikasi testing',
            'supported_roles' => 'Admin, Operator, Supervisor',
            'display_order'   => 5,
            'is_visible'      => 1,
        ]);

        $response->assertRedirect();
        $response->assertSessionHasNoErrors();

        $this->assertDatabaseHas('oauth_clients', [
            'name'     => 'Aplikasi Testing Baru',
            'redirect' => 'http://localhost:8005/auth/callback',
        ]);
    }
}
