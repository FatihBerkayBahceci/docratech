<!--
Developer: DocraTech Team
Language: American English (US)  
License: MIT
Project: DocraTech Medical Website Platform
Version: 3.0 - Enterprise Role View Modal
Description: Professional role viewing modal with enterprise design standards

UX IMPROVEMENTS:
- Modern modal design with glass-morphism effects
- Comprehensive role information display
- Professional styling and interactions
- Enhanced accessibility features
- Consistent with enterprise design patterns
-->

<template>
  <Teleport to="body">
    <div v-if="role" class="fixed inset-0 z-50 overflow-y-auto">
      <!-- Backdrop -->
      <div 
        class="fixed inset-0 bg-black/30 backdrop-blur-sm transition-opacity duration-300"
        @click="$emit('close')"
      ></div>

      <!-- Modal Container -->
      <div class="relative min-h-screen flex items-center justify-center p-4">
        <div class="relative bg-white/95 backdrop-blur-xl rounded-2xl shadow-2xl border border-white/20 w-full max-w-2xl max-h-[90vh] overflow-hidden">
      <!-- Header -->
          <div class="relative bg-gradient-to-r from-purple-600 to-blue-600 px-6 py-8">
            <div class="absolute inset-0 bg-white/10 backdrop-blur-sm"></div>
            <div class="relative">
              <div class="flex items-center justify-between">
                <div class="flex items-center gap-4">
                  <div class="w-16 h-16 rounded-2xl flex items-center justify-center shadow-lg"
                       :class="role.type === 'system' ? 'bg-white/20' : 'bg-white/20'">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                            :d="role.type === 'system' ? 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z' : 'M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1721 9z'"/>
                    </svg>
                  </div>
                  <div>
                    <h2 class="text-2xl font-bold text-white">{{ role.display_name || role.name }}</h2>
                    <p class="text-white/80 mt-1">{{ role.description || 'No description available' }}</p>
                  </div>
                </div>
        <button
          @click="$emit('close')"
                  class="p-2 text-white/80 hover:text-white hover:bg-white/10 rounded-lg transition-colors duration-200"
        >
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
          </svg>
        </button>
      </div>
            </div>
          </div>

          <!-- Content -->
          <div class="p-6 max-h-[60vh] overflow-y-auto">
            <!-- Role Details Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
              <!-- Basic Information -->
              <div class="space-y-4">
                <h3 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
                  <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                  </svg>
                  Basic Information
                </h3>
                <div class="bg-gray-50 rounded-xl p-4 space-y-3">
                  <div class="flex justify-between items-center">
                    <span class="text-sm font-medium text-gray-600">Role Name</span>
                    <span class="text-sm text-gray-900">{{ role.name }}</span>
            </div>
                  <div class="flex justify-between items-center">
                    <span class="text-sm font-medium text-gray-600">Display Name</span>
                    <span class="text-sm text-gray-900">{{ role.display_name || role.name }}</span>
            </div>
                  <div class="flex justify-between items-center">
                    <span class="text-sm font-medium text-gray-600">Type</span>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                          :class="role.type === 'system' ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800'">
                      {{ role.type }}
              </span>
            </div>
                  <div class="flex justify-between items-center">
                    <span class="text-sm font-medium text-gray-600">Status</span>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                          :class="role.status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'">
                      <span class="w-2 h-2 mr-1 rounded-full"
                            :class="role.status === 'active' ? 'bg-green-500' : 'bg-red-500'"></span>
                      {{ role.status }}
                    </span>
            </div>
          </div>
        </div>

              <!-- Statistics -->
              <div class="space-y-4">
                <h3 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
                  <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                  </svg>
                  Statistics
                </h3>
                <div class="grid grid-cols-2 gap-4">
                  <div class="bg-gradient-to-br from-purple-50 to-blue-50 rounded-xl p-4 text-center">
                    <div class="text-2xl font-bold text-purple-600">{{ role.permissions_count || 0 }}</div>
                    <div class="text-sm text-gray-600">Permissions</div>
                  </div>
                  <div class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-xl p-4 text-center">
                    <div class="text-2xl font-bold text-blue-600">{{ role.users_count || 0 }}</div>
                    <div class="text-sm text-gray-600">Users</div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Permissions Section -->
            <div class="space-y-4">
              <h3 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
                <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                </svg>
                Permissions
                <span class="text-sm text-gray-500">({{ permissions.length }})</span>
              </h3>
              
              <div v-if="loadingPermissions" class="flex justify-center py-8">
                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-purple-600"></div>
              </div>
              
              <div v-else-if="permissions.length === 0" class="text-center py-8">
                <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                </svg>
                <p class="text-gray-500">No permissions assigned to this role.</p>
          </div>
              
              <div v-else class="space-y-3">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                  <div
                    v-for="permission in permissions"
                    :key="permission.id"
                    class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors duration-200"
                  >
                    <div class="w-8 h-8 bg-gradient-to-br from-purple-500 to-blue-600 rounded-lg flex items-center justify-center">
                      <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
                    </div>
                    <div class="flex-1">
                      <div class="text-sm font-medium text-gray-900">{{ permission.display_name || permission.name }}</div>
                      <div class="text-xs text-gray-500">{{ permission.description || permission.name }}</div>
                    </div>
            </div>
          </div>
        </div>
            </div>

            <!-- Users Section -->
            <div class="space-y-4 mt-8">
              <h3 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
                <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m3 5.197V9a3 3 0 00-3-3m3 3a3 3 0 11-6 0m6 0a3 3 0 11-6 0m6 0v1a6 6 0 01-12 0V9a3 3 0 016-6m6 0a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                Users with this Role
                <span class="text-sm text-gray-500">({{ users.length }})</span>
              </h3>
              
              <div v-if="loadingUsers" class="flex justify-center py-8">
                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-purple-600"></div>
              </div>
              
              <div v-else-if="users.length === 0" class="text-center py-8">
                <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m3 5.197V9a3 3 0 00-3-3m3 3a3 3 0 11-6 0m6 0a3 3 0 11-6 0m6 0v1a6 6 0 01-12 0V9a3 3 0 016-6m6 0a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                <p class="text-gray-500">No users assigned to this role.</p>
              </div>
              
              <div v-else class="space-y-3">
                <div class="grid grid-cols-1 gap-3">
                  <div
                    v-for="user in users"
                    :key="user.id"
                    class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors duration-200"
                  >
                    <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center">
                      <span class="text-white font-medium text-sm">{{ getUserInitials(user) }}</span>
                    </div>
                    <div class="flex-1">
                      <div class="text-sm font-medium text-gray-900">{{ user.name }}</div>
                      <div class="text-xs text-gray-500">{{ user.email }}</div>
                    </div>
                    <div class="text-xs text-gray-400">{{ formatDate(user.created_at) }}</div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Timestamps -->
            <div class="mt-8 pt-6 border-t border-gray-200">
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm text-gray-600">
                <div>
                  <span class="font-medium">Created:</span>
                  {{ formatDate(role.created_at) }}
                </div>
                <div>
                  <span class="font-medium">Last Updated:</span>
                  {{ formatDate(role.updated_at) }}
                </div>
              </div>
            </div>
          </div>

          <!-- Footer -->
          <div class="border-t border-gray-200 px-6 py-4 bg-gray-50 rounded-b-2xl">
            <div class="flex items-center justify-between">
              <div class="flex items-center gap-2 text-sm text-gray-600">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Role ID: {{ role.id }}
        </div>
              <div class="flex items-center gap-3">
                <button
                  @click="$emit('close')"
                  class="px-4 py-2 text-gray-600 hover:text-gray-800 transition-colors duration-200"
                >
                  Close
                </button>
                <button
                  @click="$emit('edit', role)"
                  class="px-6 py-2 bg-gradient-to-r from-purple-600 to-blue-600 text-white rounded-lg hover:shadow-lg hover:scale-105 transition-all duration-200"
                >
                  Edit Role
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </Teleport>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import { useRolesStore } from '@/stores/roles'

const props = defineProps({
    role: {
      type: Object,
      required: true
    }
})

const emit = defineEmits(['close', 'edit'])

// Stores
const rolesStore = useRolesStore()

// State
const permissions = ref([])
const users = ref([])
const loadingPermissions = ref(false)
const loadingUsers = ref(false)

// Methods
const getUserInitials = (user) => {
  return user.name.split(' ').map(n => n[0]).join('').toUpperCase()
}

const formatDate = (dateString) => {
  if (!dateString) return 'N/A'
      return new Date(dateString).toLocaleDateString('en-US', {
        year: 'numeric',
    month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
  })
}

const loadRolePermissions = async () => {
  if (!props.role?.id) return
  
  loadingPermissions.value = true
  try {
    const response = await rolesStore.getRolePermissions(props.role.id)
    // Handle different response formats
    if (response.data) {
      permissions.value = Array.isArray(response.data) ? response.data : (response.data.permissions || [])
    } else if (response.permissions) {
      permissions.value = response.permissions
    } else {
      permissions.value = []
    }
  } catch (error) {
    console.error('Failed to load role permissions:', error)
    permissions.value = []
  } finally {
    loadingPermissions.value = false
  }
}

const loadRoleUsers = async () => {
  if (!props.role?.id) return
  
  loadingUsers.value = true
  try {
    const response = await rolesStore.getRoleUsers(props.role.id)
    // Handle different response formats
    if (response.data) {
      users.value = Array.isArray(response.data) ? response.data : (response.data.users || [])
    } else if (response.users) {
      users.value = response.users
    } else {
      users.value = []
    }
  } catch (error) {
    console.error('Failed to load role users:', error)
    users.value = []
  } finally {
    loadingUsers.value = false
  }
}

// Lifecycle
onMounted(async () => {
  await Promise.all([
    loadRolePermissions(),
    loadRoleUsers()
  ])
})
</script>

<style scoped>
/* Custom scrollbar for modal content */
.max-h-\[60vh\]::-webkit-scrollbar {
  width: 6px;
}

.max-h-\[60vh\]::-webkit-scrollbar-track {
  background: #f1f5f9;
  border-radius: 3px;
}

.max-h-\[60vh\]::-webkit-scrollbar-thumb {
  background: #cbd5e1;
  border-radius: 3px;
}

.max-h-\[60vh\]::-webkit-scrollbar-thumb:hover {
  background: #94a3b8;
}

/* Animation for modal */
@keyframes modalSlideIn {
  from {
    opacity: 0;
    transform: translateY(-10px) scale(0.95);
  }
  to {
    opacity: 1;
    transform: translateY(0) scale(1);
  }
}

.relative.bg-white\/95 {
  animation: modalSlideIn 0.3s ease-out;
}
</style>
