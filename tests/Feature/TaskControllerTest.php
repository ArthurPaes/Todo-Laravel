<?php

namespace Tests\Feature;

use App\Models\Task;
use App\Models\User;
use App\Models\TaskList;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_returns_tasks()
    {
        Task::factory()->count(2)->create();
        $response = $this->getJson('/api/tasks');
        $response->assertOk()->assertJsonCount(2);
    }

    public function test_store_creates_task()
    {
        $user = User::factory()->create();
        $taskList = TaskList::factory()->create(['user_id' => $user->id]);
        $data = [
            'title' => 'Test Task',
            'description' => 'Test Description',
            'status' => 'pending',
            'task_list_id' => $taskList->id,
            'user_id' => $user->id,
        ];
        $response = $this->postJson('/api/tasks', $data);
        $response->assertCreated()->assertJsonFragment(['title' => 'Test Task']);
        $this->assertDatabaseHas('tasks', ['title' => 'Test Task']);
    }

    public function test_show_returns_task()
    {
        $task = Task::factory()->create();
        $response = $this->getJson('/api/tasks/' . $task->id);
        $response->assertOk()->assertJsonFragment(['id' => $task->id]);
    }

    public function test_update_modifies_task()
    {
        $task = Task::factory()->create();
        $data = ['title' => 'Updated Task'];
        $response = $this->putJson('/api/tasks/' . $task->id, $data);
        $response->assertOk()->assertJsonFragment(['title' => 'Updated Task']);
        $this->assertDatabaseHas('tasks', ['id' => $task->id, 'title' => 'Updated Task']);
    }

    public function test_destroy_deletes_task()
    {
        $task = Task::factory()->create();
        $response = $this->deleteJson('/api/tasks/' . $task->id);
        $response->assertNoContent();
        $this->assertDatabaseMissing('tasks', ['id' => $task->id]);
    }
} 