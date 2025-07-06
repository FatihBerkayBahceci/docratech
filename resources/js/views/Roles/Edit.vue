<template>
  <AppLayout>
    <template #header>
      <PageHeader
        :title="role ? `Edit Role: ${role.name}` : 'Edit Role'"
        subtitle="Update role information and permissions."
      >
        <template #actions>
          <AppButton @click="saveRole" variant="primary" :loading="loading" class="hover-lift animate-bounceIn">
            <Icon name="save" class="w-4 h-4 mr-2" />
            Save Changes
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
    <div v-else class="max-w-3xl mx-auto space-y-8 animate-fadeInUp">
      <AppCard>
        <template #header>
          <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Edit Role Information</h3>
        </template>
        <form @submit.prevent="saveRole" class="space-y-6">
          <FormGroup label="Role Name" required>
            <InputText v-model="form.name" placeholder="Enter role name" :error="errors.name" required />
          </FormGroup>
          <FormGroup label="Type" required>
            <Select v-model="form.type" :options="typeOptions" placeholder="Select type" :error="errors.type" required />
          </FormGroup>
          <FormGroup label="Status" required>
            <Select v-model="form.status" :options="statusOptions" placeholder="Select status" :error="errors.status" required />
          </FormGroup>
          <FormGroup label="Description">
            <Textarea v-model="form.description" placeholder="Describe this role..." :error="errors.description" :rows="3" />
          </FormGroup>
          <FormGroup label="Permissions">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
              <Checkbox v-for="permission in availablePermissions" :key="permission.id" v-model="form.permissions" :value="permission.id" :label="permission.name" />
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
import { ref, reactive, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useRolesStore } from '@/stores/roles'
import { usePermissionsStore } from '@/stores/permissions'
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
  name: 'EditRole',
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
    const rolesStore = useRolesStore()
    const permissionsStore = usePermissionsStore()
    const loading = ref(false)
    const errors = reactive({})
    const role = ref(null)
    const form = reactive({
      name: '',
      type: '',
      status: 'active',
      description: '',
      permissions: []
    })
    const typeOptions = ref([
      { value: 'system', label: 'System' },
      { value: 'custom', label: 'Custom' }
    ])
    const statusOptions = ref([
      { value: 'active', label: 'Active' },
      { value: 'inactive', label: 'Inactive' }
    ])
    const availablePermissions = ref([])
    const roleStats = ref({
      totalUsers: 0,
      activeUsers: 0,
      assignedPermissions: 0
    })
    const validateForm = () => {
      errors.value = {}
      if (!form.name) errors.name = 'Role name is required'
      if (!form.type) errors.type = 'Type is required'
      if (!form.status) errors.status = 'Status is required'
      return Object.keys(errors.value).length === 0
    }
    const fetchRole = async () => {
      loading.value = true
      try {
        const response = await rolesStore.fetchRole(route.params.id)
        role.value = response.data
        Object.assign(form, {
          name: response.data.name,
          type: response.data.type,
          status: response.data.status,
          description: response.data.description,
          permissions: response.data.permissions?.map(p => p.id) || []
        })
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
    const saveRole = async () => {
      if (!validateForm()) return
      loading.value = true
      try {
        await rolesStore.updateRole(route.params.id, {
          name: form.name,
          type: form.type,
          status: form.status,
          description: form.description,
          permissions: form.permissions
        })
        router.push('/roles')
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
      if (!role.value) return
      Object.assign(form, {
        name: role.value.name,
        type: role.value.type,
        status: role.value.status,
        description: role.value.description,
        permissions: role.value.permissions?.map(p => p.id) || []
      })
      Object.keys(errors).forEach(key => delete errors[key])
    }
    const loadData = async () => {
      try {
        const permissions = await permissionsStore.fetchPermissions()
        availablePermissions.value = permissions.data
      } catch (error) {
        // handle error
      }
    }
    onMounted(() => {
      fetchRole()
      loadData()
    })
    return {
      form,
      errors,
      loading,
      typeOptions,
      statusOptions,
      availablePermissions,
      roleStats,
      role,
      saveRole,
      resetForm
    }
  }
}
</script>
<style scoped>

</style> 