<!--
Developer: DocraTech Team
Language: American English (US)  
License: MIT
Project: DocraTech Medical Website Platform
Version: 2.0 - Complete Frontend Rewrite
Description: Modern enterprise dashboard with comprehensive medical platform overview
-->

<template>
  <div class="min-h-screen bg-docratech-surface">
    <!-- Page Header -->
    <div class="bg-white border-b border-gray-200 px-6 py-4">
      <div class="flex items-center justify-between">
        <div>
          <h1 class="text-3xl font-bold text-gray-900">Dashboard</h1>
          <p class="text-gray-600 mt-2">Welcome back! Here's your medical platform overview.</p>
        </div>
        <div class="flex items-center gap-3">
          <AppButton 
            variant="secondary" 
            size="sm"
            class="hover:scale-105 transition-transform"
          >
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            Export Report
          </AppButton>
          <AppButton 
            variant="primary" 
            size="sm"
            class="hover:scale-105 transition-transform"
          >
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
            </svg>
            Add Patient
          </AppButton>
        </div>
      </div>
    </div>

    <!-- Content with padding like Support page -->
    <div class="px-6 py-6">
      <!-- Key Metrics Overview -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <AppCard 
          v-for="(metric, index) in keyMetrics" 
          :key="metric.key"
          variant="elevated"
          class="hover-lift cursor-pointer transition-all duration-300 bg-white"
          :class="{ 'animate-pulse': isLoading }"
          @click="navigateToMetricDetail(metric.key)"
        >
          <div class="flex items-center justify-between">
            <div class="flex-1">
              <div class="flex items-center gap-3 mb-2">
                <div 
                  class="w-12 h-12 rounded-xl flex items-center justify-center"
                  :class="getMetricIconBg(metric.type)"
                >
                  <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 24 24">
                    <path :d="metric.iconPath"/>
                  </svg>
                </div>
                <div class="flex-1">
                  <p class="text-sm font-medium text-gray-600">{{ metric.label }}</p>
                  <p class="text-2xl font-bold text-gray-900">
                    {{ isLoading ? '---' : formatMetricValue(metric.value, metric.format) }}
                  </p>
                </div>
              </div>
              <div class="flex items-center justify-between">
                <div class="flex items-center gap-1">
                  <svg 
                    class="w-4 h-4" 
                    :class="metric.trend > 0 ? 'text-green-500' : metric.trend < 0 ? 'text-red-500' : 'text-gray-400'"
                    fill="currentColor" 
                    viewBox="0 0 24 24"
                  >
                    <path :d="metric.trend > 0 ? 'M7 14l5-5 5 5z' : metric.trend < 0 ? 'M7 10l5 5 5-5z' : 'M7 12l5 0 5 0z'"/>
                  </svg>
                  <span 
                    class="text-sm font-medium"
                    :class="metric.trend > 0 ? 'text-green-600' : metric.trend < 0 ? 'text-red-600' : 'text-gray-400'"
                  >
                    {{ Math.abs(metric.trend) }}%
                  </span>
                </div>
                <span class="text-xs text-gray-500">vs last month</span>
              </div>
            </div>
          </div>
        </AppCard>
      </div>

      <!-- Main Content Grid -->
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Recent Activity & Notifications (2/3 width) -->
        <div class="lg:col-span-2 space-y-6">
          
          <!-- Recent Activity -->
          <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
              <div class="flex items-center gap-2">
                <svg class="w-5 h-5 text-purple-600" fill="currentColor" viewBox="0 0 24 24">
                  <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                </svg>
                <h3 class="text-lg font-semibold text-gray-900">Recent Activity</h3>
              </div>
              <AppButton variant="ghost" size="sm" @click="viewAllActivity">
                View All
                <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
              </AppButton>
            </div>
            
            <div class="space-y-4">
              <div 
                v-for="activity in recentActivity" 
                :key="activity.id"
                class="flex items-start gap-4 p-4 rounded-xl bg-gray-50 hover:bg-gray-100 transition-colors duration-200"
              >
                <div class="w-10 h-10 rounded-full bg-gradient-to-br from-purple-600 to-purple-700 flex items-center justify-center flex-shrink-0">
                  <span class="text-sm font-medium text-white">{{ activity.user.initials }}</span>
                </div>
                <div class="flex-1 min-w-0">
                  <div class="flex items-center gap-2 mb-1">
                    <p class="text-sm font-medium text-gray-900 truncate">{{ activity.user.name }}</p>
                    <span class="text-xs text-gray-500">{{ formatTimeAgo(activity.timestamp) }}</span>
                  </div>
                  <p class="text-sm text-gray-600">{{ activity.action }}</p>
                  <div v-if="activity.meta" class="mt-2 text-xs text-gray-500">
                    {{ activity.meta }}
                  </div>
                </div>
                <div 
                  class="w-2 h-2 rounded-full flex-shrink-0"
                  :class="getActivityStatusColor(activity.type)"
                ></div>
              </div>
              
              <div v-if="isLoading" class="space-y-4">
                <div v-for="i in 3" :key="i" class="flex items-center gap-4 p-4">
                  <div class="w-10 h-10 rounded-full bg-gray-200 animate-pulse"></div>
                  <div class="flex-1 space-y-2">
                    <div class="h-4 bg-gray-200 rounded animate-pulse"></div>
                    <div class="h-3 bg-gray-200 rounded w-2/3 animate-pulse"></div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- System Performance Chart -->
          <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
              <div class="flex items-center gap-2">
                <svg class="w-5 h-5 text-purple-600" fill="currentColor" viewBox="0 0 24 24">
                  <path d="M16 6l2.29 2.29-4.88 4.88-4-4L2 16.59 3.41 18l6-6 4 4 6.3-6.29L22 12V6h-6z"/>
                </svg>
                <h3 class="text-lg font-semibold text-gray-900">Performance Overview</h3>
              </div>
              <select 
                v-model="selectedChartPeriod" 
                class="px-3 py-1 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-600 focus:border-transparent"
              >
                <option v-for="period in chartPeriods" :key="period.value" :value="period.value">
                  {{ period.label }}
                </option>
              </select>
            </div>
            
            <div class="h-80 flex items-center justify-center">
              <div v-if="isLoading" class="text-center">
                <div class="w-8 h-8 border-4 border-purple-600 border-t-transparent rounded-full animate-spin mx-auto mb-2"></div>
                <p class="text-sm text-gray-500">Loading chart data...</p>
              </div>
              <div v-else class="w-full h-full flex items-center justify-center text-gray-500">
                <!-- Chart placeholder - in real implementation, use Chart.js or similar -->
                <div class="text-center">
                  <svg class="w-16 h-16 mx-auto mb-4 text-gray-200" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M16 6l2.29 2.29-4.88 4.88-4-4L2 16.59 3.41 18l6-6 4 4 6.3-6.29L22 12V6h-6z"/>
                  </svg>
                  <p class="text-sm">Performance chart will render here</p>
                  <p class="text-xs text-gray-400 mt-1">{{ selectedChartPeriod }} data</p>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Right Sidebar (1/3 width) -->
        <div class="space-y-6">
          
          <!-- System Status -->
          <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center gap-2">
              <div 
                class="w-3 h-3 rounded-full"
                :class="systemHealth.overall === 'healthy' ? 'bg-green-500' : systemHealth.overall === 'warning' ? 'bg-yellow-500' : 'bg-red-500'"
              ></div>
              <h3 class="text-lg font-semibold text-gray-900">System Status</h3>
            </div>
            
            <div class="space-y-3">
              <div 
                v-for="service in systemServices" 
                :key="service.name"
                class="flex items-center justify-between p-3 rounded-lg bg-gray-50"
              >
                <div class="flex items-center gap-3">
                  <svg class="w-4 h-4 text-gray-500" fill="currentColor" viewBox="0 0 24 24">
                    <path :d="service.iconPath"/>
                  </svg>
                  <span class="text-sm font-medium text-gray-900">{{ service.name }}</span>
                </div>
                <div class="flex items-center gap-2">
                  <div 
                    class="w-2 h-2 rounded-full"
                    :class="getServiceStatusColor(service.status)"
                  ></div>
                  <span 
                    class="text-xs font-medium capitalize"
                    :class="getServiceStatusTextColor(service.status)"
                  >
                    {{ service.status }}
                  </span>
                </div>
              </div>
            </div>
          </div>

          <!-- Quick Stats -->
          <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900">Quick Stats</h3>
            
            <div class="space-y-4">
              <div 
                v-for="stat in quickStats" 
                :key="stat.key"
                class="flex items-center justify-between"
              >
                <span class="text-sm text-gray-500">{{ stat.label }}</span>
                <span class="text-sm font-semibold text-gray-900">{{ stat.value }}</span>
              </div>
            </div>
          </div>

          <!-- Recent Notifications -->
          <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
              <h3 class="text-lg font-semibold text-gray-900">Notifications</h3>
              <AppButton variant="ghost" size="sm" @click="viewAllNotifications">
                View All
              </AppButton>
            </div>
            
            <div class="space-y-3">
              <div 
                v-for="notification in recentNotifications" 
                :key="notification.id"
                class="flex items-start gap-3 p-3 rounded-lg"
                :class="getNotificationBg(notification.type)"
              >
                <div 
                  class="w-6 h-6 rounded-full flex items-center justify-center flex-shrink-0"
                  :class="getNotificationIconBg(notification.type)"
                >
                  <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 24 24">
                    <path :d="notification.iconPath"/>
                  </svg>
                </div>
                <div class="flex-1 min-w-0">
                  <p class="text-sm text-gray-900">{{ notification.message }}</p>
                  <p class="text-xs text-gray-500 mt-1">{{ formatTimeAgo(notification.timestamp) }}</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { useRouter } from 'vue-router'
import AppCard from '@/components/card/AppCard.vue'
import AppButton from '@/components/button/AppButton.vue'

// Router
const router = useRouter()

// State
const isLoading = ref(true)
const showQuickActions = ref(false)
const selectedChartPeriod = ref('7d')

// Key metrics data
const keyMetrics = ref([
  {
    key: 'total_users',
    label: 'Total Users',
    value: 2847,
    trend: 12.5,
    type: 'users',
    format: 'number',
    iconPath: 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z'
  },
  {
    key: 'active_clinics',
    label: 'Active Clinics',
    value: 156,
    trend: 8.2,
    type: 'clinics',
    format: 'number',
    iconPath: 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4'
  },
  {
    key: 'total_patients',
    label: 'Total Patients',
    value: 18392,
    trend: 15.3,
    type: 'patients',
    format: 'number',
    iconPath: 'M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z'
  },
  {
    key: 'system_uptime',
    label: 'System Uptime',
    value: 99.94,
    trend: 0.02,
    type: 'performance',
    format: 'percentage',
    iconPath: 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'
  }
])

// Chart periods
const chartPeriods = ref([
  { value: '24h', label: 'Last 24 Hours' },
  { value: '7d', label: 'Last 7 Days' },
  { value: '30d', label: 'Last 30 Days' },
  { value: '90d', label: 'Last 3 Months' }
])

// Recent activity data
const recentActivity = ref([
  {
    id: 1,
    user: { name: 'Dr. Sarah Wilson', initials: 'SW' },
    action: 'Created new patient record for John Anderson',
    type: 'success',
    timestamp: new Date(Date.now() - 5 * 60 * 1000),
    meta: 'Patient ID: PAT-2024-001'
  },
  {
    id: 2,
    user: { name: 'Admin Mike Chen', initials: 'MC' },
    action: 'Updated system permissions for Clinic Managers',
    type: 'info',
    timestamp: new Date(Date.now() - 15 * 60 * 1000),
    meta: 'Role: clinic_manager'
  },
  {
    id: 3,
    user: { name: 'Dr. Emily Rodriguez', initials: 'ER' },
    action: 'Completed medical consultation',
    type: 'success',
    timestamp: new Date(Date.now() - 30 * 60 * 1000),
    meta: 'Duration: 45 minutes'
  },
  {
    id: 4,
    user: { name: 'System', initials: 'SY' },
    action: 'Automated backup completed successfully',
    type: 'info',
    timestamp: new Date(Date.now() - 60 * 60 * 1000),
    meta: 'Size: 2.4 GB'
  }
])

// System health
const systemHealth = ref({
  overall: 'healthy'
})

// System services
const systemServices = ref([
  {
    name: 'Web Server',
    status: 'healthy',
    iconPath: 'M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2'
  },
  {
    name: 'Database',
    status: 'healthy',
    iconPath: 'M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4'
  },
  {
    name: 'API Gateway',
    status: 'healthy',
    iconPath: 'M13 10V3L4 14h7v7l9-11h-7z'
  },
  {
    name: 'File Storage',
    status: 'warning',
    iconPath: 'M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z'
  }
])

// Quick stats
const quickStats = ref([
  { key: 'online_users', label: 'Users Online', value: '847' },
  { key: 'pending_approvals', label: 'Pending Approvals', value: '12' },
  { key: 'scheduled_appointments', label: 'Today\'s Appointments', value: '156' },
  { key: 'system_alerts', label: 'System Alerts', value: '3' }
])

// Recent notifications
const recentNotifications = ref([
  {
    id: 1,
    type: 'success',
    message: 'Weekly backup completed successfully',
    timestamp: new Date(Date.now() - 30 * 60 * 1000),
    iconPath: 'M5 13l4 4L19 7'
  },
  {
    id: 2,
    type: 'info',
    message: 'New clinic registration awaiting approval',
    timestamp: new Date(Date.now() - 60 * 60 * 1000),
    iconPath: 'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z'
  },
  {
    id: 3,
    type: 'warning',
    message: 'Storage usage at 85% capacity',
    timestamp: new Date(Date.now() - 2 * 60 * 60 * 1000),
    iconPath: 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z'
  }
])

// Auto-refresh interval
let refreshInterval = null

// Computed properties
const getMetricIconBg = (type) => {
  const types = {
    users: 'bg-gradient-to-br from-blue-500 to-blue-600',
    clinics: 'bg-gradient-to-br from-green-500 to-green-600',
    patients: 'bg-gradient-to-br from-docratech-primary to-docratech-primaryAccent',
    performance: 'bg-gradient-to-br from-purple-500 to-purple-600'
  }
  return types[type] || 'bg-gradient-to-br from-gray-500 to-gray-600'
}

const getActivityStatusColor = (type) => {
  const colors = {
    success: 'bg-green-400',
    info: 'bg-blue-400',
    warning: 'bg-yellow-400',
    error: 'bg-red-400'
  }
  return colors[type] || 'bg-gray-400'
}

const getServiceStatusColor = (status) => {
  const colors = {
    healthy: 'bg-green-400',
    warning: 'bg-yellow-400',
    error: 'bg-red-400'
  }
  return colors[status] || 'bg-gray-400'
}

const getServiceStatusTextColor = (status) => {
  const colors = {
    healthy: 'text-green-600',
    warning: 'text-yellow-600',
    error: 'text-red-600'
  }
  return colors[status] || 'text-gray-600'
}

const getNotificationBg = (type) => {
  const bgs = {
    success: 'bg-green-50 border-l-4 border-green-400',
    info: 'bg-blue-50 border-l-4 border-blue-400',
    warning: 'bg-yellow-50 border-l-4 border-yellow-400',
    error: 'bg-red-50 border-l-4 border-red-400'
  }
  return bgs[type] || 'bg-gray-50 border-l-4 border-gray-400'
}

const getNotificationIconBg = (type) => {
  const bgs = {
    success: 'bg-green-500',
    info: 'bg-blue-500',
    warning: 'bg-yellow-500',
    error: 'bg-red-500'
  }
  return bgs[type] || 'bg-gray-500'
}

// Methods
const formatMetricValue = (value, format) => {
  if (format === 'percentage') {
    return `${value}%`
  }
  if (format === 'currency') {
    return new Intl.NumberFormat('en-US', { style: 'currency', currency: 'USD' }).format(value)
  }
  return new Intl.NumberFormat('en-US').format(value)
}

const formatTimeAgo = (timestamp) => {
  const now = new Date()
  const diff = now - timestamp
  const minutes = Math.floor(diff / (1000 * 60))
  const hours = Math.floor(diff / (1000 * 60 * 60))
  
  if (minutes < 1) return 'Just now'
  if (minutes < 60) return `${minutes}m ago`
  if (hours < 24) return `${hours}h ago`
  return timestamp.toLocaleDateString()
}

const navigateToMetricDetail = (metricKey) => {
  const routes = {
    total_users: '/users',
    active_clinics: '/clinics',
    total_patients: '/patients',
    system_uptime: '/system/status'
  }
  if (routes[metricKey]) {
    router.push(routes[metricKey])
  }
}

const refreshDashboard = async () => {
  isLoading.value = true
  try {
    // Simulate API call
    await new Promise(resolve => setTimeout(resolve, 1500))
    
    // In real implementation, fetch fresh data from API
    console.log('Dashboard refreshed')
  } catch (error) {
    console.error('Failed to refresh dashboard:', error)
  } finally {
    isLoading.value = false
  }
}

const viewAllActivity = () => router.push('/activity')
const viewAllNotifications = () => router.push('/notifications')

// Lifecycle
onMounted(async () => {
  // Initial load
  await refreshDashboard()
  
  // Set up auto-refresh every 5 minutes
  refreshInterval = setInterval(() => {
    if (!isLoading.value) {
      refreshDashboard()
    }
  }, 5 * 60 * 1000)
})

onUnmounted(() => {
  if (refreshInterval) {
    clearInterval(refreshInterval)
  }
})
</script>

<style scoped>
.hover-lift:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 25px -8px rgba(90, 17, 136, 0.2);
}

@keyframes fadeInUp {
  from {
    opacity: 0;
    transform: translateY(20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.animate-fadeInUp {
  animation: fadeInUp 0.6s ease-out;
}

.dashboard-page {
  @apply space-y-6 p-6 max-w-7xl mx-auto;
}
</style> 