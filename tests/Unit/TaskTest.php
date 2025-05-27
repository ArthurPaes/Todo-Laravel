<?php

namespace Tests\Unit;

use App\Models\Task;
use App\Models\User;
use App\Models\TaskList;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskTest extends TestCase
{
    use RefreshDatabase;

    public function test_fillable_attributes()
    {
        $task = new Task([
            'title' => 'Test Task',
            'description' => 'Test Description',
            'due_date' => now(),
            'is_completed' => true,
            'user_id' => 1,
            'task_list_id' => 1,
        ]);
        $this->assertEquals('Test Task', $task->title);
        $this->assertEquals('Test Description', $task->description);
    }

    public function test_casts()
    {
        $task = Task::factory()->create([
            'due_date' => now(),
            'is_completed' => 1,
        ]);
        $this->assertInstanceOf(\Illuminate\Support\Carbon::class, $task->due_date);
        $this->assertIsBool($task->is_completed);
    }

    public function test_user_relationship()
    {
        $task = Task::factory()->create();
        $this->assertInstanceOf(User::class, $task->user);
    }

    public function test_task_list_relationship()
    {
        $task = Task::factory()->create();
        $this->assertInstanceOf(TaskList::class, $task->taskList);
    }

    public function test_factory_creates_task()
    {
        $task = Task::factory()->create();
        $this->assertDatabaseHas('tasks', ['id' => $task->id]);
    }
} 