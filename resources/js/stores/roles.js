import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import { roleService } from '@/services/api'

export const useRolesStore = defineStore('roles', () => {
  // State
  const roles = ref([])
  const currentRole = ref(null)
  const loading = ref(false)
  const pagination = ref({
    currentPage: 1,
    perPage: 10,
    total: 0,
    lastPage: 1
  })
  const filters = ref({
    search: '',
    type: null,
    status: null
  })
  const selectedRoles = ref([])

  // Getters
  const filteredRoles = computed(() => {
    let filtered = [...roles.value]

    // Search filter
    if (filters.value.search) {
      const search = filters.value.search.toLowerCase()
      filtered = filtered.filter(role =>
        role.name.toLowerCase().includes(search) ||
        role.description.toLowerCase().includes(search)
      )
    }

    // Type filter
    if (filters.value.type) {
      filtered = filtered.filter(role => role.type === filters.value.type)
    }

    // Status filter
    if (filters.value.status) {
      filtered = filtered.filter(role => role.status === filters.value.status)
    }

    return filtered
  })

  const totalRoles = computed(() => roles.value.length)
  const activeRoles = computed(() => roles.value.filter(r => r.status === 'active').length)
  const customRoles = computed(() => roles.value.filter(r => r.type === 'custom').length)
  const systemRoles = computed(() => roles.value.filter(r => r.type === 'system').length)

  // Actions
  const fetchRoles = async (params = {}) => {
    loading.value = true
    console.log('🔄 [RoleStore] fetchRoles called with params:', params)
    
    try {
      const response = await roleService.getRoles({
        page: 1, // Always get first page for all roles
        per_page: 100, // Get all roles
        ...filters.value,
        ...params
      })
      
      console.log('✅ [RoleStore] API response:', response)
      
      // Handle different response formats
      if (response && response.data && Array.isArray(response.data)) {
        roles.value = response.data
        console.log('📊 [RoleStore] Set roles from response.data:', roles.value)
      } else if (response && Array.isArray(response)) {
        roles.value = response
        console.log('📊 [RoleStore] Set roles from direct array:', roles.value)
      } else {
        console.warn('⚠️ [RoleStore] Unexpected response format:', response)
        roles.value = []
      }
      
      // Update pagination if available
      if (response.current_page) {
        pagination.value = {
          currentPage: response.current_page || 1,
          perPage: response.per_page || 10,
          total: response.total || roles.value.length,
          lastPage: response.last_page || 1
        }
      }
      
      console.log('✅ [RoleStore] Final roles state:', roles.value)
      return response
    } catch (error) {
      console.error('❌ [RoleStore] Fetch roles error:', error)
      console.error('❌ [RoleStore] Error details:', {
        message: error.message,
        status: error.response?.status,
        data: error.response?.data
      })
      throw error
    } finally {
      loading.value = false
    }
  }

  const fetchRole = async (id) => {
    loading.value = true
    try {
      const response = await roleService.getRole(id)
      currentRole.value = response.data
      return response
    } catch (error) {
      console.error('Fetch role error:', error)
      throw error
    } finally {
      loading.value = false
    }
  }

  const createRole = async (roleData) => {
    loading.value = true
    try {
      const response = await roleService.createRole(roleData)
      
      // Add new role to list
      roles.value.unshift(response.data)
      
      return response
    } catch (error) {
      console.error('Create role error:', error)
      throw error
    } finally {
      loading.value = false
    }
  }

  const updateRole = async (id, roleData) => {
    loading.value = true
    try {
      const response = await roleService.updateRole(id, roleData)
      
      // Update role in list
      const index = roles.value.findIndex(r => r.id === id)
      if (index !== -1) {
        roles.value[index] = response.data
      }
      
      // Update current role if it's the same
      if (currentRole.value && currentRole.value.id === id) {
        currentRole.value = response.data
      }
      
      return response
    } catch (error) {
      console.error('Update role error:', error)
      throw error
    } finally {
      loading.value = false
    }
  }

  const deleteRole = async (id) => {
    loading.value = true
    try {
      await roleService.deleteRole(id)
      
      // Remove role from list
      roles.value = roles.value.filter(r => r.id !== id)
      
      // Clear current role if it's the same
      if (currentRole.value && currentRole.value.id === id) {
        currentRole.value = null
      }
      
      return true
    } catch (error) {
      console.error('Delete role error:', error)
      throw error
    } finally {
      loading.value = false
    }
  }

  const getRolePermissions = async (id) => {
    loading.value = true
    try {
      const response = await roleService.getRolePermissions(id)
      return response
    } catch (error) {
      console.error('Get role permissions error:', error)
      throw error
    } finally {
      loading.value = false
    }
  }

  const getRoleUsers = async (id) => {
    loading.value = true
    try {
      const response = await roleService.getRoleUsers(id)
      return response
    } catch (error) {
      console.error('Get role users error:', error)
      throw error
    } finally {
      loading.value = false
    }
  }

  const assignPermissions = async (id, permissions) => {
    loading.value = true
    try {
      const response = await roleService.assignPermissions(id, permissions)
      
      // Update role permissions in list
      const index = roles.value.findIndex(r => r.id === id)
      if (index !== -1) {
        roles.value[index].permissions = permissions
      }
      
      // Update current role if it's the same
      if (currentRole.value && currentRole.value.id === id) {
        currentRole.value.permissions = permissions
      }
      
      return response
    } catch (error) {
      console.error('Assign permissions error:', error)
      throw error
    } finally {
      loading.value = false
    }
  }

  const revokePermissions = async (id, permissions) => {
    loading.value = true
    try {
      // For now, we'll use the same assignPermissions endpoint 
      // but filter out the permissions to revoke
      const role = roles.value.find(r => r.id === id)
      if (!role) throw new Error('Role not found')
      
      const currentPermissions = role.permissions || []
      const newPermissions = currentPermissions.filter(p => 
        !permissions.includes(p.id || p)
      )
      
      const response = await roleService.assignPermissions(id, newPermissions.map(p => p.id || p))
      
      // Update role permissions in list
      const index = roles.value.findIndex(r => r.id === id)
      if (index !== -1) {
        roles.value[index].permissions = newPermissions
      }
      
      // Update current role if it's the same
      if (currentRole.value && currentRole.value.id === id) {
        currentRole.value.permissions = newPermissions
      }
      
      return response
    } catch (error) {
      console.error('Revoke permissions error:', error)
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
  const selectRole = (role) => {
    const index = selectedRoles.value.findIndex(r => r.id === role.id)
    if (index === -1) {
      selectedRoles.value.push(role)
    }
  }

  const deselectRole = (role) => {
    selectedRoles.value = selectedRoles.value.filter(r => r.id !== role.id)
  }

  const selectAllRoles = () => {
    selectedRoles.value = [...filteredRoles.value]
  }

  const deselectAllRoles = () => {
    selectedRoles.value = []
  }

  const toggleRoleSelection = (role) => {
    const index = selectedRoles.value.findIndex(r => r.id === role.id)
    if (index === -1) {
      selectRole(role)
    } else {
      deselectRole(role)
    }
  }

  // Utility actions
  const getRoleById = (id) => {
    return roles.value.find(r => r.id === id)
  }

  const getRoleByName = (name) => {
    return roles.value.find(r => r.name === name)
  }

  const getRolesByType = (type) => {
    return roles.value.filter(r => r.type === type)
  }

  const getRolesByStatus = (status) => {
    return roles.value.filter(r => r.status === status)
  }

  const duplicateRole = (role) => {
    const duplicatedRole = {
      ...role,
      id: null,
      name: `${role.name} (Copy)`,
      userCount: 0,
      createdAt: new Date()
    }
    return duplicatedRole
  }

  // Reset store
  const reset = () => {
    roles.value = []
    currentRole.value = null
    loading.value = false
    pagination.value = {
      currentPage: 1,
      perPage: 10,
      total: 0,
      lastPage: 1
    }
    filters.value = {
      search: '',
      type: null,
      status: null
    }
    selectedRoles.value = []
  }

  return {
    // State
    roles,
    currentRole,
    loading,
    pagination,
    filters,
    selectedRoles,
    
    // Getters
    filteredRoles,
    totalRoles,
    activeRoles,
    customRoles,
    systemRoles,
    
    // Actions
    fetchRoles,
    fetchRole,
    createRole,
    updateRole,
    deleteRole,
    getRolePermissions,
    getRoleUsers,
    assignPermissions,
    revokePermissions,
    
    // Filter actions
    setFilters,
    clearFilters,
    
    // Pagination actions
    setPage,
    setPerPage,
    
    // Selection actions
    selectRole,
    deselectRole,
    selectAllRoles,
    deselectAllRoles,
    toggleRoleSelection,
    
    // Utility actions
    getRoleById,
    getRoleByName,
    getRolesByType,
    getRolesByStatus,
    duplicateRole,
    
    // Reset
    reset
  }
}) 