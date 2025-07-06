<template>
  <div
    class="bg-white rounded-2xl shadow-lg border border-gray-100 hover:shadow-2xl transition-all duration-300 overflow-hidden group cursor-pointer relative"
    @click="$emit('open-module-detail', module)"
  >
    <!-- Animated Gradient Border -->
    <div class="absolute inset-0 rounded-2xl pointer-events-none z-10 group-hover:shadow-[0_0_0_4px_rgba(93,52,236,0.12)] transition-all duration-300"></div>
    <!-- Module Header -->
    <div 
      class="p-6 border-b border-gray-100 relative bg-gradient-to-r from-blue-600/90 to-indigo-600/90"
    >
      <div class="flex items-center justify-between">
        <div class="flex items-center space-x-4">
          <div class="p-3 bg-white/20 backdrop-blur-sm rounded-xl">
            <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 24 24">
              <path :d="getModuleIcon(module.name)"/>
            </svg>
          </div>
          <div>
            <h3 class="text-2xl font-bold text-white tracking-tight flex items-center gap-2">
              {{ formatModuleName(module.name) }}
              <span v-if="module.trend !== undefined" :class="module.trend >= 0 ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'" class="ml-2 px-2 py-0.5 rounded-full text-xs font-semibold animate-pulse">
                {{ module.trend >= 0 ? '+' : '' }}{{ module.trend }}%
              </span>
            </h3>
            <p class="text-white/80 text-sm mt-1">{{ module.description || `${module.name} module permissions` }}</p>
          </div>
        </div>
        <div class="text-right">
          <div class="text-3xl font-extrabold text-white drop-shadow-lg">{{ modulePermissions.length }}</div>
          <div class="text-white/80 text-sm">Permissions</div>
        </div>
      </div>
      <!-- Most assigned roles badge -->
      <div v-if="mostAssignedRoles.length" class="mt-4 flex flex-wrap gap-2">
        <span v-for="role in mostAssignedRoles" :key="role.id" class="inline-flex items-center px-3 py-1 rounded-full bg-purple-100 text-purple-700 text-xs font-medium shadow-sm">
          <svg class="w-3 h-3 mr-1 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4"/></svg>
          {{ role.display_name || role.name }}
        </span>
      </div>
    </div>
    <!-- Module Stats -->
    <div class="p-6 border-b border-gray-100 bg-gradient-to-r from-white to-gray-50">
      <div class="grid grid-cols-3 gap-4">
        <div class="text-center">
          <div class="text-2xl font-bold text-green-600 animate-fade-in">{{ activePermissions }}</div>
          <div class="text-sm text-gray-600">Active</div>
        </div>
        <div class="text-center">
          <div class="text-2xl font-bold text-blue-600 animate-fade-in">{{ systemPermissions }}</div>
          <div class="text-sm text-gray-600">System</div>
        </div>
        <div class="text-center">
          <div class="text-2xl font-bold text-purple-600 animate-fade-in">{{ assignedRoles }}</div>
          <div class="text-sm text-gray-600">Roles</div>
        </div>
      </div>
    </div>
    <!-- Permission Groups -->
    <div class="p-6">
      <div class="space-y-4">
        <!-- Action-based grouping -->
        <div v-for="(permissions, action) in groupedPermissions" :key="action" class="space-y-3">
          <div class="flex items-center justify-between sticky top-0 bg-white/80 z-10 py-2">
            <h4 class="font-semibold text-gray-900 capitalize flex items-center">
              <div 
                class="w-3 h-3 rounded-full mr-3"
                :class="getActionColor(action)"
              ></div>
              {{ formatActionName(action) }}
              <span class="ml-2 text-sm text-gray-500">({{ permissions.length }})</span>
            </h4>
            <!-- Bulk Action Dropdown -->
            <div class="relative">
              <button
                @click.stop="toggleBulkMenu(action)"
                class="p-1 text-gray-400 hover:text-gray-600 rounded-lg hover:bg-gray-100"
              >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"/>
                </svg>
              </button>
              <!-- Bulk Menu -->
              <div 
                v-if="activeBulkMenu === action"
                class="absolute right-0 top-8 bg-white rounded-lg shadow-lg border border-gray-200 py-2 z-20 min-w-[200px] animate-fade-in"
              >
                <button
                  @click.stop="bulkAssignToRole(permissions, 'admin')"
                  class="w-full px-4 py-2 text-left text-sm text-gray-700 hover:bg-gray-100 flex items-center"
                >
                  <svg class="w-4 h-4 mr-3 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                  </svg>
                  Assign to Admin
                </button>
                <button
                  @click.stop="bulkAssignToRole(permissions, 'manager')"
                  class="w-full px-4 py-2 text-left text-sm text-gray-700 hover:bg-gray-100 flex items-center"
                >
                  <svg class="w-4 h-4 mr-3 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                  </svg>
                  Assign to Manager
                </button>
                <hr class="my-1">
                <button
                  @click.stop="showRoleAssignmentModal(permissions)"
                  class="w-full px-4 py-2 text-left text-sm text-gray-700 hover:bg-gray-100 flex items-center"
                >
                  <svg class="w-4 h-4 mr-3 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                  </svg>
                  Custom Assignment
                </button>
              </div>
            </div>
          </div>
          <!-- Permission Items -->
          <div class="grid grid-cols-1 gap-2">
            <div 
              v-for="permission in permissions" 
              :key="permission.id"
              class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors animate-fade-in"
            >
              <div class="flex items-center space-x-3">
                <div 
                  class="w-2 h-2 rounded-full"
                  :class="permission.status === 'active' ? 'bg-green-500' : 'bg-gray-400'"
                ></div>
                <div>
                  <div class="font-medium text-gray-900">
                    {{ formatPermissionName(permission.name) }}
                  </div>
                  <div class="text-sm text-gray-600">
                    {{ permission.description || permission.key }}
                  </div>
                </div>
              </div>
              <div class="flex items-center space-x-2">
                <!-- Permission Type Badge -->
                <span 
                  class="px-2 py-1 text-xs font-medium rounded-full"
                  :class="permission.type === 'system' 
                    ? 'bg-blue-100 text-blue-800' 
                    : 'bg-green-100 text-green-800'"
                >
                  {{ permission.type }}
                </span>
                <!-- Roles Count -->
                <div class="flex items-center space-x-1 text-sm text-gray-500">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                  </svg>
                  <span>{{ permission.roles?.length || 0 }}</span>
                </div>
                <!-- Quick Actions -->
                <button
                  @click.stop="togglePermissionRoles(permission)"
                  class="p-1 text-gray-400 hover:text-blue-600 rounded transition-colors"
                  :title="'View roles with this permission'"
                >
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                  </svg>
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <!-- Module Actions Footer -->
    <div class="px-6 py-4 bg-gray-50 border-t border-gray-100">
      <div class="flex items-center justify-between">
        <div class="flex items-center space-x-3">
          <button
            @click.stop="$emit('show-role-matrix', module)"
            class="px-4 py-2 text-sm font-medium text-blue-600 bg-blue-50 rounded-lg hover:bg-blue-100 transition-colors"
          >
            Role Matrix
          </button>
          <button
            @click.stop="exportModulePermissions"
            class="px-4 py-2 text-sm font-medium text-gray-600 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors"
          >
            Export
          </button>
        </div>
        <div class="text-sm text-gray-500">
          Last updated: {{ formatDate(module.updated_at) }}
        </div>
      </div>
    </div>
    <!-- Permission Roles Modal -->
    <PermissionRolesModal
      :show="showRolesModal"
      :permission="selectedPermission"
      :roles="availableRoles"
      @close="showRolesModal = false"
      @roles-updated="handleRolesUpdated"
    />
  </div>
</template>

<script setup>
import { ref, computed, defineProps, defineEmits } from 'vue'
import { useRolesStore } from '@/stores/roles'
import { useToast } from '@/composables/useToast'
import PermissionRolesModal from './PermissionRolesModal.vue'

const props = defineProps({
  module: {
    type: Object,
    required: true
  }
})

const emit = defineEmits(['permissions-updated', 'show-role-matrix', 'open-module-detail'])

// Stores
const rolesStore = useRolesStore()
const { success: showSuccess, error: showError } = useToast()

// State
const activeBulkMenu = ref(null)
const showRolesModal = ref(false)
const selectedPermission = ref(null)

// Computed
const modulePermissions = computed(() => Array.isArray(props.module.permissions) ? props.module.permissions : [])
const activePermissions = computed(() => modulePermissions.value.filter(p => p.status === 'active').length)
const systemPermissions = computed(() => modulePermissions.value.filter(p => p.type === 'system').length)
const assignedRoles = computed(() => {
  const roleIds = new Set()
  modulePermissions.value.forEach(permission => {
    permission.roles?.forEach(role => roleIds.add(role.id))
  })
  return roleIds.size
})

const availableRoles = computed(() => rolesStore.roles || [])

const groupedPermissions = computed(() => {
  const groups = {}
  modulePermissions.value.forEach(permission => {
    const action = extractAction(permission.name)
    if (!groups[action]) {
      groups[action] = []
    }
    groups[action].push(permission)
  })
  return groups
})

const mostAssignedRoles = computed(() => {
  // Find the most assigned roles for this module (top 3)
  const roleCounts = {}
  modulePermissions.value.forEach(p => {
    if (Array.isArray(p.roles)) {
      p.roles.forEach(role => {
        if (!roleCounts[role.id]) roleCounts[role.id] = { ...role, count: 0 }
        roleCounts[role.id].count++
      })
    }
  })
  return Object.values(roleCounts).sort((a, b) => b.count - a.count).slice(0, 3)
})

// Methods
const extractAction = (permissionName) => {
  const parts = permissionName.split('.')
  return parts[parts.length - 1] || 'manage'
}

const formatModuleName = (name) => {
  return name.charAt(0).toUpperCase() + name.slice(1).replace(/[_-]/g, ' ')
}

const formatPermissionName = (name) => {
  return name.split('.').map(part => 
    part.charAt(0).toUpperCase() + part.slice(1)
  ).join(' ')
}

const formatActionName = (action) => {
  const actionNames = {
    'view': 'View Access',
    'create': 'Create',
    'edit': 'Edit',
    'delete': 'Delete',
    'export': 'Export',
    'manage': 'Manage',
    'assign': 'Assign'
  }
  return actionNames[action] || action.charAt(0).toUpperCase() + action.slice(1)
}

const getActionColor = (action) => {
  const colors = {
    'view': 'bg-blue-500',
    'create': 'bg-green-500',
    'edit': 'bg-yellow-500',
    'delete': 'bg-red-500',
    'export': 'bg-purple-500',
    'manage': 'bg-indigo-500',
    'assign': 'bg-pink-500'
  }
  return colors[action] || 'bg-gray-500'
}

const getModuleGradient = (moduleName) => {
  const gradients = {
    'users': 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)',
    'roles': 'linear-gradient(135deg, #f093fb 0%, #f5576c 100%)',
    'permissions': 'linear-gradient(135deg, #4facfe 0%, #00f2fe 100%)',
    'dashboard': 'linear-gradient(135deg, #43e97b 0%, #38f9d7 100%)',
    'activity': 'linear-gradient(135deg, #fa709a 0%, #fee140 100%)',
    'settings': 'linear-gradient(135deg, #a8edea 0%, #fed6e3 100%)',
    'support': 'linear-gradient(135deg, #ff9a9e 0%, #fecfef 100%)',
    'appointments': 'linear-gradient(135deg, #ffecd2 0%, #fcb69f 100%)',
    'medical_records': 'linear-gradient(135deg, #a18cd1 0%, #fbc2eb 100%)',
    'audit': 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)'
  }
  return gradients[moduleName] || 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)'
}

const getModuleIcon = (moduleName) => {
  const icons = {
    'users': 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m3 5.197V9a3 3 0 00-3-3m3 3a3 3 0 11-6 0m6 0a3 3 0 11-6 0m6 0v1a6 6 0 01-12 0V9a3 3 0 016-6m6 0a3 3 0 11-6 0 3 3 0 016 0z',
    'roles': 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z',
    'permissions': 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z',
    'dashboard': 'M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z',
    'activity': 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z',
    'settings': 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z'
  }
  return icons[moduleName] || 'M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10'
}

const formatDate = (date) => {
  return new Date(date).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric'
  })
}

const toggleBulkMenu = (action) => {
  activeBulkMenu.value = activeBulkMenu.value === action ? null : action
}

const bulkAssignToRole = async (permissions, roleName) => {
  try {
    const role = availableRoles.value.find(r => r.name.toLowerCase().includes(roleName))
    if (!role) {
      showError(`${roleName} role not found`)
      return
    }
    
    const permissionIds = permissions.map(p => p.id)
    await rolesStore.assignPermissions(role.id, permissionIds)
    
    emit('permissions-updated')
    showSuccess(`${permissions.length} permissions assigned to ${role.display_name || role.name}`)
    activeBulkMenu.value = null
  } catch (error) {
    showError('Failed to assign permissions')
  }
}

const showRoleAssignmentModal = (permissions) => {
  // This would open a custom assignment modal
  activeBulkMenu.value = null
  // Implement custom assignment logic
}

const togglePermissionRoles = (permission) => {
  selectedPermission.value = permission
  showRolesModal.value = true
}

const handleRolesUpdated = () => {
  emit('permissions-updated')
  showRolesModal.value = false
}

const exportModulePermissions = () => {
  const csvData = modulePermissions.value.map(permission => ({
    name: permission.name,
    description: permission.description,
    type: permission.type,
    status: permission.status,
    roles: permission.roles?.map(r => r.name).join(', ') || ''
  }))
  
  // Convert to CSV and download
  const csv = convertToCSV(csvData)
  downloadCSV(csv, `${props.module.name}_permissions.csv`)
}

const convertToCSV = (data) => {
  const headers = Object.keys(data[0]).join(',')
  const rows = data.map(row => Object.values(row).join(','))
  return [headers, ...rows].join('\n')
}

const downloadCSV = (csv, filename) => {
  const blob = new Blob([csv], { type: 'text/csv' })
  const url = window.URL.createObjectURL(blob)
  const a = document.createElement('a')
  a.href = url
  a.download = filename
  a.click()
  window.URL.revokeObjectURL(url)
}

// Close bulk menu when clicking outside
document.addEventListener('click', (e) => {
  if (!e.target.closest('.relative')) {
    activeBulkMenu.value = null
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