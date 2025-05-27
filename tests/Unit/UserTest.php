<?php

namespace Tests\Unit;

use App\Models\User;
use App\Models\Task;
use App\Models\TaskList;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    public function test_fillable_attributes()
    {
        $user = new User([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'secret',
        ]);
        $this->assertEquals('Test User', $user->name);
        $this->assertEquals('test@example.com', $user->email);
    }

    public function test_casts()
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
        ]);
        $this->assertInstanceOf(\Illuminate\Support\Carbon::class, $user->email_verified_at);
    }

    public function test_tasks_relationship()
    {
        $user = User::factory()->create();
        $task = Task::factory()->create(['user_id' => $user->id]);
        $this->assertTrue($user->tasks->contains($task));
    }

    public function test_task_lists_relationship()
    {
        $user = User::factory()->create();
        $taskList = TaskList::factory()->create(['user_id' => $user->id]);
        $this->assertTrue($user->taskLists->contains($taskList));
    }

    public function test_shared_task_lists_relationship()
    {
        $user = User::factory()->create();
        $taskList = TaskList::factory()->create();
        $user->sharedTaskLists()->attach($taskList);
        $this->assertTrue($user->sharedTaskLists->contains($taskList));
    }

    public function test_factory_creates_user()
    {
        $user = User::factory()->create();
        $this->assertDatabaseHas('users', ['id' => $user->id]);
    }
} 