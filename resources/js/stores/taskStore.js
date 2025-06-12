import { defineStore } from 'pinia'
import axios from 'axios'
import { debounce } from 'lodash'

export const useTaskStore = defineStore('tasks', {
    state: () => ({
        tasks: [],
        categories: [],
        loading: false,
        error: null,
        filters: {
            status: '',
            category_id: '',
            search: '',
            due: ''
        },
        pagination: {
            current_page: 1,
            last_page: 1,
            per_page: 10,
            total: 0
        }
    }),

    getters: {
        // Returns the list of tasks
        getTasks: (state) => state.tasks,
        // Returns the list of categories
        getCategories: (state) => state.categories,
        // Returns the loading state
        isLoading: (state) => state.loading,
        // Returns the current filters
        getFilters: (state) => state.filters,
        // Returns the pagination object
        getPagination: (state) => state.pagination,
        // Returns the filtered tasks (currently just all tasks)
        filteredTasks: (state) => state.tasks,
        // Returns true if any filter is active
        hasFilters: (state) => 
            state.filters.status || 
            state.filters.category_id || 
            state.filters.search ||
            state.filters.due
    },

    actions: {
        // Fetches tasks from the API with current filters and pagination
        async fetchTasks() {
            this.loading = true
            this.error = null
            
            try {
                const params = {
                    ...this.filters,
                    page: this.pagination.current_page,
                }
                
                const response = await axios.get('/api/v1/tasks', { params })
                this.tasks = response.data.data
                
                // Handle pagination data that comes as arrays
                const meta = response.data.meta
                this.pagination = {
                    current_page: Array.isArray(meta.current_page) ? meta.current_page[0] : meta.current_page,
                    last_page: Array.isArray(meta.last_page) ? meta.last_page[0] : meta.last_page,
                    per_page: Array.isArray(meta.per_page) ? meta.per_page[0] : meta.per_page,
                    total: Array.isArray(meta.total) ? meta.total[0] : meta.total
                }
            } catch (error) {
                console.error('Error fetching tasks:', error)
                this.error = error.response?.data?.message || 'Error fetching tasks'
            } finally {
                this.loading = false
            }
        },

        // Fetches categories from the API
        async fetchCategories() {
            try {
                const response = await axios.get('/api/v1/categories')
                this.categories = response.data.data
            } catch (error) {
                console.error('Error fetching categories:', error)
                this.error = error.response?.data?.message || 'Error fetching categories'
            }
        },

        // Creates a new task via the API and adds it to the local state
        async createTask(taskData) {
            this.loading = true
            this.error = null
            try {
                const response = await axios.post('/api/v1/tasks', taskData)
                this.tasks.unshift(response.data.data)
                return response.data
            } catch (error) {
                this.error = error.response?.data?.message || 'Failed to create task'
                throw error
            } finally {
                this.loading = false
            }
        },

        // Deletes a task via the API and removes it from the local state
        async deleteTask(taskId) {
            try {
                await axios.delete(`/api/v1/tasks/${taskId}`)
                // Remove the task from the local state
                this.tasks = this.tasks.filter(task => task.id !== taskId)
                // Update total in pagination
                this.pagination.total--
            } catch (error) {
                throw error.response?.data?.message || 'Failed to delete task'
            }
        },

        // Sets a filter value and fetches tasks
        setFilter(key, value) {
            this.filters[key] = value
            this.pagination.current_page = 1
            this.fetchTasks()
        },

        // Sets the search filter and fetches tasks
        setSearch(value) {
            this.filters.search = value
            this.pagination.current_page = 1
            this.fetchTasks()
        },

        // Sets the current page and fetches tasks
        setPage(page) {
            this.pagination.current_page = page
            this.fetchTasks()
        },

        // Resets all filters and fetches tasks
        resetFilters() {
            this.filters = {
                status: '',
                category_id: '',
                search: '',
                due: ''
            }
            this.pagination.current_page = 1
            this.fetchTasks()
        },
    }
})
