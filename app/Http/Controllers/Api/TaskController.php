<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\TaskRequest;
use App\Http\Resources\TaskCollection;
use App\Http\Resources\TaskResource;
use App\Models\Task;
use App\Services\TaskService;
use Illuminate\Http\Request;

/**
 * Task API Controller
 * 
 * Handles HTTP requests for task operations.
 * Uses TaskService for business logic and TaskResource for response formatting.
 */
class TaskController extends Controller
{
    /**
     * The task service instance.
     *
     * @var TaskService
     */
    protected $taskService;

    /**
     * Create a new controller instance.
     *
     * @param TaskService $taskService
     */
    public function __construct(TaskService $taskService)
    {
        $this->taskService = $taskService;
    }

    /**
     * Display a listing of tasks.
     *
     * @param Request $request
     * @return TaskCollection
     */
    public function index(Request $request): TaskCollection
    {
        $filters = [
            'status' => $request->status,
            'category_id' => $request->category_id,
            'search' => $request->search,
            'due' => $request->due,
        ];

        $tasks = $this->taskService->getTasks($filters, $perPage = 10);
        return new TaskCollection($tasks);
    }

    /**
     * Store a newly created task.
     *
     * @param TaskRequest $request
     * @return TaskResource
     */
    public function store(TaskRequest $request): TaskResource
    {
        $task = $this->taskService->createTask($request->validated());
        return new TaskResource($task);
    }

    /**
     * Display the specified task.
     *
     * @param Task $task
     * @return TaskResource
     */
    public function show(Task $task): TaskResource
    {
        return new TaskResource($task->load('category'));
    }
}
