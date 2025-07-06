import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import { dashboardService } from '@/services/api'

export const useDashboardStore = defineStore('dashboard', () => {
  // State
  const stats = ref({
    users: { total: 0, active: 0, inactive: 0, new: 0 },
    roles: { total: 0, active: 0, custom: 0, system: 0 },
    permissions: { total: 0, active: 0, custom: 0, system: 0 },
    activity: { total: 0, today: 0, week: 0, month: 0 }
  })
  const recentActivity = ref([])
  const systemStatus = ref({
    status: 'operational',
    services: []
  })
  const performance = ref({
    labels: [],
    datasets: []
  })
  const loading = ref(false)

  // Getters
  const isSystemHealthy = computed(() => {
    return systemStatus.value.status === 'operational'
  })

  const criticalServices = computed(() => {
    return systemStatus.value.services.filter(service => 
      service.status === 'error' || service.status === 'warning'
    )
  })

  const performanceData = computed(() => {
    return {
      labels: performance.value.labels,
      datasets: performance.value.datasets
    }
  })

  // Actions
  const fetchStats = async () => {
    loading.value = true
    try {
      const response = await dashboardService.getStats()
      stats.value = response.data
      return response
    } catch (error) {
      console.error('Fetch stats error:', error)
      throw error
    } finally {
      loading.value = false
    }
  }

  const fetchActivity = async (params = {}) => {
    loading.value = true
    try {
      const response = await dashboardService.getActivity(params)
      recentActivity.value = response.data
      return response
    } catch (error) {
      console.error('Fetch activity error:', error)
      throw error
    } finally {
      loading.value = false
    }
  }

  const fetchSystemStatus = async () => {
    loading.value = true
    try {
      const response = await dashboardService.getSystemStatus()
      systemStatus.value = response.data
      return response
    } catch (error) {
      console.error('Fetch system status error:', error)
      throw error
    } finally {
      loading.value = false
    }
  }

  const fetchPerformance = async (params = {}) => {
    loading.value = true
    try {
      const response = await dashboardService.getPerformance(params)
      performance.value = response.data
      return response
    } catch (error) {
      console.error('Fetch performance error:', error)
      throw error
    } finally {
      loading.value = false
    }
  }

  const refreshDashboard = async () => {
    try {
      await Promise.all([
        fetchStats(),
        fetchActivity(),
        fetchSystemStatus(),
        fetchPerformance()
      ])
    } catch (error) {
      console.error('Refresh dashboard error:', error)
      throw error
    }
  }

  // Utility actions
  const getStatValue = (category, metric) => {
    return stats.value[category]?.[metric] || 0
  }

  const getActivityByType = (type) => {
    return recentActivity.value.filter(activity => activity.type === type)
  }

  const getActivityByUser = (userId) => {
    return recentActivity.value.filter(activity => activity.user_id === userId)
  }

  const getActivityByDate = (date) => {
    const targetDate = new Date(date)
    return recentActivity.value.filter(activity => {
      const activityDate = new Date(activity.created_at)
      return activityDate.toDateString() === targetDate.toDateString()
    })
  }

  const getServiceStatus = (serviceName) => {
    const service = systemStatus.value.services.find(s => s.name === serviceName)
    return service?.status || 'unknown'
  }

  const getPerformanceMetric = (metric) => {
    const dataset = performance.value.datasets.find(d => d.label === metric)
    return dataset?.data || []
  }

  // Reset store
  const reset = () => {
    stats.value = {
      users: { total: 0, active: 0, inactive: 0, new: 0 },
      roles: { total: 0, active: 0, custom: 0, system: 0 },
      permissions: { total: 0, active: 0, custom: 0, system: 0 },
      activity: { total: 0, today: 0, week: 0, month: 0 }
    }
    recentActivity.value = []
    systemStatus.value = {
      status: 'operational',
      services: []
    }
    performance.value = {
      labels: [],
      datasets: []
    }
    loading.value = false
  }

  return {
    // State
    stats,
    recentActivity,
    systemStatus,
    performance,
    loading,
    
    // Getters
    isSystemHealthy,
    criticalServices,
    performanceData,
    
    // Actions
    fetchStats,
    fetchActivity,
    fetchSystemStatus,
    fetchPerformance,
    refreshDashboard,
    
    // Utility actions
    getStatValue,
    getActivityByType,
    getActivityByUser,
    getActivityByDate,
    getServiceStatus,
    getPerformanceMetric,
    
    // Reset
    reset
  }
}) 