<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Task;
use App\Http\Resources\TaskCollection;
use App\Http\Resources\TaskResource;
use App\Http\Requests\TaskRequest;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

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
                    $query->where(DB::raw('LOWER(title)'), 'like', '%' . strtolower($search) . '%')
                        ->orWhere(DB::raw('LOWER(description)'), 'like', '%' . strtolower($search) . '%');
                });
            })
            ->when($request->due, function ($query, $due) {
                return match($due) {
                    'today' => $query->whereDate('due_date', Carbon::today()),
                    'overdue' => $query->whereDate('due_date', '<', Carbon::today())
                        ->whereNotIn('status', ['completed']),
                    'upcoming' => $query->whereDate('due_date', '>', Carbon::today())
                        ->whereNotIn('status', ['completed']),
                    default => $query
                };
            });

        $tasks = $query->latest()->paginate(10);
        return new TaskCollection($tasks);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(TaskRequest $request)
    {
        $task = Task::create($request->validated());
        return new TaskResource($task->load('category'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Task $task)
    {
        return new TaskResource($task->load('category'));
    }
}
