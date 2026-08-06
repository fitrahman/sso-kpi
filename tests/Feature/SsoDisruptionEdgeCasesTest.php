<?php

namespace Tests\Feature;

use App\Models\User;
use Laravel\Passport\Client;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class SsoDisruptionEdgeCasesTest extends TestCase
{
    use RefreshDatabase;

    protected function createClient(array $attributes = [])
    {
        $id = $attributes['id'] ?? 2;
        DB::table('oauth_clients')->insert(array_merge([
            'id'                     => $id,
            'name'                   => 'Aplikasi Kepegawaian Test',
            'secret'                 => 'secret_key_' . $id,
            'redirect'               => 'http://localhost:8001/callback',
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
    public function deep_linking_without_valid_session_redirects_to_login()
    {
        // Deep linking to /dashboard without session
        $responseDash = $this->get('/dashboard');
        $responseDash->assertRedirect(route('login'));

        // Deep linking to /admin/users without session
        $responseAdmin = $this->get('/admin/users');
        $responseAdmin->assertRedirect(route('login'));

        // Deep linking to gateway without session
        $responseGateway = $this->get('/sso/gateway?appName=Aplikasi Kepegawaian Test');
        $responseGateway->assertRedirect(route('login'));
    }

    /** @test */
    public function api_user_with_invalid_or_malformed_token_returns_401()
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer invalid_token_12345',
            'Accept'        => 'application/json',
        ])->getJson('/api/v1/user?client_id=2');

        $response->assertStatus(401);
    }

    /** @test */
    public function gateway_with_non_existent_app_name_redirects_with_error()
    {
        $user = User::factory()->create(['status' => 'approved']);
        $this->actingAs($user);

        $response = $this->get('/sso/gateway?appName=NonExistentApp123');
        $response->assertRedirect(route('dashboard'));
        $response->assertSessionHasErrors(['error']);
    }
}
