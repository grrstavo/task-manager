<template>
    <AppLayout>
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <!-- Header with Create Button -->
                        <div class="flex justify-between items-center mb-6">
                            <h2 class="text-lg font-medium text-gray-900">Tasks</h2>
                            <button
                                @click="openCreateModal"
                                class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150"
                            >
                                Create Task
                            </button>
                        </div>

                        <!-- Filters -->
                        <div class="mb-6 grid grid-cols-1 md:grid-cols-5 gap-4">
                            <!-- Search -->
                            <div class="col-span-1 md:col-span-2">
                                <input
                                    type="text"
                                    v-model="searchQuery"
                                    placeholder="Search tasks..."
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                />
                            </div>
                            <!-- Status Filter -->
                            <div>
                                <select
                                    v-model="selectedStatus"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                >
                                    <option value="">All Statuses</option>
                                    <option value="pending">Pending</option>
                                    <option value="in_progress">In Progress</option>
                                    <option value="completed">Completed</option>
                                </select>
                            </div>
                            <!-- Category Filter -->
                            <div>
                                <select
                                    v-model="selectedCategory"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                >
                                    <option value="">All Categories</option>
                                    <option v-for="category in categories" :key="category.id" :value="category.id">
                                        {{ category.name }}
                                    </option>
                                </select>
                            </div>
                            <!-- Due Filter -->
                            <div>
                                <select
                                    v-model="selectedDue"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                >
                                    <option value="">All Due Dates</option>
                                    <option value="today">Due Today</option>
                                    <option value="overdue">Overdue</option>
                                    <option value="upcoming">Upcoming</option>
                                </select>
                            </div>
                        </div>

                        <!-- Task List -->
                        <div v-if="loading" class="flex justify-center items-center py-8">
                            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-indigo-500"></div>
                        </div>
                        <div v-else>
                            <div v-if="tasks.length === 0" class="text-center py-8 text-gray-500">
                                No tasks found.
                            </div>
                            <div v-else class="space-y-4">
                                <div v-for="task in tasks" :key="task.id" class="border rounded-lg p-4 hover:bg-gray-50">
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <h3 class="text-lg font-medium text-gray-900">{{ task.title }}</h3>
                                            <p class="mt-1 text-sm text-gray-500">{{ task.description }}</p>
                                        </div>
                                        <span
                                            class="px-2 py-1 text-xs font-medium rounded-full"
                                            :class="{
                                                'bg-yellow-100 text-yellow-800': task.status === 'pending',
                                                'bg-blue-100 text-blue-800': task.status === 'in_progress',
                                                'bg-green-100 text-green-800': task.status === 'completed'
                                            }"
                                        >
                                            {{ task.status_label }}
                                        </span>
                                    </div>
                                    <div class="mt-2 flex items-center text-sm text-gray-500">
                                        <span class="mr-4">
                                            <span class="font-medium">Category:</span>
                                            {{ task.category?.name }}
                                        </span>
                                        <span v-if="task.due_date">
                                            <span class="font-medium">Due:</span>
                                            {{ new Date(task.due_date).toLocaleDateString() }}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <!-- Pagination -->
                            <div class="mt-6 flex justify-between items-center">
                                <div class="text-sm text-gray-700">
                                    <template v-if="pagination.total > 0">
                                        Showing {{ (pagination.current_page - 1) * pagination.per_page + 1 }}
                                        to {{ Math.min(pagination.current_page * pagination.per_page, pagination.total) }}
                                        of {{ pagination.total }} results
                                    </template>
                                    <template v-else>
                                        No results found
                                    </template>
                                </div>
                                <div class="flex space-x-2">
                                    <button
                                        @click="setPage(pagination.current_page - 1)"
                                        :disabled="pagination.current_page === 1"
                                        class="px-3 py-1 rounded-md border"
                                        :class="pagination.current_page === 1 ? 'bg-gray-100 text-gray-400' : 'hover:bg-gray-50'"
                                    >
                                        Previous
                                    </button>
                                    <button
                                        @click="setPage(pagination.current_page + 1)"
                                        :disabled="pagination.current_page === pagination.last_page"
                                        class="px-3 py-1 rounded-md border"
                                        :class="pagination.current_page === pagination.last_page ? 'bg-gray-100 text-gray-400' : 'hover:bg-gray-50'"
                                    >
                                        Next
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Create Task Modal -->
        <div v-if="showCreateModal" class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center">
            <div class="bg-white rounded-lg p-6 max-w-2xl w-full mx-4">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-lg font-medium text-gray-900">Create New Task</h3>
                    <button
                        @click="showCreateModal = false"
                        class="text-gray-400 hover:text-gray-500"
                    >
                        <span class="sr-only">Close</span>
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <TaskForm
                    :categories="categories"
                    @task-created="handleTaskCreated"
                />
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue';
import { useTaskStore } from '@/stores/taskStore';
import { storeToRefs } from 'pinia';
import AppLayout from '@/Layouts/AppLayout.vue';
import TaskForm from '@/Components/Tasks/TaskForm.vue';
import TaskList from '@/Components/Tasks/TaskList.vue';
import { useTaskList } from '@/composables/useTaskList';

const store = useTaskStore();
const { tasks, categories, loading, pagination } = storeToRefs(store);

const showCreateModal = ref(false);
const searchQuery = ref('');
const selectedStatus = ref('');
const selectedCategory = ref('');
const selectedDue = ref('');

// Watch for filter changes
watch(searchQuery, (value) => {
    store.setSearch(value);
});

watch(selectedStatus, (value) => {
    store.setFilter('status', value);
});

watch(selectedCategory, (value) => {
    store.setFilter('category_id', value);
});

watch(selectedDue, (value) => {
    store.setFilter('due', value);
});

// Methods
const setPage = (page) => {
    store.setPage(page);
};

const handleTaskCreated = () => {
    showCreateModal.value = false;
    store.fetchTasks();
};

const openCreateModal = () => {
    showCreateModal.value = true;
};

// Initialize data
onMounted(() => {
    store.fetchTasks();
    store.fetchCategories();
});
</script> 