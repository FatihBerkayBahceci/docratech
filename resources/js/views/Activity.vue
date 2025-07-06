<template>
  <div class="px-6 py-6">
    <!-- Page Header -->
    <div class="bg-white border-b border-gray-200 p-6 rounded-lg shadow-sm">
      <div class="flex items-center justify-between">
        <div>
          <h1 class="text-3xl font-bold text-gray-900">Activity Log</h1>
          <p class="text-gray-600 mt-2">Monitor user activities and system events</p>
        </div>
        <div class="flex items-center gap-3">
          <AppButton
            @click="exportActivity"
            variant="outline"
            :loading="exportLoading"
          >
            <Icon name="download" class="w-4 h-4 mr-2" />
            Export
          </AppButton>
        </div>
      </div>
    </div>

    <div class="space-y-6">
      <!-- Filters -->
      <AppCard>
        <FilterBar
          :filters="filters"
          @update:filters="updateFilters"
          @clear="clearFilters"
        >
          <template #filters>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
              <FormGroup label="Search">
                <SearchInput
                  v-model="filters.search"
                  placeholder="Search activities..."
                />
              </FormGroup>

              <FormGroup label="User">
                <Select
                  v-model="filters.userId"
                  :options="userOptions"
                  placeholder="All users"
                  clearable
                />
              </FormGroup>

              <FormGroup label="Type">
                <Select
                  v-model="filters.type"
                  :options="typeOptions"
                  placeholder="All types"
                  clearable
                />
              </FormGroup>

              <FormGroup label="Date Range">
                <DateRangePicker
                  v-model="filters.dateRange"
                  placeholder="Select date range"
                />
              </FormGroup>
            </div>
          </template>
        </FilterBar>
      </AppCard>

      <!-- Activity List -->
      <AppCard>
        <template #header>
          <div class="flex items-center justify-between">
            <div>
              <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                Recent Activities
              </h3>
              <p class="text-sm text-gray-600 dark:text-gray-400">
                Showing {{ pagination.total }} activities
              </p>
            </div>
            <div class="flex items-center space-x-2">
              <AppButton
                @click="refreshActivities"
                variant="outline"
                size="sm"
                :loading="loading"
              >
                <Icon name="refresh" class="w-4 h-4" />
              </AppButton>
            </div>
          </div>
        </template>

        <div v-if="loading" class="py-8">
          <div class="flex justify-center">
            <LoadingSpinner size="lg" />
          </div>
        </div>

        <div v-else-if="activities.length === 0" class="py-8">
          <EmptyState
            title="No activities found"
            description="No activities match your current filters."
            icon="activity"
          >
            <template #actions>
              <AppButton
                @click="clearFilters"
                variant="primary"
              >
                Clear Filters
              </AppButton>
            </template>
          </EmptyState>
        </div>

        <div v-else class="space-y-4">
          <div
            v-for="activity in activities"
            :key="activity.id"
            class="flex items-start space-x-4 p-4 border border-gray-200 dark:border-gray-700 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors"
          >
            <!-- User Avatar -->
            <Avatar
              :src="activity.user?.avatar"
              :name="activity.user?.name"
              size="md"
            />

            <!-- Activity Content -->
            <div class="flex-1 min-w-0">
              <div class="flex items-center space-x-2 mb-1">
                <span class="font-medium text-gray-900 dark:text-white">
                  {{ activity.user?.name || 'Unknown User' }}
                </span>
                <StatusBadge
                  :status="getActivityStatus(activity.type)"
                  :variant="getActivityVariant(activity.type)"
                >
                  {{ activity.type }}
                </StatusBadge>
              </div>

              <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">
                {{ activity.description }}
              </p>

              <div class="flex items-center space-x-4 text-xs text-gray-500 dark:text-gray-400">
                <span class="flex items-center">
                  <Icon name="clock" class="w-3 h-3 mr-1" />
                  {{ formatDateTime(activity.created_at) }}
                </span>
                
                <span v-if="activity.ip_address" class="flex items-center">
                  <Icon name="globe" class="w-3 h-3 mr-1" />
                  {{ activity.ip_address }}
                </span>
                
                <span v-if="activity.user_agent" class="flex items-center">
                  <Icon name="device" class="w-3 h-3 mr-1" />
                  {{ getBrowserInfo(activity.user_agent) }}
                </span>
              </div>

              <!-- Additional Data -->
              <div v-if="activity.metadata" class="mt-3 p-3 bg-gray-50 dark:bg-gray-800 rounded-md">
                <pre class="text-xs text-gray-600 dark:text-gray-400 overflow-x-auto">
                  {{ JSON.stringify(activity.metadata, null, 2) }}
                </pre>
              </div>
            </div>

            <!-- Actions -->
            <div class="flex items-center space-x-2">
              <AppButton
                @click="viewActivity(activity)"
                variant="ghost"
                size="sm"
              >
                <Icon name="eye" class="w-4 h-4" />
              </AppButton>
            </div>
          </div>
        </div>

        <!-- Pagination -->
        <div v-if="pagination.lastPage > 1" class="mt-6">
          <Pagination
            :current-page="pagination.currentPage"
            :last-page="pagination.lastPage"
            :total="pagination.total"
            @page-change="changePage"
          />
        </div>
      </AppCard>
    </div>

    <!-- Activity Detail Modal -->
    <AppDialog
      v-model="showActivityModal"
      title="Activity Details"
      size="lg"
    >
      <div v-if="selectedActivity" class="space-y-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div>
            <h4 class="text-sm font-medium text-gray-900 dark:text-white mb-2">
              User Information
            </h4>
            <div class="space-y-2">
              <div class="flex items-center space-x-3">
                <Avatar
                  :src="selectedActivity.user?.avatar"
                  :name="selectedActivity.user?.name"
                  size="sm"
                />
                <div>
                  <p class="font-medium text-gray-900 dark:text-white">
                    {{ selectedActivity.user?.name || 'Unknown User' }}
                  </p>
                  <p class="text-sm text-gray-600 dark:text-gray-400">
                    {{ selectedActivity.user?.email }}
                  </p>
                </div>
              </div>
            </div>
          </div>

          <div>
            <h4 class="text-sm font-medium text-gray-900 dark:text-white mb-2">
              Activity Information
            </h4>
            <div class="space-y-2 text-sm">
              <div class="flex justify-between">
                <span class="text-gray-600 dark:text-gray-400">Type:</span>
                <StatusBadge
                  :status="getActivityStatus(selectedActivity.type)"
                  :variant="getActivityVariant(selectedActivity.type)"
                >
                  {{ selectedActivity.type }}
                </StatusBadge>
              </div>
              <div class="flex justify-between">
                <span class="text-gray-600 dark:text-gray-400">Date:</span>
                <span class="text-gray-900 dark:text-white">
                  {{ formatDateTime(selectedActivity.created_at) }}
                </span>
              </div>
              <div class="flex justify-between">
                <span class="text-gray-600 dark:text-gray-400">IP Address:</span>
                <span class="text-gray-900 dark:text-white">
                  {{ selectedActivity.ip_address || 'N/A' }}
                </span>
              </div>
            </div>
          </div>
        </div>

        <div>
          <h4 class="text-sm font-medium text-gray-900 dark:text-white mb-2">
            Description
          </h4>
          <p class="text-gray-600 dark:text-gray-400">
            {{ selectedActivity.description }}
          </p>
        </div>

        <div v-if="selectedActivity.metadata">
          <h4 class="text-sm font-medium text-gray-900 dark:text-white mb-2">
            Additional Data
          </h4>
          <div class="bg-gray-50 dark:bg-gray-800 rounded-md p-4">
            <pre class="text-sm text-gray-600 dark:text-gray-400 overflow-x-auto">
              {{ JSON.stringify(selectedActivity.metadata, null, 2) }}
            </pre>
          </div>
        </div>
      </div>
    </AppDialog>
  </div>
</template>

<script>
import { ref, reactive, onMounted, computed } from 'vue'
import { activityService } from '@/services/api'
import AppCard from '@/components/card/AppCard.vue'
import AppButton from '@/components/button/AppButton.vue'
import AppDialog from '@/components/modal/AppDialog.vue'
import FilterBar from '@/components/filter/FilterBar.vue'
import FormGroup from '@/components/form/FormGroup.vue'
import SearchInput from '@/components/form/SearchInput.vue'
import Select from '@/components/form/Select.vue'
import DateRangePicker from '@/components/form/DateRangePicker.vue'
import LoadingSpinner from '@/components/loading/LoadingSpinner.vue'
import EmptyState from '@/components/empty/EmptyState.vue'
import Avatar from '@/components/media/Avatar.vue'
import StatusBadge from '@/components/badge/StatusBadge.vue'
import Pagination from '@/components/navigation/Pagination.vue'
import Icon from '@/components/visual/Icon.vue'

export default {
  name: 'Activity',
  components: {
    AppCard,
    AppButton,
    AppDialog,
    FilterBar,
    FormGroup,
    SearchInput,
    Select,
    DateRangePicker,
    LoadingSpinner,
    EmptyState,
    Avatar,
    StatusBadge,
    Pagination,
    Icon
  },
  setup() {
    const activities = ref([])
    const loading = ref(false)
    const exportLoading = ref(false)
    const showActivityModal = ref(false)
    const selectedActivity = ref(null)

    const pagination = reactive({
      currentPage: 1,
      perPage: 20,
      total: 0,
      lastPage: 1
    })

    const filters = reactive({
      search: '',
      userId: null,
      type: null,
      dateRange: { start: null, end: null }
    })

    const userOptions = ref([])
    const typeOptions = ref([
      { value: 'login', label: 'Login' },
      { value: 'logout', label: 'Logout' },
      { value: 'create', label: 'Create' },
      { value: 'update', label: 'Update' },
      { value: 'delete', label: 'Delete' },
      { value: 'export', label: 'Export' },
      { value: 'import', label: 'Import' }
    ])

    const fetchActivities = async () => {
      loading.value = true
      try {
        const response = await activityService.getActivity({
          page: pagination.currentPage,
          per_page: pagination.perPage,
          ...filters
        })

        activities.value = response.data
        pagination.total = response.total || 0
        pagination.lastPage = response.last_page || 1
      } catch (error) {
        console.error('Failed to fetch activities:', error)
      } finally {
        loading.value = false
      }
    }

    const exportActivity = async () => {
      exportLoading.value = true
      try {
        await activityService.exportActivity(filters)
      } catch (error) {
        console.error('Failed to export activities:', error)
      } finally {
        exportLoading.value = false
      }
    }

    const updateFilters = (newFilters) => {
      Object.assign(filters, newFilters)
      pagination.currentPage = 1
      fetchActivities()
    }

    const clearFilters = () => {
      Object.assign(filters, {
        search: '',
        userId: null,
        type: null,
        dateRange: { start: null, end: null }
      })
      pagination.currentPage = 1
      fetchActivities()
    }

    const changePage = (page) => {
      pagination.currentPage = page
      fetchActivities()
    }

    const refreshActivities = () => {
      fetchActivities()
    }

    const viewActivity = (activity) => {
      selectedActivity.value = activity
      showActivityModal.value = true
    }

    const getActivityStatus = (type) => {
      const statusMap = {
        login: 'success',
        logout: 'info',
        create: 'success',
        update: 'warning',
        delete: 'danger',
        export: 'info',
        import: 'info'
      }
      return statusMap[type] || 'default'
    }

    const getActivityVariant = (type) => {
      const variantMap = {
        login: 'success',
        logout: 'info',
        create: 'success',
        update: 'warning',
        delete: 'danger',
        export: 'info',
        import: 'info'
      }
      return variantMap[type] || 'default'
    }

    const formatDateTime = (date) => {
      if (!date) return ''
      return new Date(date).toLocaleString()
    }

    const getBrowserInfo = (userAgent) => {
      if (!userAgent) return 'Unknown'
      
      // Simple browser detection
      if (userAgent.includes('Chrome')) return 'Chrome'
      if (userAgent.includes('Firefox')) return 'Firefox'
      if (userAgent.includes('Safari')) return 'Safari'
      if (userAgent.includes('Edge')) return 'Edge'
      
      return 'Unknown Browser'
    }

    onMounted(() => {
      fetchActivities()
    })

    return {
      activities,
      loading,
      exportLoading,
      pagination,
      filters,
      userOptions,
      typeOptions,
      showActivityModal,
      selectedActivity,
      fetchActivities,
      exportActivity,
      updateFilters,
      clearFilters,
      changePage,
      refreshActivities,
      viewActivity,
      getActivityStatus,
      getActivityVariant,
      formatDateTime,
      getBrowserInfo
    }
  }
}
</script> 