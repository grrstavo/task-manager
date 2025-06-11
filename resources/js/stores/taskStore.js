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
        getTasks: (state) => state.tasks,
        getCategories: (state) => state.categories,
        isLoading: (state) => state.loading,
        getFilters: (state) => state.filters,
        getPagination: (state) => state.pagination,
        filteredTasks: (state) => state.tasks,
        hasFilters: (state) => 
            state.filters.status || 
            state.filters.category_id || 
            state.filters.search ||
            state.filters.due
    },

    actions: {
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

        async fetchCategories() {
            try {
                const response = await axios.get('/api/v1/categories')
                this.categories = response.data.data
            } catch (error) {
                console.error('Error fetching categories:', error)
                this.error = error.response?.data?.message || 'Error fetching categories'
            }
        },

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

        setFilter(key, value) {
            this.filters[key] = value
            this.pagination.current_page = 1
            this.fetchTasks()
        },

        setSearch(value) {
            this.filters.search = value
            this.pagination.current_page = 1
            this.fetchTasks()
        },

        setPage(page) {
            this.pagination.current_page = page
            this.fetchTasks()
        },

        resetFilters() {
            this.filters = {
                status: '',
                category_id: '',
                search: '',
                due: ''
            }
            this.pagination.current_page = 1
            this.fetchTasks()
        }
    }
})
