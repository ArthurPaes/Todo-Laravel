<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class UserControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_returns_users()
    {
        User::factory()->count(3)->create();
        $response = $this->getJson('/api/users');
        $response->assertOk()->assertJsonCount(3);
    }

    public function test_store_creates_user()
    {
        $data = [
            'name' => 'Test User',
            'email' => 'test' . Str::random(5) . '@example.com',
            'password' => 'password123',
        ];
        $response = $this->postJson('/api/users', $data);
        $response->assertCreated()->assertJsonFragment(['name' => 'Test User']);
        $this->assertDatabaseHas('users', ['email' => $data['email']]);
    }

    public function test_show_returns_user()
    {
        $user = User::factory()->create();
        $response = $this->getJson('/api/users/' . $user->id);
        $response->assertOk()->assertJsonFragment(['id' => $user->id]);
    }

    public function test_update_modifies_user()
    {
        $user = User::factory()->create();
        $data = ['name' => 'Updated Name'];
        $response = $this->putJson('/api/users/' . $user->id, $data);
        $response->assertOk()->assertJsonFragment(['name' => 'Updated Name']);
        $this->assertDatabaseHas('users', ['id' => $user->id, 'name' => 'Updated Name']);
    }

    public function test_destroy_deletes_user()
    {
        $user = User::factory()->create();
        $response = $this->deleteJson('/api/users/' . $user->id);
        $response->assertNoContent();
        $this->assertDatabaseMissing('users', ['id' => $user->id]);
    }
} 