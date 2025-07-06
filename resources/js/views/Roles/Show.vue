<template>
  <AppLayout>
    <template #header>
      <PageHeader
        :title="role ? `Role: ${role.name}` : 'Role Details'"
        subtitle="View role details, permissions, and statistics."
      >
        <template #actions>
          <AppButton @click="editRole" variant="primary" class="hover-lift animate-bounceIn">
            <Icon name="edit" class="w-4 h-4 mr-2" />
            Edit Role
          </AppButton>
        </template>
      </PageHeader>
    </template>
    <div v-if="loading" class="flex justify-center py-12">
      <LoadingSpinner size="lg" />
    </div>
    <div v-else-if="!role" class="py-12">
      <ErrorState title="Role Not Found" description="The role you're looking for doesn't exist or has been deleted.">
        <template #actions>
          <AppButton @click="$router.push('/roles')" variant="primary">Back to Roles</AppButton>
        </template>
      </ErrorState>
    </div>
    <div v-else class="max-w-4xl mx-auto space-y-8 animate-fadeInUp">
      <AppCard>
        <template #header>
          <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Role Information</h3>
        </template>
        <div class="space-y-4">
          <div class="flex justify-between">
            <span class="text-sm font-medium text-gray-600 dark:text-gray-400">Name</span>
            <span class="text-sm text-gray-900 dark:text-white">{{ role.name }}</span>
          </div>
          <div class="flex justify-between">
            <span class="text-sm font-medium text-gray-600 dark:text-gray-400">Type</span>
            <AppBadge :variant="getTypeVariant(role.type)">{{ role.type }}</AppBadge>
          </div>
          <div class="flex justify-between">
            <span class="text-sm font-medium text-gray-600 dark:text-gray-400">Status</span>
            <StatusBadge :status="role.status" />
          </div>
          <div class="flex justify-between">
            <span class="text-sm font-medium text-gray-600 dark:text-gray-400">Description</span>
            <span class="text-sm text-gray-900 dark:text-white">{{ role.description || '-' }}</span>
          </div>
        </div>
      </AppCard>
      <AppCard>
        <template #header>
          <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Permissions</h3>
        </template>
        <div v-if="role.permissions && role.permissions.length" class="space-y-2">
          <div v-for="permission in role.permissions" :key="permission.id" class="flex items-center justify-between p-2 bg-gray-50 dark:bg-gray-800 rounded">
            <span class="text-sm text-gray-900 dark:text-white">{{ permission.name }}</span>
            <AppBadge :variant="getPermissionVariant(permission.type)">{{ permission.type }}</AppBadge>
          </div>
        </div>
        <div v-else class="text-center py-4">
          <p class="text-gray-500 dark:text-gray-400">No permissions assigned</p>
        </div>
      </AppCard>
      <AppCard>
        <template #header>
          <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Role Statistics</h3>
        </template>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <MetricCard title="Total Users" :value="roleStats.totalUsers" icon="users" color="primary" />
          <MetricCard title="Active Users" :value="roleStats.activeUsers" icon="user-check" color="success" />
          <MetricCard title="Assigned Permissions" :value="roleStats.assignedPermissions" icon="key" color="info" />
        </div>
      </AppCard>
    </div>
  </AppLayout>
</template>
<script>
import { ref, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useRolesStore } from '@/stores/roles'
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
  name: 'ShowRole',
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
    const rolesStore = useRolesStore()
    const loading = ref(false)
    const role = ref(null)
    const roleStats = ref({
      totalUsers: 0,
      activeUsers: 0,
      assignedPermissions: 0
    })
    const getTypeVariant = (type) => {
      return type === 'system' ? 'danger' : 'info'
    }
    const getPermissionVariant = (type) => {
      return type === 'system' ? 'danger' : 'info'
    }
    const fetchRole = async () => {
      loading.value = true
      try {
        const response = await rolesStore.fetchRole(route.params.id)
        role.value = response.data
        // Dummy stats, replace with API if available
        roleStats.value = {
          totalUsers: 12,
          activeUsers: 10,
          assignedPermissions: response.data.permissions?.length || 0
        }
      } catch (error) {
        // handle error
      } finally {
        loading.value = false
      }
    }
    const editRole = () => {
      router.push(`/roles/${route.params.id}/edit`)
    }
    onMounted(() => {
      fetchRole()
    })
    return {
      loading,
      role,
      roleStats,
      getTypeVariant,
      getPermissionVariant,
      editRole
    }
  }
}
</script>
<style scoped>

</style> 