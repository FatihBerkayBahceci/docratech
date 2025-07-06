import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import { permissionService } from '@/services/api'

export const usePermissionsStore = defineStore('permissions', () => {
  // State
  const permissions = ref([])
  const currentPermission = ref(null)
  const modules = ref([])
  const loading = ref(false)
  const pagination = ref({
    currentPage: 1,
    perPage: 100,
    total: 0,
    lastPage: 1
  })

  // Enterprise features  
  const auditLogs = ref([])
  const statistics = ref({})
  const permissionTemplates = ref([])
  const filters = ref({
    search: '',
    module: null,
    type: null,
    status: null
  })
  const selectedPermissions = ref([])

  // Getters
  const filteredPermissions = computed(() => {
    let filtered = [...permissions.value]

    // Search filter
    if (filters.value.search) {
      const search = filters.value.search.toLowerCase()
      filtered = filtered.filter(permission =>
        permission.name.toLowerCase().includes(search) ||
        permission.key.toLowerCase().includes(search) ||
        permission.description.toLowerCase().includes(search)
      )
    }

    // Module filter
    if (filters.value.module) {
      filtered = filtered.filter(permission => permission.module === filters.value.module)
    }

    // Type filter
    if (filters.value.type) {
      filtered = filtered.filter(permission => permission.type === filters.value.type)
    }

    // Status filter
    if (filters.value.status) {
      filtered = filtered.filter(permission => permission.status === filters.value.status)
    }

    return filtered
  })

  const totalPermissions = computed(() => permissions.value.length)
  const activePermissions = computed(() => permissions.value.filter(p => p.status === 'active').length)
  const customPermissions = computed(() => permissions.value.filter(p => p.type === 'custom').length)
  const systemPermissions = computed(() => permissions.value.filter(p => p.type !== 'custom').length)

  const permissionsByModule = computed(() => {
    const grouped = {}
    permissions.value.forEach(permission => {
      if (!grouped[permission.module]) {
        grouped[permission.module] = []
      }
      grouped[permission.module].push(permission)
    })
    return grouped
  })

  const permissionsByType = computed(() => {
    const grouped = {}
    permissions.value.forEach(permission => {
      if (!grouped[permission.type]) {
        grouped[permission.type] = []
      }
      grouped[permission.type].push(permission)
    })
    return grouped
  })

  // Actions
  const fetchPermissions = async (params = {}) => {
    loading.value = true
    try {
      // Filter out null/empty values to prevent validation errors
      const cleanFilters = Object.fromEntries(
        Object.entries(filters.value).filter(([key, value]) => value !== null && value !== '' && value !== undefined)
      )
      
      const response = await permissionService.getPermissions({
        page: pagination.value.currentPage,
        per_page: pagination.value.perPage,
        ...cleanFilters,
        ...params
      })
      
      permissions.value = response.data
      pagination.value = {
        currentPage: response.current_page || 1,
        perPage: response.per_page || 10,
        total: response.total || 0,
        lastPage: response.last_page || 1
      }
      return response
    } catch (error) {
      console.error('Fetch permissions error:', error)
      throw error
    } finally {
      loading.value = false
    }
  }

  const fetchPermission = async (id) => {
    loading.value = true
    try {
      const response = await permissionService.getPermission(id)
      currentPermission.value = response.data
      return response
    } catch (error) {
      console.error('Fetch permission error:', error)
      throw error
    } finally {
      loading.value = false
    }
  }

  const createPermission = async (permissionData) => {
    loading.value = true
    try {
      const response = await permissionService.createPermission(permissionData)
      
      // Add new permission to list
      permissions.value.unshift(response.data)
      
      return response
    } catch (error) {
      console.error('Create permission error:', error)
      throw error
    } finally {
      loading.value = false
    }
  }

  const updatePermission = async (id, permissionData) => {
    loading.value = true
    try {
      const response = await permissionService.updatePermission(id, permissionData)
      
      // Update permission in list
      const index = permissions.value.findIndex(p => p.id === id)
      if (index !== -1) {
        permissions.value[index] = response.data
      }
      
      // Update current permission if it's the same
      if (currentPermission.value && currentPermission.value.id === id) {
        currentPermission.value = response.data
      }
      
      return response
    } catch (error) {
      console.error('Update permission error:', error)
      throw error
    } finally {
      loading.value = false
    }
  }

  const deletePermission = async (id) => {
    loading.value = true
    try {
      await permissionService.deletePermission(id)
      
      // Remove permission from list
      permissions.value = permissions.value.filter(p => p.id !== id)
      
      // Clear current permission if it's the same
      if (currentPermission.value && currentPermission.value.id === id) {
        currentPermission.value = null
      }
      
      return true
    } catch (error) {
      console.error('Delete permission error:', error)
      throw error
    } finally {
      loading.value = false
    }
  }

  const fetchModules = async () => {
    loading.value = true
    try {
      const response = await permissionService.getModules()
      modules.value = response.data
      return response
    } catch (error) {
      console.error('Fetch modules error:', error)
      throw error
    } finally {
      loading.value = false
    }
  }

  const fetchStatistics = async () => {
    loading.value = true
    try {
      const response = await permissionService.getStatistics()
      return response.data
    } catch (error) {
      console.error('Fetch statistics error:', error)
      throw error
    } finally {
      loading.value = false
    }
  }

  // Filter actions
  const setFilters = (newFilters) => {
    filters.value = { ...filters.value, ...newFilters }
    pagination.value.currentPage = 1
  }

  const clearFilters = () => {
    filters.value = {
      search: '',
      module: null,
      type: null,
      status: null
    }
    pagination.value.currentPage = 1
  }

  // Pagination actions
  const setPage = (page) => {
    pagination.value.currentPage = page
  }

  const setPerPage = (perPage) => {
    pagination.value.perPage = perPage
    pagination.value.currentPage = 1
  }

  // Selection actions
  const selectPermission = (permission) => {
    const index = selectedPermissions.value.findIndex(p => p.id === permission.id)
    if (index === -1) {
      selectedPermissions.value.push(permission)
    }
  }

  const deselectPermission = (permission) => {
    selectedPermissions.value = selectedPermissions.value.filter(p => p.id !== permission.id)
  }

  const selectAllPermissions = () => {
    selectedPermissions.value = [...filteredPermissions.value]
  }

  const deselectAllPermissions = () => {
    selectedPermissions.value = []
  }

  const togglePermissionSelection = (permission) => {
    const index = selectedPermissions.value.findIndex(p => p.id === permission.id)
    if (index === -1) {
      selectPermission(permission)
    } else {
      deselectPermission(permission)
    }
  }

  // Utility actions
  const getPermissionById = (id) => {
    return permissions.value.find(p => p.id === id)
  }

  const getPermissionByKey = (key) => {
    return permissions.value.find(p => p.key === key)
  }

  const getPermissionsByModule = (module) => {
    return permissions.value.filter(p => p.module === module)
  }

  const getPermissionsByType = (type) => {
    return permissions.value.filter(p => p.type === type)
  }

  const getPermissionsByStatus = (status) => {
    return permissions.value.filter(p => p.status === status)
  }

  const getModuleStats = () => {
    const stats = {}
    modules.value.forEach(module => {
      const modulePermissions = permissions.value.filter(p => p.module === module.name)
      stats[module.name] = {
        name: module.name,
        description: module.description,
        icon: module.icon,
        permissions: modulePermissions.length,
        roles: module.roles || 0,
        status: module.status || 'active'
      }
    })
    return Object.values(stats)
  }

  const validatePermissionKey = (key, excludeId = null) => {
    const existing = permissions.value.find(p => p.key === key && p.id !== excludeId)
    return !existing
  }

  const generatePermissionKey = (name, module) => {
    const baseKey = name.toLowerCase().replace(/[^a-z0-9]/g, '.')
    let key = `${module}.${baseKey}`
    let counter = 1
    
    while (!validatePermissionKey(key)) {
      key = `${module}.${baseKey}.${counter}`
      counter++
    }
    
    return key
  }

  // Reset store
  const reset = () => {
    permissions.value = []
    currentPermission.value = null
    modules.value = []
    loading.value = false
    pagination.value = {
      currentPage: 1,
      perPage: 10,
      total: 0,
      lastPage: 1
    }
    filters.value = {
      search: '',
      module: null,
      type: null,
      status: null
    }
    selectedPermissions.value = []
  }

  // Enterprise methods
  const fetchAuditLogs = async (params = {}) => {
    try {
      // Mock audit logs for now
      const mockLogs = [
        {
          id: 1,
          user: 'Admin User',
          action: 'permission.assigned',
          description: 'Assigned "users.create" permission to Admin role',
          created_at: new Date().toISOString()
        }
      ]
      if (params.offset) {
        auditLogs.value.push(...mockLogs)
      } else {
        auditLogs.value = mockLogs
      }
      return { data: mockLogs }
    } catch (error) {
      console.error('Fetch audit logs error:', error)
      throw error
    }
  }

  const getModulesWithPermissions = () => {
    const moduleGroups = {}
    permissions.value.forEach(permission => {
      if (!permission.module) return;
      if (!moduleGroups[permission.module]) {
        moduleGroups[permission.module] = {
          name: permission.module,
          display_name: formatModuleName(permission.module),
          description: getModuleDescription(permission.module),
          permissions: [],
          stats: {
            total: 0,
            active: 0,
            system: 0,
            custom: 0
          }
        }
      }
      moduleGroups[permission.module].permissions.push(permission)
      moduleGroups[permission.module].stats.total++
      if (permission.status === 'active') {
        moduleGroups[permission.module].stats.active++
      }
      if (permission.type === 'system') {
        moduleGroups[permission.module].stats.system++
      } else {
        moduleGroups[permission.module].stats.custom++
      }
    })
    return Object.values(moduleGroups)
  }

  const formatModuleName = (name) => {
    return name.charAt(0).toUpperCase() + name.slice(1).replace(/[_-]/g, ' ')
  }

  const getModuleDescription = (moduleName) => {
    const descriptions = {
      'users': 'User management and authentication',
      'roles': 'Role definitions and assignments', 
      'permissions': 'Permission management and control',
      'dashboard': 'Dashboard views and analytics',
      'activity': 'Activity tracking and monitoring',
      'settings': 'System settings and configuration',
      'support': 'Support and help desk',
      'appointments': 'Appointment scheduling and management',
      'medical_records': 'Medical records and patient data',
      'audit': 'Audit logs and compliance'
    }
    return descriptions[moduleName] || `${moduleName} module operations`
  }

  return {
    // State
    permissions,
    currentPermission,
    modules,
    loading,
    pagination,
    filters,
    selectedPermissions,
    auditLogs,
    statistics,
    permissionTemplates,
    
    // Getters
    filteredPermissions,
    totalPermissions,
    activePermissions,
    customPermissions,
    systemPermissions,
    permissionsByModule,
    permissionsByType,
    
    // Actions
    fetchPermissions,
    fetchPermission,
    createPermission,
    updatePermission,
    deletePermission,
    fetchModules,
    fetchStatistics,
    
    // Filter actions
    setFilters,
    clearFilters,
    
    // Pagination actions
    setPage,
    setPerPage,
    
    // Selection actions
    selectPermission,
    deselectPermission,
    selectAllPermissions,
    deselectAllPermissions,
    togglePermissionSelection,
    
    // Utility actions
    getPermissionById,
    getPermissionByKey,
    getPermissionsByModule,
    getPermissionsByType,
    getPermissionsByStatus,
    getModuleStats,
    validatePermissionKey,
    generatePermissionKey,
    
    // Enterprise actions
    fetchAuditLogs,
    getModulesWithPermissions,
    
    // Reset
    reset
  }
}) 