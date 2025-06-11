<?php

namespace App\Exceptions;

use Exception;

/**
 * Task Not Found Exception
 * 
 * This exception is thrown when attempting to access a task that doesn't exist.
 */
class TaskNotFoundException extends Exception
{
    /**
     * Create a new exception instance.
     *
     * @param string $message
     * @param int $code
     * @param \Throwable|null $previous
     */
    public function __construct(string $message = "Task not found", int $code = 404, \Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
} 