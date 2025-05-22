<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\TaskList;
use App\Models\Task;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create users
        $user1 = User::create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => bcrypt('password123'),
        ]);

        $user2 = User::create([
            'name' => 'Jane Smith',
            'email' => 'jane@example.com',
            'password' => bcrypt('password123'),
        ]);

        // Create task lists for user1
        $taskList1 = TaskList::create([
            'user_id' => $user1->id,
            'title' => 'Work Tasks',
            'description' => 'Tasks related to work projects',
            'is_public' => true,
        ]);

        $taskList2 = TaskList::create([
            'user_id' => $user1->id,
            'title' => 'Personal Tasks',
            'description' => 'Personal todo items',
            'is_public' => false,
        ]);

        // Create task list for user2
        $taskList3 = TaskList::create([
            'user_id' => $user2->id,
            'title' => 'Shopping List',
            'description' => 'Items to buy',
            'is_public' => true,
        ]);

        // Create tasks for taskList1
        Task::create([
            'user_id' => $user1->id,
            'task_list_id' => $taskList1->id,
            'title' => 'Complete project proposal',
            'description' => 'Write and submit the project proposal',
            'status' => 'pending',
        ]);

        Task::create([
            'user_id' => $user1->id,
            'task_list_id' => $taskList1->id,
            'title' => 'Review code changes',
            'description' => 'Review pull requests from team members',
            'status' => 'in_progress',
        ]);

        // Create tasks for taskList2
        Task::create([
            'user_id' => $user1->id,
            'task_list_id' => $taskList2->id,
            'title' => 'Gym workout',
            'description' => 'Complete daily workout routine',
            'status' => 'completed',
        ]);

        // Create tasks for taskList3
        Task::create([
            'user_id' => $user2->id,
            'task_list_id' => $taskList3->id,
            'title' => 'Buy groceries',
            'description' => 'Get items for dinner',
            'status' => 'pending',
        ]);

        // Share taskList1 with user2
        $taskList1->sharedUsers()->attach($user2->id);
    }
}
