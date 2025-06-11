<?php

namespace App\Events;

use App\Models\Task;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Task Created Event
 * 
 * This event is dispatched when a new task is created.
 * It can be used to trigger notifications, logging, or other side effects.
 */
class TaskCreated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * The task that was created.
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