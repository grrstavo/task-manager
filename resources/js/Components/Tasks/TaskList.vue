<template>
    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <!-- Filters -->
        <div class="mb-6 bg-white shadow rounded-lg p-4">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <!-- Status Filter -->
                <div>
                    <label class="block text-sm font-medium text-gray-700">Status</label>
                    <select
                        v-model="selectedStatus"
                        @change="store.setFilter('status', selectedStatus)"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                    >
                        <option value="">All Statuses</option>
                        <option value="pending">Pending</option>
                        <option value="in progress">In Progress</option>
                        <option value="completed">Completed</option>
                    </select>
                </div>

                <!-- Category Filter -->
                <div>
                    <label class="block text-sm font-medium text-gray-700">Category</label>
                    <select
                        v-model="selectedCategory"
                        @change="store.setFilter('category_id', selectedCategory)"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                    >
                        <option value="">All Categories</option>
                        <option v-for="category in store.categories" :key="category.id" :value="category.id">
                            {{ category.name }}
                        </option>
                    </select>
                </div>

                <!-- Search -->
                <div>
                    <label class="block text-sm font-medium text-gray-700">Search</label>
                    <input
                        type="text"
                        v-model="searchQuery"
                        @input="debouncedSearch"
                        placeholder="Search tasks..."
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                    />
                </div>
            </div>

            <!-- Clear Filters -->
            <div class="mt-4 flex justify-end">
                <button
                    v-if="store.hasFilters"
                    @click="clearFilters"
                    class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                >
                    Clear Filters
                </button>
            </div>
        </div>

        <!-- Loading State -->
        <div v-if="store.loading" class="flex justify-center items-center py-8">
            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-indigo-500"></div>
        </div>

        <!-- Error State -->
        <div v-else-if="store.error" class="bg-red-50 border-l-4 border-red-400 p-4 mb-6">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-red-700">{{ store.error }}</p>
                </div>
            </div>
        </div>

        <!-- Task List -->
        <div v-else class="bg-white shadow overflow-hidden sm:rounded-md">
            <ul role="list" class="divide-y divide-gray-200">
                <li v-for="task in store.tasks" :key="task.id" class="px-6 py-4">
                    <div class="flex items-center justify-between">
                        <div class="flex-1 min-w-0">
                            <h3 class="text-lg font-medium text-gray-900 truncate">{{ task.title }}</h3>
                            <p class="mt-1 text-sm text-gray-500">{{ task.description }}</p>
                            <div class="mt-2 flex items-center text-sm text-gray-500">
                                <span class="flex items-center">
                                    <span :class="`inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-${task.status_color}-100 text-${task.status_color}-800`">
                                        {{ task.status_label }}
                                    </span>
                                    <span v-if="task.category" class="ml-4">
                                        {{ task.category.name }}
                                    </span>
                                </span>
                            </div>
                        </div>
                        <div class="ml-4 flex-shrink-0">
                            <span class="text-sm text-gray-500">
                                Due: {{ new Date(task.due_date).toLocaleDateString() }}
                            </span>
                        </div>
                    </div>
                </li>
            </ul>
        </div>

        <!-- Pagination -->
        <div v-if="store.pagination.last_page > 1" class="mt-6 flex justify-center">
            <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                <button
                    v-for="page in store.pagination.last_page"
                    :key="page"
                    @click="store.setPage(page)"
                    :class="[
                        page === store.pagination.current_page
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
import { ref, onMounted, watch } from 'vue'
import { useTaskStore } from '@/stores/taskStore'
import debounce from 'lodash/debounce'

const store = useTaskStore()
const selectedStatus = ref('')
const selectedCategory = ref('')
const searchQuery = ref('')

// Initialize data
onMounted(async () => {
    await Promise.all([
        store.fetchTasks(),
        store.fetchCategories()
    ])
})

// Debounced search
const debouncedSearch = debounce((event) => {
    store.setFilter('search', event.target.value)
}, 300)

// Clear all filters
const clearFilters = () => {
    selectedStatus.value = ''
    selectedCategory.value = ''
    searchQuery.value = ''
    store.clearFilters()
}
</script> 