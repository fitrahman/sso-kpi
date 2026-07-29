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
        $response = $this->getJson('/api/user?client_id=1');

        $response->assertStatus(401);
    }

    /** @test */
    public function it_returns_role_none_when_client_role_not_found()
    {
        $user = User::factory()->create();
        
        Passport::actingAs($user);

        $response = $this->getJson('/api/user?client_id=999');

        $response->assertStatus(200)
            ->assertJson([
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => 'none',
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
            'role' => 'atasan',
        ]);

        Passport::actingAs($user);

        $response = $this->getJson('/api/user?client_id=2');

        $response->assertStatus(200)
            ->assertJson([
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => 'atasan',
            ]);
    }
}
