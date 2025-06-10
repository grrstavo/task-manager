<?php

namespace App\Enums;

enum TaskStatus: string
{
    case PENDING = 'pending';
    case IN_PROGRESS = 'in progress';
    case COMPLETED = 'completed';

    /**
     * Get the label of the task status
     * @return string
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
     * Get the color of the task status
     * @return string
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
     * Get the values of the task status
     * @return array
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
