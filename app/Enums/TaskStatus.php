<?php

namespace App\Enums;

/**
 * Task Status Enumeration
 * 
 * Defines the possible statuses for tasks in the task management system.
 * Includes methods for getting human-readable labels and color codes for UI.
 * 
 * @package App\Enums
 */
enum TaskStatus: string
{
    /**
     * Task is pending and not started
     */
    case PENDING = 'pending';

    /**
     * Task is currently being worked on
     */
    case IN_PROGRESS = 'in_progress';

    /**
     * Task has been completed
     */
    case COMPLETED = 'completed';

    /**
     * Get the human-readable label for the task status
     *
     * @return string The formatted label for display
     */
    public function label(): string
    {
        return match($this) {
            self::PENDING => 'Pending',
            self::IN_PROGRESS => 'In Progress',
            self::COMPLETED => 'Completed',
        };
    }

    /**
     * Get the color code for the task status
     * Used for UI elements like badges or status indicators
     *
     * @return string The color name for the status
     */
    public function color(): string
    {
        return match($this) {
            self::PENDING => 'gray',
            self::IN_PROGRESS => 'blue',
            self::COMPLETED => 'green',
        };
    }

    /**
     * Get all possible values of the task status
     * Useful for validation and form generation
     *
     * @return array<string> Array of all possible status values
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
