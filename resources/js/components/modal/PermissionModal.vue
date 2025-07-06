<!--
  Developer: DocraTech Team - Fatih Berkay Bahceci
  Component: Permission Dialog (Enterprise UI)
-->
<template>
  <Teleport to="body">
    <div v-if="show" class="fixed inset-0 z-50 overflow-y-auto">
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
                  <div class="w-16 h-16 rounded-2xl bg-white/20 flex items-center justify-center shadow-lg">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                  </div>
                  <div>
                    <h2 class="text-2xl font-bold text-white">
                      {{ isEditing ? 'Edit Permission' : 'Create Permission' }}
                    </h2>
                    <p class="text-white/80 mt-1">
                      {{ isEditing ? 'Update permission details' : 'Add a new permission to the system' }}
                    </p>
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
          <form @submit.prevent="handleSubmit" class="p-6 max-h-[60vh] overflow-y-auto">
            <div class="space-y-6">
              <!-- Name & Key Row -->
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                  <label class="block text-sm font-semibold text-gray-700 mb-2">
                    Permission Name <span class="text-red-500">*</span>
                  </label>
                  <input
                    v-model="form.name"
                    type="text"
                    required
                    placeholder="e.g., View Users"
                    class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-300"
                    :class="{ 'border-red-300 bg-red-50': errors.name }"
                    @input="generateKey"
                  >
                  <p v-if="errors.name" class="text-red-600 text-sm mt-1">{{ errors.name }}</p>
                </div>

                <div>
                  <label class="block text-sm font-semibold text-gray-700 mb-2">
                    Permission Key <span class="text-red-500">*</span>
                  </label>
                  <input
                    v-model="form.key"
                    type="text"
                    required
                    placeholder="e.g., users.view"
                    class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-300 font-mono text-sm"
                    :class="{ 'border-red-300 bg-red-50': errors.key }"
                  >
                  <p v-if="errors.key" class="text-red-600 text-sm mt-1">{{ errors.key }}</p>
                </div>
              </div>

              <!-- Description -->
              <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                  Description
                </label>
                <textarea
                  v-model="form.description"
                  rows="3"
                  placeholder="Describe what this permission allows..."
                  class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-300 resize-none"
                  :class="{ 'border-red-300 bg-red-50': errors.description }"
                ></textarea>
                <p v-if="errors.description" class="text-red-600 text-sm mt-1">{{ errors.description }}</p>
              </div>

              <!-- Module & Type Row -->
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                  <label class="block text-sm font-semibold text-gray-700 mb-2">
                    Module <span class="text-red-500">*</span>
                  </label>
                  <select
                    v-model="form.module"
                    required
                    class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-300"
                    :class="{ 'border-red-300 bg-red-50': errors.module }"
                  >
                    <option value="">Select Module</option>
                    <option v-for="module in availableModules" :key="module" :value="module">
                      {{ module }}
                    </option>
                  </select>
                  <p v-if="errors.module" class="text-red-600 text-sm mt-1">{{ errors.module }}</p>
                </div>

                <div>
                  <label class="block text-sm font-semibold text-gray-700 mb-2">
                    Type <span class="text-red-500">*</span>
                  </label>
                  <select
                    v-model="form.type"
                    required
                    class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-300"
                    :class="{ 'border-red-300 bg-red-50': errors.type }"
                  >
                    <option value="">Select Type</option>
                    <option value="system">System</option>
                    <option value="custom">Custom</option>
                  </select>
                  <p v-if="errors.type" class="text-red-600 text-sm mt-1">{{ errors.type }}</p>
                </div>
              </div>

              <!-- Status -->
              <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                  Status <span class="text-red-500">*</span>
                </label>
                <div class="flex items-center gap-4">
                  <label class="flex items-center gap-2 cursor-pointer">
                    <input
                      v-model="form.status"
                      type="radio"
                      value="active"
                      class="w-4 h-4 text-purple-600 focus:ring-purple-500"
                    >
                    <span class="text-sm font-medium text-gray-700">Active</span>
                  </label>
                  <label class="flex items-center gap-2 cursor-pointer">
                    <input
                      v-model="form.status"
                      type="radio"
                      value="inactive"
                      class="w-4 h-4 text-purple-600 focus:ring-purple-500"
                    >
                    <span class="text-sm font-medium text-gray-700">Inactive</span>
                  </label>
                </div>
                <p v-if="errors.status" class="text-red-600 text-sm mt-1">{{ errors.status }}</p>
              </div>
            </div>
          </form>

          <!-- Footer -->
          <div class="border-t border-gray-200 px-6 py-4 bg-gray-50 rounded-b-2xl">
            <div class="flex items-center justify-between">
              <div class="text-sm text-gray-500">
                <span class="text-red-500">*</span> Required fields
              </div>
              <div class="flex items-center gap-3">
                <button
                  type="button"
                  @click="$emit('close')"
                  class="px-6 py-2 text-gray-600 hover:text-gray-800 transition-colors duration-200"
                >
                  Cancel
                </button>
                <button
                  @click="handleSubmit"
                  :disabled="isSubmitting"
                  class="px-6 py-2 bg-gradient-to-r from-purple-600 to-blue-600 text-white rounded-lg hover:shadow-lg hover:scale-105 transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2"
                >
                  <svg v-if="isSubmitting" class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                  </svg>
                  {{ isSubmitting ? 'Saving...' : (isEditing ? 'Update Permission' : 'Create Permission') }}
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
import { ref, computed, watch, nextTick } from 'vue'
import { usePermissionsStore } from '@/stores/permissions'

const props = defineProps({
  show: {
    type: Boolean,
    default: false
  },
  permission: {
    type: Object,
    default: null
  }
})

const emit = defineEmits(['close', 'saved'])

// Store
const permissionsStore = usePermissionsStore()

// State
const isSubmitting = ref(false)
const errors = ref({})

// Form data
const form = ref({
  name: '',
  key: '',
  description: '',
  module: '',
  type: '',
  status: 'active'
})

// Computed
const isEditing = computed(() => !!props.permission)
const availableModules = computed(() => [
  'Users',
  'Roles',
  'Permissions',
  'Patients',
  'Clinics',
  'Appointments',
  'Medical Records',
  'Settings',
  'Reports',
  'Billing'
])

// Methods
const generateKey = () => {
  if (!isEditing.value && form.value.name && form.value.module) {
    const module = form.value.module.toLowerCase().replace(/\s+/g, '_')
    const action = form.value.name.toLowerCase().replace(/\s+/g, '_')
    form.value.key = `${module}.${action}`
  }
}

const validateForm = () => {
  errors.value = {}

  if (!form.value.name.trim()) {
    errors.value.name = 'Permission name is required'
  }

  if (!form.value.key.trim()) {
    errors.value.key = 'Permission key is required'
  } else if (!/^[a-z_]+\.[a-z_]+$/.test(form.value.key)) {
    errors.value.key = 'Permission key must be in format: module.action (lowercase, underscores only)'
  }

  if (!form.value.module) {
    errors.value.module = 'Module is required'
  }

  if (!form.value.type) {
    errors.value.type = 'Type is required'
  }

  if (!form.value.status) {
    errors.value.status = 'Status is required'
  }

  return Object.keys(errors.value).length === 0
}

const handleSubmit = async () => {
  if (!validateForm()) return

  isSubmitting.value = true

  try {
    if (isEditing.value) {
      await permissionsStore.updatePermission(props.permission.id, form.value)
    } else {
      await permissionsStore.createPermission(form.value)
    }

    emit('saved')
  } catch (error) {
    if (error.response?.status === 422) {
      errors.value = error.response.data.errors
    }
  } finally {
    isSubmitting.value = false
  }
}

const resetForm = () => {
  form.value = {
    name: '',
    key: '',
    description: '',
    module: '',
    type: '',
    status: 'active'
  }
  errors.value = {}
}

// Watchers
watch(() => props.show, (show) => {
  if (show) {
    if (props.permission) {
      form.value = { ...props.permission }
    } else {
      resetForm()
    }
  }
})

watch(() => form.value.module, () => {
  generateKey()
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
</style>
