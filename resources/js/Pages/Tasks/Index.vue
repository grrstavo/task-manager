<template>
    <AppLayout>
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex justify-between items-center mb-6">
                            <h1 class="text-2xl font-semibold text-gray-900">Tasks</h1>
                            <button 
                                @click="showCreateModal = true"
                                class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700"
                            >
                                Create Task
                            </button>
                        </div>

                        <TaskList 
                            :tasks="tasks" 
                            :loading="loading"
                            :error="error"
                            @refresh="fetchTasks"
                        />
                    </div>
                </div>
            </div>
        </div>

        <Modal :show="showCreateModal" @close="showCreateModal = false">
            <TaskForm 
                :categories="categories"
                @task-created="handleTaskCreated"
            />
        </Modal>
    </AppLayout>
</template>

<script setup>
import { ref, watch } from 'vue';
import { useTaskStore } from '@/stores/taskStore';
import { storeToRefs } from 'pinia';
import AppLayout from '@/Layouts/AppLayout.vue';
import TaskForm from '@/Components/Tasks/TaskForm.vue';
import TaskList from '@/Components/Tasks/TaskList.vue';
import Modal from '@/Components/Modal.vue';

const taskStore = useTaskStore();
//const categoryStore = useCategoryStore();
const { tasks, categories, loading, pagination } = storeToRefs(taskStore);

const showCreateModal = ref(false);
const searchQuery = ref('');
const selectedStatus = ref('');
const selectedCategory = ref('');
const selectedDue = ref('');
const error = ref(null);

// Watch for filter changes
watch(searchQuery, (value) => {
    taskStore.setSearch(value);
});

watch(selectedStatus, (value) => {
    taskStore.setFilter('status', value);
});

watch(selectedCategory, (value) => {
    taskStore.setFilter('category_id', value);
});

watch(selectedDue, (value) => {
    taskStore.setFilter('due', value);
});

// Methods
const setPage = (page) => {
    taskStore.setPage(page);
};

const handleTaskCreated = async () => {
    showCreateModal.value = false;
    await fetchTasks();
};

const fetchTasks = async () => {
    loading.value = true;
    error.value = null;
    try {
        await taskStore.fetchTasks();
    } catch (err) {
        error.value = err.message || 'Failed to fetch tasks';
    } finally {
        loading.value = false;
    }
};

</script> 