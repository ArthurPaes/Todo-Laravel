<?php

namespace Database\Factories;

use App\Models\Task;
use App\Models\User;
use App\Models\TaskList;
use Illuminate\Database\Eloquent\Factories\Factory;

class TaskFactory extends Factory
{
    protected $model = Task::class;

    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence,
            'description' => $this->faker->paragraph,
            'due_date' => $this->faker->dateTimeBetween('now', '+1 year'),
            'is_completed' => $this->faker->boolean,
            'user_id' => User::factory(),
            'task_list_id' => TaskList::factory(),
        ];
    }
} 