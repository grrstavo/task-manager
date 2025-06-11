<template>
    <AppLayout>
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <!-- Filters -->
                        <div class="mb-6 grid grid-cols-1 md:grid-cols-4 gap-4">
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
                                    Showing {{ pagination.total > 0 ? (pagination.current_page - 1) * pagination.per_page + 1 : 0 }}
                                    to {{ Math.min(pagination.current_page * pagination.per_page, pagination.total) }}
                                    of {{ pagination.total }} results
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
    </AppLayout>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue';
import { useTaskStore } from '@/stores/taskStore';
import { storeToRefs } from 'pinia';
import AppLayout from '@/Layouts/AppLayout.vue';

const store = useTaskStore();
const { tasks, categories, loading, pagination } = storeToRefs(store);

const searchQuery = ref('');
const selectedStatus = ref('');
const selectedCategory = ref('');

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

// Methods
const setPage = (page) => {
    store.setPage(page);
};

// Initialize data
onMounted(() => {
    store.fetchTasks();
    store.fetchCategories();
});
</script> 