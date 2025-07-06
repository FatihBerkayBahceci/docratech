<template>
  <div
    v-if="isVisible"
    class="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-50 animate-fadeIn"
    @click="handleBackdropClick"
  >
    <div
      class="bg-white rounded-3xl shadow-2xl w-full max-w-4xl mx-4 max-h-[90vh] overflow-hidden animate-slideUp border border-gray-200/50"
      @click.stop
    >
      <!-- Header -->
      <div class="relative px-8 py-6 bg-gradient-to-r from-emerald-600 to-green-600 text-white">
        <div class="flex items-center gap-4">
          <div class="w-12 h-12 bg-white/20 rounded-2xl flex items-center justify-center backdrop-blur-sm">
            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
            </svg>
          </div>
          <div>
            <h3 class="text-xl font-bold">Login History</h3>
            <p class="text-emerald-100 text-sm">Access logs for {{ userName }}</p>
          </div>
        </div>
        
        <!-- Close Button -->
        <button
          @click="closeModal"
          class="absolute top-6 right-6 w-10 h-10 bg-white/20 hover:bg-white/30 rounded-xl flex items-center justify-center transition-colors duration-200"
        >
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
          </svg>
        </button>
      </div>

      <!-- Content -->
      <div class="p-8 overflow-y-auto max-h-[70vh]">
        <!-- Loading State -->
        <div v-if="isLoading" class="flex items-center justify-center py-12">
          <div class="flex items-center gap-3 text-gray-600">
            <svg class="w-6 h-6 animate-spin" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span class="font-medium">Loading login history...</span>
          </div>
        </div>

        <!-- Error State -->
        <div v-else-if="error" class="text-center py-12">
          <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
          </div>
          <h4 class="text-lg font-semibold text-gray-900 mb-2">Failed to Load History</h4>
          <p class="text-gray-600 mb-4">{{ error }}</p>
          <button
            @click="$emit('retry')"
            class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors duration-200"
          >
            Try Again
          </button>
        </div>

        <!-- Success State -->
        <div v-else-if="historyData">
          <!-- Summary Stats -->
          <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
            <div class="bg-gradient-to-br from-blue-50 to-blue-100 p-4 rounded-2xl border border-blue-200">
              <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-blue-600 rounded-xl flex items-center justify-center">
                  <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                  </svg>
                </div>
                <div>
                  <p class="text-sm text-blue-600 font-medium">Total Logins</p>
                  <p class="text-2xl font-bold text-blue-900">{{ historyData.summary?.total_logins || 0 }}</p>
                </div>
              </div>
            </div>
            
            <div class="bg-gradient-to-br from-red-50 to-red-100 p-4 rounded-2xl border border-red-200">
              <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-red-600 rounded-xl flex items-center justify-center">
                  <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                  </svg>
                </div>
                <div>
                  <p class="text-sm text-red-600 font-medium">Failed Logins</p>
                  <p class="text-2xl font-bold text-red-900">{{ historyData.summary?.failed_logins || 0 }}</p>
                </div>
              </div>
            </div>
            
            <div class="bg-gradient-to-br from-green-50 to-green-100 p-4 rounded-2xl border border-green-200">
              <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-green-600 rounded-xl flex items-center justify-center">
                  <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707"/>
                  </svg>
                </div>
                <div>
                  <p class="text-sm text-green-600 font-medium">Last Login</p>
                  <p class="text-sm font-bold text-green-900">
                    {{ formatDate(historyData.summary?.last_login) || 'Never' }}
                  </p>
                </div>
              </div>
            </div>
            
            <div class="bg-gradient-to-br from-purple-50 to-purple-100 p-4 rounded-2xl border border-purple-200">
              <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-purple-600 rounded-xl flex items-center justify-center">
                  <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9v-9m0-9v9"/>
                  </svg>
                </div>
                <div>
                  <p class="text-sm text-purple-600 font-medium">Last IP</p>
                  <p class="text-sm font-bold text-purple-900">
                    {{ historyData.summary?.last_ip || 'Unknown' }}
                  </p>
                </div>
              </div>
            </div>
          </div>

          <!-- Activity Table -->
          <div class="bg-white border border-gray-200 rounded-2xl overflow-hidden">
            <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
              <h4 class="font-semibold text-gray-900">Recent Activity</h4>
              <p class="text-sm text-gray-600">Last 50 login attempts</p>
            </div>
            
            <div class="overflow-x-auto">
              <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                  <tr>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Event</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Date & Time</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">IP Address</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Device</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Location</th>
                  </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                  <tr 
                    v-for="log in historyData.login_history" 
                    :key="log.id"
                    class="hover:bg-gray-50 transition-colors duration-150"
                  >
                    <td class="px-6 py-4">
                      <div class="flex items-center gap-2">
                        <div :class="getEventBadgeClass(log.event)" class="w-2 h-2 rounded-full"></div>
                        <span class="font-medium" :class="getEventTextClass(log.event)">
                          {{ formatEvent(log.event) }}
                        </span>
                      </div>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-900">
                      {{ formatDate(log.created_at) }}
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-900 font-mono">
                      {{ log.ip_address }}
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-600">
                      {{ log.device || 'Unknown Device' }}
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-600">
                      {{ log.location || 'Unknown Location' }}
                    </td>
                  </tr>
                </tbody>
              </table>
              
              <!-- Empty State -->
              <div v-if="!historyData.login_history?.length" class="text-center py-12">
                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                  <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                  </svg>
                </div>
                <h4 class="text-lg font-semibold text-gray-900 mb-2">No Login History</h4>
                <p class="text-gray-600">This user has no recorded login activity.</p>
              </div>
            </div>
          </div>

          <!-- Active Sessions -->
          <div v-if="historyData.active_sessions?.length" class="mt-8 bg-blue-50 border border-blue-200 rounded-2xl overflow-hidden">
            <div class="px-6 py-4 bg-blue-100 border-b border-blue-200">
              <h4 class="font-semibold text-blue-900">Active Sessions</h4>
              <p class="text-sm text-blue-700">Currently logged in sessions</p>
            </div>
            
            <div class="p-6">
              <div class="space-y-3">
                <div 
                  v-for="session in historyData.active_sessions" 
                  :key="session.id"
                  class="flex items-center justify-between p-4 bg-white rounded-xl border border-blue-200"
                >
                  <div class="flex items-center gap-3">
                    <div class="w-3 h-3 bg-green-500 rounded-full animate-pulse"></div>
                    <div>
                      <p class="font-medium text-gray-900">{{ session.name || 'Web Session' }}</p>
                      <p class="text-sm text-gray-600">
                        Created: {{ formatDate(session.created_at) }}
                      </p>
                    </div>
                  </div>
                  <div class="text-right">
                    <p class="text-sm text-gray-600">
                      Last used: {{ formatDate(session.last_used_at) }}
                    </p>
                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                      Active
                    </span>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Footer -->
      <div class="px-8 py-4 bg-gray-50 border-t border-gray-200 rounded-b-3xl">
        <div class="flex items-center justify-between">
          <div class="flex items-center gap-2 text-sm text-gray-500">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            DocraTech Security Monitoring
          </div>
          
          <button
            @click="closeModal"
            class="px-6 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors duration-200"
          >
            Close
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue'

// Props
const props = defineProps({
  isVisible: {
    type: Boolean,
    default: false
  },
  userName: {
    type: String,
    required: true
  },
  historyData: {
    type: Object,
    default: null
  },
  isLoading: {
    type: Boolean,
    default: false
  },
  error: {
    type: String,
    default: null
  }
})

// Emits
const emit = defineEmits(['close', 'retry'])

// Methods
const closeModal = () => {
  emit('close')
}

const handleBackdropClick = () => {
  closeModal()
}

const formatDate = (dateString) => {
  if (!dateString) return 'Unknown'
  
  try {
    const date = new Date(dateString)
    return date.toLocaleString('en-US', {
      year: 'numeric',
      month: 'short',
      day: 'numeric',
      hour: '2-digit',
      minute: '2-digit',
      second: '2-digit'
    })
  } catch (error) {
    return 'Invalid Date'
  }
}

const formatEvent = (event) => {
  const eventMap = {
    'login': 'Login',
    'logout': 'Logout',
    'failed_login': 'Failed Login'
  }
  return eventMap[event] || event
}

const getEventBadgeClass = (event) => {
  const classMap = {
    'login': 'bg-green-500',
    'logout': 'bg-blue-500',
    'failed_login': 'bg-red-500'
  }
  return classMap[event] || 'bg-gray-500'
}

const getEventTextClass = (event) => {
  const classMap = {
    'login': 'text-green-700',
    'logout': 'text-blue-700',
    'failed_login': 'text-red-700'
  }
  return classMap[event] || 'text-gray-700'
}
</script>

<style scoped>
@keyframes fadeIn {
  from { opacity: 0; }
  to { opacity: 1; }
}

@keyframes slideUp {
  from { 
    opacity: 0;
    transform: translateY(20px) scale(0.95);
  }
  to { 
    opacity: 1;
    transform: translateY(0) scale(1);
  }
}

.animate-fadeIn {
  animation: fadeIn 0.3s ease-out;
}

.animate-slideUp {
  animation: slideUp 0.3s ease-out;
}
</style> 