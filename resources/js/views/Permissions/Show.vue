<template>
  <AppLayout>
    <template #header>
      <PageHeader
        :title="permission ? `Permission: ${permission.name}` : 'Permission Details'"
        subtitle="View permission details, assigned roles, and statistics."
      >
        <template #actions>
          <AppButton @click="editPermission" variant="primary" class="hover-lift animate-bounceIn">
            <Icon name="edit" class="w-4 h-4 mr-2" />
            Edit Permission
          </AppButton>
        </template>
      </PageHeader>
    </template>
    <div v-if="loading" class="flex justify-center py-12">
      <LoadingSpinner size="lg" />
    </div>
    <div v-else-if="!permission" class="py-12">
      <ErrorState title="Permission Not Found" description="The permission you're looking for doesn't exist or has been deleted.">
        <template #actions>
          <AppButton @click="$router.push('/permissions')" variant="primary">Back to Permissions</AppButton>
        </template>
      </ErrorState>
    </div>
    <div v-else class="max-w-4xl mx-auto space-y-8 animate-fadeInUp">
      <AppCard>
        <template #header>
          <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Permission Information</h3>
        </template>
        <div class="space-y-4">
          <div class="flex justify-between">
            <span class="text-sm font-medium text-gray-600 dark:text-gray-400">Name</span>
            <span class="text-sm text-gray-900 dark:text-white">{{ permission.name }}</span>
          </div>
          <div class="flex justify-between">
            <span class="text-sm font-medium text-gray-600 dark:text-gray-400">Key</span>
            <span class="text-sm text-gray-900 dark:text-white">{{ permission.key }}</span>
          </div>
          <div class="flex justify-between">
            <span class="text-sm font-medium text-gray-600 dark:text-gray-400">Type</span>
            <AppBadge :variant="getTypeVariant(permission.type)">{{ permission.type }}</AppBadge>
          </div>
          <div class="flex justify-between">
            <span class="text-sm font-medium text-gray-600 dark:text-gray-400">Status</span>
            <StatusBadge :status="permission.status" />
          </div>
          <div class="flex justify-between">
            <span class="text-sm font-medium text-gray-600 dark:text-gray-400">Description</span>
            <span class="text-sm text-gray-900 dark:text-white">{{ permission.description || '-' }}</span>
          </div>
        </div>
      </AppCard>
      <AppCard>
        <template #header>
          <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Assigned Roles</h3>
        </template>
        <div v-if="permission.roles && permission.roles.length" class="space-y-2">
          <div v-for="role in permission.roles" :key="role.id" class="flex items-center justify-between p-2 bg-gray-50 dark:bg-gray-800 rounded">
            <span class="text-sm text-gray-900 dark:text-white">{{ role.name }}</span>
            <AppBadge :variant="getRoleVariant(role.type)">{{ role.type }}</AppBadge>
          </div>
        </div>
        <div v-else class="text-center py-4">
          <p class="text-gray-500 dark:text-gray-400">No roles assigned</p>
        </div>
      </AppCard>
      <AppCard>
        <template #header>
          <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Permission Statistics</h3>
        </template>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <MetricCard title="Total Roles" :value="permissionStats.totalRoles" icon="shield" color="primary" />
          <MetricCard title="Active Roles" :value="permissionStats.activeRoles" icon="shield-check" color="success" />
          <MetricCard title="Assigned Users" :value="permissionStats.assignedUsers" icon="users" color="info" />
        </div>
      </AppCard>
    </div>
  </AppLayout>
</template>
<script>
import { ref, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { usePermissionsStore } from '@/stores/permissions'
import AppLayout from '@/components/layout/AppLayout.vue'
import PageHeader from '@/components/header/PageHeader.vue'
import AppCard from '@/components/card/AppCard.vue'
import AppButton from '@/components/button/AppButton.vue'
import AppBadge from '@/components/badge/AppBadge.vue'
import StatusBadge from '@/components/badge/StatusBadge.vue'
import LoadingSpinner from '@/components/loading/LoadingSpinner.vue'
import ErrorState from '@/components/empty/ErrorState.vue'
import Icon from '@/components/visual/Icon.vue'
import MetricCard from '@/components/card/MetricCard.vue'
export default {
  name: 'ShowPermission',
  components: {
    AppLayout,
    PageHeader,
    AppCard,
    AppButton,
    AppBadge,
    StatusBadge,
    LoadingSpinner,
    ErrorState,
    Icon,
    MetricCard
  },
  setup() {
    const route = useRoute()
    const router = useRouter()
    const permissionsStore = usePermissionsStore()
    const loading = ref(false)
    const permission = ref(null)
    const permissionStats = ref({
      totalRoles: 0,
      activeRoles: 0,
      assignedUsers: 0
    })
    const getTypeVariant = (type) => {
      return type === 'system' ? 'danger' : 'info'
    }
    const getRoleVariant = (type) => {
      return type === 'system' ? 'danger' : 'info'
    }
    const fetchPermission = async () => {
      loading.value = true
      try {
        const response = await permissionsStore.fetchPermission(route.params.id)
        permission.value = response.data
        // Dummy stats, replace with API if available
        permissionStats.value = {
          totalRoles: response.data.roles?.length || 0,
          activeRoles: response.data.roles?.filter(r => r.status === 'active').length || 0,
          assignedUsers: response.data.usersCount || 0
        }
      } catch (error) {
        // handle error
      } finally {
        loading.value = false
      }
    }
    const editPermission = () => {
      router.push(`/permissions/${route.params.id}/edit`)
    }
    onMounted(() => {
      fetchPermission()
    })
    return {
      loading,
      permission,
      permissionStats,
      getTypeVariant,
      getRoleVariant,
      editPermission
    }
  }
}
</script>
<style scoped>

</style> 