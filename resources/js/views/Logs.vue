<template>
  <div class="logs-page px-6 py-6">
    <!-- Page Header -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
      <div class="flex items-center justify-between">
        <div>
          <h1 class="text-3xl font-bold text-gray-900">Activity Logs</h1>
          <p class="text-gray-600 mt-2">Monitor system activities and user actions</p>
        </div>
        <button 
          @click="exportLogs" 
          class="bg-docratech-primary text-white px-4 py-2 rounded-lg hover:bg-docratech-primaryAccent transition-colors font-medium flex items-center gap-2"
        >
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
          </svg>
          Export Logs
        </button>
      </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
      <div class="bg-white rounded-lg p-6 shadow-sm border border-gray-200">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-gray-600 text-sm font-medium">Total Activities</p>
            <p class="text-2xl font-bold text-gray-900 mt-1">{{ stats.total }}</p>
          </div>
          <div class="w-12 h-12 bg-docratech-primary/10 rounded-lg flex items-center justify-center">
            <svg class="w-6 h-6 text-docratech-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
            </svg>
          </div>
        </div>
        <div class="flex items-center mt-4 text-sm">
          <span class="text-green-600 font-medium">+8%</span>
          <span class="text-gray-500 ml-1">vs last week</span>
        </div>
      </div>

      <div class="bg-white rounded-lg p-6 shadow-sm border border-gray-200">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-gray-600 text-sm font-medium">User Actions</p>
            <p class="text-2xl font-bold text-gray-900 mt-1">{{ stats.userActions }}</p>
          </div>
          <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
            </svg>
          </div>
        </div>
        <div class="flex items-center mt-4 text-sm">
          <span class="text-blue-600 font-medium">{{ stats.userActionsPercentage }}%</span>
          <span class="text-gray-500 ml-1">of total activities</span>
        </div>
      </div>

      <div class="bg-white rounded-lg p-6 shadow-sm border border-gray-200">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-gray-600 text-sm font-medium">System Events</p>
            <p class="text-2xl font-bold text-gray-900 mt-1">{{ stats.systemEvents }}</p>
          </div>
          <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
            <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
          </div>
        </div>
        <div class="flex items-center mt-4 text-sm">
          <span class="text-purple-600 font-medium">{{ stats.systemEventsPercentage }}%</span>
          <span class="text-gray-500 ml-1">of total activities</span>
        </div>
      </div>

      <div class="bg-white rounded-lg p-6 shadow-sm border border-gray-200">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-gray-600 text-sm font-medium">Errors/Warnings</p>
            <p class="text-2xl font-bold text-gray-900 mt-1">{{ stats.errors }}</p>
          </div>
          <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center">
            <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
          </div>
        </div>
        <div class="flex items-center mt-4 text-sm">
          <span class="text-red-600 font-medium">{{ stats.errorsPercentage }}%</span>
          <span class="text-gray-500 ml-1">needs attention</span>
        </div>
      </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg p-6 shadow-sm border border-gray-200">
      <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Search</label>
          <div class="relative">
            <input 
              v-model="filters.search" 
              type="text" 
              placeholder="Search activities..." 
              class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-docratech-primary focus:border-transparent"
            >
            <svg class="w-5 h-5 text-gray-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
          </div>
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Activity Type</label>
          <select v-model="filters.type" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-docratech-primary focus:border-transparent">
            <option value="">All Types</option>
            <option value="login">Login</option>
            <option value="logout">Logout</option>
            <option value="create">Create</option>
            <option value="update">Update</option>
            <option value="delete">Delete</option>
            <option value="system">System</option>
          </select>
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">User</label>
          <select v-model="filters.user" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-docratech-primary focus:border-transparent">
            <option value="">All Users</option>
            <option value="admin">Admin</option>
            <option value="dr-johnson">Dr. Johnson</option>
            <option value="nurse-smith">Nurse Smith</option>
          </select>
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Level</label>
          <select v-model="filters.level" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-docratech-primary focus:border-transparent">
            <option value="">All Levels</option>
            <option value="info">Info</option>
            <option value="warning">Warning</option>
            <option value="error">Error</option>
            <option value="critical">Critical</option>
          </select>
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Date Range</label>
          <select v-model="filters.dateRange" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-docratech-primary focus:border-transparent">
            <option value="">All Time</option>
            <option value="today">Today</option>
            <option value="week">This Week</option>
            <option value="month">This Month</option>
          </select>
        </div>
      </div>
    </div>

    <!-- Logs Table -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
      <div class="px-6 py-4 border-b border-gray-200">
        <h3 class="text-lg font-medium text-gray-900">Activity Log Entries</h3>
      </div>
      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Time</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Details</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Level</th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            <tr v-for="log in filteredLogs" :key="log.id" class="hover:bg-gray-50">
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ formatTime(log.timestamp) }}</td>
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="flex items-center">
                  <div class="w-8 h-8 bg-gray-200 rounded-full flex items-center justify-center">
                    <span class="text-xs font-medium">{{ log.user.initials }}</span>
                  </div>
                  <div class="ml-3">
                    <div class="text-sm font-medium text-gray-900">{{ log.user.name }}</div>
                    <div class="text-sm text-gray-500">{{ log.user.role }}</div>
                  </div>
                </div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ log.action }}</td>
              <td class="px-6 py-4 text-sm text-gray-500">{{ log.details }}</td>
              <td class="px-6 py-4 whitespace-nowrap">
                <span :class="getLevelClass(log.level)" class="inline-flex px-2 py-1 text-xs font-semibold rounded-full">
                  {{ log.level }}
                </span>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'

const stats = ref({
  total: 12847,
  userActions: 8392,
  systemEvents: 3847,
  errors: 608,
  userActionsPercentage: 65.3,
  systemEventsPercentage: 29.9,
  errorsPercentage: 4.7
})

const filters = ref({
  search: '',
  type: '',
  user: '',
  level: '',
  dateRange: ''
})

const logs = ref([
  {
    id: 1,
    timestamp: new Date(),
    user: { name: 'Dr. Sarah Wilson', role: 'Doctor', initials: 'SW' },
    action: 'Created Patient Record',
    details: 'Created new patient record for John Anderson',
    level: 'info'
  },
  {
    id: 2,
    timestamp: new Date(Date.now() - 30000),
    user: { name: 'Admin Mike Chen', role: 'Admin', initials: 'MC' },
    action: 'Updated Permissions',
    details: 'Updated system permissions for Clinic Managers',
    level: 'warning'
  },
  {
    id: 3,
    timestamp: new Date(Date.now() - 60000),
    user: { name: 'Dr. Emily Rodriguez', role: 'Doctor', initials: 'ER' },
    action: 'Completed Consultation',
    details: 'Completed medical consultation',
    level: 'info'
  }
])

const filteredLogs = computed(() => {
  return logs.value.filter(log => {
    const matchesSearch = !filters.value.search || 
      log.user.name.toLowerCase().includes(filters.value.search.toLowerCase()) ||
      log.action.toLowerCase().includes(filters.value.search.toLowerCase()) ||
      log.details.toLowerCase().includes(filters.value.search.toLowerCase())
    
    const matchesType = !filters.value.type || log.action.toLowerCase().includes(filters.value.type)
    const matchesLevel = !filters.value.level || log.level === filters.value.level
    
    return matchesSearch && matchesType && matchesLevel
  })
})

const formatTime = (date) => {
  return new Intl.DateTimeFormat('en-US', {
    hour: '2-digit',
    minute: '2-digit',
    day: '2-digit',
    month: 'short'
  }).format(date)
}

const getLevelClass = (level) => {
  const classes = {
    info: 'bg-blue-100 text-blue-800',
    warning: 'bg-yellow-100 text-yellow-800',
    error: 'bg-red-100 text-red-800',
    critical: 'bg-red-200 text-red-900'
  }
  return classes[level] || 'bg-gray-100 text-gray-800'
}

const exportLogs = () => {
  console.log('Exporting logs...')
  // Implementation for exporting logs
}

onMounted(() => {
  console.log('Logs page mounted')
})
</script>

<style scoped>
.logs-page {
  /* Custom styles if needed */
}
</style>
