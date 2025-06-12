<?php

namespace App\Services;

use App\Repositories\Interfaces\TaskRepositoryInterface;
use App\Events\TaskCreated;
use App\Events\TaskUpdated;
use App\Models\Task;
use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use InvalidArgumentException;

/**
 * Task Service
 * 
 * Handles business logic for task operations.
 * This service layer sits between the controller and repository,
 * handling business rules, validation, and orchestration.
 */
class TaskService
{
    private const CACHE_TTL = 3600; // 1 hour in seconds
    private const CACHE_PREFIX = 'tasks';

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
    public function getTasks(array $filters = [], int $perPage = 10): LengthAwarePaginator
    {
        $cacheKey = $this->generateCacheKey($filters, $perPage);

        return Cache::tags([self::CACHE_PREFIX])->remember($cacheKey, self::CACHE_TTL, function () use ($filters, $perPage) {
            return $this->taskRepository->getPaginatedTasks($filters, $perPage);
        });
    }

    /**
     * Create a new task with validation and event dispatch
     *
     * @param array $data
     * @return \App\Models\Task
     */
    public function createTask(array $data)
    {
        $data = $this->validateAndFormatTaskData($data);
        $task = $this->taskRepository->create($data);

        // Clear task list cache
        $this->clearTaskCache();

        event(new TaskCreated($task));
        
        return $task;
    }

    /**
     * Validate and format task data
     *
     * @param array $data
     * @param bool $setDefaults
     * @return array
     */
    private function validateAndFormatTaskData(array $data, bool $setDefaults = true): array
    {
        if ($setDefaults && !isset($data['status'])) {
            $data['status'] = 'pending';
        }

        if (isset($data['status']) && !in_array($data['status'], ['pending', 'in_progress', 'completed'])) {
            throw new InvalidArgumentException('Invalid status value');
        }

        if (isset($data['due_date'])) {
            $data['due_date'] = Carbon::parse($data['due_date']);
        }

        return $data;
    }


    /**
     * Delete a task by ID
     *
     * @param int $id Task ID
     * @return void
     */
    public function deleteTask(int $id): void
    {
        $task = $this->taskRepository->find($id);
        
        if (!$task) {
            throw new \Exception('Task not found');
        }

        $this->taskRepository->delete($id);
        $this->clearTaskCache();
    }

    /**
     * Generate cache key
     *
     * @param array $params
     * @param int|null $perPage
     * @return string
     */
    private function generateCacheKey(array $params = [], ?int $perPage = null): string
    {
        $key = self::CACHE_PREFIX;
        
        if (!empty($params)) {
            $key .= ':' . md5(json_encode($params));
        }
        
        if ($perPage !== null) {
            $key .= ':page_' . $perPage;
        }
        
        return $key;
    }

    /**
     * Clear task cache
     *
     * @return void
     */
    private function clearTaskCache(): void
    {
        Cache::flush();
    }
} 