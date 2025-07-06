<template>
  <AppDialog :show="show" @close="$emit('close')" max-width="6xl">
    <template #header>
      <div class="flex items-center space-x-3">
        <div class="p-2 bg-indigo-100 rounded-lg">
          <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7m0 10a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2-2v10z"/>
          </svg>
        </div>
        <div>
          <h3 class="text-lg font-semibold text-gray-900">Role Matrix: {{ formatModuleName(module?.name) }}</h3>
          <p class="text-sm text-gray-600">{{ module?.description || `Manage ${module?.name} permissions` }}</p>
        </div>
      </div>
    </template>

    <div v-if="module" class="space-y-6">
      <!-- Module Summary -->
      <div class="bg-indigo-50 rounded-lg p-4">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
          <div class="text-center">
            <div class="text-2xl font-bold text-indigo-600">{{ modulePermissions.length }}</div>
            <div class="text-sm text-gray-600">Permissions</div>
          </div>
          <div class="text-center">
            <div class="text-2xl font-bold text-purple-600">{{ roles.length }}</div>
            <div class="text-sm text-gray-600">Roles</div>
          </div>
          <div class="text-center">
            <div class="text-2xl font-bold text-green-600">{{ totalAssignments }}</div>
            <div class="text-sm text-gray-600">Assignments</div>
          </div>
          <div class="text-center">
            <div class="text-2xl font-bold text-orange-600">{{ pendingChanges.length }}</div>
            <div class="text-sm text-gray-600">Pending</div>
          </div>
        </div>
      </div>

      <!-- Controls -->
      <div class="flex items-center justify-between">
        <div class="flex items-center gap-4">
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
              @click="viewMode = 'list'"
              :class="[
                'px-3 py-1 text-sm font-medium rounded-md transition-colors',
                viewMode === 'list' ? 'bg-white text-gray-900 shadow-sm' : 'text-gray-600 hover:text-gray-900'
              ]"
            >
              List View
            </button>
          </div>

          <!-- Quick Actions -->
          <div class="flex items-center gap-2">
            <button
              @click="selectAllPermissions"
              class="px-3 py-1 text-sm text-indigo-600 hover:text-indigo-800 bg-indigo-50 hover:bg-indigo-100 rounded-lg transition-colors"
            >
              Grant All
            </button>
            <button
              @click="clearAllPermissions"
              class="px-3 py-1 text-sm text-red-600 hover:text-red-800 bg-red-50 hover:bg-red-100 rounded-lg transition-colors"
            >
              Revoke All
            </button>
          </div>
        </div>

        <div class="flex items-center gap-3">
          <!-- Export Button -->
          <button
            @click="exportMatrix"
            class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg transition-colors flex items-center gap-2"
          >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            Export CSV
          </button>

          <!-- Bulk Apply Template -->
          <button
            @click="showTemplateModal = true"
            class="px-4 py-2 bg-indigo-500 text-white rounded-lg hover:bg-indigo-600 transition-colors flex items-center gap-2"
          >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
            </svg>
            Apply Template
          </button>
        </div>
      </div>

      <!-- Loading State -->
      <div v-if="loading" class="text-center py-12">
        <svg class="w-8 h-8 text-indigo-500 animate-spin mx-auto mb-4" fill="none" viewBox="0 0 24 24">
          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
        <p class="text-gray-600">Loading matrix...</p>
      </div>

      <!-- Matrix Content -->
      <div v-else class="bg-white border border-gray-200 rounded-lg overflow-hidden">
        <!-- Grid View -->
        <div v-if="viewMode === 'grid'" class="overflow-x-auto">
          <div class="min-w-[600px]">
            <!-- Header -->
            <div class="grid bg-gray-50 border-b border-gray-200" :style="{ gridTemplateColumns: `200px repeat(${modulePermissions.length}, minmax(80px, 1fr))` }">
              <div class="p-3 font-semibold text-gray-900 border-r border-gray-200">
                Roles / Permissions
              </div>
              <div 
                v-for="permission in modulePermissions" 
                :key="permission.id"
                class="p-2 border-r border-gray-200 text-center"
              >
                <div class="transform -rotate-45 text-xs font-medium text-gray-700 whitespace-nowrap origin-center">
                  {{ permission.display_name || permission.name.split('.').pop() }}
                </div>
              </div>
            </div>

            <!-- Role Rows -->
            <div 
              v-for="role in roles" 
              :key="role.id"
              class="grid border-b border-gray-200 hover:bg-gray-50" 
              :style="{ gridTemplateColumns: `200px repeat(${modulePermissions.length}, minmax(80px, 1fr))` }"
            >
              <!-- Role Info -->
              <div class="p-3 border-r border-gray-200 flex items-center">
                <div>
                  <div class="font-medium text-gray-900">{{ role.display_name || role.name }}</div>
                  <div class="text-sm text-gray-500">{{ role.type }}</div>
                </div>
              </div>

              <!-- Permission Toggles -->
              <div 
                v-for="permission in modulePermissions" 
                :key="`${role.id}-${permission.id}`"
                class="p-2 border-r border-gray-200 flex items-center justify-center"
              >
                <button
                  @click="togglePermission(role.id, permission.id)"
                  :disabled="role.type === 'system' && permission.type === 'system'"
                  :class="[
                    'w-8 h-8 rounded-lg border-2 transition-all duration-200 relative',
                    hasPermission(role.id, permission.id) 
                      ? 'bg-green-500 border-green-500 text-white' 
                      : 'bg-white border-gray-300 hover:border-gray-400',
                    role.type === 'system' && permission.type === 'system'
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

                  <!-- Pending indicator -->
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

        <!-- List View -->
        <div v-else class="divide-y divide-gray-200">
          <div v-for="role in roles" :key="role.id" class="p-6">
            <div class="flex items-center justify-between mb-4">
              <div>
                <h4 class="font-semibold text-gray-900">{{ role.display_name || role.name }}</h4>
                <p class="text-sm text-gray-500">{{ role.description || `${role.type} role` }}</p>
              </div>
              <div class="text-sm text-gray-600">
                {{ getRolePermissionCount(role.id) }} / {{ modulePermissions.length }} permissions
              </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
              <div v-for="permission in modulePermissions" :key="permission.id" class="flex items-center">
                <button
                  @click="togglePermission(role.id, permission.id)"
                  :disabled="role.type === 'system' && permission.type === 'system'"
                  :class="[
                    'w-5 h-5 rounded border-2 mr-3 transition-all duration-200',
                    hasPermission(role.id, permission.id) 
                      ? 'bg-green-500 border-green-500' 
                      : 'bg-white border-gray-300',
                    role.type === 'system' && permission.type === 'system'
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
                <span class="text-sm text-gray-700">{{ permission.display_name || permission.name.split('.').pop() }}</span>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Pending Changes Summary -->
      <div v-if="pendingChanges.length > 0" class="border-t border-gray-200 p-4 bg-blue-50">
        <div class="flex items-center justify-between">
          <div>
            <h4 class="font-medium text-blue-900">{{ pendingChanges.length }} Pending Changes</h4>
            <p class="text-sm text-blue-700">Click apply to save all changes</p>
          </div>
          <div class="flex gap-2">
            <button
              @click="applyChanges"
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
              @click="discardChanges"
              class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600"
            >
              Discard
            </button>
          </div>
        </div>
      </div>
    </div>

    <template #footer>
      <div class="flex justify-end">
        <button
          @click="$emit('close')"
          class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50"
        >
          Close
        </button>
      </div>
    </template>
  </AppDialog>
</template>

<script setup>
import { ref, computed, watch, onMounted } from 'vue'
import { useToast } from '@/composables/useToast'
import { useRolesStore } from '@/stores/roles'
import AppDialog from '@/components/modal/AppDialog.vue'

const props = defineProps({
  show: Boolean,
  module: {
    type: Object,
    default: null
  },
  roles: {
    type: Array,
    default: () => []
  }
})

const emit = defineEmits(['close', 'permissions-updated'])

// Composables
const { success: showSuccess, error: showError } = useToast()
const rolesStore = useRolesStore()

// State
const loading = ref(false)
const applying = ref(false)
const viewMode = ref('grid')
const showTemplateModal = ref(false)
const matrixData = ref({})
const pendingChanges = ref([])

// Computed
const modulePermissions = computed(() => props.module?.permissions || [])

const totalAssignments = computed(() => {
  let count = 0
  props.roles.forEach(role => {
    modulePermissions.value.forEach(permission => {
      if (hasPermission(role.id, permission.id)) count++
    })
  })
  return count
})

// Methods
const formatModuleName = (name) => {
  if (!name) return 'Unknown Module'
  return name.charAt(0).toUpperCase() + name.slice(1).replace(/[_-]/g, ' ')
}

const hasPermission = (roleId, permissionId) => {
  const role = props.roles.find(r => r.id === roleId)
  return role?.permissions?.some(p => p.id === permissionId) || false
}

const isPending = (roleId, permissionId) => {
  return pendingChanges.value.some(change => 
    change.role_id === roleId && change.permission_id === permissionId
  )
}

const getRolePermissionCount = (roleId) => {
  let count = 0
  modulePermissions.value.forEach(permission => {
    if (hasPermission(roleId, permission.id)) count++
  })
  return count
}

const getPermissionTooltip = (role, permission) => {
  const hasIt = hasPermission(role.id, permission.id)
  const isPend = isPending(role.id, permission.id)
  
  if (role.type === 'system' && permission.type === 'system') {
    return 'System permissions cannot be modified'
  }
  
  if (isPend) {
    return 'Change pending...'
  }
  
  return hasIt ? 'Click to revoke permission' : 'Click to grant permission'
}

const togglePermission = (roleId, permissionId) => {
  if (isPending(roleId, permissionId)) return
  
  const role = props.roles.find(r => r.id === roleId)
  const permission = modulePermissions.value.find(p => p.id === permissionId)
  
  if (role.type === 'system' && permission.type === 'system') {
    showError('Cannot modify system permissions for system roles')
    return
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
  if (currentState) {
    // Remove permission
    const rolePermissions = role.permissions || []
    role.permissions = rolePermissions.filter(p => p.id !== permissionId)
  } else {
    // Add permission
    if (!role.permissions) role.permissions = []
    role.permissions.push(permission)
  }
  
  // Auto-apply after a delay
  setTimeout(() => {
    if (pendingChanges.value.length > 0) {
      applyChanges()
    }
  }, 1000)
}

const selectAllPermissions = () => {
  const changes = []
  props.roles.forEach(role => {
    if (role.type === 'system') return // Skip system roles
    
    modulePermissions.value.forEach(permission => {
      if (permission.type === 'system') return // Skip system permissions
      if (hasPermission(role.id, permission.id)) return // Already has permission
      if (isPending(role.id, permission.id)) return // Already pending
      
      changes.push({
        role_id: role.id,
        permission_id: permission.id,
        action: 'grant'
      })
      
      // Optimistically update UI
      if (!role.permissions) role.permissions = []
      role.permissions.push(permission)
    })
  })
  
  pendingChanges.value.push(...changes)
  
  if (changes.length > 0) {
    showSuccess(`Queued ${changes.length} permissions for assignment`)
  }
}

const clearAllPermissions = () => {
  const changes = []
  props.roles.forEach(role => {
    if (role.type === 'system') return // Skip system roles
    
    modulePermissions.value.forEach(permission => {
      if (permission.type === 'system') return // Skip system permissions
      if (!hasPermission(role.id, permission.id)) return // Doesn't have permission
      if (isPending(role.id, permission.id)) return // Already pending
      
      changes.push({
        role_id: role.id,
        permission_id: permission.id,
        action: 'revoke'
      })
      
      // Optimistically update UI
      const rolePermissions = role.permissions || []
      role.permissions = rolePermissions.filter(p => p.id !== permission.id)
    })
  })
  
  pendingChanges.value.push(...changes)
  
  if (changes.length > 0) {
    showSuccess(`Queued ${changes.length} permissions for removal`)
  }
}

const applyChanges = async () => {
  if (pendingChanges.value.length === 0) return
  
  applying.value = true
  try {
    // Group changes by action
    const grants = pendingChanges.value.filter(c => c.action === 'grant')
    const revokes = pendingChanges.value.filter(c => c.action === 'revoke')
    
    // Apply grants
    for (const change of grants) {
      await rolesStore.assignPermissions(change.role_id, [change.permission_id])
    }
    
    // Apply revokes
    for (const change of revokes) {
      await rolesStore.revokePermissions(change.role_id, [change.permission_id])
    }
    
    pendingChanges.value = []
    emit('permissions-updated')
    showSuccess(`${grants.length + revokes.length} permission changes applied successfully`)
    
  } catch (error) {
    showError('Failed to apply permission changes')
    console.error('Apply changes error:', error)
    
    // Revert optimistic updates on error
    discardChanges()
  } finally {
    applying.value = false
  }
}

const discardChanges = () => {
  // Revert all optimistic updates
  pendingChanges.value.forEach(change => {
    const role = props.roles.find(r => r.id === change.role_id)
    const permission = modulePermissions.value.find(p => p.id === change.permission_id)
    
    if (change.action === 'grant') {
      // Remove the optimistically added permission
      const rolePermissions = role.permissions || []
      role.permissions = rolePermissions.filter(p => p.id !== permission.id)
    } else if (change.action === 'revoke') {
      // Add back the optimistically removed permission
      if (!role.permissions) role.permissions = []
      role.permissions.push(permission)
    }
  })
  
  pendingChanges.value = []
  showSuccess('Pending changes discarded')
}

const exportMatrix = () => {
  const csvData = []
  
  // Header row
  const header = ['Role', ...modulePermissions.value.map(p => p.display_name || p.name)]
  csvData.push(header.join(','))
  
  // Data rows
  props.roles.forEach(role => {
    const row = [role.display_name || role.name]
    modulePermissions.value.forEach(permission => {
      row.push(hasPermission(role.id, permission.id) ? 'Yes' : 'No')
    })
    csvData.push(row.join(','))
  })
  
  // Download CSV
  const csv = csvData.join('\n')
  const blob = new Blob([csv], { type: 'text/csv' })
  const url = window.URL.createObjectURL(blob)
  const a = document.createElement('a')
  a.href = url
  a.download = `${props.module?.name || 'module'}_role_matrix.csv`
  a.click()
  window.URL.revokeObjectURL(url)
}

// Watch for modal state changes
watch(() => props.show, (show) => {
  if (!show) {
    // Reset state when modal closes
    pendingChanges.value = []
    viewMode.value = 'grid'
  }
})
</script> 

<style scoped>
.transition-all {
  transition-property: all;
  transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
  transition-duration: 150ms;
}
</style> 