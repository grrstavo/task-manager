<?php

namespace App\Services;

use App\Repositories\Interfaces\TaskRepositoryInterface;
use App\Events\TaskCreated;
use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;

/**
 * Task Service
 * 
 * Handles business logic for task operations.
 * This service layer sits between the controller and repository,
 * handling business rules, validation, and orchestration.
 */
class TaskService
{
    /**
     * @var TaskRepositoryInterface
     */
    protected $taskRepository;

    /**
     * TaskService constructor.
     *
     * @param TaskRepositoryInterface $taskRepository
     */
    public function __construct(TaskRepositoryInterface $taskRepository)
    {
        $this->taskRepository = $taskRepository;
    }

    /**
     * Get tasks with filters
     *
     * @param array $filters
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getTasks(array $filters, int $perPage = 10): LengthAwarePaginator
    {
        return $this->taskRepository->getPaginatedTasks($filters, $perPage);
    }

    /**
     * Create a new task with validation and event dispatch
     *
     * @param array $data
     * @return \App\Models\Task
     */
    public function createTask(array $data)
    {
        // Set default status if not provided
        if (!isset($data['status'])) {
            $data['status'] = 'pending';
        }

        // Validate and format due date
        if (isset($data['due_date'])) {
            $data['due_date'] = Carbon::parse($data['due_date'])->startOfDay();
        }

        // Create task
        $task = $this->taskRepository->create($data);

        // Dispatch event
        event(new TaskCreated($task));

        return $task;
    }
} 