import { ref, watch } from 'vue'
import { useTaskStore } from '@/stores/taskStore'

/**
 * Task Filtering Composable
 * 
 * Provides reactive state and methods for filtering tasks by various criteria.
 * This composable handles the filter state management and synchronization with the task store.
 * 
 * @example
 * ```js
 * const {
 *   searchQuery,
 *   selectedStatus,
 *   selectedCategory,
 *   selectedDue,
 *   clearFilters,
 *   hasFilters
 * } = useTaskFilters()
 * ```
 * 
 * @returns {Object} An object containing:
 * @returns {Ref<string>} searchQuery - Reactive reference for search text
 * @returns {Ref<string>} selectedStatus - Reactive reference for selected task status
 * @returns {Ref<string|number>} selectedCategory - Reactive reference for selected category ID
 * @returns {Ref<string>} selectedDue - Reactive reference for due date filter
 * @returns {Function} clearFilters - Method to reset all filters to default values
 * @returns {Function} getCurrentFilters - Method to get current filter values
 * @returns {ComputedRef<boolean>} hasFilters - Whether any filters are currently active
 */
export function useTaskFilters() {
    const store = useTaskStore()
    
    // Filter state
    const searchQuery = ref('')
    const selectedStatus = ref('')
    const selectedCategory = ref('')
    const selectedDue = ref('')

    // Watch for filter changes and update store
    watch(searchQuery, (value) => {
        store.setFilter('search', value)
    })

    watch(selectedStatus, (value) => {
        store.setFilter('status', value)
    })

    watch(selectedCategory, (value) => {
        store.setFilter('category_id', value)
    })

    watch(selectedDue, (value) => {
        store.setFilter('due', value)
    })

    /**
     * Resets all filters to their default values and updates the store
     * This will trigger a new task fetch with no filters applied
     * 
     * @example
     * ```js
     * clearFilters() // Resets all filters to empty strings
     * ```
     */
    const clearFilters = () => {
        searchQuery.value = ''
        selectedStatus.value = ''
        selectedCategory.value = ''
        selectedDue.value = ''
        store.resetFilters()
    }

    /**
     * Returns the current state of all filters
     * Useful for saving filter state or debugging
     * 
     * @returns {Object} Current filter values
     * @returns {string} search - Current search query
     * @returns {string} status - Current status filter
     * @returns {string|number} category - Current category ID filter
     * @returns {string} due - Current due date filter
     * 
     * @example
     * ```js
     * const filters = getCurrentFilters()
     * console.log(filters) // { search: '', status: 'pending', ... }
     * ```
     */
    const getCurrentFilters = () => ({
        search: searchQuery.value,
        status: selectedStatus.value,
        category: selectedCategory.value,
        due: selectedDue.value
    })

    return {
        // Filter state
        searchQuery,
        selectedStatus,
        selectedCategory,
        selectedDue,
        
        // Methods
        clearFilters,
        getCurrentFilters,
        
        // Computed
        hasFilters: store.hasFilters
    }
} 