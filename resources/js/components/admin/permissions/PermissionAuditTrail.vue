<template>
  <div class="bg-white/70 backdrop-blur-xl border border-white/20 rounded-2xl shadow-xl overflow-hidden">
    <!-- Header -->
    <div class="p-6 border-b border-gray-100">
      <div class="flex items-center justify-between">
        <div>
          <h3 class="text-xl font-bold text-gray-900">Permission Audit Trail</h3>
          <p class="text-gray-600 mt-1">HIPAA-compliant audit logging and tracking for all permission changes</p>
        </div>
        
        <div class="flex items-center gap-3">
          <!-- Real-time toggle -->
          <div class="flex items-center gap-2">
            <span class="text-sm text-gray-600">Real-time</span>
            <button
              @click="toggleRealTime"
              :class="[
                'relative inline-flex h-6 w-11 items-center rounded-full transition-colors',
                realTimeEnabled ? 'bg-green-500' : 'bg-gray-300'
              ]"
            >
              <span
                :class="[
                  'inline-block h-4 w-4 transform rounded-full bg-white transition-transform',
                  realTimeEnabled ? 'translate-x-6' : 'translate-x-1'
                ]"
              />
            </button>
          </div>
          
          <!-- Export Button -->
          <button
            @click="exportAuditLogs"
            :disabled="loading || !filteredLogs.length"
            class="px-4 py-2 bg-gradient-to-r from-purple-500 to-purple-600 text-white rounded-lg hover:from-purple-600 hover:to-purple-700 transition-all duration-200 flex items-center gap-2 disabled:opacity-50"
          >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            Export
          </button>
          
          <!-- Compliance Report Button -->
          <button
            @click="generateComplianceReport"
            class="px-4 py-2 bg-gradient-to-r from-red-500 to-red-600 text-white rounded-lg hover:from-red-600 hover:to-red-700 transition-all duration-200 flex items-center gap-2"
          >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            HIPAA Report
          </button>
        </div>
      </div>
      
      <!-- Advanced Filters -->
      <div class="mt-4 grid grid-cols-1 md:grid-cols-6 gap-4">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Event Type</label>
          <select 
            v-model="filters.eventType"
            @change="applyFilters"
            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
          >
            <option value="">All Events</option>
            <option value="permission_granted">Permission Granted</option>
            <option value="permission_revoked">Permission Revoked</option>
            <option value="role_assigned">Role Assigned</option>
            <option value="template_applied">Template Applied</option>
            <option value="data_exported">Data Exported</option>
          </select>
        </div>
        
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Severity</label>
          <select 
            v-model="filters.severity"
            @change="applyFilters"
            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
          >
            <option value="">All Levels</option>
            <option value="low">Low</option>
            <option value="medium">Medium</option>
            <option value="high">High</option>
            <option value="critical">Critical</option>
          </select>
        </div>
        
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Module</label>
          <select 
            v-model="filters.module"
            @change="applyFilters"
            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
          >
            <option value="">All Modules</option>
            <option v-for="module in availableModules" :key="module" :value="module">
              {{ formatModuleName(module) }}
            </option>
          </select>
        </div>
        
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">User</label>
          <input
            v-model="filters.userSearch"
            @input="debounceSearch"
            type="text"
            placeholder="Search by user..."
            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
          />
        </div>
        
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Date Range</label>
          <select 
            v-model="filters.dateRange"
            @change="applyDateRange"
            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
          >
            <option value="today">Today</option>
            <option value="week">Last 7 days</option>
            <option value="month">Last 30 days</option>
            <option value="quarter">Last 90 days</option>
            <option value="custom">Custom Range</option>
          </select>
        </div>
        
        <div class="flex items-end">
          <button
            @click="resetFilters"
            class="w-full px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg transition-colors"
          >
            Reset Filters
          </button>
        </div>
      </div>
      
      <!-- High-Risk Alerts -->
      <div v-if="highRiskEvents.length > 0" class="mt-4 p-4 bg-red-50 border border-red-200 rounded-lg">
        <div class="flex items-center gap-2 mb-2">
          <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/>
          </svg>
          <h4 class="font-medium text-red-900">High-Risk Events Detected</h4>
        </div>
        <p class="text-sm text-red-700">{{ highRiskEvents.length }} events require immediate attention</p>
      </div>
    </div>

    <!-- Statistics Summary -->
    <div class="p-6 bg-gray-50 border-b border-gray-100">
      <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="text-center">
          <div class="text-2xl font-bold text-gray-900">{{ auditStats.total || 0 }}</div>
          <div class="text-sm text-gray-600">Total Events</div>
        </div>
        <div class="text-center">
          <div class="text-2xl font-bold text-orange-600">{{ auditStats.high_risk || 0 }}</div>
          <div class="text-sm text-gray-600">High Risk</div>
        </div>
        <div class="text-center">
          <div class="text-2xl font-bold text-blue-600">{{ auditStats.unique_users || 0 }}</div>
          <div class="text-sm text-gray-600">Unique Users</div>
        </div>
        <div class="text-center">
          <div class="text-2xl font-bold text-green-600">{{ auditStats.unique_ips || 0 }}</div>
          <div class="text-sm text-gray-600">IP Addresses</div>
        </div>
      </div>
    </div>

    <!-- Loading State -->
    <div v-if="loading" class="p-12 text-center">
      <svg class="w-8 h-8 text-purple-500 animate-spin mx-auto mb-4" fill="none" viewBox="0 0 24 24">
        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
      </svg>
      <p class="text-gray-600">Loading audit logs...</p>
    </div>

    <!-- Audit Logs List -->
    <div v-else-if="filteredLogs.length > 0" class="divide-y divide-gray-200">
      <div 
        v-for="log in paginatedLogs" 
        :key="log.id"
        class="p-6 hover:bg-gray-50 transition-colors"
      >
        <div class="flex items-start justify-between">
          <div class="flex items-start gap-4">
            <!-- Event Icon -->
            <div 
              :class="[
                'w-10 h-10 rounded-lg flex items-center justify-center',
                getSeverityColors(log.severity).bg
              ]"
            >
              <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="getEventIcon(log.event_type)"/>
              </svg>
            </div>
            
            <!-- Event Details -->
            <div class="flex-1 min-w-0">
              <div class="flex items-center gap-2 mb-1">
                <h4 class="font-medium text-gray-900">{{ formatEventType(log.event_type) }}</h4>
                <span 
                  :class="[
                    'px-2 py-1 text-xs font-medium rounded-full',
                    getSeverityColors(log.severity).badge
                  ]"
                >
                  {{ log.severity }}
                </span>
                <span v-if="log.requires_attention" class="px-2 py-1 text-xs font-medium bg-red-100 text-red-800 rounded-full">
                  Attention Required
                </span>
              </div>
              
              <p class="text-gray-700 mb-2">{{ log.description }}</p>
              
              <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm text-gray-600">
                <div>
                  <span class="font-medium">User:</span> 
                  {{ log.user_name || 'System' }}
                </div>
                <div>
                  <span class="font-medium">Module:</span> 
                  {{ log.permission_module || 'N/A' }}
                </div>
                <div>
                  <span class="font-medium">IP:</span> 
                  {{ log.ip_address || 'Unknown' }}
                </div>
              </div>
              
              <!-- Changes Details -->
              <div v-if="log.changes_summary?.length" class="mt-3 p-3 bg-blue-50 rounded-lg">
                <h5 class="font-medium text-blue-900 mb-2">Changes Made:</h5>
                <div class="space-y-1">
                  <div v-for="change in log.changes_summary" :key="change.field" class="flex items-center gap-2 text-sm">
                    <span class="font-medium text-blue-800">{{ change.field }}:</span>
                    <span class="text-red-600">{{ change.old || 'null' }}</span>
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                    </svg>
                    <span class="text-green-600">{{ change.new || 'null' }}</span>
                  </div>
                </div>
              </div>
            </div>
          </div>
          
          <!-- Timestamp and Actions -->
          <div class="text-right">
            <div class="text-sm text-gray-600 mb-2">
              {{ formatTimestamp(log.occurred_at) }}
            </div>
            <div class="flex items-center gap-2">
              <button
                @click="viewLogDetails(log)"
                class="text-blue-600 hover:text-blue-800 text-sm"
              >
                View Details
              </button>
              <button
                v-if="log.requires_attention"
                @click="resolveEvent(log.id)"
                class="text-green-600 hover:text-green-800 text-sm"
              >
                Resolve
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Empty State -->
    <div v-else class="p-12 text-center">
      <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v6a2 2 0 002 2h2m5 0h2a2 2 0 002-2V7a2 2 0 00-2-2h-2m-5 0V3a2 2 0 012-2h2a2 2 0 012 2v2M9 5a2 2 0 002 2h2a2 2 0 002-2"/>
      </svg>
      <h3 class="text-lg font-medium text-gray-900 mb-2">No audit logs found</h3>
      <p class="text-gray-500">No events match your current filters</p>
    </div>

    <!-- Pagination -->
    <div v-if="filteredLogs.length > itemsPerPage" class="p-6 border-t border-gray-200 flex items-center justify-between">
      <div class="text-sm text-gray-600">
        Showing {{ ((currentPage - 1) * itemsPerPage) + 1 }} to {{ Math.min(currentPage * itemsPerPage, filteredLogs.length) }} of {{ filteredLogs.length }} results
      </div>
      <div class="flex gap-2">
        <button
          @click="currentPage = Math.max(1, currentPage - 1)"
          :disabled="currentPage === 1"
          class="px-3 py-2 text-sm bg-white border border-gray-300 rounded-lg hover:bg-gray-50 disabled:opacity-50"
        >
          Previous
        </button>
        <button
          @click="currentPage = Math.min(totalPages, currentPage + 1)"
          :disabled="currentPage === totalPages"
          class="px-3 py-2 text-sm bg-white border border-gray-300 rounded-lg hover:bg-gray-50 disabled:opacity-50"
        >
          Next
        </button>
      </div>
    </div>

    <!-- Log Details Modal -->
    <div v-if="showDetailsModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
      <div class="bg-white rounded-lg max-w-2xl w-full mx-4 max-h-[80vh] overflow-y-auto">
        <div class="p-6 border-b border-gray-200">
          <div class="flex items-center justify-between">
            <h3 class="text-lg font-semibold text-gray-900">Audit Log Details</h3>
            <button @click="showDetailsModal = false" class="text-gray-400 hover:text-gray-600">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
              </svg>
            </button>
          </div>
        </div>
        <div class="p-6">
          <pre class="text-sm text-gray-700 whitespace-pre-wrap">{{ JSON.stringify(selectedLog, null, 2) }}</pre>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted, watch } from 'vue'
import { useToast } from '@/composables/useToast'
import api from '@/services/api'

const props = defineProps({
  auditLogs: {
    type: Array,
    default: () => []
  }
})

const emit = defineEmits(['load-more'])

// Composables
const { success: showSuccess, error: showError } = useToast()

// State
const loading = ref(false)
const realTimeEnabled = ref(false)
const showDetailsModal = ref(false)
const selectedLog = ref(null)
const currentPage = ref(1)
const itemsPerPage = ref(20)
const searchTimeout = ref(null)

const auditLogs = ref([])
const auditStats = ref({})
const availableModules = ref([])

const filters = ref({
  eventType: '',
  severity: '',
  module: '',
  userSearch: '',
  dateRange: 'week',
  startDate: null,
  endDate: null
})

// Computed
const filteredLogs = computed(() => {
  let logs = auditLogs.value

  if (filters.value.eventType) {
    logs = logs.filter(log => log.event_type === filters.value.eventType)
  }

  if (filters.value.severity) {
    logs = logs.filter(log => log.severity === filters.value.severity)
  }

  if (filters.value.module) {
    logs = logs.filter(log => log.permission_module === filters.value.module)
  }

  if (filters.value.userSearch) {
    const search = filters.value.userSearch.toLowerCase()
    logs = logs.filter(log => 
      (log.user_name || '').toLowerCase().includes(search) ||
      (log.user_email || '').toLowerCase().includes(search)
    )
  }

  return logs.sort((a, b) => new Date(b.occurred_at) - new Date(a.occurred_at))
})

const paginatedLogs = computed(() => {
  const start = (currentPage.value - 1) * itemsPerPage.value
  const end = start + itemsPerPage.value
  return filteredLogs.value.slice(start, end)
})

const totalPages = computed(() => {
  return Math.ceil(filteredLogs.value.length / itemsPerPage.value)
})

const highRiskEvents = computed(() => {
  return auditLogs.value.filter(log => 
    log.severity === 'critical' || log.severity === 'high' || log.requires_attention
  )
})

// Methods
const loadAuditLogs = async () => {
  loading.value = true
  try {
    const params = { ...filters.value }
    delete params.userSearch // Don't send search to server
    delete params.dateRange // Use computed dates instead
    
    const response = await api.get('/permissions/audit-logs', { params })
    
    auditLogs.value = response.data.data.data || []
    auditStats.value = response.data.data.meta || {}
    
    // Extract available modules
    const modules = new Set()
    auditLogs.value.forEach(log => {
      if (log.permission_module) {
        modules.add(log.permission_module)
      }
    })
    availableModules.value = Array.from(modules).sort()
    
  } catch (error) {
    showError('Failed to load audit logs')
    console.error('Audit logs load error:', error)
  } finally {
    loading.value = false
  }
}

const getSeverityColors = (severity) => {
  const colors = {
    low: {
      bg: 'bg-green-500',
      badge: 'bg-green-100 text-green-800'
    },
    medium: {
      bg: 'bg-blue-500',
      badge: 'bg-blue-100 text-blue-800'
    },
    high: {
      bg: 'bg-orange-500',
      badge: 'bg-orange-100 text-orange-800'
    },
    critical: {
      bg: 'bg-red-500',
      badge: 'bg-red-100 text-red-800'
    }
  }
  return colors[severity] || colors.medium
}

const getEventIcon = (eventType) => {
  const icons = {
    permission_granted: 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',
    permission_revoked: 'M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z',
    role_assigned: 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z',
    template_applied: 'M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10',
    data_exported: 'M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'
  }
  return icons[eventType] || 'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z'
}

const formatEventType = (eventType) => {
  if (!eventType) return 'Unknown Event'
  return eventType.split('_').map(word => 
    word.charAt(0).toUpperCase() + word.slice(1)
  ).join(' ')
}

const formatModuleName = (moduleName) => {
  if (!moduleName) return 'Unknown Module'
  return moduleName.split('_').map(word => 
    word.charAt(0).toUpperCase() + word.slice(1)
  ).join(' ')
}

const formatTimestamp = (timestamp) => {
  const date = new Date(timestamp)
  const now = new Date()
  const diffMs = now - date
  const diffMins = Math.floor(diffMs / (1000 * 60))
  const diffHours = Math.floor(diffMs / (1000 * 60 * 60))
  const diffDays = Math.floor(diffMs / (1000 * 60 * 60 * 24))

  if (diffMins < 1) return 'Just now'
  if (diffMins < 60) return `${diffMins}m ago`
  if (diffHours < 24) return `${diffHours}h ago`
  if (diffDays < 7) return `${diffDays}d ago`

  return date.toLocaleDateString() + ' ' + date.toLocaleTimeString()
}

const viewLogDetails = (log) => {
  selectedLog.value = log
  showDetailsModal.value = true
}

const resolveEvent = async (logId) => {
  try {
    // Update the log locally
    const log = auditLogs.value.find(l => l.id === logId)
    if (log) {
      log.requires_attention = false
    }
    showSuccess('Event resolved')
  } catch (error) {
    showError('Failed to resolve event')
  }
}

const applyFilters = () => {
  currentPage.value = 1
  loadAuditLogs()
}

const applyDateRange = () => {
  const now = new Date()
  const ranges = {
    today: { start: new Date(now.getFullYear(), now.getMonth(), now.getDate()), end: now },
    week: { start: new Date(now.getTime() - 7 * 24 * 60 * 60 * 1000), end: now },
    month: { start: new Date(now.getTime() - 30 * 24 * 60 * 60 * 1000), end: now },
    quarter: { start: new Date(now.getTime() - 90 * 24 * 60 * 60 * 1000), end: now }
  }

  if (ranges[filters.value.dateRange]) {
    filters.value.startDate = ranges[filters.value.dateRange].start.toISOString()
    filters.value.endDate = ranges[filters.value.dateRange].end.toISOString()
  }

  applyFilters()
}

const debounceSearch = () => {
  if (searchTimeout.value) {
    clearTimeout(searchTimeout.value)
  }
  searchTimeout.value = setTimeout(() => {
    applyFilters()
  }, 500)
}

const resetFilters = () => {
  filters.value = {
    eventType: '',
    severity: '',
    module: '',
    userSearch: '',
    dateRange: 'week',
    startDate: null,
    endDate: null
  }
  currentPage.value = 1
  applyDateRange()
}

const toggleRealTime = () => {
  realTimeEnabled.value = !realTimeEnabled.value
  if (realTimeEnabled.value) {
    showSuccess('Real-time monitoring enabled')
    // Start polling for new logs
    startRealTimeUpdates()
  } else {
    showSuccess('Real-time monitoring disabled')
    stopRealTimeUpdates()
  }
}

let realTimeInterval = null

const startRealTimeUpdates = () => {
  realTimeInterval = setInterval(() => {
    if (!loading.value) {
      loadAuditLogs()
    }
  }, 30000) // Update every 30 seconds
}

const stopRealTimeUpdates = () => {
  if (realTimeInterval) {
    clearInterval(realTimeInterval)
    realTimeInterval = null
  }
}

const exportAuditLogs = async () => {
  try {
    const response = await api.get('/permissions/audit-logs/export', {
      params: filters.value
    })
    
    const blob = new Blob([JSON.stringify(response.data.data, null, 2)], {
      type: 'application/json'
    })
    const url = URL.createObjectURL(blob)
    const a = document.createElement('a')
    a.href = url
    a.download = `audit-logs-${new Date().toISOString().split('T')[0]}.json`
    document.body.appendChild(a)
    a.click()
    document.body.removeChild(a)
    URL.revokeObjectURL(url)
    
    showSuccess('Audit logs exported successfully')
  } catch (error) {
    showError('Failed to export audit logs')
  }
}

const generateComplianceReport = async () => {
  try {
    const response = await api.get('/permissions/compliance-report', {
      params: filters.value
    })
    
    const blob = new Blob([JSON.stringify(response.data.data, null, 2)], {
      type: 'application/json'
    })
    const url = URL.createObjectURL(blob)
    const a = document.createElement('a')
    a.href = url
    a.download = `hipaa-compliance-report-${new Date().toISOString().split('T')[0]}.json`
    document.body.appendChild(a)
    a.click()
    document.body.removeChild(a)
    URL.revokeObjectURL(url)
    
    showSuccess('HIPAA compliance report generated successfully')
  } catch (error) {
    showError('Failed to generate compliance report')
  }
}

// Lifecycle
onMounted(() => {
  applyDateRange()
})

onUnmounted(() => {
  stopRealTimeUpdates()
  if (searchTimeout.value) {
    clearTimeout(searchTimeout.value)
  }
})

// Watch for prop changes
watch(() => props.auditLogs, (newLogs) => {
  if (newLogs && newLogs.length > 0) {
    auditLogs.value = newLogs
  }
}, { deep: true, immediate: true })
</script> 