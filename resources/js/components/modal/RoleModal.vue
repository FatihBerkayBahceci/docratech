<!--
Developer: DocraTech Team
Language: American English (US)  
License: MIT
Project: DocraTech Medical Website Platform
Version: 3.0 - Enterprise Role Modal
Description: Professional role creation/editing modal with enterprise design standards

UX IMPROVEMENTS:
- Modern glass-morphism modal design
- Enhanced form validation and error handling
- Professional styling consistent with enterprise standards
- Improved accessibility and keyboard navigation
- Auto-slug generation from display name
- Enhanced visual feedback and transitions
-->

<template>
  <Teleport to="body">
    <transition name="modal-fade">
      <div
        v-if="showModal"
        class="fixed inset-0 z-50 overflow-y-auto"
        @mousedown="handleBackdropClick"
        tabindex="-1"
        aria-modal="true"
        :aria-label="isEditing ? 'Edit Role' : 'Create Role'"
      >
        <!-- Backdrop -->
        <div class="fixed inset-0 bg-black/40 backdrop-blur-sm transition-opacity duration-300"></div>

        <!-- Modal Container -->
        <div class="relative min-h-screen flex items-center justify-center p-4">
          <div class="relative bg-white/95 backdrop-blur-xl rounded-2xl shadow-2xl border border-white/20 w-full max-w-md max-h-[90vh] overflow-hidden">
            <!-- Header -->
            <div class="relative bg-gradient-to-r from-purple-600 to-blue-600 px-6 py-6">
              <div class="absolute inset-0 bg-white/10 backdrop-blur-sm"></div>
              <div class="relative">
                <div class="flex items-center justify-between">
                  <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center">
                      <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                              :d="isEditing ? 'M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z' : 'M12 6v6m0 0v6m0-6h6m-6 0H6'"/>
                      </svg>
                    </div>
                    <div>
                      <h2 class="text-xl font-bold text-white">
                        {{ isEditing ? 'Edit Role' : 'Create Role' }}
                      </h2>
                      <p class="text-white/80 text-sm">
                        {{ isEditing ? 'Update role information' : 'Create a new system role' }}
                      </p>
                    </div>
                  </div>
                  <button
                    @click="$emit('close')"
                    :disabled="loading"
                    class="p-2 text-white/80 hover:text-white hover:bg-white/10 rounded-lg transition-colors duration-200 disabled:opacity-50"
                  >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                  </button>
                </div>
              </div>
            </div>

            <!-- Form Content -->
            <form @submit.prevent="onSubmit" class="p-6 space-y-6">
              <!-- Display Name Field -->
              <div class="space-y-2">
                <label class="block text-sm font-semibold text-gray-700">
                  Display Name <span class="text-red-500">*</span>
                </label>
                <input
                  v-model="form.display_name"
                  type="text"
                  placeholder="Enter role display name"
                  class="w-full px-4 py-3 border-0 bg-gray-50 rounded-xl focus:ring-2 focus:ring-purple-500 focus:bg-white transition-all duration-300 placeholder-gray-400"
                  :class="{ 'ring-2 ring-red-500 bg-red-50': errors.display_name }"
                  @input="handleDisplayNameChange"
                  @focus="clearError('display_name')"
                  :disabled="loading"
                  required
                  autofocus
                />
                <p v-if="errors.display_name" class="text-sm text-red-600 flex items-center gap-1">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16c-.77.833.192 2.5 1.732 2.5z"/>
                  </svg>
                  {{ errors.display_name }}
                </p>
              </div>

              <!-- Role Name (Slug) Field -->
              <div class="space-y-2">
                <label class="block text-sm font-semibold text-gray-700">
                  Role Name (Slug) <span class="text-red-500">*</span>
                </label>
                <input
                  v-model="form.name"
                  type="text"
                  placeholder="role-name-slug"
                  class="w-full px-4 py-3 border-0 bg-gray-50 rounded-xl focus:ring-2 focus:ring-purple-500 focus:bg-white transition-all duration-300 placeholder-gray-400 font-mono text-sm"
                  :class="{ 'ring-2 ring-red-500 bg-red-50': errors.name }"
                  @focus="clearError('name')"
                  :disabled="loading"
                  required
                />
                <p v-if="errors.name" class="text-sm text-red-600 flex items-center gap-1">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16c-.77.833.192 2.5 1.732 2.5z"/>
                  </svg>
                  {{ errors.name }}
                </p>
                <p class="text-xs text-gray-500">
                  Used internally for system identification. Use lowercase letters, numbers, and hyphens only.
                </p>
              </div>

              <!-- Description Field -->
              <div class="space-y-2">
                <label class="block text-sm font-semibold text-gray-700">
                  Description
                </label>
                <textarea
                  v-model="form.description"
                  placeholder="Describe the role's purpose and responsibilities..."
                  rows="3"
                  class="w-full px-4 py-3 border-0 bg-gray-50 rounded-xl focus:ring-2 focus:ring-purple-500 focus:bg-white transition-all duration-300 placeholder-gray-400 resize-none"
                  :disabled="loading"
                  maxlength="500"
                ></textarea>
                <p class="text-xs text-gray-500">
                  {{ form.description.length }}/500 characters
                </p>
              </div>

              <!-- Role Type (for editing) -->
              <div v-if="isEditing" class="space-y-2">
                <label class="block text-sm font-semibold text-gray-700">
                  Role Type
                </label>
                <div class="p-3 bg-gray-50 rounded-xl">
                  <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium"
                        :class="role?.type === 'system' ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800'">
                    {{ role?.type || 'custom' }}
                  </span>
                </div>
              </div>

              <!-- Error Message -->
              <div v-if="formError" class="p-3 bg-red-50 border border-red-200 rounded-xl">
                <p class="text-sm text-red-800 flex items-center gap-2">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16c-.77.833.192 2.5 1.732 2.5z"/>
                  </svg>
                  {{ formError }}
                </p>
              </div>

              <!-- Actions -->
              <div class="flex items-center justify-end gap-3 pt-4">
                <button
                  type="button"
                  @click="$emit('close')"
                  :disabled="loading"
                  class="px-6 py-2 text-gray-600 hover:text-gray-800 transition-colors duration-200 disabled:opacity-50"
                >
                  Cancel
                </button>
                <button
                  type="submit"
                  :disabled="loading || !isFormValid"
                  class="group relative overflow-hidden bg-gradient-to-r from-purple-600 to-blue-600 text-white px-8 py-2 rounded-xl font-medium transition-all duration-300 hover:shadow-lg hover:scale-105 disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none"
                >
                  <div class="absolute inset-0 bg-white/20 translate-y-full group-hover:translate-y-0 transition-transform duration-300"></div>
                  <div class="relative flex items-center gap-2">
                    <svg v-if="loading" class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                    <span>{{ loading ? 'Saving...' : (isEditing ? 'Update Role' : 'Create Role') }}</span>
                  </div>
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </transition>
  </Teleport>
</template>

<script setup>
import { ref, computed, watch, onMounted, onUnmounted } from 'vue'
import { useRolesStore } from '@/stores/roles'
import { useToast } from '@/composables/useToast'

const props = defineProps({
  role: {
    type: Object,
    default: null
  }
})

const emit = defineEmits(['close', 'saved'])

// Store & Composables
const rolesStore = useRolesStore()
const { success: showSuccess, error: showError } = useToast()

// State
const showModal = ref(true)
const loading = ref(false)
const form = ref({
  display_name: '',
  name: '',
  description: ''
})
const errors = ref({})
const formError = ref('')

// Computed
const isEditing = computed(() => !!props.role)

const isFormValid = computed(() => {
  return form.value.display_name.trim() && 
         form.value.name.trim() && 
         !Object.values(errors.value).some(error => error)
})

// Methods
const generateSlug = (text) => {
  return text
    .toLowerCase()
    .replace(/[^a-z0-9\s-]/g, '')
    .replace(/\s+/g, '-')
    .replace(/-+/g, '-')
    .trim()
}

const handleDisplayNameChange = () => {
  // Auto-generate slug if not editing
  if (!isEditing.value && form.value.display_name) {
    form.value.name = generateSlug(form.value.display_name)
  }
  clearError('display_name')
}

const clearError = (field) => {
  if (errors.value[field]) {
    errors.value[field] = ''
  }
  if (formError.value) {
    formError.value = ''
  }
}

const validateForm = () => {
  errors.value = {}
  let isValid = true

  if (!form.value.display_name.trim()) {
    errors.value.display_name = 'Display name is required'
    isValid = false
  }

  if (!form.value.name.trim()) {
    errors.value.name = 'Role name is required'
    isValid = false
  } else if (!/^[a-z0-9-]+$/.test(form.value.name)) {
    errors.value.name = 'Role name must contain only lowercase letters, numbers, and hyphens'
    isValid = false
  }

  return isValid
}

const onSubmit = async () => {
  if (loading.value) return

  if (!validateForm()) {
    formError.value = 'Please correct the errors above'
    return
  }

  loading.value = true
  formError.value = ''

  try {
    const roleData = {
      display_name: form.value.display_name.trim(),
      name: form.value.name.trim(),
      description: form.value.description.trim(),
      type: 'custom',
      status: 'active'
    }

    if (isEditing.value) {
      await rolesStore.updateRole(props.role.id, roleData)
      showSuccess('Role updated successfully!')
    } else {
      await rolesStore.createRole(roleData)
      showSuccess('Role created successfully!')
    }

    emit('saved')
  } catch (error) {
    console.error('Role save error:', error)
    formError.value = error.response?.data?.message || 'An error occurred while saving the role'
  } finally {
    loading.value = false
  }
}

const handleBackdropClick = (e) => {
  if (e.target === e.currentTarget && !loading.value) {
    emit('close')
  }
}

const handleEscape = (e) => {
  if (e.key === 'Escape' && !loading.value) {
    emit('close')
  }
}

// Watchers
watch(
  () => props.role,
  (newRole) => {
    if (newRole) {
      form.value = {
        display_name: newRole.display_name || '',
        name: newRole.name || '',
        description: newRole.description || ''
      }
    } else {
      form.value = {
        display_name: '',
        name: '',
        description: ''
      }
    }
    errors.value = {}
    formError.value = ''
  },
  { immediate: true }
)

// Lifecycle
onMounted(() => {
  document.addEventListener('keydown', handleEscape)
  document.body.style.overflow = 'hidden'
})

onUnmounted(() => {
  document.removeEventListener('keydown', handleEscape)
  document.body.style.overflow = ''
})
</script>

<style scoped>
/* Modal transition animations */
.modal-fade-enter-active,
.modal-fade-leave-active {
  transition: all 0.3s ease;
}

.modal-fade-enter-from,
.modal-fade-leave-to {
  opacity: 0;
  transform: scale(0.95);
}

/* Custom scrollbar */
::-webkit-scrollbar {
  width: 6px;
}

::-webkit-scrollbar-track {
  background: #f1f5f9;
  border-radius: 3px;
}

::-webkit-scrollbar-thumb {
  background: #cbd5e1;
  border-radius: 3px;
}

::-webkit-scrollbar-thumb:hover {
  background: #94a3b8;
}

/* Form focus styles */
input:focus,
textarea:focus {
  outline: none;
}

/* Animation for form elements */
@keyframes slideUp {
  from {
    opacity: 0;
    transform: translateY(10px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.relative.bg-white\/95 {
  animation: slideUp 0.3s ease-out;
}
</style>
