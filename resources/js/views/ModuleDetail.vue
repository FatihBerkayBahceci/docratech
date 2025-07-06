<template>
  <div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-100">
    <!-- Loading State -->
    <div v-if="loading" class="min-h-screen flex items-center justify-center">
      <div class="text-center">
        <div class="animate-spin rounded-full h-16 w-16 border-b-2 border-blue-600 mx-auto mb-4"></div>
        <p class="text-gray-600">Loading module details...</p>
      </div>
    </div>

    <!-- Error State -->
    <div v-else-if="error" class="min-h-screen flex items-center justify-center">
      <div class="text-center max-w-md mx-auto">
        <div class="bg-red-100 rounded-full p-4 w-16 h-16 mx-auto mb-4 flex items-center justify-center">
          <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"/>
          </svg>
        </div>
        <h3 class="text-lg font-medium text-gray-900 mb-2">Module Not Found</h3>
        <p class="text-gray-600 mb-4">{{ error }}</p>
        <button @click="goBack" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
          Go Back
        </button>
      </div>
    </div>

    <!-- Main Content -->
    <div v-else>
      <!-- Sticky Module Header -->
      <div class="sticky top-0 z-30 bg-white/90 backdrop-blur-sm border-b border-gray-200 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 flex items-center justify-between">
          <div class="flex items-center gap-4">
            <div class="p-3 bg-gradient-to-r from-blue-600 to-indigo-600 rounded-xl">
              <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path :d="moduleIcon"/>
              </svg>
            </div>
            <div>
              <h1 class="text-2xl font-bold text-gray-900 flex items-center gap-2">
                {{ moduleData.displayName }}
                <span v-if="moduleData.trend !== undefined" :class="moduleData.trend >= 0 ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'" class="ml-2 px-2 py-0.5 rounded-full text-xs font-semibold animate-pulse">
                  {{ moduleData.trend >= 0 ? '+' : '' }}{{ moduleData.trend }}%
                </span>
              </h1>
              <p class="text-gray-600 text-sm">{{ moduleData.description }}</p>
            </div>
          </div>
          <div class="flex items-center gap-3">
            <button @click="goBack" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg font-medium transition-all flex items-center gap-2">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
              </svg>
              Back
            </button>
            <button @click="exportPermissions" class="px-4 py-2 bg-gradient-to-r from-green-600 to-emerald-600 text-white rounded-lg hover:from-green-700 hover:to-emerald-700 transition-all font-medium flex items-center gap-2">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
              </svg>
              Export
            </button>
          </div>
        </div>
      </div>

      <!-- Module Stats -->
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100 flex flex-col items-center hover:shadow-md transition-all">
          <div class="text-3xl font-extrabold text-blue-600">{{ moduleData.permissions.length }}</div>
          <div class="text-sm text-gray-600">Total Permissions</div>
        </div>
        <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100 flex flex-col items-center hover:shadow-md transition-all">
          <div class="text-3xl font-extrabold text-green-600">{{ activePermissions }}</div>
          <div class="text-sm text-gray-600">Active</div>
        </div>
        <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100 flex flex-col items-center hover:shadow-md transition-all">
          <div class="text-3xl font-extrabold text-purple-600">{{ systemPermissions }}</div>
          <div class="text-sm text-gray-600">System</div>
        </div>
        <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100 flex flex-col items-center hover:shadow-md transition-all">
          <div class="text-3xl font-extrabold text-indigo-600">{{ assignedRoles }}</div>
          <div class="text-sm text-gray-600">Roles</div>
        </div>
      </div>

      <!-- Main Content -->
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-10">
        <!-- Permissions Grid -->
        <div>
          <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-bold text-gray-900">Permissions in {{ moduleData.displayName }}</h2>
            <div class="flex items-center gap-4">
              <input v-model="searchQuery" type="text" placeholder="Search permissions..." class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent w-80" />
              <button @click="refreshData" class="px-3 py-2 text-gray-600 hover:text-gray-800 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                </svg>
              </button>
            </div>
          </div>
          <div v-if="filteredPermissions.length > 0" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
            <div v-for="permission in filteredPermissions" :key="permission.id" class="bg-white rounded-xl shadow p-5 flex flex-col gap-2 hover:shadow-lg transition-all border border-gray-100">
              <div class="flex items-center gap-2">
                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium"
                  :class="{
                    'bg-blue-100 text-blue-800': permission.type === 'system',
                    'bg-purple-100 text-purple-800': permission.type === 'custom',
                    'bg-green-100 text-green-800': permission.type === 'hipaa',
                    'bg-orange-100 text-orange-800': permission.type === 'medical',
                    'bg-gray-100 text-gray-800': !permission.type
                  }">
                  {{ formatPermissionType(permission.type) }}
                </span>
                <span v-if="permission.status === 'active'" class="ml-2 px-2 py-0.5 rounded-full bg-green-100 text-green-700 text-xs font-semibold">Active</span>
              </div>
              <div class="text-lg font-semibold text-gray-900">{{ formatPermissionName(permission.name) }}</div>
              <div class="text-sm text-gray-600">{{ permission.description || 'No description available' }}</div>
              <div class="flex items-center gap-2 mt-2">
                <span class="text-xs text-gray-500">Key: {{ permission.key }}</span>
                <span v-if="permission.guard_name && permission.guard_name !== 'web'" class="text-xs text-gray-400">Guard: {{ permission.guard_name }}</span>
              </div>
              <div class="flex items-center gap-2 mt-2">
                <span class="text-xs text-gray-500">Roles: {{ permission.roles?.length || 0 }}</span>
                <button @click="showRoles(permission)" class="ml-auto px-2 py-1 text-xs bg-blue-50 text-blue-700 rounded hover:bg-blue-100 transition">View Roles</button>
              </div>
            </div>
          </div>
          <div v-else class="text-center py-12">
            <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
            </svg>
            <h3 class="text-lg font-medium text-gray-900 mb-2">No permissions found</h3>
            <p class="text-gray-600">No permissions match your search in this module.</p>
          </div>
        </div>

        <!-- Role Matrix -->
        <div>
          <h2 class="text-xl font-bold text-gray-900 mb-4">Role Matrix</h2>
          <RolePermissionMatrix :roles="roles" :permissions="moduleData.permissions" />
        </div>

        <!-- Audit Trail -->
        <div>
          <h2 class="text-xl font-bold text-gray-900 mb-4">Audit Trail</h2>
          <PermissionAuditTrail :audit-logs="auditLogs" />
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { usePermissionsStore } from '@/stores/permissions'
import { useRolesStore } from '@/stores/roles'
import { useToast } from '@/composables/useToast'
import RolePermissionMatrix from '@/components/admin/permissions/RolePermissionMatrix.vue'
import PermissionAuditTrail from '@/components/admin/permissions/PermissionAuditTrail.vue'

const route = useRoute()
const router = useRouter()
const permissionsStore = usePermissionsStore()
const rolesStore = useRolesStore()
const { success: showSuccess, error: showError } = useToast()

const moduleName = route.params.moduleName
const loading = ref(true)
const error = ref(null)
const moduleData = ref({
  name: '',
  displayName: '',
  description: '',
  permissions: [],
  trend: 0
})
const roles = ref([])
const auditLogs = ref([])
const searchQuery = ref('')

const moduleIcon = computed(() => {
  // Map module names to specific icons
  const iconMap = {
    'users': 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z',
    'roles': 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z',
    'permissions': 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z',
    'dashboard': 'M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2zM8 5h8M8 11h8M8 17h8',
    'settings': 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z',
    'activity': 'M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01',
    'logs': 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'
  }
  return iconMap[moduleName] || 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z'
})

const formatPermissionName = (name) => {
  if (!name) return 'Unknown Permission'
  return name.replace(/[-_]/g, ' ').replace(/\b\w/g, l => l.toUpperCase()).trim()
}

const formatPermissionType = (type) => {
  if (!type) return 'General'
  const typeMap = {
    'system': 'System',
    'custom': 'Custom',
    'hipaa': 'HIPAA',
    'medical': 'Medical',
    'admin': 'Administrative',
    'user': 'User',
    'role': 'Role Management',
    'permission': 'Permission Management'
  }
  return typeMap[type] || type.charAt(0).toUpperCase() + type.slice(1)
}

const activePermissions = computed(() => moduleData.value.permissions.filter(p => p.status === 'active').length)
const systemPermissions = computed(() => moduleData.value.permissions.filter(p => p.type === 'system').length)
const assignedRoles = computed(() => {
  const roleSet = new Set()
  moduleData.value.permissions.forEach(p => {
    if (Array.isArray(p.roles)) {
      p.roles.forEach(r => roleSet.add(r.id))
    }
  })
  return roleSet.size
})

const filteredPermissions = computed(() => {
  if (!searchQuery.value) return moduleData.value.permissions
  const search = searchQuery.value.toLowerCase()
  return moduleData.value.permissions.filter(p =>
    p.name.toLowerCase().includes(search) ||
    (p.description && p.description.toLowerCase().includes(search)) ||
    (p.key && p.key.toLowerCase().includes(search))
  )
})

const loadModuleData = async () => {
  try {
    loading.value = true
    error.value = null
    
    // Load permissions and roles if not already loaded
    if (!permissionsStore.permissions || permissionsStore.permissions.length === 0) {
      await permissionsStore.fetchPermissions({ per_page: 100 })
    }
    
    if (!rolesStore.roles || rolesStore.roles.length === 0) {
      await rolesStore.fetchRoles({ per_page: 100 })
    }
    
    // Get modules from store
    const allModules = permissionsStore.getModulesWithPermissions()
    const found = allModules.find(m => m.name === moduleName)
    
    if (!found) {
      error.value = `Module "${moduleName}" not found. It may have been deleted or you may not have permission to access it.`
      return
    }
    
    // Set module data
    moduleData.value = {
      ...found,
      displayName: found.displayName || formatPermissionName(found.name),
      description: found.description || `${found.name} module permissions and management`
    }
    
    // Set roles
    roles.value = rolesStore.roles || []
    
    // Load audit logs for this module
    try {
      await permissionsStore.fetchAuditLogs({ module: moduleName })
      auditLogs.value = permissionsStore.auditLogs.filter(a => 
        a.module === moduleName || a.permission?.includes(moduleName)
      )
    } catch (auditError) {
      console.warn('Failed to load audit logs:', auditError)
      auditLogs.value = []
    }
    
    console.log('✅ Module data loaded successfully:', {
      module: moduleData.value.name,
      permissions: moduleData.value.permissions.length,
      roles: roles.value.length,
      auditLogs: auditLogs.value.length
    })
    
  } catch (err) {
    console.error('❌ Failed to load module data:', err)
    error.value = 'Failed to load module data. Please try again.'
    showError('Failed to load module data')
  } finally {
    loading.value = false
  }
}

const refreshData = async () => {
  await loadModuleData()
  showSuccess('Module data refreshed successfully!')
}

const goBack = () => {
  router.push({ name: 'permissions' })
}

const exportPermissions = () => {
  // Create CSV export
  const csvContent = [
    ['Permission Name', 'Type', 'Status', 'Key', 'Description', 'Roles Count'],
    ...moduleData.value.permissions.map(p => [
      formatPermissionName(p.name),
      formatPermissionType(p.type),
      p.status || 'unknown',
      p.key || '',
      p.description || '',
      p.roles?.length || 0
    ])
  ].map(row => row.map(cell => `"${cell}"`).join(',')).join('\n')
  
  const blob = new Blob([csvContent], { type: 'text/csv' })
  const url = window.URL.createObjectURL(blob)
  const a = document.createElement('a')
  a.href = url
  a.download = `${moduleName}_permissions_${new Date().toISOString().split('T')[0]}.csv`
  document.body.appendChild(a)
  a.click()
  document.body.removeChild(a)
  window.URL.revokeObjectURL(url)
  
  showSuccess('Permissions exported successfully!')
}

const showRoles = (permission) => {
  // This would typically open a modal showing roles with this permission
  const roleNames = permission.roles?.map(r => r.name || r.display_name).join(', ') || 'No roles assigned'
  showSuccess(`Roles with "${permission.name}": ${roleNames}`)
}

// Watch for route changes
watch(() => route.params.moduleName, (newModuleName) => {
  if (newModuleName && newModuleName !== moduleName) {
    loadModuleData()
  }
})

onMounted(() => {
  loadModuleData()
})
</script>

<style scoped>
/* Custom animations */
@keyframes fade-in {
  from { opacity: 0; transform: translateY(10px); }
  to { opacity: 1; transform: translateY(0); }
}

.animate-fade-in {
  animation: fade-in 0.3s ease-out;
}

/* Smooth transitions */
.transition-all {
  transition: all 0.2s ease-in-out;
}

/* Hover effects */
.hover\:shadow-md:hover {
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
}

.hover\:shadow-lg:hover {
  box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
}
</style> 