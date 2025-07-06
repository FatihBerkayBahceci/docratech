<template>
  <div class="px-6 py-6">
    <!-- Page Header -->
    <div class="bg-white border-b border-gray-200 p-6 rounded-lg shadow-sm">
      <div class="flex items-center justify-between">
        <div>
          <h1 class="text-3xl font-bold text-gray-900">Notifications</h1>
          <p class="text-gray-600 mt-2">Manage your notifications and preferences</p>
        </div>
        <div class="flex items-center gap-3">
          <AppButton
            @click="markAllAsRead"
            variant="secondary"
            :loading="markAllLoading"
          >
            <Icon name="check-double" class="w-4 h-4 mr-2" />
            Mark All as Read
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
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
              <FormGroup label="Search">
                <SearchInput
                  v-model="filters.search"
                  placeholder="Search notifications..."
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

              <FormGroup label="Status">
                <Select
                  v-model="filters.read"
                  :options="statusOptions"
                  placeholder="All status"
                  clearable
                />
              </FormGroup>
            </div>
          </template>
        </FilterBar>
      </AppCard>

      <!-- Notifications List -->
      <AppCard>
        <template #header>
          <div class="flex items-center justify-between">
            <div>
              <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                Notifications
              </h3>
              <p class="text-sm text-gray-600 dark:text-gray-400">
                Showing {{ pagination.total }} notifications
              </p>
            </div>
            <div class="flex items-center space-x-2">
              <AppButton
                @click="refreshNotifications"
                variant="secondary"
                size="small"
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

        <div v-else-if="notifications.length === 0" class="py-8">
          <EmptyState
            title="No notifications"
            description="You don't have any notifications at the moment."
            icon="bell"
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

        <div v-else class="space-y-2">
          <div
            v-for="notification in notifications"
            :key="notification.id"
            class="flex items-start space-x-4 p-4 border border-gray-200 dark:border-gray-700 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors"
            :class="{ 'bg-blue-50 dark:bg-blue-900/20': !notification.read_at }"
          >
            <!-- Notification Icon -->
            <div class="flex-shrink-0">
              <div
                class="w-10 h-10 rounded-full flex items-center justify-center"
                :class="getNotificationIconClass(notification.type)"
              >
                <Icon
                  :name="getNotificationIcon(notification.type)"
                  class="w-5 h-5 text-white"
                />
              </div>
            </div>

            <!-- Notification Content -->
            <div class="flex-1 min-w-0">
              <div class="flex items-start justify-between">
                <div class="flex-1">
                  <div class="flex items-center space-x-2 mb-1">
                    <h4 class="text-sm font-medium text-gray-900 dark:text-white">
                      {{ notification.title }}
                    </h4>
                    <StatusBadge
                      :status="getNotificationStatus(notification.type)"
                      :variant="getNotificationVariant(notification.type)"
                      size="medium"
                    >
                      {{ notification.type }}
                    </StatusBadge>
                    <div
                      v-if="!notification.read_at"
                      class="w-2 h-2 bg-blue-500 rounded-full"
                    ></div>
                  </div>

                  <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">
                    {{ notification.message }}
                  </p>

                  <div class="flex items-center space-x-4 text-xs text-gray-500 dark:text-gray-400">
                    <span class="flex items-center">
                      <Icon name="clock" class="w-3 h-3 mr-1" />
                      {{ formatDateTime(notification.created_at) }}
                    </span>
                    
                    <span v-if="notification.data?.action_url" class="flex items-center">
                      <Icon name="link" class="w-3 h-3 mr-1" />
                      <a
                        :href="notification.data.action_url"
                        class="text-blue-600 hover:text-blue-500 dark:text-blue-400"
                        target="_blank"
                      >
                        View Details
                      </a>
                    </span>
                  </div>

                  <!-- Additional Data -->
                  <div v-if="notification.data?.details" class="mt-3 p-3 bg-gray-50 dark:bg-gray-800 rounded-md">
                    <pre class="text-xs text-gray-600 dark:text-gray-400 overflow-x-auto">
                      {{ JSON.stringify(notification.data.details, null, 2) }}
                    </pre>
                  </div>
                </div>

                <!-- Actions -->
                <div class="flex items-center space-x-2 ml-4">
                  <AppButton
                    v-if="!notification.read_at"
                    @click="markAsRead(notification)"
                    variant="secondary"
                    size="small"
                  >
                    <Icon name="check" class="w-4 h-4" />
                  </AppButton>
                  
                  <AppButton
                    @click="viewNotification(notification)"
                    variant="secondary"
                    size="small"
                  >
                    <Icon name="eye" class="w-4 h-4" />
                  </AppButton>
                </div>
              </div>
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

    <!-- Notification Detail Modal -->
    <AppDialog
      v-model="showNotificationModal"
      title="Notification Details"
      size="lg"
    >
      <div v-if="selectedNotification" class="space-y-6">
        <div class="flex items-start space-x-4">
          <div
            class="w-12 h-12 rounded-full flex items-center justify-center"
            :class="getNotificationIconClass(selectedNotification.type)"
          >
            <Icon
              :name="getNotificationIcon(selectedNotification.type)"
              class="w-6 h-6 text-white"
            />
          </div>

          <div class="flex-1">
            <div class="flex items-center space-x-2 mb-2">
              <h4 class="text-lg font-semibold text-gray-900 dark:text-white">
                {{ selectedNotification.title }}
              </h4>
              <StatusBadge
                :status="getNotificationStatus(selectedNotification.type)"
                :variant="getNotificationVariant(selectedNotification.type)"
                size="medium"
              >
                {{ selectedNotification.type }}
              </StatusBadge>
            </div>

            <p class="text-gray-600 dark:text-gray-400 mb-4">
              {{ selectedNotification.message }}
            </p>

            <div class="text-sm text-gray-500 dark:text-gray-400">
              <p>Created: {{ formatDateTime(selectedNotification.created_at) }}</p>
              <p v-if="selectedNotification.read_at">
                Read: {{ formatDateTime(selectedNotification.read_at) }}
              </p>
            </div>
          </div>
        </div>

        <div v-if="selectedNotification.data?.details">
          <h4 class="text-sm font-medium text-gray-900 dark:text-white mb-2">
            Additional Details
          </h4>
          <div class="bg-gray-50 dark:bg-gray-800 rounded-md p-4">
            <pre class="text-sm text-gray-600 dark:text-gray-400 overflow-x-auto">
              {{ JSON.stringify(selectedNotification.data.details, null, 2) }}
            </pre>
          </div>
        </div>

        <div v-if="selectedNotification.data?.action_url" class="flex justify-end">
          <AppButton
            :href="selectedNotification.data.action_url"
            variant="primary"
            target="_blank"
          >
            <Icon name="external-link" class="w-4 h-4 mr-2" />
            View Details
          </AppButton>
        </div>
      </div>
    </AppDialog>
  </div>
</template>

<script>
import { ref, reactive, onMounted } from 'vue'
import { notificationService } from '@/services/api'
import AppCard from '@/components/card/AppCard.vue'
import AppButton from '@/components/button/AppButton.vue'
import AppDialog from '@/components/modal/AppDialog.vue'
import FilterBar from '@/components/filter/FilterBar.vue'
import FormGroup from '@/components/form/FormGroup.vue'
import SearchInput from '@/components/form/SearchInput.vue'
import Select from '@/components/form/Select.vue'
import LoadingSpinner from '@/components/loading/LoadingSpinner.vue'
import EmptyState from '@/components/empty/EmptyState.vue'
import StatusBadge from '@/components/badge/StatusBadge.vue'
import Pagination from '@/components/navigation/Pagination.vue'
import Icon from '@/components/visual/Icon.vue'

export default {
  name: 'Notifications',
  components: {
    AppCard,
    AppButton,
    AppDialog,
    FilterBar,
    FormGroup,
    SearchInput,
    Select,
    LoadingSpinner,
    EmptyState,
    StatusBadge,
    Pagination,
    Icon
  },
  setup() {
    const notifications = ref([])
    const loading = ref(false)
    const markAllLoading = ref(false)
    const showNotificationModal = ref(false)
    const selectedNotification = ref(null)

    const pagination = reactive({
      currentPage: 1,
      perPage: 20,
      total: 0,
      lastPage: 1
    })

    const filters = reactive({
      search: '',
      type: null,
      read: null
    })

    const typeOptions = ref([
      { value: 'info', label: 'Information' },
      { value: 'success', label: 'Success' },
      { value: 'warning', label: 'Warning' },
      { value: 'error', label: 'Error' },
      { value: 'system', label: 'System' }
    ])

    const statusOptions = ref([
      { value: false, label: 'Unread' },
      { value: true, label: 'Read' }
    ])

    const fetchNotifications = async () => {
      loading.value = true
      try {
        const response = await notificationService.getNotifications({
          page: pagination.currentPage,
          per_page: pagination.perPage,
          ...filters
        })

        notifications.value = response.data
        pagination.total = response.total || 0
        pagination.lastPage = response.last_page || 1
      } catch (error) {
        console.error('Failed to fetch notifications:', error)
      } finally {
        loading.value = false
      }
    }

    const markAsRead = async (notification) => {
      try {
        await notificationService.markAsRead(notification.id)
        notification.read_at = new Date().toISOString()
      } catch (error) {
        console.error('Failed to mark notification as read:', error)
      }
    }

    const markAllAsRead = async () => {
      markAllLoading.value = true
      try {
        await notificationService.markAllAsRead()
        notifications.value.forEach(notification => {
          notification.read_at = new Date().toISOString()
        })
      } catch (error) {
        console.error('Failed to mark all notifications as read:', error)
      } finally {
        markAllLoading.value = false
      }
    }

    const updateFilters = (newFilters) => {
      Object.assign(filters, newFilters)
      pagination.currentPage = 1
      fetchNotifications()
    }

    const clearFilters = () => {
      Object.assign(filters, {
        search: '',
        type: null,
        read: null
      })
      pagination.currentPage = 1
      fetchNotifications()
    }

    const changePage = (page) => {
      pagination.currentPage = page
      fetchNotifications()
    }

    const refreshNotifications = () => {
      fetchNotifications()
    }

    const viewNotification = (notification) => {
      selectedNotification.value = notification
      showNotificationModal.value = true
    }

    const getNotificationIcon = (type) => {
      const iconMap = {
        info: 'information-circle',
        success: 'check-circle',
        warning: 'exclamation-triangle',
        error: 'x-circle',
        system: 'cog'
      }
      return iconMap[type] || 'bell'
    }

    const getNotificationIconClass = (type) => {
      const classMap = {
        info: 'bg-blue-500',
        success: 'bg-green-500',
        warning: 'bg-yellow-500',
        error: 'bg-red-500',
        system: 'bg-gray-500'
      }
      return classMap[type] || 'bg-gray-500'
    }

    const getNotificationStatus = (type) => {
      const statusMap = {
        info: 'info',
        success: 'success',
        warning: 'warning',
        error: 'danger',
        system: 'default'
      }
      return statusMap[type] || 'default'
    }

    const getNotificationVariant = (type) => {
      const variantMap = {
        info: 'info',
        success: 'success',
        warning: 'warning',
        error: 'danger',
        system: 'default'
      }
      return variantMap[type] || 'default'
    }

    const formatDateTime = (date) => {
      if (!date) return ''
      return new Date(date).toLocaleString()
    }

    onMounted(() => {
      fetchNotifications()
    })

    return {
      notifications,
      loading,
      markAllLoading,
      pagination,
      filters,
      typeOptions,
      statusOptions,
      showNotificationModal,
      selectedNotification,
      fetchNotifications,
      markAsRead,
      markAllAsRead,
      updateFilters,
      clearFilters,
      changePage,
      refreshNotifications,
      viewNotification,
      getNotificationIcon,
      getNotificationIconClass,
      getNotificationStatus,
      getNotificationVariant,
      formatDateTime
    }
  }
}
</script> 