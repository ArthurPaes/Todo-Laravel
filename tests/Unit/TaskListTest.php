<?php

namespace Tests\Unit;

use App\Models\TaskList;
use App\Models\User;
use App\Models\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskListTest extends TestCase
{
    use RefreshDatabase;

    public function test_fillable_attributes()
    {
        $taskList = new TaskList([
            'title' => 'Test List',
            'description' => 'Test Description',
            'is_public' => true,
            'user_id' => 1,
        ]);
        $this->assertEquals('Test List', $taskList->title);
        $this->assertEquals('Test Description', $taskList->description);
    }

    public function test_casts()
    {
        $taskList = TaskList::factory()->create(['is_public' => 1]);
        $this->assertIsBool($taskList->is_public);
    }

    public function test_user_relationship()
    {
        $taskList = TaskList::factory()->create();
        $this->assertInstanceOf(User::class, $taskList->user);
    }

    public function test_tasks_relationship()
    {
        $taskList = TaskList::factory()->create();
        $task = Task::factory()->create(['task_list_id' => $taskList->id]);
        $this->assertTrue($taskList->tasks->contains($task));
    }

    public function test_shared_users_relationship()
    {
        $taskList = TaskList::factory()->create();
        $user = User::factory()->create();
        $taskList->sharedUsers()->attach($user);
        $this->assertTrue($taskList->sharedUsers->contains($user));
    }

    public function test_factory_creates_task_list()
    {
        $taskList = TaskList::factory()->create();
        $this->assertDatabaseHas('task_lists', ['id' => $taskList->id]);
    }
} 