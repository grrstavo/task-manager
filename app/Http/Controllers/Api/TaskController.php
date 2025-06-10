<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Task;
use App\Http\Resources\TaskCollection;
use App\Http\Resources\TaskResource;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Task::query()
            ->with('category')
            ->when($request->status, function ($query, $status) {
                return $query->filterByStatus($status);
            })
            ->when($request->category_id, function ($query, $categoryId) {
                return $query->filterByCategory($categoryId);
            })
            ->when($request->search, function ($query, $search) {
                return $query->where(function ($query) use ($search) {
                    $query->where('title', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%");
                });
            });

        $tasks = $query->latest()->paginate(10);
        return new TaskCollection($tasks);
    }

    /**
     * Display the specified resource.
     */
    public function show(Task $task)
    {
        return new TaskResource($task->load('category'));
    }
}
