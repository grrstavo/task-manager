import { onMounted } from 'vue'
import { useTaskStore } from '@/stores/taskStore'
import { storeToRefs } from 'pinia'

/**
 * Task List Management Composable
 * 
 * Provides functionality for managing and displaying a list of tasks.
 * Handles task data fetching, pagination, and task-related utilities.
 * 
 * @example
 * ```js
 * const {
 *   tasks,
 *   categories,
 *   loading,
 *   pagination,
 *   error,
 *   setPage,
 *   isTaskOverdue,
 *   formatDueDate
 * } = useTaskList()
 * 
 * // Check if a task is overdue
 * const overdue = isTaskOverdue(task)
 * 
 * // Format a due date
 * const formattedDate = formatDueDate(task.due_date)
 * ```
 * 
 * @returns {Object} An object containing:
 * @returns {Ref<Array>} tasks - List of tasks
 * @returns {Ref<Array>} categories - List of categories
 * @returns {Ref<boolean>} loading - Loading state
 * @returns {Ref<Object>} pagination - Pagination information
 * @returns {Ref<string|null>} error - Error message if any
 * @returns {Function} setPage - Method to change the current page
 * @returns {Function} isTaskOverdue - Method to check if a task is overdue
 * @returns {Function} formatDueDate - Method to format a due date
 * @returns {Function} initializeData - Method to fetch initial data
 */
export function useTaskList() {
    const store = useTaskStore()
    const { tasks, categories, loading, pagination, error } = storeToRefs(store)

    /**
     * Initializes task and category data by fetching from the API
     * Should be called when the component mounts or when data needs to be refreshed
     * 
     * @async
     * @returns {Promise<void>}
     * 
     * @example
     * ```js
     * onMounted(async () => {
     *   await initializeData()
     * })
     * ```
     */
    const initializeData = async () => {
        await Promise.all([
            store.fetchTasks(),
            store.fetchCategories()
        ])
    }

    /**
     * Changes the current page and fetches the corresponding tasks
     * 
     * @param {number} page - The page number to set
     * @returns {Promise<void>}
     * 
     * @example
     * ```js
     * // Go to page 2
     * await setPage(2)
     * ```
     */
    const setPage = (page) => {
        store.setPage(page)
    }

    /**
     * Checks if a task is overdue based on its due date and status
     * A task is considered overdue if:
     * - It has a due date
     * - The due date is in the past
     * - The task is not completed
     * 
     * @param {Object} task - The task to check
     * @param {string} task.due_date - The task's due date
     * @param {string} task.status - The task's status
     * @returns {boolean} True if the task is overdue
     * 
     * @example
     * ```js
     * const task = { due_date: '2024-03-01', status: 'pending' }
     * const overdue = isTaskOverdue(task) // true if date is past
     * ```
     */
    const isTaskOverdue = (task) => {
        if (!task.due_date || task.status === 'completed') return false
        return new Date(task.due_date) < new Date()
    }

    /**
     * Formats a date string for display
     * Returns a localized date string or 'No due date' if no date is provided
     * 
     * @param {string|null} date - The date to format (YYYY-MM-DD)
     * @returns {string} Formatted date string
     * 
     * @example
     * ```js
     * const date = formatDueDate('2024-03-19') // '3/19/2024'
     * const noDate = formatDueDate(null) // 'No due date'
     * ```
     */
    const formatDueDate = (date) => {
        if (!date) return 'No due date'
        return new Date(date).toLocaleDateString()
    }

    /**
     * Deletes a task via the API and removes it from the local state
     * 
     * @param {number} taskId - The ID of the task to delete
     * @returns {Promise<void>}
     */
    const deleteTask = async (taskId) => {
        await store.deleteTask(taskId)
    }

    // Initialize data on component mount
    onMounted(() => {
        initializeData()
    })

    return {
        // State
        tasks,
        categories,
        loading,
        pagination,
        error,
        
        // Methods
        setPage,
        isTaskOverdue,
        formatDueDate,
        initializeData,
        deleteTask
    }
} 