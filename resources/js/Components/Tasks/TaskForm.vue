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
  <form @submit.prevent="handleSubmit" novalidate>
    <div class="mb-4">
      <label class="block text-gray-700">Title</label>
      <input 
        v-model="form.title" 
        type="text" 
        @blur="touch('title')"
        :class="[
          'w-full rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500',
          (touched.title && errors.title) ? 'border-red-300' : 'border-gray-300'
        ]"
        required 
      />
      <div v-if="touched.title && errors.title" class="text-red-500 text-xs mt-1">{{ errors.title }}</div>
    </div>

    <div class="mb-4">
      <label class="block text-gray-700">Description</label>
      <textarea 
        v-model="form.description" 
        :class="[
          'w-full rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500',
          'border-gray-300'
        ]"
      ></textarea>
    </div>

    <div class="mb-4">
      <label class="block text-gray-700">Status</label>
      <select 
        v-model="form.status" 
        @blur="touch('status')"
        :class="[
          'w-full rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500',
          (touched.status && errors.status) ? 'border-red-300' : 'border-gray-300'
        ]"
        required
      >
        <option value="pending">Pending</option>
        <option value="in_progress">In Progress</option>
        <option value="completed">Completed</option>
      </select>
      <div v-if="touched.status && errors.status" class="text-red-500 text-xs mt-1">{{ errors.status }}</div>
    </div>

    <div class="mb-4">
      <label class="block text-gray-700">Due Date</label>
      <input 
        v-model="form.due_date" 
        type="date" 
        @blur="touch('due_date')"
        :class="[
          'w-full rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500',
          (touched.due_date && errors.due_date) ? 'border-red-300' : 'border-gray-300'
        ]"
      />
      <div v-if="touched.due_date && errors.due_date" class="text-red-500 text-xs mt-1">{{ errors.due_date }}</div>
    </div>

    <div class="mb-4">
      <label class="block text-gray-700">Category</label>
      <select 
        v-model="form.category_id" 
        :class="[
          'w-full rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500',
          'border-gray-300'
        ]"
      >
        <option value="">Select Category</option>
        <option v-for="category in categories" :key="category.id" :value="category.id">
          {{ category.name }}
        </option>
      </select>
    </div>

    <div class="flex justify-end">
      <button 
        type="submit" 
        :disabled="loading || !isValid" 
        :class="[
          'px-4 py-2 rounded text-white',
          loading || !isValid ? 'bg-indigo-400 cursor-not-allowed' : 'bg-indigo-600 hover:bg-indigo-700'
        ]"
      >
        {{ loading ? 'Creating...' : 'Create Task' }}
      </button>
    </div>

    <div v-if="formError" class="mt-4 p-4 bg-red-50 border-l-4 border-red-400 text-red-700">
      {{ formError }}
    </div>
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
  categories: {
    type: Array,
    required: true
  }
})

/**
 * Component emits
 * @type {Object}
 * @property {Function} task-created - Emitted when a task is successfully created
 */
const emit = defineEmits(['task-created'])

// Initialize form management using the composable
const { 
  form, 
  errors, 
  formError, 
  touched,
  touch,
  isValid,
  loading,
  submitForm 
} = useTaskForm()

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