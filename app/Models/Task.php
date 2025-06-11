<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Enums\TaskStatus;

/**
 * Task Model
 * 
 * Represents a task in the task management system.
 * Tasks belong to a category and have various attributes like status and due date.
 * 
 * @package App\Models
 */
class Task extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'title',
        'description',
        'status',
        'due_date',
        'category_id'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string|class-string>
     */
    protected $casts = [
        'due_date' => 'datetime',
        'status' => TaskStatus::class
    ];

    /**
     * Get the category this task belongs to.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Scope a query to filter tasks by status.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query The query builder instance
     * @param string $status The status to filter by
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFilterByStatus($query, $status)
    {
        return $query->when($status, function ($query) use ($status) {
            return $query->where('status', $status);
        });
    }

    /**
     * Scope a query to filter tasks by category.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query The query builder instance
     * @param int $categoryId The category ID to filter by
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFilterByCategory($query, $categoryId)
    {
        return $query->when($categoryId, function ($query) use ($categoryId) {
            return $query->where('category_id', $categoryId);
        });
    }
}
