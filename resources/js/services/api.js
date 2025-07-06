import axios from 'axios'
import { useAuthStore } from '@/stores/auth'

// Get API base URL - always use relative path for Ubuntu development
const getApiBaseUrl = () => {
  return '/api'
}

// Create axios instance
const api = axios.create({
  baseURL: getApiBaseUrl(),
  timeout: 10000,
  withCredentials: true, // Important for Sanctum
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json',
    'X-Requested-With': 'XMLHttpRequest' // Important for Laravel
  }
})

// Debug: Log the API base URL being used
console.log('🌐 [API Service] Using base URL:', getApiBaseUrl())

// Request interceptor
api.interceptors.request.use(
  (config) => {
    const authStore = useAuthStore()
    
    // Add auth token to requests
    if (authStore.token) {
      config.headers.Authorization = `Bearer ${authStore.token}`
    }
    
    // Add CSRF token for Laravel
    const token = document.querySelector('meta[name="csrf-token"]')
    if (token) {
      config.headers['X-CSRF-TOKEN'] = token.getAttribute('content')
    }
    
    // Add referrer header for Sanctum
    if (import.meta.env.DEV && window.location.hostname === 'localhost') {
      config.headers['Referer'] = 'http://localhost:5173'
    }
    
    return config
  },
  (error) => {
    console.error('🔐 [API Interceptor] Request error:', error)
    return Promise.reject(error)
  }
)

// Response interceptor
api.interceptors.response.use(
  (response) => {
    return response
  },
  async (error) => {
    const authStore = useAuthStore()
    const originalRequest = error.config
    
    // Handle 401 Unauthorized
    if (error.response?.status === 401 && !originalRequest._retry) {
      originalRequest._retry = true
      
      // Check if this is a login request (don't try to refresh for login failures)
      if (originalRequest.url?.includes('/auth/login')) {
        return Promise.reject(error)
      }
      
      try {
        // Try to refresh token
        console.log('401 error detected, attempting token refresh');
        await authStore.refreshTokenAction()
        
        // Retry original request
        if (authStore.token) {
          originalRequest.headers.Authorization = `Bearer ${authStore.token}`
          console.log('Token refreshed, retrying original request');
          return api(originalRequest)
        }
      } catch (refreshError) {
        // Refresh failed, logout user
        console.error('Token refresh failed, logging out user');
        await authStore.logout()
        if (typeof window !== 'undefined') {
          window.location.href = '/login'
        }
        return Promise.reject(refreshError)
      }
    }
    
    // Handle 403 Forbidden
    if (error.response?.status === 403) {
      console.error('Access forbidden:', error.response.data)
    }
    
    // Handle 422 Validation errors
    if (error.response?.status === 422) {
      return Promise.reject(error)
    }
    
    // Handle 500 Server errors
    if (error.response?.status >= 500) {
      console.error('Server error:', error.response.data)
    }
    
    return Promise.reject(error)
  }
)

// API endpoints
export const endpoints = {
  // Auth
  auth: {
    login: '/auth/login',
    register: '/auth/register',
    logout: '/auth/logout',
    refresh: '/auth/refresh',
    forgotPassword: '/auth/forgot-password',
    resetPassword: '/auth/reset-password',
    profile: '/auth/profile'
  },
  
  // Users
  users: {
    list: '/users',
    create: '/users',
    show: (id) => `/users/${id}`,
    update: (id) => `/users/${id}`,
    delete: (id) => `/users/${id}`,
    bulkUpdate: '/users/bulk-update',
    export: '/users/export'
  },
  
  // Roles
  roles: {
    list: '/roles',
    create: '/roles',
    show: (id) => `/roles/${id}`,
    update: (id) => `/roles/${id}`,
    delete: (id) => `/roles/${id}`,
    permissions: (id) => `/roles/${id}/permissions`,
    assignPermissions: (id) => `/roles/${id}/permissions/assign`
  },
  
  // Permissions
  permissions: {
    list: '/permissions',
    create: '/permissions',
    show: (id) => `/permissions/${id}`,
    update: (id) => `/permissions/${id}`,
    delete: (id) => `/permissions/${id}`,
    modules: '/permissions/modules'
  },
  
  // Dashboard
  dashboard: {
    stats: '/dashboard/stats',
    activity: '/dashboard/activity',
    systemStatus: '/dashboard/system-status',
    performance: '/dashboard/performance'
  },
  
  // Activity
  activity: {
    list: '/activity',
    export: '/activity/export'
  },
  
  // Logs
  logs: {
    list: '/logs',
    download: '/logs/download'
  },
  
  // Notifications
  notifications: {
    list: '/notifications',
    markAsRead: (id) => `/notifications/${id}/read`,
    markAllAsRead: '/notifications/mark-all-read'
  },
  
  // Settings
  settings: {
    general: '/settings/general',
    security: '/settings/security',
    notifications: '/settings/notifications'
  }
}

// API service methods
export const apiService = {
  // Generic CRUD operations
  async get(endpoint, params = {}) {
    console.log('🔧 [apiService] GET request:', { endpoint, params, fullURL: api.defaults.baseURL + endpoint })
    
    try {
      const response = await api.get(endpoint, { params })
      console.log('🔧 [apiService] GET response:', response.data)
      return response.data
    } catch (error) {
      console.error('🔧 [apiService] GET error:', error)
      throw error
    }
  },
  
  async post(endpoint, data = {}) {
    const response = await api.post(endpoint, data)
    return response.data
  },
  
  async put(endpoint, data = {}) {
    const response = await api.put(endpoint, data)
    return response.data
  },
  
  async patch(endpoint, data = {}) {
    const response = await api.patch(endpoint, data)
    return response.data
  },
  
  async delete(endpoint) {
    const response = await api.delete(endpoint)
    return response.data
  },
  
  // File upload
  async upload(endpoint, file, onProgress = null) {
    const formData = new FormData()
    formData.append('file', file)
    
    const response = await api.post(endpoint, formData, {
      headers: {
        'Content-Type': 'multipart/form-data'
      },
      onUploadProgress: onProgress
    })
    
    return response.data
  },
  
  // File download
  async download(endpoint, filename = null) {
    const response = await api.get(endpoint, {
      responseType: 'blob'
    })
    
    const url = window.URL.createObjectURL(new Blob([response.data]))
    const link = document.createElement('a')
    link.href = url
    link.setAttribute('download', filename || 'download')
    document.body.appendChild(link)
    link.click()
    link.remove()
    window.URL.revokeObjectURL(url)
  }
}

// Auth service
export const authService = {
  async login(credentials) {
    return apiService.post(endpoints.auth.login, credentials)
  },
  
  async register(userData) {
    return apiService.post(endpoints.auth.register, userData)
  },
  
  async logout() {
    return apiService.post(endpoints.auth.logout)
  },
  
  async refresh(refreshToken) {
    return apiService.post(endpoints.auth.refresh, { refresh_token: refreshToken })
  },
  
  async forgotPassword(email) {
    return apiService.post(endpoints.auth.forgotPassword, { email })
  },
  
  async resetPassword(data) {
    return apiService.post(endpoints.auth.resetPassword, data)
  },
  
  async getProfile() {
    return apiService.get(endpoints.auth.profile)
  },
  
  async updateProfile(data) {
    return apiService.put(endpoints.auth.profile, data)
  }
}

// User service
export const userService = {
  async getUsers(params = {}) {
    try {
      const result = await apiService.get(endpoints.users.list, params)
      return result
    } catch (error) {
      console.error('Failed to fetch users:', error)
      throw error
    }
  },
  
  async getUser(id) {
    return apiService.get(endpoints.users.show(id))
  },
  
  async createUser(data) {
    // Check if data is FormData (file upload)
    if (data instanceof FormData) {
      // Use custom axios call for multipart/form-data
      const response = await api.post(endpoints.users.create, data, {
        headers: {
          'Content-Type': 'multipart/form-data'
        }
      })
      return response.data
    } else {
      // Regular JSON data
      const response = await apiService.post(endpoints.users.create, data)
      return response
    }
  },
  
  async updateUser(id, data) {
    // Check if data is FormData (file upload)
    if (data instanceof FormData) {
      // Use custom axios call for multipart/form-data
      const response = await api.put(endpoints.users.update(id), data, {
        headers: {
          'Content-Type': 'multipart/form-data'
        }
      })
      return response.data
    } else {
      // Regular JSON data
      return apiService.put(endpoints.users.update(id), data)
    }
  },
  
  async deleteUser(id) {
    return apiService.delete(endpoints.users.delete(id))
  },
  
  async bulkUpdateUsers(data) {
    return apiService.post(endpoints.users.bulkUpdate, data)
  },
  
  async exportUsers(params = {}) {
    return apiService.download(endpoints.users.export, 'users.xlsx')
  },

  async sendEmailToUser(userId, emailData) {
    return apiService.post(`/users/${userId}/send-email`, emailData)
  },

  async resetUserPassword(userId) {
    return apiService.post(`/users/${userId}/reset-password`)
  },

  async getUserLoginHistory(userId) {
    return apiService.get(`/users/${userId}/login-history`)
  }
}

// Role service
export const roleService = {
  async getRoles(params = {}) {
    return apiService.get(endpoints.roles.list, params)
  },
  
  async getRole(id) {
    return apiService.get(endpoints.roles.show(id))
  },
  
  async createRole(data) {
    return apiService.post(endpoints.roles.create, data)
  },
  
  async updateRole(id, data) {
    return apiService.put(endpoints.roles.update(id), data)
  },
  
  async deleteRole(id) {
    return apiService.delete(endpoints.roles.delete(id))
  },
  
  async getRolePermissions(id) {
    return apiService.get(endpoints.roles.permissions(id))
  },
  
  async assignPermissions(id, permissions) {
    return apiService.post(endpoints.roles.assignPermissions(id), { permissions })
  },
  
  async getRoleUsers(id) {
    return apiService.get(`/roles/${id}/users`)
  }
}

// Permission service
export const permissionService = {
  async getPermissions(params = {}) {
    return apiService.get(endpoints.permissions.list, params)
  },
  
  async getPermission(id) {
    return apiService.get(endpoints.permissions.show(id))
  },
  
  async createPermission(data) {
    return apiService.post(endpoints.permissions.create, data)
  },
  
  async updatePermission(id, data) {
    return apiService.put(endpoints.permissions.update(id), data)
  },
  
  async deletePermission(id) {
    return apiService.delete(endpoints.permissions.delete(id))
  },
  
  async getModules() {
    return apiService.get(endpoints.permissions.modules)
  },
  
  async getStatistics() {
    return apiService.get('/permissions/statistics')
  },

  async getAuditLogs(params = {}) {
    // Mock endpoint for now - replace with real endpoint later
    return Promise.resolve({ 
      data: [
        {
          id: 1,
          user: 'Admin User',
          action: 'permission.assigned',
          description: 'Assigned "users.create" permission to Admin role',
          created_at: new Date().toISOString()
        }
      ]
    })
  }
}

// Dashboard service
export const dashboardService = {
  async getStats() {
    return apiService.get(endpoints.dashboard.stats)
  },
  
  async getActivity(params = {}) {
    return apiService.get(endpoints.dashboard.activity, params)
  },
  
  async getSystemStatus() {
    return apiService.get(endpoints.dashboard.systemStatus)
  },
  
  async getPerformance(params = {}) {
    return apiService.get(endpoints.dashboard.performance, params)
  }
}

// Activity service
export const activityService = {
  async getActivity(params = {}) {
    return apiService.get(endpoints.activity.list, params)
  },
  
  async exportActivity(params = {}) {
    return apiService.download(endpoints.activity.export, 'activity.xlsx')
  }
}

// Log service
export const logService = {
  async getLogs(params = {}) {
    return apiService.get(endpoints.logs.list, params)
  },
  
  async downloadLogs(params = {}) {
    return apiService.download(endpoints.logs.download, 'logs.zip')
  }
}

// Notification service
export const notificationService = {
  async getNotifications(params = {}) {
    return apiService.get(endpoints.notifications.list, params)
  },
  
  async markAsRead(id) {
    return apiService.post(endpoints.notifications.markAsRead(id))
  },
  
  async markAllAsRead() {
    return apiService.post(endpoints.notifications.markAllAsRead)
  }
}

// Settings service
export const settingsService = {
  async getGeneralSettings() {
    return apiService.get(endpoints.settings.general)
  },
  
  async updateGeneralSettings(data) {
    return apiService.put(endpoints.settings.general, data)
  },
  
  async getSecuritySettings() {
    return apiService.get(endpoints.settings.security)
  },
  
  async updateSecuritySettings(data) {
    return apiService.put(endpoints.settings.security, data)
  },
  
  async getNotificationSettings() {
    return apiService.get(endpoints.settings.notifications)
  },
  
  async updateNotificationSettings(data) {
    return apiService.put(endpoints.settings.notifications, data)
  }
}

// Export default API instance
export default api 