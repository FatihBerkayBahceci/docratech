<template>
  <AppDialog :show="show" @close="$emit('close')" max-width="3xl">
    <template #header>
      <div class="flex items-center space-x-3">
        <div class="p-2 bg-purple-100 rounded-lg">
          <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
          </svg>
        </div>
        <div>
          <h3 class="text-lg font-semibold text-gray-900">Permission Roles</h3>
          <p class="text-sm text-gray-600">{{ permission?.name || 'N/A' }}</p>
        </div>
      </div>
    </template>

    <div v-if="permission" class="space-y-6">
      <!-- Permission Details -->
      <div class="bg-gray-50 rounded-lg p-4">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
          <div>
            <span class="text-sm font-medium text-gray-600">Permission Name</span>
            <p class="text-sm text-gray-900">{{ permission.display_name || permission.name }}</p>
          </div>
          <div>
            <span class="text-sm font-medium text-gray-600">Module</span>
            <p class="text-sm text-gray-900">{{ permission.module || 'N/A' }}</p>
          </div>
          <div>
            <span class="text-sm font-medium text-gray-600">Type</span>
            <span :class="[
              'px-2 py-1 text-xs font-medium rounded-full',
              permission.type === 'system' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800'
            ]">
              {{ permission.type }}
            </span>
          </div>
        </div>
        <div class="mt-3">
          <span class="text-sm font-medium text-gray-600">Description</span>
          <p class="text-sm text-gray-900">{{ permission.description || 'No description available' }}</p>
        </div>
      </div>

      <!-- Role Assignment Section -->
      <div class="space-y-4">
        <div class="flex items-center justify-between">
          <h4 class="text-lg font-medium text-gray-900">Role Assignments</h4>
          <button
            @click="showAddRoleForm = true"
            class="px-4 py-2 bg-purple-500 text-white rounded-lg hover:bg-purple-600 transition-colors flex items-center gap-2"
          >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
            </svg>
            Assign Role
          </button>
        </div>

        <!-- Loading State -->
        <div v-if="loading" class="text-center py-8">
          <svg class="w-8 h-8 text-purple-500 animate-spin mx-auto mb-4" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
          </svg>
          <p class="text-gray-600">Loading roles...</p>
        </div>

        <!-- Assigned Roles List -->
        <div v-else-if="assignedRoles.length > 0" class="space-y-3">
          <div 
            v-for="role in assignedRoles" 
            :key="role.id"
            class="flex items-center justify-between p-4 bg-white border border-gray-200 rounded-lg hover:border-purple-300 transition-colors"
          >
            <div class="flex items-center space-x-3">
              <div :class="[
                'w-10 h-10 rounded-lg flex items-center justify-center',
                role.type === 'system' ? 'bg-blue-100' : 'bg-green-100'
              ]">
                <svg class="w-5 h-5" :class="role.type === 'system' ? 'text-blue-600' : 'text-green-600'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
              </div>
              <div>
                <h5 class="font-medium text-gray-900">{{ role.display_name || role.name }}</h5>
                <p class="text-sm text-gray-600">{{ role.description || 'No description' }}</p>
                <div class="flex items-center gap-2 mt-1">
                  <span :class="[
                    'px-2 py-1 text-xs font-medium rounded-full',
                    role.type === 'system' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800'
                  ]">
                    {{ role.type }}
                  </span>
                  <span class="text-xs text-gray-500">{{ role.users_count || 0 }} users</span>
                </div>
              </div>
            </div>
            
            <div class="flex items-center gap-2">
              <!-- Assignment Details -->
              <div class="text-right text-sm text-gray-500">
                <div>Assigned: {{ formatDate(role.pivot?.created_at) }}</div>
                <div v-if="role.pivot?.granted_by">By: {{ role.pivot.granted_by_name }}</div>
              </div>
              
              <!-- Remove Button -->
              <button
                @click="removeRole(role)"
                :disabled="role.type === 'system' && permission?.type === 'system'"
                class="p-2 text-red-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                :title="role.type === 'system' && permission?.type === 'system' ? 'Cannot remove system permissions from system roles' : 'Remove this role'"
              >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>
              </button>
            </div>
          </div>
        </div>

        <!-- Empty State -->
        <div v-else class="text-center py-8">
          <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
          </svg>
          <h3 class="text-lg font-medium text-gray-900 mb-2">No roles assigned</h3>
          <p class="text-gray-500 mb-4">This permission is not assigned to any roles yet</p>
          <button
            @click="showAddRoleForm = true"
            class="px-4 py-2 bg-purple-500 text-white rounded-lg hover:bg-purple-600 transition-colors"
          >
            Assign First Role
          </button>
        </div>
      </div>
    </div>

    <!-- Add Role Form -->
    <div v-if="showAddRoleForm" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
      <div class="bg-white rounded-lg max-w-md w-full mx-4">
        <div class="p-6 border-b border-gray-200">
          <h3 class="text-lg font-semibold text-gray-900">Assign Role</h3>
          <p class="text-sm text-gray-600">Select a role to assign this permission to</p>
        </div>
        
        <div class="p-6 space-y-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Available Roles</label>
            <select
              v-model="selectedRoleId"
              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
            >
              <option value="">Select a role...</option>
              <option v-for="role in availableRoles" :key="role.id" :value="role.id">
                {{ role.display_name || role.name }} ({{ role.type }})
              </option>
            </select>
          </div>
          
          <div v-if="selectedRole" class="p-3 bg-gray-50 rounded-lg">
            <h4 class="font-medium text-gray-900">{{ selectedRole.display_name || selectedRole.name }}</h4>
            <p class="text-sm text-gray-600">{{ selectedRole.description || 'No description' }}</p>
            <div class="flex items-center gap-2 mt-2">
              <span :class="[
                'px-2 py-1 text-xs font-medium rounded-full',
                selectedRole.type === 'system' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800'
              ]">
                {{ selectedRole.type }}
              </span>
              <span class="text-xs text-gray-500">{{ selectedRole.users_count || 0 }} users</span>
            </div>
          </div>
          
          <div class="flex justify-end gap-3 pt-4">
            <button
              @click="cancelAddRole"
              class="px-4 py-2 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors"
            >
              Cancel
            </button>
            <button
              @click="assignRole"
              :disabled="!selectedRoleId || assigning"
              class="px-4 py-2 bg-purple-500 text-white rounded-lg hover:bg-purple-600 disabled:opacity-50 disabled:cursor-not-allowed transition-colors flex items-center gap-2"
            >
              <svg v-if="assigning" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              {{ assigning ? 'Assigning...' : 'Assign Role' }}
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
import api from '@/services/api'

const props = defineProps({
  show: Boolean,
  permission: {
    type: Object,
    default: null
  },
  roles: {
    type: Array,
    default: () => []
  }
})

const emit = defineEmits(['close', 'roles-updated'])

// Composables
const { success: showSuccess, error: showError } = useToast()
const rolesStore = useRolesStore()

// State
const loading = ref(false)
const assigning = ref(false)
const showAddRoleForm = ref(false)
const selectedRoleId = ref('')
const assignedRoles = ref([])

// Computed
const selectedRole = computed(() => {
  return props.roles.find(role => role.id === parseInt(selectedRoleId.value))
})

const availableRoles = computed(() => {
  const assignedRoleIds = assignedRoles.value.map(role => role.id)
  return props.roles.filter(role => !assignedRoleIds.includes(role.id))
})

// Methods
const loadAssignedRoles = async () => {
  if (!props.permission?.id) return
  
  loading.value = true
  try {
    const response = await api.get(`/permissions/${props.permission.id}/roles`)
    assignedRoles.value = response.data.data.roles || []
  } catch (error) {
    showError('Failed to load assigned roles')
    console.error('Load assigned roles error:', error)
  } finally {
    loading.value = false
  }
}

const assignRole = async () => {
  if (!selectedRoleId.value || !props.permission?.id) return
  
  assigning.value = true
  try {
    await rolesStore.assignPermissions(selectedRoleId.value, [props.permission.id])
    
    // Reload assigned roles
    await loadAssignedRoles()
    
    showSuccess('Role assigned successfully')
    cancelAddRole()
    emit('roles-updated')
  } catch (error) {
    showError('Failed to assign role')
    console.error('Assign role error:', error)
  } finally {
    assigning.value = false
  }
}

const removeRole = async (role) => {
  if (!props.permission?.id) return
  
  try {
    await rolesStore.revokePermissions(role.id, [props.permission.id])
    
    // Reload assigned roles
    await loadAssignedRoles()
    
    showSuccess('Role removed successfully')
    emit('roles-updated')
  } catch (error) {
    showError('Failed to remove role')
    console.error('Remove role error:', error)
  }
}

const cancelAddRole = () => {
  showAddRoleForm.value = false
  selectedRoleId.value = ''
}

const formatDate = (date) => {
  if (!date) return 'Unknown'
  return new Date(date).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric'
  })
}

// Watch for permission changes
watch(() => props.permission, (newPermission) => {
  if (newPermission && props.show) {
    loadAssignedRoles()
  }
}, { immediate: true })

watch(() => props.show, (show) => {
  if (show && props.permission) {
    loadAssignedRoles()
  } else {
    // Reset state when modal closes
    assignedRoles.value = []
    cancelAddRole()
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