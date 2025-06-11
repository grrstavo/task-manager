<!--
/**
 * Task Creation Form Component
 * 
 * A form component for creating new tasks. Handles form state management,
 * validation, and submission through the useTaskForm composable.
 * 
 * @component
 * @example
 * ```vue
 * <TaskForm
 *   :categories="categories"
 *   @task-created="handleTaskCreated"
 * />
 * ```
 */
-->
<template>
  <form @submit.prevent="handleSubmit">
    <div class="mb-4">
      <label class="block text-gray-700">Title</label>
      <input v-model="form.title" type="text" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required />
      <div v-if="errors.title" class="text-red-500 text-xs mt-1">{{ errors.title }}</div>
    </div>
    <div class="mb-4">
      <label class="block text-gray-700">Description</label>
      <textarea v-model="form.description" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
      <div v-if="errors.description" class="text-red-500 text-xs mt-1">{{ errors.description }}</div>
    </div>
    <div class="mb-4">
      <label class="block text-gray-700">Status</label>
      <select v-model="form.status" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
        <option value="pending">Pending</option>
        <option value="in_progress">In Progress</option>
        <option value="completed">Completed</option>
      </select>
      <div v-if="errors.status" class="text-red-500 text-xs mt-1">{{ errors.status }}</div>
    </div>
    <div class="mb-4">
      <label class="block text-gray-700">Due Date</label>
      <input v-model="form.due_date" type="date" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" />
      <div v-if="errors.due_date" class="text-red-500 text-xs mt-1">{{ errors.due_date }}</div>
    </div>
    <div class="mb-4">
      <label class="block text-gray-700">Category</label>
      <select v-model="form.category_id" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
        <option value="">Select Category</option>
        <option v-for="category in categories" :key="category.id" :value="category.id">{{ category.name }}</option>
      </select>
      <div v-if="errors.category_id" class="text-red-500 text-xs mt-1">{{ errors.category_id }}</div>
    </div>
    <div class="flex justify-end">
      <button type="submit" :disabled="loading" class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700">
        Create Task
      </button>
    </div>
    <div v-if="formError" class="text-red-500 text-xs mt-2">{{ formError }}</div>
  </form>
</template>

<script setup>
import { useTaskForm } from '@/composables/useTaskForm'

/**
 * Component props
 * @type {Object}
 * @property {Array} categories - List of available categories for task assignment
 */
const props = defineProps({
  categories: Array
})

/**
 * Component emits
 * @type {Object}
 * @property {Function} task-created - Emitted when a task is successfully created
 */
const emit = defineEmits(['task-created'])

// Initialize form management using the composable
const { form, errors, formError, loading, submitForm } = useTaskForm()

/**
 * Handles form submission
 * Attempts to create a task and emits success event if successful
 * 
 * @async
 * @function handleSubmit
 * @example
 * ```vue
 * <form @submit.prevent="handleSubmit">
 * ```
 */
const handleSubmit = async () => {
  try {
    await submitForm()
    emit('task-created')
  } catch (error) {
    // Error is already handled in the composable
  }
}
</script> 