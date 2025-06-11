<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Http\Resources\CategoryResource;

/**
 * Category API Controller
 * 
 * Handles API requests related to categories in the task management system.
 * 
 * @package App\Http\Controllers\Api
 */
class CategoryController extends Controller
{
    /**
     * Display a listing of the categories.
     * 
     * Retrieves all categories with their associated task counts.
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index()
    {
        $categories = Category::withCount('tasks')->get();
        return CategoryResource::collection($categories);
    }

    /**
     * Display the specified category.
     * 
     * Retrieves a single category by its ID with the count of associated tasks.
     *
     * @param Category $category The category model instance (automatically resolved)
     * @return CategoryResource
     */
    public function show(Category $category): CategoryResource
    {
        return new CategoryResource($category->loadCount('tasks'));
    }
}
