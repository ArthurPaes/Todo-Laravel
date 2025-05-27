<?php

namespace Tests\Feature;

use App\Models\TaskList;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskListControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_returns_task_lists_for_authenticated_user()
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        TaskList::factory()->count(2)->create(['user_id' => $user->id]);
        $response = $this->getJson('/api/task-lists');
        $response->assertOk();
        $this->assertGreaterThanOrEqual(2, count($response->json()));
    }

    public function test_store_creates_task_list()
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        $data = [
            'title' => 'Test List',
            'description' => 'Test Description',
            'is_public' => true,
        ];
        $response = $this->postJson('/api/task-lists', $data);
        $response->assertCreated()->assertJsonFragment(['title' => 'Test List']);
        $this->assertDatabaseHas('task_lists', ['title' => 'Test List']);
    }

    public function test_show_returns_task_list_if_authorized()
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        $taskList = TaskList::factory()->create(['user_id' => $user->id]);
        $response = $this->getJson('/api/task-lists/' . $taskList->id);
        $response->assertOk()->assertJsonFragment(['id' => $taskList->id]);
    }

    public function test_update_modifies_task_list_if_owner()
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        $taskList = TaskList::factory()->create(['user_id' => $user->id]);
        $data = ['title' => 'Updated List'];
        $response = $this->putJson('/api/task-lists/' . $taskList->id, $data);
        $response->assertOk()->assertJsonFragment(['title' => 'Updated List']);
        $this->assertDatabaseHas('task_lists', ['id' => $taskList->id, 'title' => 'Updated List']);
    }

    public function test_destroy_deletes_task_list_if_owner()
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        $taskList = TaskList::factory()->create(['user_id' => $user->id]);
        $response = $this->deleteJson('/api/task-lists/' . $taskList->id);
        $response->assertNoContent();
        $this->assertDatabaseMissing('task_lists', ['id' => $taskList->id]);
    }

    public function test_share_task_list_with_users()
    {
        $owner = User::factory()->create();
        $this->actingAs($owner);
        $taskList = TaskList::factory()->create(['user_id' => $owner->id]);
        $users = User::factory()->count(2)->create();
        $userIds = $users->pluck('id')->toArray();
        $response = $this->postJson('/api/task-lists/' . $taskList->id . '/share', [
            'user_ids' => $userIds
        ]);
        $response->assertOk();
        $this->assertEqualsCanonicalizing($userIds, $taskList->sharedUsers()->pluck('user_id')->toArray());
    }
} 