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
            search: ''
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
            state.filters.search
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
                this.pagination = {
                    current_page: response.data.current_page,
                    last_page: response.data.last_page,
                    per_page: response.data.per_page,
                    total: response.data.total
                }
            } catch (error) {
                console.error('Error fetching tasks:', error)
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
            }
        },

        setFilter(key, value) {
            this.filters[key] = value
            this.pagination.current_page = 1 // Reset to first page when filter changes
            this.fetchTasks()
        },

        setSearch: debounce(function(value) {
            this.filters.search = value
            this.pagination.current_page = 1
            this.fetchTasks()
        }, 300),

        setPage(page) {
            this.pagination.current_page = page
            this.fetchTasks()
        },

        resetFilters() {
            this.filters = {
                status: '',
                category_id: '',
                search: ''
            }
            this.pagination.current_page = 1
            this.fetchTasks()
        }
    }
})
