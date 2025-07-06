<template>
  <AppLayout>
    <template #header>
      <PageHeader
        title="Create Permission"
        subtitle="Add a new permission and assign it to roles."
      >
        <template #actions>
          <AppButton
            @click="savePermission"
            variant="primary"
            :loading="loading"
            class="hover-lift animate-bounceIn"
          >
            <Icon name="key-plus" class="w-4 h-4 mr-2" />
            Create Permission
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
          <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Permission Information</h3>
          <p class="text-sm text-gray-600 dark:text-gray-400">Fill in the details for the new permission.</p>
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
              <Icon name="key-plus" class="w-4 h-4 mr-2" />
              Create Permission
            </AppButton>
          </div>
        </form>
      </AppCard>
      <AppCard>
        <template #header>
          <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Permission Statistics</h3>
        </template>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <MetricCard title="Total Permissions" :value="permissionStats.totalPermissions" icon="key" color="primary" />
          <MetricCard title="Active Permissions" :value="permissionStats.activePermissions" icon="key-check" color="success" />
          <MetricCard title="Custom Permissions" :value="permissionStats.customPermissions" icon="key-plus" color="info" />
          <MetricCard title="System Permissions" :value="permissionStats.systemPermissions" icon="key" color="warning" />
        </div>
      </AppCard>
    </div>
  </AppLayout>
</template>
<script>
import { ref, reactive, onMounted } from 'vue'
import { useRouter } from 'vue-router'
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
import Icon from '@/components/visual/Icon.vue'
import MetricCard from '@/components/card/MetricCard.vue'
export default {
  name: 'CreatePermission',
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
    const permissionsStore = usePermissionsStore()
    const rolesStore = useRolesStore()
    const loading = ref(false)
    const errors = reactive({})
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
    const permissionStats = reactive({
      totalPermissions: 0,
      activePermissions: 0,
      customPermissions: 0,
      systemPermissions: 0
    })
    const validateForm = () => {
      errors.value = {}
      if (!form.name) errors.name = 'Permission name is required'
      if (!form.key) errors.key = 'Key is required'
      if (!form.type) errors.type = 'Type is required'
      if (!form.status) errors.status = 'Status is required'
      return Object.keys(errors.value).length === 0
    }
    const savePermission = async () => {
      if (!validateForm()) return
      loading.value = true
      try {
        await permissionsStore.createPermission({
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
      Object.assign(form, {
        name: '',
        key: '',
        type: '',
        status: 'active',
        description: '',
        roles: []
      })
      Object.keys(errors).forEach(key => delete errors[key])
    }
    const loadData = async () => {
      try {
        const roles = await rolesStore.fetchRoles()
        availableRoles.value = roles.data
        // Dummy stats, replace with API if available
        Object.assign(permissionStats, {
          totalPermissions: 120,
          activePermissions: 100,
          customPermissions: 15,
          systemPermissions: 5
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
      availableRoles,
      permissionStats,
      savePermission,
      resetForm
    }
  }
}
</script>
<style scoped>

</style> 