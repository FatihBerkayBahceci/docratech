<template>
  <div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-100">
    <!-- Header -->
    <div class="bg-white border-b border-gray-200 shadow-sm">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <div class="flex items-center justify-between">
          <div class="flex items-center space-x-4">
            <div class="flex items-center space-x-3">
              <div class="p-3 bg-gradient-to-r from-blue-600 to-indigo-600 rounded-xl">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                </svg>
              </div>
              <div>
                <h1 class="text-3xl font-bold text-gray-900">Enterprise Permission Manager</h1>
                <p class="text-gray-600 mt-1">Advanced role and permission management system</p>
              </div>
            </div>
          </div>
          
          <div class="flex items-center space-x-4">
            <!-- View Toggle -->
            <div class="flex items-center bg-gray-100 rounded-lg p-1">
              <button
                @click="viewMode = 'modules'"
                :class="[
                  'px-4 py-2 rounded-md text-sm font-medium transition-all duration-200',
                  viewMode === 'modules' 
                    ? 'bg-white text-blue-600 shadow-sm' 
                    : 'text-gray-600 hover:text-gray-900'
                ]"
              >
                Modules
              </button>
              <button
                @click="viewMode = 'roles'"
                :class="[
                  'px-4 py-2 rounded-md text-sm font-medium transition-all duration-200',
                  viewMode === 'roles' 
                    ? 'bg-white text-blue-600 shadow-sm' 
                    : 'text-gray-600 hover:text-gray-900'
                ]"
              >
                Role Matrix
              </button>
              <button
                @click="viewMode = 'audit'"
                :class="[
                  'px-4 py-2 rounded-md text-sm font-medium transition-all duration-200',
                  viewMode === 'audit' 
                    ? 'bg-white text-blue-600 shadow-sm' 
                    : 'text-gray-600 hover:text-gray-900'
                ]"
              >
                Audit Trail
              </button>
            </div>
            
            <!-- Quick Actions -->
            <button 
              @click="showTemplateModal = true"
              class="px-4 py-2 bg-gradient-to-r from-green-600 to-emerald-600 text-white rounded-lg hover:from-green-700 hover:to-emerald-700 transition-all duration-200 shadow-sm hover:shadow-md"
            >
              Permission Templates
            </button>
          </div>
        </div>
        
        <!-- Statistics Bar -->
        <div class="mt-8 grid grid-cols-1 md:grid-cols-5 gap-6">
          <div 
            v-for="stat in statistics" 
            :key="stat.key"
            class="bg-white rounded-xl p-6 shadow-sm border border-gray-100 hover:shadow-md transition-all duration-200"
          >
            <div class="flex items-center">
              <div 
                class="flex-shrink-0 p-3 rounded-lg"
                :style="{ background: stat.bgColor }"
              >
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="stat.iconPath"/>
                </svg>
              </div>
              <div class="ml-4">
                <p class="text-sm font-medium text-gray-600">{{ stat.label }}</p>
                <p class="text-2xl font-bold text-gray-900">{{ formatNumber(stat.value) }}</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <!-- Modules View -->
      <div v-if="viewMode === 'modules'" class="space-y-8">
        <!-- Search and Filters -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
          <div class="flex items-center justify-between">
            <div class="flex items-center space-x-4">
              <div class="relative">
                <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input
                  v-model="searchQuery"
                  type="text"
                  placeholder="Search modules, permissions..."
                  class="pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent w-80"
                />
              </div>
              
              <select 
                v-model="selectedPermissionType"
                class="px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              >
                <option value="">All Permission Types</option>
                <option value="system">System Permissions</option>
                <option value="custom">Custom Permissions</option>
              </select>
            </div>
            
            <div class="flex items-center space-x-3">
              <button
                @click="refreshModules"
                class="px-4 py-2 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors"
              >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                </svg>
              </button>
            </div>
          </div>
        </div>

        <!-- Module Cards Grid -->
        <div v-if="filteredModules.length > 0" class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-8">
          <ModulePermissionCard
            v-for="module in filteredModules"
            :key="module.name"
            :module="module"
            @permissions-updated="handlePermissionsUpdated"
            @show-role-matrix="showRoleMatrix"
            @open-module-detail="openModuleDetail"
          />
        </div>
        
        <!-- Empty State for Modules -->
        <div v-else class="text-center py-12">
          <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
          </svg>
          <h3 class="text-lg font-medium text-gray-900 mb-2">No modules found</h3>
          <p class="text-gray-600">No permission modules match your current filters.</p>
        </div>
      </div>

      <!-- Role Matrix View -->
      <div v-else-if="viewMode === 'roles'">
        <RolePermissionMatrix 
          :roles="roles"
          :permissions="permissions"
          @permissions-changed="handleRolePermissionsChanged"
        />
      </div>

      <!-- Audit Trail View -->
      <div v-else-if="viewMode === 'audit'">
        <PermissionAuditTrail 
          :audit-logs="auditLogs"
          @load-more="loadMoreAudits"
        />
      </div>
    </div>

    <!-- Permission Templates Modal -->
    <PermissionTemplateModal
      v-model:modelValue="showTemplateModal"
      :templates="permissionTemplates"
      @apply-template="applyPermissionTemplate"
    />

    <!-- Role Matrix Modal -->
    <RoleMatrixModal
      :show="showRoleMatrixModal"
      :module="selectedModuleForMatrix"
      :roles="roles"
      @close="showRoleMatrixModal = false"
      @permissions-updated="handlePermissionsUpdated"
    />
  </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue'
import { usePermissionsStore } from '@/stores/permissions'
import { useRolesStore } from '@/stores/roles'
import { useToast } from '@/composables/useToast'
import { useRouter } from 'vue-router'

// Components
import ModulePermissionCard from '@/components/admin/permissions/ModulePermissionCard.vue'
import RolePermissionMatrix from '@/components/admin/permissions/RolePermissionMatrix.vue'
import PermissionAuditTrail from '@/components/admin/permissions/PermissionAuditTrail.vue'
import PermissionTemplateModal from '@/components/admin/permissions/PermissionTemplateModal.vue'
import RoleMatrixModal from '@/components/admin/permissions/RoleMatrixModal.vue'

// Stores
const permissionsStore = usePermissionsStore()
const rolesStore = useRolesStore()
const { success: showSuccess, error: showError } = useToast()
const router = useRouter()

// State
const viewMode = ref('modules')
const searchQuery = ref('')
const selectedPermissionType = ref('')
const showTemplateModal = ref(false)
const showRoleMatrixModal = ref(false)
const selectedModuleForMatrix = ref(null)

// Computed
const permissions = computed(() => {
  const perms = permissionsStore.permissions || []
  console.log('📊 [EnterprisePermissions] Permissions computed:', perms.length)
  return perms
})
const roles = computed(() => {
  const rolesList = rolesStore.roles || []
  console.log('📊 [EnterprisePermissions] Roles computed:', rolesList.length)
  return rolesList
})
const modules = computed(() => {
  // Defensive: Ensure every module has a permissions array
  const modulesList = permissionsStore.getModulesWithPermissions().map(m => ({
    ...m,
    permissions: Array.isArray(m.permissions) ? m.permissions : []
  }))
  console.log('📊 [EnterprisePermissions] Modules computed:', modulesList.length, modulesList)
  return modulesList
})
const auditLogs = computed(() => permissionsStore.auditLogs || [])

// Statistics
const statistics = ref([
  {
    key: 'total_permissions',
    label: 'Total Permissions',
    value: 0,
    bgColor: 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)',
    iconPath: 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z'
  },
  {
    key: 'active_roles',
    label: 'Active Roles',
    value: 0,
    bgColor: 'linear-gradient(135deg, #11998e 0%, #38ef7d 100%)',
    iconPath: 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z'
  },
  {
    key: 'modules',
    label: 'Modules',
    value: 0,
    bgColor: 'linear-gradient(135deg, #6a11cb 0%, #2575fc 100%)',
    iconPath: 'M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10'
  },
  {
    key: 'system_permissions',
    label: 'System Permissions',
    value: 0,
    bgColor: 'linear-gradient(135deg, #f093fb 0%, #f5576c 100%)',
    iconPath: 'M9 5H7a2 2 0 00-2 2v6a2 2 0 002 2h2m5 0h2a2 2 0 002-2V7a2 2 0 00-2-2h-2m-5 0V3a2 2 0 012-2h2a2 2 0 012 2v2M9 5a2 2 0 002 2h2a2 2 0 002-2'
  },
  {
    key: 'recent_changes',
    label: 'Recent Changes',
    value: 0,
    bgColor: 'linear-gradient(135deg, #ff9a9e 0%, #fecfef 100%)',
    iconPath: 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z'
  }
])

// Permission Templates
const permissionTemplates = ref([
  {
    id: 'admin_template',
    name: 'Administrator',
    description: 'Full system access except sensitive operations',
    permissions: ['users.*', 'roles.*', 'dashboard.*', 'settings.view'],
    color: 'bg-red-500'
  },
  {
    id: 'manager_template', 
    name: 'Manager',
    description: 'User and content management permissions',
    permissions: ['users.view', 'users.create', 'users.edit', 'dashboard.*'],
    color: 'bg-blue-500'
  },
  {
    id: 'staff_template',
    name: 'Staff Member',
    description: 'Basic operational permissions',
    permissions: ['dashboard.view', 'profile.*'],
    color: 'bg-green-500'
  },
  {
    id: 'readonly_template',
    name: 'Read Only',
    description: 'View-only access to most sections',
    permissions: ['*.view', 'dashboard.view', 'profile.view'],
    color: 'bg-gray-500'
  }
])

// Filtered modules
const filteredModules = computed(() => {
  let filtered = [...modules.value]
  
  if (searchQuery.value) {
    const search = searchQuery.value.toLowerCase()
    filtered = filtered.filter(module => 
      module.name.toLowerCase().includes(search) ||
      module.description?.toLowerCase().includes(search)
    )
  }
  
  if (selectedPermissionType.value) {
    filtered = filtered.filter(module => 
      module.permissions?.some(p => p.type === selectedPermissionType.value)
    )
  }
  
  // LOG: Modül ve permissions yapısını kontrol et
  console.log('MODÜLLER:', filtered)
  filtered.forEach(m => {
    console.log('MODÜL:', m.name, 'PERMISSIONS:', m.permissions, 'TİPİ:', typeof m.permissions, 'ISARRAY:', Array.isArray(m.permissions))
  })
  return filtered
})

// Methods
const formatNumber = (value) => {
  return new Intl.NumberFormat('en-US').format(value)
}

const refreshModules = async () => {
  try {
    await loadData()
    showSuccess('Data refreshed successfully!')
  } catch (error) {
    showError('Failed to refresh data')
  }
}

const showRoleMatrix = (module) => {
  selectedModuleForMatrix.value = module
  showRoleMatrixModal.value = true
}

const handlePermissionsUpdated = async () => {
  await loadData()
  showSuccess('Permissions updated successfully!')
}

const handleRolePermissionsChanged = async (changes) => {
  try {
    // Handle batch permission changes
    for (const change of changes) {
      if (change.action === 'grant') {
        await rolesStore.assignPermissions(change.roleId, [change.permissionId])
      } else if (change.action === 'revoke') {
        await rolesStore.revokePermissions(change.roleId, [change.permissionId])
      }
    }
    
    await loadData()
    showSuccess(`${changes.length} permission(s) updated successfully!`)
  } catch (error) {
    showError('Failed to update permissions')
  }
}

const applyPermissionTemplate = async (template, targetRoles) => {
  try {
    for (const roleId of targetRoles) {
      const permissionIds = await resolveTemplatePermissions(template.permissions)
      await rolesStore.assignPermissions(roleId, permissionIds)
    }
    
    await loadData()
    showSuccess(`Template "${template.name}" applied successfully!`)
    showTemplateModal.value = false
  } catch (error) {
    showError('Failed to apply permission template')
  }
}

const resolveTemplatePermissions = async (permissionPatterns) => {
  // Resolve wildcard patterns to actual permission IDs
  const allPermissions = permissions.value
  const resolvedIds = []
  
  for (const pattern of permissionPatterns) {
    if (pattern.endsWith('*')) {
      const prefix = pattern.slice(0, -1)
      const matchingPermissions = allPermissions.filter(p => 
        p.name.startsWith(prefix)
      )
      resolvedIds.push(...matchingPermissions.map(p => p.id))
    } else {
      const exactMatch = allPermissions.find(p => p.name === pattern)
      if (exactMatch) {
        resolvedIds.push(exactMatch.id)
      }
    }
  }
  
  return [...new Set(resolvedIds)] // Remove duplicates
}

const loadMoreAudits = async () => {
  // Load more audit logs
  await permissionsStore.fetchAuditLogs({ offset: auditLogs.value.length })
}

const loadData = async () => {
  try {
    console.log('🔄 Loading Enterprise Permissions data...')
    
    // Load core data with error handling
    const promises = []
    
    // Load permissions
    promises.push(
      permissionsStore.fetchPermissions({ per_page: 100 }).catch(error => {
        console.error('❌ Failed to load permissions:', error)
        return { data: [] }
      })
    )
    
    // Load roles
    promises.push(
      rolesStore.fetchRoles({ per_page: 100 }).catch(error => {
        console.error('❌ Failed to load roles:', error)
        return { data: [] }
      })
    )
    
    await Promise.all(promises)
    
    // Try to get statistics, but don't fail if it doesn't work
    try {
      const stats = await permissionsStore.fetchStatistics()
      // Update statistics with real data
      statistics.value[0].value = stats.total || permissions.value.length
      statistics.value[1].value = roles.value.filter(r => r.status === 'active').length
      statistics.value[2].value = modules.value.length
      statistics.value[3].value = stats.system || permissions.value.filter(p => p.type === 'system').length
      statistics.value[4].value = 0 // Recent changes - set to 0 for now
    } catch (error) {
      console.warn('⚠️ Statistics API failed, using local data:', error)
      // Fallback to local data
      statistics.value[0].value = permissions.value.length
      statistics.value[1].value = roles.value.filter(r => r.status === 'active').length
      statistics.value[2].value = modules.value.length
      statistics.value[3].value = permissions.value.filter(p => p.type === 'system').length
      statistics.value[4].value = 0
    }
    
    console.log('✅ Enterprise Permissions Data Loaded:', {
      permissions: permissions.value.length,
      roles: roles.value.length, 
      modules: modules.value.length,
      statistics: statistics.value.map(s => ({ [s.key]: s.value }))
    })
    
  } catch (error) {
    console.error('❌ Failed to load permission data:', error)
    showError('Some data could not be loaded. Please refresh the page.')
  }
}

const openModuleDetail = (module) => {
  if (module && module.name) {
    router.push({ name: 'permissions.module', params: { moduleName: module.name } })
  }
}

// Lifecycle
onMounted(() => {
  loadData()
})

// Watch for view mode changes
watch(viewMode, (newMode) => {
  if (newMode === 'audit' && auditLogs.value.length === 0) {
    permissionsStore.fetchAuditLogs()
  }
})
</script>

<style scoped>
/* Custom animations */
.fade-enter-active, .fade-leave-active {
  transition: opacity 0.3s ease;
}

.fade-enter-from, .fade-leave-to {
  opacity: 0;
}

.slide-up-enter-active, .slide-up-leave-active {
  transition: all 0.3s ease;
}

.slide-up-enter-from, .slide-up-leave-to {
  opacity: 0;
  transform: translateY(20px);
}
</style> 