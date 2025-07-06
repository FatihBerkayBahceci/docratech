<template>
  <AppLayout>
    <template #header>
      <PageHeader
        :title="permission ? `Edit Permission: ${permission.name}` : 'Edit Permission'"
        subtitle="Update permission information and assigned roles."
      >
        <template #actions>
          <AppButton @click="savePermission" variant="primary" :loading="loading" class="hover-lift animate-bounceIn">
            <Icon name="save" class="w-4 h-4 mr-2" />
            Save Changes
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
    <div v-else class="max-w-3xl mx-auto space-y-8 animate-fadeInUp">
      <AppCard>
        <template #header>
          <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Edit Permission Information</h3>
        </template>
        <form @submit.prevent="savePermission" class="space-y-6">
          <FormGroup label="Permission Name" required>
            <InputText v-model="form.name" placeholder="Enter permission name" :error="errors.name" required />
          </FormGroup>
          <FormGroup label="Key" required>
            <InputText v-model="form.key" placeholder="Enter unique key" :error="errors.key" required />
          </FormGroup>
          <FormGroup label="Type" required>
            <Select v-model="form.type" :options="typeOptions" placeholder="Select type" :error="errors.type" required />
          </FormGroup>
          <FormGroup label="Status" required>
            <Select v-model="form.status" :options="statusOptions" placeholder="Select status" :error="errors.status" required />
          </FormGroup>
          <FormGroup label="Description">
            <Textarea v-model="form.description" placeholder="Describe this permission..." :error="errors.description" :rows="3" />
          </FormGroup>
          <FormGroup label="Roles">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
              <Checkbox v-for="role in availableRoles" :key="role.id" v-model="form.roles" :value="role.id" :label="role.name" />
            </div>
          </FormGroup>
          <div class="flex justify-end space-x-4 pt-6 border-t border-gray-200 dark:border-gray-700 animate-fadeInUp">
            <AppButton type="button" variant="outline" @click="resetForm" class="hover-lift">
              Reset
            </AppButton>
            <AppButton type="submit" variant="primary" :loading="loading" class="hover-lift animate-bounceIn">
              <Icon name="save" class="w-4 h-4 mr-2" />
              Save Changes
            </AppButton>
          </div>
        </form>
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
import { ref, reactive, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { usePermissionsStore } from '@/stores/permissions'
import { useRolesStore } from '@/stores/roles'
import AppLayout from '@/components/layout/AppLayout.vue'
import PageHeader from '@/components/header/PageHeader.vue'
import AppCard from '@/components/card/AppCard.vue'
import AppButton from '@/components/button/AppButton.vue'
import FormGroup from '@/components/form/FormGroup.vue'
import InputText from '@/components/form/InputText.vue'
import Select from '@/components/form/Select.vue'
import Textarea from '@/components/form/Textarea.vue'
import Checkbox from '@/components/form/Checkbox.vue'
import LoadingSpinner from '@/components/loading/LoadingSpinner.vue'
import ErrorState from '@/components/empty/ErrorState.vue'
import Icon from '@/components/visual/Icon.vue'
import MetricCard from '@/components/card/MetricCard.vue'
export default {
  name: 'EditPermission',
  components: {
    AppLayout,
    PageHeader,
    AppCard,
    AppButton,
    FormGroup,
    InputText,
    Select,
    Textarea,
    Checkbox,
    LoadingSpinner,
    ErrorState,
    Icon,
    MetricCard
  },
  setup() {
    const route = useRoute()
    const router = useRouter()
    const permissionsStore = usePermissionsStore()
    const rolesStore = useRolesStore()
    const loading = ref(false)
    const errors = reactive({})
    const permission = ref(null)
    const form = reactive({
      name: '',
      key: '',
      type: '',
      status: 'active',
      description: '',
      roles: []
    })
    const typeOptions = ref([
      { value: 'system', label: 'System' },
      { value: 'custom', label: 'Custom' }
    ])
    const statusOptions = ref([
      { value: 'active', label: 'Active' },
      { value: 'inactive', label: 'Inactive' }
    ])
    const availableRoles = ref([])
    const permissionStats = ref({
      totalRoles: 0,
      activeRoles: 0,
      assignedUsers: 0
    })
    const validateForm = () => {
      errors.value = {}
      if (!form.name) errors.name = 'Permission name is required'
      if (!form.key) errors.key = 'Key is required'
      if (!form.type) errors.type = 'Type is required'
      if (!form.status) errors.status = 'Status is required'
      return Object.keys(errors.value).length === 0
    }
    const fetchPermission = async () => {
      loading.value = true
      try {
        const response = await permissionsStore.fetchPermission(route.params.id)
        permission.value = response.data
        Object.assign(form, {
          name: response.data.name,
          key: response.data.key,
          type: response.data.type,
          status: response.data.status,
          description: response.data.description,
          roles: response.data.roles?.map(r => r.id) || []
        })
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
    const savePermission = async () => {
      if (!validateForm()) return
      loading.value = true
      try {
        await permissionsStore.updatePermission(route.params.id, {
          name: form.name,
          key: form.key,
          type: form.type,
          status: form.status,
          description: form.description,
          roles: form.roles
        })
        router.push('/permissions')
      } catch (error) {
        if (error.response?.data?.errors) {
          Object.keys(error.response.data.errors).forEach(key => {
            errors[key] = error.response.data.errors[key][0]
          })
        }
      } finally {
        loading.value = false
      }
    }
    const resetForm = () => {
      if (!permission.value) return
      Object.assign(form, {
        name: permission.value.name,
        key: permission.value.key,
        type: permission.value.type,
        status: permission.value.status,
        description: permission.value.description,
        roles: permission.value.roles?.map(r => r.id) || []
      })
      Object.keys(errors).forEach(key => delete errors[key])
    }
    const loadData = async () => {
      try {
        const roles = await rolesStore.fetchRoles()
        availableRoles.value = roles.data
      } catch (error) {
        // handle error
      }
    }
    onMounted(() => {
      fetchPermission()
      loadData()
    })
    return {
      form,
      errors,
      loading,
      typeOptions,
      statusOptions,
      availableRoles,
      permissionStats,
      permission,
      savePermission,
      resetForm
    }
  }
}
</script>
<style scoped>

</style> 