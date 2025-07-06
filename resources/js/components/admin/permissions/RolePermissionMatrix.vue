<template>
  <div class="bg-white/70 backdrop-blur-xl border border-white/20 rounded-2xl shadow-xl overflow-hidden">
    <!-- Header -->
    <div class="p-6 border-b border-gray-100">
      <div class="flex items-center justify-between">
        <div>
          <h3 class="text-xl font-bold text-gray-900">Role Permission Matrix</h3>
          <p class="text-gray-600 mt-1">Manage permissions across all roles with enterprise-level control</p>
        </div>
        
        <div class="flex items-center gap-3">
          <!-- View Toggle -->
          <div class="flex bg-gray-100 rounded-lg p-1">
            <button
              @click="viewMode = 'grid'"
              :class="[
                'px-3 py-1 text-sm font-medium rounded-md transition-colors',
                viewMode === 'grid' ? 'bg-white text-gray-900 shadow-sm' : 'text-gray-600 hover:text-gray-900'
              ]"
            >
              Grid View
            </button>
            <button
              @click="viewMode = 'compact'"
              :class="[
                'px-3 py-1 text-sm font-medium rounded-md transition-colors',
                viewMode === 'compact' ? 'bg-white text-gray-900 shadow-sm' : 'text-gray-600 hover:text-gray-900'
              ]"
            >
              Compact
            </button>
          </div>
          
          <!-- Export Button -->
          <button
            @click="exportMatrix"
            class="px-4 py-2 bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-lg hover:from-blue-600 hover:to-blue-700 transition-all duration-200 flex items-center gap-2"
          >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            Export CSV
          </button>
        </div>
      </div>
      
      <!-- Filters -->
      <div class="mt-4 grid grid-cols-1 md:grid-cols-4 gap-4">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Filter by Role Type</label>
          <select 
            v-model="filters.roleType"
            @change="applyFilters"
            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
          >
            <option value="">All Types</option>
            <option value="system">System</option>
            <option value="custom">Custom</option>
          </select>
        </div>
        
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Filter by Module</label>
          <select 
            v-model="filters.module"
            @change="applyFilters"
            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
          >
            <option value="">All Modules</option>
            <option v-for="module in availableModules" :key="module" :value="module">
              {{ formatModuleName(module) }}
            </option>
          </select>
        </div>
        
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Permission Type</label>
          <select 
            v-model="filters.permissionType"
            @change="applyFilters"
            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
          >
            <option value="">All Types</option>
            <option value="system">System</option>
            <option value="custom">Custom</option>
          </select>
        </div>
        
        <div class="flex items-end">
          <button
            @click="resetFilters"
            class="w-full px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg transition-colors"
          >
            Reset Filters
          </button>
        </div>
      </div>
    </div>

    <!-- Loading State -->
    <div v-if="loading" class="p-12 text-center">
      <svg class="w-8 h-8 text-blue-500 animate-spin mx-auto mb-4" fill="none" viewBox="0 0 24 24">
        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
      </svg>
      <p class="text-gray-600">Loading matrix data...</p>
    </div>

    <!-- Matrix Content -->
    <div v-else-if="matrixData" class="overflow-hidden">
      <!-- Grid View -->
      <div v-if="viewMode === 'grid'" class="overflow-x-auto">
        <div class="min-w-[800px]">
          <!-- Header with permissions -->
          <div class="grid" :style="{ gridTemplateColumns: `200px repeat(${filteredPermissions.length}, 80px)` }">
            <!-- Corner cell -->
            <div class="p-3 bg-gray-50 border-b border-r border-gray-200 font-semibold text-gray-900">
              Roles / Permissions
            </div>
            
            <!-- Permission headers -->
            <div 
              v-for="permission in filteredPermissions" 
              :key="permission.id"
              class="p-2 bg-gray-50 border-b border-r border-gray-200 text-center"
            >
              <div class="transform -rotate-45 text-xs font-medium text-gray-700 whitespace-nowrap origin-center">
                {{ permission.display_name || permission.name }}
              </div>
              <div class="text-xs text-gray-500 mt-1">
                {{ permission.module }}
              </div>
            </div>
          </div>
          
          <!-- Role rows -->
          <div 
            v-for="role in filteredRoles" 
            :key="role.id"
            class="grid" 
            :style="{ gridTemplateColumns: `200px repeat(${filteredPermissions.length}, 80px)` }"
          >
            <!-- Role name -->
            <div class="p-3 bg-white border-b border-r border-gray-200 flex items-center">
              <div>
                <div class="font-medium text-gray-900">{{ role.display_name || role.name }}</div>
                <div class="text-sm text-gray-500">{{ role.type }}</div>
              </div>
            </div>
            
            <!-- Permission cells -->
            <div 
              v-for="permission in filteredPermissions" 
              :key="`${role.id}-${permission.id}`"
              class="p-2 bg-white border-b border-r border-gray-200 flex items-center justify-center"
            >
              <button
                @click="togglePermission(role.id, permission.id)"
                :disabled="role.type === 'system' && isSystemPermission(permission)"
                :class="[
                  'w-8 h-8 rounded-lg border-2 transition-all duration-200 relative',
                  hasPermission(role.id, permission.id) 
                    ? 'bg-green-500 border-green-500 text-white' 
                    : 'bg-white border-gray-300 hover:border-gray-400',
                  role.type === 'system' && isSystemPermission(permission) 
                    ? 'opacity-50 cursor-not-allowed' 
                    : 'cursor-pointer hover:scale-105'
                ]"
                :title="getPermissionTooltip(role, permission)"
              >
                <svg 
                  v-if="hasPermission(role.id, permission.id)" 
                  class="w-4 h-4" 
                  fill="currentColor" 
                  viewBox="0 0 20 20"
                >
                  <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                </svg>
                
                <!-- Loading indicator for pending changes -->
                <div 
                  v-if="isPending(role.id, permission.id)"
                  class="absolute inset-0 bg-blue-500 rounded-lg flex items-center justify-center"
                >
                  <svg class="w-3 h-3 text-white animate-spin" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                  </svg>
                </div>
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Compact View -->
      <div v-else class="p-6 space-y-6">
        <div v-for="role in filteredRoles" :key="role.id" class="border border-gray-200 rounded-lg p-4">
          <div class="flex items-center justify-between mb-4">
            <div>
              <h4 class="font-semibold text-gray-900">{{ role.display_name || role.name }}</h4>
              <p class="text-sm text-gray-500">{{ role.type }} role</p>
            </div>
            <div class="text-sm text-gray-600">
              {{ getPermissionCount(role.id) }} / {{ filteredPermissions.length }} permissions
            </div>
          </div>
          
          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
            <div v-for="permission in filteredPermissions" :key="permission.id" class="flex items-center">
              <button
                @click="togglePermission(role.id, permission.id)"
                :disabled="role.type === 'system' && isSystemPermission(permission)"
                :class="[
                  'w-5 h-5 rounded border-2 mr-3 transition-all duration-200',
                  hasPermission(role.id, permission.id) 
                    ? 'bg-green-500 border-green-500' 
                    : 'bg-white border-gray-300',
                  role.type === 'system' && isSystemPermission(permission) 
                    ? 'opacity-50 cursor-not-allowed' 
                    : 'cursor-pointer hover:scale-105'
                ]"
              >
                <svg 
                  v-if="hasPermission(role.id, permission.id)" 
                  class="w-3 h-3 text-white" 
                  fill="currentColor" 
                  viewBox="0 0 20 20"
                >
                  <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                </svg>
              </button>
              <span class="text-sm text-gray-700">{{ permission.display_name || permission.name }}</span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Empty State -->
    <div v-else class="p-12 text-center">
      <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
      </svg>
      <h3 class="text-lg font-medium text-gray-900 mb-2">No matrix data available</h3>
      <p class="text-gray-500">Please check your permissions and try again</p>
    </div>

    <!-- Pending Changes Summary -->
    <div v-if="pendingChanges.length > 0" class="border-t border-gray-200 p-4 bg-blue-50">
      <div class="flex items-center justify-between">
        <div>
          <h4 class="font-medium text-blue-900">{{ pendingChanges.length }} Pending Changes</h4>
          <p class="text-sm text-blue-700">Changes will be saved automatically</p>
        </div>
        <div class="flex gap-2">
          <button
            @click="applyPendingChanges"
            :disabled="applying"
            class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 disabled:opacity-50 flex items-center gap-2"
          >
            <svg v-if="applying" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            {{ applying ? 'Applying...' : 'Apply Changes' }}
          </button>
          <button
            @click="discardPendingChanges"
            class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600"
          >
            Discard
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue'
import { useToast } from '@/composables/useToast'
import api from '@/services/api'

const props = defineProps({
  roles: {
    type: Array,
    default: () => []
  },
  permissions: {
    type: Array,
    default: () => []
  }
})

const emit = defineEmits(['permissions-changed'])

// Composables
const { success: showSuccess, error: showError } = useToast()

// State
const loading = ref(false)
const applying = ref(false)
const viewMode = ref('grid')
const matrixData = ref(null)
const pendingChanges = ref([])

const filters = ref({
  roleType: '',
  module: '',
  permissionType: ''
})

// Computed
const filteredRoles = computed(() => {
  if (!matrixData.value?.roles) return []
  
  let roles = matrixData.value.roles
  
  if (filters.value.roleType) {
    roles = roles.filter(role => role.type === filters.value.roleType)
  }
  
  return roles
})

const filteredPermissions = computed(() => {
  if (!matrixData.value?.permissions) return []
  
  let permissions = matrixData.value.permissions
  
  if (filters.value.module) {
    permissions = permissions.filter(permission => permission.module === filters.value.module)
  }
  
  if (filters.value.permissionType) {
    permissions = permissions.filter(permission => permission.type === filters.value.permissionType)
  }
  
  return permissions
})

const availableModules = computed(() => {
  if (!matrixData.value?.permissions) return []
  return [...new Set(matrixData.value.permissions.map(p => p.module))].sort()
})

// Methods
const loadMatrix = async () => {
  loading.value = true
  try {
    const response = await api.get('/permissions/role-matrix', {
      params: filters.value
    })
    matrixData.value = response.data.data
  } catch (error) {
    showError('Failed to load role-permission matrix')
    console.error('Matrix load error:', error)
  } finally {
    loading.value = false
  }
}

const hasPermission = (roleId, permissionId) => {
  const role = matrixData.value?.roles?.find(r => r.id === roleId)
  return role?.permissions?.[permissionId]?.has_permission || false
}

const getPermissionCount = (roleId) => {
  const role = matrixData.value?.roles?.find(r => r.id === roleId)
  if (!role) return 0
  return Object.values(role.permissions || {}).filter(p => p.has_permission).length
}

const isPending = (roleId, permissionId) => {
  return pendingChanges.value.some(change => 
    change.role_id === roleId && change.permission_id === permissionId
  )
}

const isSystemPermission = (permission) => {
  return permission.type === 'system'
}

const getPermissionTooltip = (role, permission) => {
  const hasIt = hasPermission(role.id, permission.id)
  const isPend = isPending(role.id, permission.id)
  
  if (role.type === 'system' && isSystemPermission(permission)) {
    return 'System permissions cannot be modified'
  }
  
  if (isPend) {
    return 'Change pending...'
  }
  
  return hasIt ? 'Click to revoke permission' : 'Click to grant permission'
}

const togglePermission = (roleId, permissionId) => {
  if (isPending(roleId, permissionId)) return
  
  const role = matrixData.value.roles.find(r => r.id === roleId)
  if (role.type === 'system') {
    const permission = matrixData.value.permissions.find(p => p.id === permissionId)
    if (isSystemPermission(permission)) {
      showError('Cannot modify system permissions for system roles')
      return
    }
  }
  
  const currentState = hasPermission(roleId, permissionId)
  const action = currentState ? 'revoke' : 'grant'
  
  // Add to pending changes
  pendingChanges.value.push({
    role_id: roleId,
    permission_id: permissionId,
    action: action
  })
  
  // Optimistically update UI
  const targetRole = matrixData.value.roles.find(r => r.id === roleId)
  if (targetRole?.permissions?.[permissionId]) {
    targetRole.permissions[permissionId].has_permission = !currentState
  }
  
  // Auto-apply after a delay
  setTimeout(() => {
    if (pendingChanges.value.length > 0) {
      applyPendingChanges()
    }
  }, 1000)
}

const applyPendingChanges = async () => {
  if (pendingChanges.value.length === 0) return
  
  applying.value = true
  try {
    const response = await api.post('/permissions/role-matrix/update', {
      changes: pendingChanges.value
    })
    
    const results = response.data.data
    const failed = results.filter(r => !r.success)
    
    if (failed.length > 0) {
      showError(`${failed.length} changes failed to apply`)
      // Revert failed changes in UI
      failed.forEach(change => {
        const failedRole = matrixData.value.roles.find(r => r.id === change.role_id)
        if (failedRole?.permissions?.[change.permission_id]) {
          // Revert the optimistic update
          failedRole.permissions[change.permission_id].has_permission = 
            !failedRole.permissions[change.permission_id].has_permission
        }
      })
    } else {
      showSuccess(`${results.length} permission changes applied successfully`)
    }
    
    pendingChanges.value = []
    emit('permissions-changed')
    
  } catch (error) {
    showError('Failed to apply permission changes')
    console.error('Apply changes error:', error)
    
    // Revert all optimistic updates
    pendingChanges.value.forEach(change => {
      const pendingRole = matrixData.value.roles.find(r => r.id === change.role_id)
      if (pendingRole?.permissions?.[change.permission_id]) {
        pendingRole.permissions[change.permission_id].has_permission = 
          !pendingRole.permissions[change.permission_id].has_permission
      }
    })
    pendingChanges.value = []
  } finally {
    applying.value = false
  }
}

const discardPendingChanges = () => {
  // Revert all optimistic updates
  pendingChanges.value.forEach(change => {
    const discardRole = matrixData.value.roles.find(r => r.id === change.role_id)
    if (discardRole?.permissions?.[change.permission_id]) {
      discardRole.permissions[change.permission_id].has_permission = 
        !discardRole.permissions[change.permission_id].has_permission
    }
  })
  pendingChanges.value = []
  showSuccess('Pending changes discarded')
}

const applyFilters = () => {
  loadMatrix()
}

const resetFilters = () => {
  filters.value = {
    roleType: '',
    module: '',
    permissionType: ''
  }
  loadMatrix()
}

const formatModuleName = (moduleName) => {
  return moduleName.split('_').map(word => 
    word.charAt(0).toUpperCase() + word.slice(1)
  ).join(' ')
}

const exportMatrix = () => {
  // Create CSV content
  const headers = ['Role', 'Type', ...filteredPermissions.value.map(p => p.display_name || p.name)]
  const rows = filteredRoles.value.map(role => [
    role.display_name || role.name,
    role.type,
    ...filteredPermissions.value.map(permission => 
      hasPermission(role.id, permission.id) ? 'Yes' : 'No'
    )
  ])
  
  const csvContent = [headers, ...rows]
    .map(row => row.map(cell => `"${cell}"`).join(','))
    .join('\n')
  
  // Download CSV
  const blob = new Blob([csvContent], { type: 'text/csv' })
  const url = URL.createObjectURL(blob)
  const a = document.createElement('a')
  a.href = url
  a.download = `role-permission-matrix-${new Date().toISOString().split('T')[0]}.csv`
  document.body.appendChild(a)
  a.click()
  document.body.removeChild(a)
  URL.revokeObjectURL(url)
  
  showSuccess('Matrix exported successfully')
}

// Lifecycle
onMounted(() => {
  loadMatrix()
})

// Watch for prop changes
watch(() => [props.roles, props.permissions], () => {
  if (props.roles.length > 0 || props.permissions.length > 0) {
    loadMatrix()
  }
}, { deep: true })
</script> 