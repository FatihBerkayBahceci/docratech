<template>
  <AppLayout>
    <template #header>
      <PageHeader
        title="Create Role"
        subtitle="Add a new role and assign permissions."
      >
        <template #actions>
          <AppButton
            @click="saveRole"
            variant="primary"
            :loading="loading"
            class="hover-lift animate-bounceIn"
          >
            <Icon name="shield-plus" class="w-4 h-4 mr-2" />
            Create Role
          </AppButton>
        </template>
      </PageHeader>
    </template>

    <div v-if="loading" class="flex justify-center py-12">
      <LoadingSpinner size="lg" />
    </div>
    <div v-else class="max-w-3xl mx-auto space-y-8 animate-fadeInUp">
      <AppCard>
        <template #header>
          <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Role Information</h3>
          <p class="text-sm text-gray-600 dark:text-gray-400">Fill in the details for the new role.</p>
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
              <Icon name="shield-plus" class="w-4 h-4 mr-2" />
              Create Role
            </AppButton>
          </div>
        </form>
      </AppCard>
      <AppCard>
        <template #header>
          <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Role Statistics</h3>
        </template>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <MetricCard title="Total Roles" :value="roleStats.totalRoles" icon="shield" color="primary" />
          <MetricCard title="Active Roles" :value="roleStats.activeRoles" icon="shield-check" color="success" />
          <MetricCard title="Custom Roles" :value="roleStats.customRoles" icon="shield-plus" color="info" />
          <MetricCard title="System Roles" :value="roleStats.systemRoles" icon="shield" color="warning" />
        </div>
      </AppCard>
    </div>
  </AppLayout>
</template>

<script>
import { ref, reactive, onMounted } from 'vue'
import { useRouter } from 'vue-router'
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
import Icon from '@/components/visual/Icon.vue'
import MetricCard from '@/components/card/MetricCard.vue'

export default {
  name: 'CreateRole',
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
    Icon,
    MetricCard
  },
  setup() {
    const router = useRouter()
    const rolesStore = useRolesStore()
    const permissionsStore = usePermissionsStore()
    const loading = ref(false)
    const errors = reactive({})
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
    const roleStats = reactive({
      totalRoles: 0,
      activeRoles: 0,
      customRoles: 0,
      systemRoles: 0
    })
    const validateForm = () => {
      errors.value = {}
      if (!form.name) errors.name = 'Role name is required'
      if (!form.type) errors.type = 'Type is required'
      if (!form.status) errors.status = 'Status is required'
      return Object.keys(errors.value).length === 0
    }
    const saveRole = async () => {
      if (!validateForm()) return
      loading.value = true
      try {
        await rolesStore.createRole({
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
      Object.assign(form, {
        name: '',
        type: '',
        status: 'active',
        description: '',
        permissions: []
      })
      Object.keys(errors).forEach(key => delete errors[key])
    }
    const loadData = async () => {
      try {
        const permissions = await permissionsStore.fetchPermissions()
        availablePermissions.value = permissions.data
        // Dummy stats, replace with API if available
        Object.assign(roleStats, {
          totalRoles: 42,
          activeRoles: 30,
          customRoles: 8,
          systemRoles: 4
        })
      } catch (error) {
        // handle error
      }
    }
    onMounted(() => {
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
      saveRole,
      resetForm
    }
  }
}
</script>

<style scoped>

</style> 