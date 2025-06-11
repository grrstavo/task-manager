<?php

namespace App\Events;

use App\Models\Task;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Task Updated Event
 * 
 * This event is dispatched when a task is updated.
 * It can be used to trigger notifications, logging, or other side effects.
 */
class TaskUpdated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * The task that was updated.
     *
     * @var Task
     */
    public $task;

    /**
     * Create a new event instance.
     *
     * @param Task $task
     */
    public function __construct(Task $task)
    {
        $this->task = $task;
    }
} 