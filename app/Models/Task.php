<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Enums\TaskStatus;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'status',
        'due_date',
        'category_id'
    ];

    protected $casts = [
        'due_date' => 'datetime',
        'status' => TaskStatus::class
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    // Query Scopes
    public function scopeFilterByStatus($query, $status)
    {
        return $query->when($status, function ($query) use ($status) {
            return $query->where('status', $status);
        });
    }

    public function scopeFilterByCategory($query, $categoryId)
    {
        return $query->when($categoryId, function ($query) use ($categoryId) {
            return $query->where('category_id', $categoryId);
        });
    }
}
