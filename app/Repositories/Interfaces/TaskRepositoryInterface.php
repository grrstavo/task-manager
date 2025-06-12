<?php

namespace App\Repositories\Interfaces;

use App\Models\Task;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * Interface TaskRepositoryInterface
 * 
 * Defines the contract for task data access operations.
 * This interface ensures consistent task data handling across the application.
 */
interface TaskRepositoryInterface
{
    /**
     * Get paginated tasks with optional filters
     *
     * @param array $filters Associative array of filter criteria
     * @param int $perPage Number of items per page
     * @return LengthAwarePaginator
     */
    public function getPaginatedTasks(array $filters = [], int $perPage = 10): LengthAwarePaginator;

    /**
     * Create a new task
     *
     * @param array $data Task data
     * @return Task
     */
    public function create(array $data): Task;

    /**
     * Find a task by ID
     *
     * @param int $id Task ID
     * @return Task|null
     */
    public function find(int $id): ?Task;

    /**
     * Delete a task by ID
     *
     * @param int $id Task ID
     * @return void
     */
    public function delete(int $id): void;
} 