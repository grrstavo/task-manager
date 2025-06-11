<!--
/**
 * Task List Component
 * 
 * Displays a list of tasks with filtering, pagination, and status indicators.
 * Includes a filter panel for searching and filtering tasks by various criteria.
 * 
 * @component
 * @example
 * ```vue
 * <TaskList />
 * ```
 * 
 * Features:
 * - Task filtering by status, category, and search text
 * - Pagination support
 * - Loading and error states
 * - Due date formatting and overdue status
 * - Responsive design
 */
-->
<template>
    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <!-- Filters -->
        <div class="mb-6 bg-white shadow rounded-lg p-4">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

                <!-- Search -->
                <div class="col-span-3">
                    <label class="block text-sm font-medium text-gray-700">Search</label>
                    <input
                        type="text"
                        v-model="searchQuery"
                        placeholder="Search tasks..."
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                    />
                </div>

                <!-- Status Filter -->
                <div>
                    <label class="block text-sm font-medium text-gray-700">Status</label>
                    <select
                        v-model="selectedStatus"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                    >
                        <option value="">All Statuses</option>
                        <option value="pending">Pending</option>
                        <option value="in_progress">In Progress</option>
                        <option value="completed">Completed</option>
                    </select>
                </div>

                <!-- Category Filter -->
                <div>
                    <label class="block text-sm font-medium text-gray-700">Category</label>
                    <select
                        v-model="selectedCategory"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                    >
                        <option value="">All Categories</option>
                        <option v-for="category in categories" :key="category.id" :value="category.id">
                            {{ category.name }}
                        </option>
                    </select>
                </div>

                <!-- Due Filter -->
                <div>
                    <label class="block text-sm font-medium text-gray-700">Due Date</label>
                    <select
                        v-model="selectedDue"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                    >

                        <option value="">All Due Dates</option>
                        <option value="today">Due Today</option>
                        <option value="overdue">Overdue</option>
                        <option value="upcoming">Upcoming</option>
                    </select>
                </div>
            </div>

            <!-- Clear Filters -->
            <div class="mt-4 flex justify-end">
                <button
                    v-if="hasFilters"
                    @click="clearFilters"
                    class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                >
                    Clear Filters
                </button>
            </div>
        </div>

        <!-- Loading State -->
        <div v-if="loading" class="flex justify-center items-center py-8">
            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-indigo-500"></div>
        </div>

        <!-- Error State -->
        <div v-else-if="error" class="bg-red-50 border-l-4 border-red-400 p-4 mb-6">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-red-700">{{ error }}</p>
                </div>
            </div>
        </div>

        <!-- Task List -->
        <div v-else class="bg-white shadow overflow-hidden sm:rounded-md">
            <ul role="list" class="divide-y divide-gray-200">
                <li v-for="task in tasks" :key="task.id" class="px-6 py-4">
                    <div class="flex items-center justify-between">
                        <div class="flex-1 min-w-0">
                            <h3 class="text-lg font-medium text-gray-900 truncate">{{ task.title }}</h3>
                            <p class="mt-1 text-sm text-gray-500">{{ task.description }}</p>
                            <div class="mt-2 flex items-center text-sm text-gray-500">
                                <span class="flex items-center">
                                    <span
                                        :class="{
                                            'bg-gray-100 text-gray-800': task.status === 'pending',
                                            'bg-blue-100 text-blue-800': task.status === 'in_progress',
                                            'bg-green-100 text-green-800': task.status === 'completed'
                                        }"
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                                    >
                                        {{ formatStatus(task.status) }}
                                    </span>
                                    <span v-if="task.category" class="ml-4">
                                        {{ task.category.name }}
                                    </span>
                                </span>
                            </div>
                        </div>
                        <div class="ml-4 flex-shrink-0">
                            <span class="text-sm" :class="{ 'text-red-500': isTaskOverdue(task), 'text-gray-500': !isTaskOverdue(task) }">
                                Due: {{ formatDueDate(task.due_date) }}
                            </span>
                        </div>
                    </div>
                </li>
            </ul>
        </div>

        <!-- Pagination -->
        <div v-if="pagination.last_page > 1" class="mt-6 flex justify-between">
            <div class="flex items-left justify-between mt-4">
                <div class="text-sm text-gray-700">
                    <span class="font-semibold">
                        {{ (pagination.current_page - 1) * pagination.per_page + 1 }}
                    </span>
                    to
                    <span class="font-semibold">
                        {{
                            Math.min(
                                pagination.current_page * pagination.per_page,
                                pagination.total
                            )
                        }}
                    </span>
                    of
                    <span class="font-semibold">{{ pagination.total }}</span>
                </div>
            </div>
            <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                <button
                    v-for="page in pagination.last_page"
                    :key="page"
                    @click="setPage(page)"
                    :class="[
                        page === pagination.current_page
                            ? 'z-10 bg-indigo-50 border-indigo-500 text-indigo-600'
                            : 'bg-white border-gray-300 text-gray-500 hover:bg-gray-50',
                        'relative inline-flex items-center px-4 py-2 border text-sm font-medium'
                    ]"
                >
                    {{ page }}
                </button>
            </nav>
        </div>
    </div>
</template>

<script setup>
import { useTaskList } from '@/composables/useTaskList'
import { useTaskFilters } from '@/composables/useTaskFilters'

/**
 * Task list functionality
 * Provides task data, loading states, and utility functions
 * @type {Object}
 */
const {
    tasks,
    categories,
    loading,
    pagination,
    error,
    setPage,
    isTaskOverdue,
    formatDueDate
} = useTaskList()

/**
 * Filter functionality
 * Provides filter state and methods for managing filters
 * @type {Object}
 */
const {
    searchQuery,
    selectedStatus,
    selectedCategory,
    selectedDue,
    clearFilters,
    hasFilters
} = useTaskFilters()

function formatStatus(status) {
    return status.split('_')
        .map(word => word.charAt(0).toUpperCase() + word.slice(1))
        .join(' ')
}
</script> 