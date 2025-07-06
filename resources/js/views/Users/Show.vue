<template>
  <AppLayout>
    <template #header>
      <PageHeader
        :title="user ? `${user.first_name} ${user.last_name}` : 'User Details'"
        subtitle="View user information and activity"
      >
        <template #actions>
          <AppButton
            @click="editUser"
            variant="outline"
          >
            <Icon name="pencil" class="w-4 h-4 mr-2" />
            Edit User
          </AppButton>
          <AppButton
            @click="deleteUser"
            variant="danger"
          >
            <Icon name="trash" class="w-4 h-4 mr-2" />
            Delete User
          </AppButton>
        </template>
      </PageHeader>
    </template>

    <div v-if="loading" class="flex justify-center py-8">
      <LoadingSpinner size="lg" />
    </div>

    <div v-else-if="!user" class="py-8">
      <ErrorState
        title="User Not Found"
        description="The user you're looking for doesn't exist or has been deleted."
      >
        <template #actions>
          <AppButton
            @click="$router.push('/users')"
            variant="primary"
          >
            Back to Users
          </AppButton>
        </template>
      </ErrorState>
    </div>

    <div v-else class="max-w-4xl mx-auto space-y-6">
      <!-- User Profile Card -->
      <AppCard>
        <div class="flex items-center space-x-6 p-6">
          <Avatar
            :src="user.avatar"
            :name="`${user.first_name} ${user.last_name}`"
            size="xl"
          />
          <div class="flex-1">
            <h1 class="text-2xl font-bold text-gray-900">
              {{ user.first_name }} {{ user.last_name }}
            </h1>
            <p class="text-lg text-gray-600 mb-2">{{ user.email }}</p>
            <div class="flex items-center space-x-3">
              <StatusBadge :status="user.status" />
              <span v-if="user.role" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                {{ user.role.name }}
              </span>
            </div>
          </div>
        </div>
      </AppCard>

      <!-- User Information -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Personal Information -->
        <AppCard>
          <template #header>
            <h3 class="text-lg font-semibold text-gray-900">
              Personal Information
            </h3>
          </template>

          <div class="space-y-4">
            <div class="flex justify-between">
              <span class="text-sm font-medium text-gray-600">Full Name</span>
              <span class="text-sm text-gray-900">{{ user.first_name }} {{ user.last_name }}</span>
            </div>
            <div class="flex justify-between">
              <span class="text-sm font-medium text-gray-600">Email</span>
              <span class="text-sm text-gray-900">{{ user.email }}</span>
            </div>
            <div v-if="user.phone" class="flex justify-between">
              <span class="text-sm font-medium text-gray-600">Phone</span>
              <span class="text-sm text-gray-900">{{ user.phone }}</span>
            </div>
            <div class="flex justify-between">
              <span class="text-sm font-medium text-gray-600">Status</span>
              <StatusBadge :status="user.status" />
            </div>
            <div class="flex justify-between">
              <span class="text-sm font-medium text-gray-600">Role</span>
              <span class="text-sm text-gray-900">{{ user.role?.name || 'No Role' }}</span>
            </div>
            <div class="flex justify-between">
              <span class="text-sm font-medium text-gray-600">Created</span>
              <span class="text-sm text-gray-900">{{ formatDateTime(user.created_at) }}</span>
            </div>
            <div v-if="user.updated_at" class="flex justify-between">
              <span class="text-sm font-medium text-gray-600">Last Updated</span>
              <span class="text-sm text-gray-900">{{ formatDateTime(user.updated_at) }}</span>
            </div>
          </div>
        </AppCard>

        <!-- Account Details -->
        <AppCard>
          <template #header>
            <h3 class="text-lg font-semibold text-gray-900">
              Account Details
            </h3>
          </template>

          <div class="space-y-4">
            <div class="flex justify-between">
              <span class="text-sm font-medium text-gray-600">Account Status</span>
              <StatusBadge :status="user.status" />
            </div>
            <div v-if="user.last_login_at" class="flex justify-between">
              <span class="text-sm font-medium text-gray-600">Last Login</span>
              <span class="text-sm text-gray-900">{{ formatDateTime(user.last_login_at) }}</span>
            </div>
            <div v-if="user.last_active_at" class="flex justify-between">
              <span class="text-sm font-medium text-gray-600">Last Active</span>
              <span class="text-sm text-gray-900">{{ formatDateTime(user.last_active_at) }}</span>
            </div>
            <div class="flex justify-between">
              <span class="text-sm font-medium text-gray-600">Two-Factor Auth</span>
              <span class="text-sm text-gray-900">
                {{ user.two_factor_enabled ? 'Enabled' : 'Disabled' }}
              </span>
            </div>
            <div v-if="user.email_verified_at" class="flex justify-between">
              <span class="text-sm font-medium text-gray-600">Email Verified</span>
              <span class="text-sm text-gray-900">{{ formatDateTime(user.email_verified_at) }}</span>
            </div>
          </div>
        </AppCard>
      </div>

      <!-- Bio Section -->
      <AppCard v-if="user.bio">
        <template #header>
          <h3 class="text-lg font-semibold text-gray-900">
            About
          </h3>
        </template>
        <p class="text-gray-700 leading-relaxed">{{ user.bio }}</p>
      </AppCard>

      <!-- Permissions -->
      <AppCard v-if="user.permissions && user.permissions.length">
        <template #header>
          <h3 class="text-lg font-semibold text-gray-900">
            Direct Permissions
          </h3>
        </template>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
          <div
            v-for="permission in user.permissions"
            :key="permission.id"
            class="flex items-center justify-between p-3 bg-gray-50 rounded-lg"
          >
            <span class="text-sm font-medium text-gray-900">{{ permission.name }}</span>
            <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-blue-100 text-blue-800">
              {{ permission.type || 'custom' }}
            </span>
          </div>
        </div>
      </AppCard>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useUsersStore } from '@/stores/users'

// Components
import AppLayout from '@/components/layout/AppLayout.vue'
import PageHeader from '@/components/header/PageHeader.vue'
import AppCard from '@/components/card/AppCard.vue'
import AppButton from '@/components/button/AppButton.vue'
import LoadingSpinner from '@/components/loading/LoadingSpinner.vue'
import ErrorState from '@/components/empty/ErrorState.vue'
import Avatar from '@/components/media/Avatar.vue'
import StatusBadge from '@/components/badge/StatusBadge.vue'
import Icon from '@/components/visual/Icon.vue'

// Composables
const route = useRoute()
const router = useRouter()
const usersStore = useUsersStore()

// State
const loading = ref(false)
const user = ref(null)

// Methods
const fetchUser = async () => {
  loading.value = true
  try {
    const response = await usersStore.fetchUser(route.params.id)
    user.value = response.data
  } catch (error) {
    console.error('Failed to fetch user:', error)
  } finally {
    loading.value = false
  }
}

const editUser = () => {
  router.push(`/users/${route.params.id}/edit`)
}

const deleteUser = async () => {
  if (confirm('Are you sure you want to delete this user?')) {
    try {
      await usersStore.deleteUser(route.params.id)
      router.push('/users')
    } catch (error) {
      console.error('Failed to delete user:', error)
    }
  }
}

const formatDateTime = (date) => {
  if (!date) return ''
  return new Date(date).toLocaleString()
}

// Initialize component
onMounted(() => {
  fetchUser()
})
</script> 