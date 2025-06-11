import { ref, computed } from 'vue'
import { useTaskStore } from '@/stores/taskStore'

/**
 * Task Form Management Composable
 * 
 * Provides form state management, validation, and submission handling for task forms.
 * This composable encapsulates all the logic needed for creating and validating tasks.
 * 
 * @example
 * ```js
 * const {
 *   form,
 *   errors,
 *   formError,
 *   loading,
 *   submitForm,
 *   resetForm
 * } = useTaskForm()
 * 
 * // Handle form submission
 * try {
 *   await submitForm()
 *   // Handle success
 * } catch (error) {
 *   // Error handling is done by the composable
 * }
 * ```
 * 
 * @returns {Object} An object containing:
 * @returns {Ref<Object>} form - Reactive form data with task fields
 * @returns {Ref<Object>} errors - Validation errors for each field
 * @returns {Ref<string>} formError - Global form error message
 * @returns {ComputedRef<boolean>} loading - Whether the form is submitting
 * @returns {Function} submitForm - Method to submit the form
 * @returns {Function} resetForm - Method to reset the form
 */
export function useTaskForm() {
    const store = useTaskStore()
    
    /**
     * Form data structure
     * @type {Ref<Object>}
     * @property {string} title - Task title
     * @property {string} description - Task description
     * @property {string} status - Task status (pending, in_progress, completed)
     * @property {string} due_date - Task due date (YYYY-MM-DD format)
     * @property {string|number} category_id - ID of the associated category
     */
    const form = ref({
        title: '',
        description: '',
        status: 'pending',
        due_date: '',
        category_id: ''
    })

    /**
     * Validation errors for each form field
     * @type {Ref<Object>}
     */
    const errors = ref({})

    /**
     * Global form error message
     * @type {Ref<string>}
     */
    const formError = ref('')

    const touched = ref({})

    // Mark field as touched when user interacts with it
    const touch = (field) => {
        touched.value[field] = true
        validateField(field)
    }

    // Validate a single field
    const validateField = (field) => {
        errors.value[field] = ''

        switch (field) {
            case 'title':
                if (!form.value.title.trim()) {
                    errors.value.title = 'Title is required'
                } else if (form.value.title.length < 3) {
                    errors.value.title = 'Title must be at least 3 characters'
                } else if (form.value.title.length > 255) {
                    errors.value.title = 'Title cannot be longer than 255 characters'
                }
                break

            case 'status':
                if (!form.value.status) {
                    errors.value.status = 'Status is required'
                } else if (!['pending', 'in_progress', 'completed'].includes(form.value.status)) {
                    errors.value.status = 'Invalid status'
                }
                break

            case 'due_date':
                if (form.value.due_date) {
                    const today = new Date()
                    today.setHours(0, 0, 0, 0)
                    const dueDate = new Date(form.value.due_date)
                    if (dueDate < today) {
                        errors.value.due_date = 'Due date cannot be in the past'
                    }
                }
                break
        }
    }

    // Validate all fields
    const validateForm = () => {
        ['title', 'status', 'due_date'].forEach(field => {
            touch(field)
        })
        return Object.keys(errors.value).length === 0
    }

    /**
     * Resets the form to its initial state
     * Clears all form data, validation errors, and global error message
     * 
     * @example
     * ```js
     * resetForm() // Resets all form fields and errors
     * ```
     */
    const resetForm = () => {
        form.value = {
            title: '',
            description: '',
            status: 'pending',
            due_date: '',
            category_id: ''
        }
        errors.value = {}
        formError.value = ''
        touched.value = {}
    }

    /**
     * Submits the form data to create a new task
     * Handles validation errors and global form errors automatically
     * 
     * @async
     * @returns {Promise<Object>} The created task data
     * @throws {Error} If the submission fails (error is handled internally)
     * 
     * @example
     * ```js
     * try {
     *   const task = await submitForm()
     *   console.log('Task created:', task)
     * } catch (error) {
     *   // Validation errors are already handled
     * }
     * ```
     */
    const submitForm = async () => {
        if (!validateForm()) {
            formError.value = 'Please fix the errors before submitting'
            return
        }

        errors.value = {}
        formError.value = ''
        
        try {
            const result = await store.createTask(form.value)
            resetForm()
            return result
        } catch (error) {
            if (error.response?.data?.errors) {
                errors.value = error.response.data.errors
            } else {
                formError.value = error.message || 'An error occurred'
            }
            throw error
        }
    }

    // Computed property to check if form is valid
    const isValid = computed(() => {
        return form.value.title.trim() !== '' && 
               form.value.status !== '' &&
               (!form.value.due_date || new Date(form.value.due_date) >= new Date().setHours(0,0,0,0))
    })

    return {
        // Form state
        form,
        errors,
        formError,
        touched,
        touch,
        isValid,
        
        // Methods
        resetForm,
        submitForm,
        
        // Store state
        loading: computed(() => store.loading)
    }
} 