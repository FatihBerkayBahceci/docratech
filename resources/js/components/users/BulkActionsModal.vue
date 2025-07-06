<!--
  DocraTech Medical Platform - Bulk Actions Modal
  Features: Bulk operations for users, validation, confirmation
-->

<template>
  <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-xl max-w-md w-full mx-4">
      <div class="p-6 border-b border-gray-200">
        <h3 class="text-lg font-semibold text-gray-900">
          Bulk Actions
        </h3>
        <p class="text-sm text-gray-600 mt-1">
          Perform actions on {{ selectedCount }} selected user{{ selectedCount !== 1 ? 's' : '' }}
        </p>
      </div>

      <div class="p-6 space-y-4">
        <!-- Action Selection -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">
            Select Action
          </label>
          <div class="space-y-2">
            <label class="flex items-center">
              <input
                v-model="selectedAction"
                type="radio"
                value="updateStatus"
                class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300"
              />
              <span class="ml-2 text-sm text-gray-700">Update Status</span>
            </label>
            <label class="flex items-center">
              <input
                v-model="selectedAction"
                type="radio"
                value="updateRole"
                class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300"
              />
              <span class="ml-2 text-sm text-gray-700">Update Role</span>
            </label>
            <label class="flex items-center">
              <input
                v-model="selectedAction"
                type="radio"
                value="delete"
                class="h-4 w-4 text-red-600 focus:ring-red-500 border-gray-300"
              />
              <span class="ml-2 text-sm text-gray-700 text-red-600">Delete Users</span>
            </label>
          </div>
        </div>

        <!-- Status Selection -->
        <div v-if="selectedAction === 'updateStatus'">
          <label for="newStatus" class="block text-sm font-medium text-gray-700 mb-1">
            New Status
          </label>
          <select
            id="newStatus"
            v-model="newStatus"
            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
          >
            <option value="">Select Status</option>
            <option value="active">Active</option>
            <option value="inactive">Inactive</option>
            <option value="pending">Pending</option>
          </select>
        </div>

        <!-- Role Selection -->
        <div v-if="selectedAction === 'updateRole'">
          <label for="newRole" class="block text-sm font-medium text-gray-700 mb-1">
            New Role
          </label>
          <select
            id="newRole"
            v-model="newRole"
            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
          >
            <option value="">Select Role</option>
            <option value="admin">Administrator</option>
            <option value="manager">Manager</option>
            <option value="user">User</option>
            <option value="guest">Guest</option>
          </select>
        </div>

        <!-- Delete Confirmation -->
        <div v-if="selectedAction === 'delete'" class="p-4 bg-red-50 border border-red-200 rounded-md">
          <div class="flex">
            <svg class="w-5 h-5 text-red-400 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.856-.833-2.626 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"/>
            </svg>
            <div class="ml-3">
              <h4 class="text-sm font-medium text-red-800">
                Delete Confirmation
              </h4>
              <p class="text-sm text-red-700 mt-1">
                This action cannot be undone. Are you sure you want to delete {{ selectedCount }} user{{ selectedCount !== 1 ? 's' : '' }}?
              </p>
            </div>
          </div>
        </div>
      </div>

      <!-- Actions -->
      <div class="flex justify-end space-x-3 p-6 border-t border-gray-200">
        <button
          type="button"
          @click="$emit('close')"
          class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
        >
          Cancel
        </button>
        <button
          type="button"
          @click="handleSubmit"
          :disabled="!canSubmit"
          :class="[
            'px-4 py-2 text-sm font-medium rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2',
            selectedAction === 'delete' 
              ? 'text-white bg-red-600 border border-transparent hover:bg-red-700 focus:ring-red-500 disabled:opacity-50'
              : 'text-white bg-blue-600 border border-transparent hover:bg-blue-700 focus:ring-blue-500 disabled:opacity-50'
          ]"
        >
          {{ getActionLabel() }}
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'

const props = defineProps({
  selectedCount: {
    type: Number,
    required: true
  }
})

const emit = defineEmits(['close', 'action'])

const selectedAction = ref('')
const newStatus = ref('')
const newRole = ref('')

const canSubmit = computed(() => {
  if (!selectedAction.value) return false
  
  if (selectedAction.value === 'updateStatus' && !newStatus.value) return false
  if (selectedAction.value === 'updateRole' && !newRole.value) return false
  
  return true
})

const getActionLabel = () => {
  switch (selectedAction.value) {
    case 'updateStatus':
      return 'Update Status'
    case 'updateRole':
      return 'Update Role'
    case 'delete':
      return 'Delete Users'
    default:
      return 'Apply Action'
  }
}

const handleSubmit = () => {
  if (!canSubmit.value) return

  const actionData = {
    action: selectedAction.value
  }

  if (selectedAction.value === 'updateStatus') {
    actionData.status = newStatus.value
  } else if (selectedAction.value === 'updateRole') {
    actionData.role = newRole.value
  }

  emit('action', actionData)
}
</script>

<style scoped>
/* Modal animation */
.modal-enter-active,
.modal-leave-active {
  transition: opacity 0.3s ease;
}

.modal-enter-from,
.modal-leave-to {
  opacity: 0;
}
</style> 