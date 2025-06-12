<?php

namespace App\Repositories;

use App\Models\Task;
use App\Repositories\Interfaces\TaskRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

/**
 * Task Repository Implementation
 * 
 * Handles all database operations for tasks, implementing the TaskRepositoryInterface.
 * This class encapsulates all database queries and data access logic.
 */
class TaskRepository implements TaskRepositoryInterface
{
    /**
     * @var Task
     */
    protected $model;

    /**
     * TaskRepository constructor.
     *
     * @param Task $model
     */
    public function __construct(Task $model)
    {
        $this->model = $model;
    }

    /**
     * Get paginated tasks with optional filters
     *
     * @param array $filters Associative array of filter criteria
     * @param int $perPage Number of items per page
     * @return LengthAwarePaginator
     */
    public function getPaginatedTasks(array $filters = [], int $perPage = 10): LengthAwarePaginator
    {
        $query = $this->model->query()
            ->with('category');

        // Apply status filter
        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        // Apply category filter
        if (!empty($filters['category_id'])) {
            $query->where('category_id', $filters['category_id']);
        }

        // Apply search filter
        if (!empty($filters['search'])) {
            $search = strtolower($filters['search']);
            $query->where(function ($query) use ($search) {
                $query->where(DB::raw('LOWER(title)'), 'like', "%{$search}%")
                    ->orWhere(DB::raw('LOWER(description)'), 'like', "%{$search}%");
            });
        }

        // Apply due date filter
        if (!empty($filters['due'])) {
            $query = match($filters['due']) {
                'today' => $query->whereDate('due_date', Carbon::today()),
                'overdue' => $query->whereDate('due_date', '<', Carbon::today())
                    ->whereNotIn('status', ['completed']),
                'upcoming' => $query->whereDate('due_date', '>', Carbon::today())
                    ->whereNotIn('status', ['completed']),
                default => $query
            };
        }

        return $query->latest()->paginate($perPage);
    }

    /**
     * Create a new task
     *
     * @param array $data Task data
     * @return Task
     */
    public function create(array $data): Task
    {
        return $this->model->create($data);
    }

    /**
     * Find a task by ID
     *
     * @param int $id Task ID
     * @return Task|null
     */
    public function find(int $id): ?Task
    {
        return Task::find($id);
    }

    /**
     * Delete a task by ID
     *
     * @param int $id Task ID
     * @return void
     */
    public function delete(int $id): void
    {
        Task::destroy($id);
    }
} 