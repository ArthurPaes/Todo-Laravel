<?php

namespace App\Http\Controllers;

use App\Models\TaskList;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TaskListController extends Controller
{
    public function index()
    {
        $taskLists = TaskList::with('tasks')
            ->where('is_public', true)
            ->orWhere('user_id', Auth::id())
            ->orWhereHas('sharedUsers', function($query) {
                $query->where('users.id', Auth::id());
            })
            ->get();
        return response()->json($taskLists);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_public' => 'boolean',
        ]);

        $validated['user_id'] = Auth::id();
        $taskList = TaskList::create($validated);
        return response()->json($taskList, 201);
    }

    public function show(TaskList $taskList)
    {
        // Check if user has access to this task list
        if (!$taskList->is_public && 
            $taskList->user_id !== Auth::id() && 
            !$taskList->sharedUsers()->where('users.id', Auth::id())->exists()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $taskList->load(['tasks', 'sharedUsers']);
        return response()->json($taskList);
    }

    public function update(Request $request, TaskList $taskList)
    {
        // Only owner can update the list
        if ($taskList->user_id !== Auth::id()) {
            return response()->json(['message' => 'Only the owner can update this list'], 403);
        }

        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'is_public' => 'boolean',
        ]);

        $taskList->update($validated);
        return response()->json($taskList);
    }

    public function destroy(TaskList $taskList)
    {
        // Only owner can delete the list
        if ($taskList->user_id !== Auth::id()) {
            return response()->json(['message' => 'Only the owner can delete this list'], 403);
        }

        $taskList->delete();
        return response()->json(null, 204);
    }

    public function share(Request $request, TaskList $taskList)
    {
        // Only owner can share the list
        if ($taskList->user_id !== Auth::id()) {
            return response()->json(['message' => 'Only the owner can share this list'], 403);
        }

        $validated = $request->validate([
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:users,id',
        ]);

        $taskList->sharedUsers()->sync($validated['user_ids']);
        return response()->json($taskList->load('sharedUsers'));
    }
} 