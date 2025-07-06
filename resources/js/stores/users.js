import { defineStore } from 'pinia'
import { userService } from '@/services/api'

// Debug: Check if userService is imported correctly
console.log('🔍 [Users Store] userService import check:', userService)
console.log('🔍 [Users Store] userService.getUsers type:', typeof userService?.getUsers)

export const useUsersStore = defineStore('users', {
  state: () => ({
    users: [],
    filteredUsers: [],
    paginatedUsers: [],
    totalUsers: 0,
    activeUsers: 0,
    inactiveUsers: 0,
    newUsers: 0,
    loading: false,
    error: null,
    filters: {
      search: '',
      role: '',
      status: '',
      sortBy: 'name',
      sortOrder: 'asc'
    },
    pagination: {
      currentPage: 1,
      perPage: 10,
      lastPage: 1,
      total: 0
    },
    stats: {
      totalGrowth: 0,
      activeGrowth: 0,
      inactiveGrowth: 0,
      newGrowth: 0
    },
    availableRoles: [
      { value: 'admin', label: 'Administrator' },
      { value: 'manager', label: 'Manager' },
      { value: 'user', label: 'User' },
      { value: 'guest', label: 'Guest' }
    ]
  }),

  getters: {
    hasActiveFilters: (state) => {
      return !!(state.filters.search || state.filters.role || state.filters.status)
    }
  },

  actions: {
    async fetchUsers(params = {}) {
      this.loading = true
      this.error = null
      
      try {
        console.log('🔄 Fetching users from API...', { 
          baseURL: '/api', 
          endpoint: '/users', 
          fullURL: `${window.location.origin}/api/users`,
          params 
        })
        
        console.log('🔍 [Users Store] About to call userService.getUsers with params:', params)
        console.log('🔍 [Users Store] userService object:', userService)
        console.log('🔍 [Users Store] userService.getUsers function:', userService.getUsers)
        
        const response = await userService.getUsers(params)
        
        console.log('✅ Users API Response:', response)
        console.log('📊 Response structure:', {
          hasData: !!response?.data,
          dataLength: response?.data?.length || 0,
          hasMeta: !!response?.meta,
          responseKeys: Object.keys(response || {})
        })
        
        if (response && response.data) {
          this.users = response.data
          this.pagination = response.pagination || {
            total: response.data.length,
            current_page: 1,
            per_page: response.data.length,
            last_page: 1
          }
          
          // Update statistics from response meta
          if (response.meta) {
            this.totalUsers = response.meta.total || response.data.length
            this.activeUsers = response.meta.active_users || 0
            this.inactiveUsers = response.meta.inactive_users || 0
            this.newUsers = response.meta.new_users || 0
            this.stats = response.meta.stats || {}
            
            console.log('📈 Statistics updated:', {
              total: this.totalUsers,
              active: this.activeUsers,
              inactive: this.inactiveUsers,
              new: this.newUsers
            })
          }
          
          console.log('📊 Users data set:', { count: this.users.length, users: this.users })
        } else if (Array.isArray(response)) {
          // Direct array response
          this.users = response
          this.pagination = {
            total: response.length,
            current_page: 1,
            per_page: response.length,
            last_page: 1
          }
          console.log('📊 Users data set (direct array):', { count: this.users.length, users: this.users })
        } else {
          console.warn('⚠️ Unexpected response format:', response)
          this.users = []
        }
        
      } catch (error) {
        console.error('❌ Error fetching users:', error)
        console.error('❌ Error details:', {
          message: error.message,
          status: error.response?.status,
          data: error.response?.data,
          config: error.config
        })
        
        this.error = error.response?.data?.message || error.message || 'Failed to fetch users'
        this.users = []
        
        // Don't use mock data - show real error
        throw error
      } finally {
        this.loading = false
      }
    },

    async fetchUser(id) {
      if (!id) {
        console.error('❌ fetchUser: ID is required')
        return null
      }
      
      this.loading = true
      this.error = null
      
      try {
        console.log('🔄 Fetching user from API...', { id })
        
        const response = await userService.getUser(id)
        
        console.log('✅ User API Response:', response)
        
        const user = response?.data || response
        
        if (user && user.id) {
          // Update user in store
          const existingIndex = this.users.findIndex(u => u.id === user.id)
          if (existingIndex !== -1) {
            this.users[existingIndex] = user
          } else {
            this.users.push(user)
          }
          
          console.log('👤 User data updated:', user)
          return user
        } else {
          console.warn('⚠️ Invalid user response:', response)
          return null
        }
        
      } catch (error) {
        console.error('❌ Error fetching user:', error)
        console.error('❌ Error details:', {
          message: error.message,
          status: error.response?.status,
          data: error.response?.data
        })
        
        this.error = error.response?.data?.message || error.message || 'Failed to fetch user'
        return null
      } finally {
        this.loading = false
      }
    },

    async createUser(userData) {
      try {
        const response = await userService.createUser(userData)
        await this.fetchUsers()
        return response.data || response
      } catch (error) {
        this.error = error.response?.data?.message || 'Failed to create user'
        throw error
      }
    },

    async updateUser(id, userData) {
      try {
        const response = await userService.updateUser(id, userData)
        await this.fetchUsers()
        return response.data || response
      } catch (error) {
        this.error = error.response?.data?.message || 'Failed to update user'
        throw error
      }
    },

    async deleteUser(id) {
      try {
        await userService.deleteUser(id)
        await this.fetchUsers()
      } catch (error) {
        this.error = error.response?.data?.message || 'Failed to delete user'
        throw error
      }
    },

    async bulkUpdateUsers({ userIds, action, ...data }) {
      try {
        await userService.bulkUpdateUsers({
          user_ids: userIds,
          updates: {
            ...data
          }
        })
        await this.fetchUsers()
      } catch (error) {
        this.error = error.response?.data?.message || 'Failed to perform bulk action'
        throw error
      }
    },

    async exportUsers() {
      try {
        await userService.exportUsers(this.filters)
      } catch (error) {
        this.error = error.response?.data?.message || 'Failed to export users'
        throw error
      }
    },

    setFilters(filters) {
      this.filters = {
        ...this.filters,
        ...filters
      }
    },

    clearFilters() {
      this.filters = {
        search: '',
        role: '',
        status: '',
        sortBy: 'name',
        sortOrder: 'asc'
      }
    },

    setPage(page) {
      this.pagination.currentPage = page
    },

    setPerPage(perPage) {
      this.pagination.perPage = perPage
    },

    clearError() {
      this.error = null
    },

    async sendEmailToUser(userId, emailData) {
      try {
        const response = await userService.sendEmailToUser(userId, emailData)
        return response.data || response
      } catch (error) {
        this.error = error.response?.data?.message || 'Failed to send email'
        throw error
      }
    },

    async resetUserPassword(userId) {
      try {
        const response = await userService.resetUserPassword(userId)
        return response.data || response
      } catch (error) {
        this.error = error.response?.data?.message || 'Failed to reset password'
        throw error
      }
    },

    async getUserLoginHistory(userId) {
      try {
        const response = await userService.getUserLoginHistory(userId)
        return response.data || response
      } catch (error) {
        this.error = error.response?.data?.message || 'Failed to fetch login history'
        throw error
      }
    },

    async suspendUser(userId, reason) {
      try {
        const response = await userService.updateUser(userId, { 
          status: 'suspended',
          admin_notes: `Suspended: ${reason} (${new Date().toLocaleString()})`
        })
        await this.fetchUsers()
        return response.data || response
      } catch (error) {
        this.error = error.response?.data?.message || 'Failed to suspend user'
        throw error
      }
    },

    async activateUser(userId) {
      try {
        const response = await userService.updateUser(userId, { 
          status: 'active',
          admin_notes: `Reactivated: ${new Date().toLocaleString()}`
        })
        await this.fetchUsers()
        return response.data || response
      } catch (error) {
        this.error = error.response?.data?.message || 'Failed to activate user'
        throw error
      }
    }
  }
}) 