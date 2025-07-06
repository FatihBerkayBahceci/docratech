<template>
  <AppLayout>
    <template #header>
      <PageHeader
        :title="user ? `Edit User: ${user.first_name} ${user.last_name}` : 'Edit User'"
        subtitle="Update user information and settings"
        size="default"
      >
        <template #breadcrumb>
          <AppBreadcrumb :items="breadcrumbItems" />
        </template>
        <template #actions>
          <AppButton
            variant="outline"
            @click="handleCancel"
            :disabled="loading"
          >
            Cancel
          </AppButton>
          <AppButton
            @click="saveUser"
            variant="primary"
            :loading="loading"
            :disabled="!isFormValid"
          >
            <Icon name="save" class="w-4 h-4 mr-2" />
            Save Changes
          </AppButton>
        </template>
      </PageHeader>
    </template>

    <div v-if="loadingUser" class="flex justify-center py-12">
      <LoadingSpinner size="lg" />
    </div>
    
    <div v-else-if="!user" class="py-12">
      <ErrorState
        title="User Not Found"
        description="The user you're looking for doesn't exist or has been deleted."
      >
        <template #actions>
          <AppButton @click="$router.push('/users')" variant="primary">
            Back to Users
          </AppButton>
        </template>
      </ErrorState>
    </div>
    
    <div v-else class="max-w-4xl mx-auto space-y-8">
      <!-- User Overview Section -->
      <AppCard>
        <div class="flex flex-col md:flex-row md:items-center space-y-4 md:space-y-0 md:space-x-6 p-6">
          <div class="flex-shrink-0">
            <Avatar
              :src="user.avatar"
              :name="`${user.first_name} ${user.last_name}`"
              size="xl"
              class="mx-auto md:mx-0"
            />
          </div>
          <div class="flex-grow text-center md:text-left">
            <h3 class="text-2xl font-bold text-gray-900">
              {{ user.first_name }} {{ user.last_name }}
            </h3>
            <p class="text-lg text-gray-600 mt-1">{{ user.email }}</p>
            <div class="flex flex-wrap justify-center md:justify-start items-center gap-3 mt-3">
              <StatusBadge :status="user.status" />
              <span class="text-sm text-gray-500">
                <Icon name="calendar" class="w-4 h-4 inline mr-1" />
                Joined {{ formatDate(user.created_at) }}
              </span>
              <span v-if="user.last_login_at" class="text-sm text-gray-500">
                <Icon name="clock" class="w-4 h-4 inline mr-1" />
                Last active {{ formatDate(user.last_login_at) }}
              </span>
            </div>
          </div>
          <div class="flex-shrink-0 flex justify-center md:justify-end">
            <div class="bg-gradient-to-r from-[#5A1188] to-[#9D38CF] rounded-lg p-4 text-white text-center">
              <div class="text-2xl font-bold">{{ user.role?.name || 'No Role' }}</div>
              <div class="text-sm opacity-90">Current Role</div>
            </div>
          </div>
        </div>
      </AppCard>

      <!-- Progress Indicator -->
      <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100">
        <div class="flex items-center justify-between mb-4">
          <h3 class="text-lg font-semibold text-gray-900">Edit Progress</h3>
          <span class="text-sm text-gray-500">{{ hasChanges ? 'Changes detected' : 'No changes' }}</span>
        </div>
        <div class="w-full bg-gray-200 rounded-full h-2">
          <div class="bg-gradient-to-r from-[#5A1188] to-[#9D38CF] h-2 rounded-full transition-all duration-500" 
               :style="{ width: formCompletionPercentage + '%' }">
          </div>
        </div>
        <p class="text-sm text-gray-600 mt-2">{{ formCompletionPercentage }}% complete</p>
      </div>

      <!-- Main Form -->
      <form @submit.prevent="saveUser" class="space-y-8">
        <!-- Personal Information Section -->
        <AppCard>
          <template #header>
            <div class="flex items-center space-x-3">
              <div class="w-8 h-8 bg-gradient-to-r from-[#5A1188] to-[#9D38CF] rounded-lg flex items-center justify-center">
                <Icon name="user" class="w-4 h-4 text-white" />
              </div>
              <div>
                <h3 class="text-lg font-semibold text-gray-900">Personal Information</h3>
                <p class="text-sm text-gray-600">Update basic user details</p>
              </div>
            </div>
          </template>

          <div class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <FormGroup label="First Name" required>
                <InputText
                  v-model="form.firstName"
                  placeholder="Enter first name"
                  :error="errors.firstName"
                  :success="validFields.firstName"
                  required
                  @blur="validateField('firstName')"
                />
              </FormGroup>

              <FormGroup label="Last Name" required>
                <InputText
                  v-model="form.lastName"
                  placeholder="Enter last name"
                  :error="errors.lastName"
                  :success="validFields.lastName"
                  required
                  @blur="validateField('lastName')"
                />
              </FormGroup>
            </div>

            <FormGroup label="Email Address" required>
              <InputText
                v-model="form.email"
                type="email"
                placeholder="Enter email address"
                :error="errors.email"
                :success="validFields.email"
                required
                @blur="validateField('email')"
              />
            </FormGroup>

            <FormGroup label="Phone Number">
              <PhoneInput
                v-model="form.phone"
                :error="errors.phone"
                :default-country="form.phoneCountry || 'TR'"
                :is-verified="form.phoneVerified || false"
                help-text="Enter a valid phone number for SMS notifications"
                show-phone-info
                show-char-count
                clearable
                @country-change="handleCountryChange"
                @validation-change="handlePhoneValidation"
                @blur="validateField('phone')"
              />
            </FormGroup>

            <FormGroup label="Bio">
              <Textarea
                v-model="form.bio"
                placeholder="Brief description about the user (optional)"
                :error="errors.bio"
                rows="4"
              />
            </FormGroup>
          </div>
        </AppCard>

        <!-- Account Settings Section -->
        <AppCard>
          <template #header>
            <div class="flex items-center space-x-3">
              <div class="w-8 h-8 bg-gradient-to-r from-[#5A1188] to-[#9D38CF] rounded-lg flex items-center justify-center">
                <Icon name="settings" class="w-4 h-4 text-white" />
              </div>
              <div>
                <h3 class="text-lg font-semibold text-gray-900">Account Settings</h3>
                <p class="text-sm text-gray-600">Role, status, and access configuration</p>
              </div>
            </div>
          </template>

          <div class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <FormGroup label="Role" required>
                <SearchableSelect
                  v-model="form.roleId"
                  :options="roleOptions"
                  placeholder="Search and select role..."
                  :error="errors.roleId"
                  :success="validFields.roleId"
                  required
                  @blur="validateField('roleId')"
                />
              </FormGroup>

              <FormGroup label="Status" required>
                <Select
                  v-model="form.status"
                  :options="statusOptions"
                  placeholder="Select status"
                  :error="errors.status"
                  :success="validFields.status"
                  required
                />
              </FormGroup>
            </div>

            <FormGroup label="Department">
              <SearchableSelect
                v-model="form.departmentId"
                :options="departmentOptions"
                placeholder="Search and select department..."
                :error="errors.departmentId"
              />
            </FormGroup>
          </div>
        </AppCard>

        <!-- Security Settings Section -->
        <AppCard>
          <template #header>
            <div class="flex items-center space-x-3">
              <div class="w-8 h-8 bg-gradient-to-r from-[#5A1188] to-[#9D38CF] rounded-lg flex items-center justify-center">
                <Icon name="shield" class="w-4 h-4 text-white" />
              </div>
              <div>
                <h3 class="text-lg font-semibold text-gray-900">Security Settings</h3>
                <p class="text-sm text-gray-600">Password and authentication options</p>
              </div>
            </div>
          </template>

          <div class="space-y-6">
            <!-- Password Change Section -->
            <div class="p-4 bg-amber-50 border border-amber-200 rounded-lg">
              <div class="flex items-start space-x-3">
                <Icon name="info" class="w-5 h-5 text-amber-600 mt-0.5" />
                <div>
                  <h4 class="text-sm font-medium text-amber-800">Password Change</h4>
                  <p class="text-sm text-amber-700 mt-1">
                    Leave password fields empty to keep the current password unchanged.
                  </p>
                </div>
              </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <FormGroup label="New Password">
                <InputText
                  v-model="form.password"
                  type="password"
                  placeholder="Enter new password (optional)"
                  :error="errors.password"
                  :success="validFields.password"
                  @blur="validateField('password')"
                />
                <div v-if="form.password" class="mt-2">
                  <PasswordStrength :password="form.password" />
                </div>
              </FormGroup>

              <FormGroup label="Confirm New Password">
                <InputText
                  v-model="form.passwordConfirmation"
                  type="password"
                  placeholder="Confirm new password"
                  :error="errors.passwordConfirmation"
                  :success="validFields.passwordConfirmation"
                  @blur="validateField('passwordConfirmation')"
                />
              </FormGroup>
            </div>

            <!-- Security Options -->
            <div class="space-y-4 p-6 bg-gray-50 rounded-lg">
              <h4 class="text-sm font-semibold text-gray-900 mb-4">Security Options</h4>
              
              <div class="flex items-center justify-between p-4 bg-white rounded-lg border border-gray-200">
                <div>
                  <h5 class="text-sm font-medium text-gray-900">Require Password Change</h5>
                  <p class="text-sm text-gray-600">Force user to change password on next login</p>
                </div>
                <SwitchToggle v-model="form.requirePasswordChange" />
              </div>

              <div class="flex items-center justify-between p-4 bg-white rounded-lg border border-gray-200">
                <div>
                  <h5 class="text-sm font-medium text-gray-900">Enable Two-Factor Authentication</h5>
                  <p class="text-sm text-gray-600">Require 2FA for enhanced security</p>
                </div>
                <SwitchToggle v-model="form.enableTwoFactor" />
              </div>
            </div>
          </div>
        </AppCard>

        <!-- Activity & Audit Section -->
        <AppCard>
          <template #header>
            <div class="flex items-center space-x-3">
              <div class="w-8 h-8 bg-gradient-to-r from-[#5A1188] to-[#9D38CF] rounded-lg flex items-center justify-center">
                <Icon name="activity" class="w-4 h-4 text-white" />
              </div>
              <div>
                <h3 class="text-lg font-semibold text-gray-900">User Activity</h3>
                <p class="text-sm text-gray-600">Recent user activity and session information</p>
              </div>
            </div>
          </template>

          <div class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
              <div class="bg-gray-50 rounded-lg p-4">
                <div class="flex items-center space-x-2">
                  <Icon name="login" class="w-4 h-4 text-green-600" />
                  <span class="text-sm font-medium text-gray-900">Last Login</span>
                </div>
                <p class="text-sm text-gray-600 mt-1">
                  {{ user.last_login_at ? formatDate(user.last_login_at) : 'Never' }}
                </p>
              </div>

              <div class="bg-gray-50 rounded-lg p-4">
                <div class="flex items-center space-x-2">
                  <Icon name="globe" class="w-4 h-4 text-blue-600" />
                  <span class="text-sm font-medium text-gray-900">Login Count</span>
                </div>
                <p class="text-sm text-gray-600 mt-1">{{ user.login_count || 0 }} times</p>
              </div>

              <div class="bg-gray-50 rounded-lg p-4">
                <div class="flex items-center space-x-2">
                  <Icon name="shield-check" class="w-4 h-4 text-purple-600" />
                  <span class="text-sm font-medium text-gray-900">2FA Status</span>
                </div>
                <p class="text-sm text-gray-600 mt-1">
                  {{ user.two_factor_enabled ? 'Enabled' : 'Disabled' }}
                </p>
              </div>
            </div>
          </div>
        </AppCard>

        <!-- Form Actions -->
        <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100">
          <div class="flex justify-between items-center">
            <div class="text-sm text-gray-600">
              <Icon name="info" class="w-4 h-4 inline mr-1" />
              {{ hasChanges ? 'You have unsaved changes' : 'No changes to save' }}
            </div>
            <div class="flex space-x-4">
              <AppButton
                type="button"
                variant="outline"
                @click="resetForm"
                :disabled="loading || !hasChanges"
              >
                Reset Changes
              </AppButton>
              <AppButton
                type="submit"
                variant="primary"
                :loading="loading"
                :disabled="!isFormValid || !hasChanges"
              >
                <Icon name="save" class="w-4 h-4 mr-2" />
                Save Changes
              </AppButton>
            </div>
          </div>
        </div>
      </form>
    </div>

    <!-- Cancel Confirmation Modal -->
    <AppDialog
      v-model="showCancelModal"
      title="Unsaved Changes"
      max-width="md"
    >
      <div class="space-y-4">
        <p class="text-gray-600">
          You have unsaved changes. Are you sure you want to leave this page? All changes will be lost.
        </p>
        <div class="flex justify-end space-x-3">
          <AppButton variant="outline" @click="showCancelModal = false">
            Continue Editing
          </AppButton>
          <AppButton variant="danger" @click="confirmCancel">
            Discard Changes
          </AppButton>
        </div>
      </div>
    </AppDialog>

    <!-- Success Toast -->
    <ToastNotification
      v-if="showSuccessToast"
      type="success"
      title="User Updated Successfully"
      :message="`${form.firstName} ${form.lastName} has been updated.`"
      @close="showSuccessToast = false"
    />
  </AppLayout>
</template>

<script setup>
import { ref, reactive, computed, onMounted, watch } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useUsersStore } from '@/stores/users'
import { useRolesStore } from '@/stores/roles'
import { useToast } from '@/composables/useToast'

// Components
import AppLayout from '@/components/layout/AppLayout.vue'
import PageHeader from '@/components/header/PageHeader.vue'
import AppBreadcrumb from '@/components/navigation/AppBreadcrumb.vue'
import AppCard from '@/components/card/AppCard.vue'
import AppButton from '@/components/button/AppButton.vue'
import AppDialog from '@/components/modal/AppDialog.vue'
import FormGroup from '@/components/form/FormGroup.vue'
import InputText from '@/components/form/InputText.vue'
import PhoneInput from '@/components/form/PhoneInput.vue'
import Select from '@/components/form/Select.vue'
import SearchableSelect from '@/components/form/SearchableSelect.vue'
import Textarea from '@/components/form/Textarea.vue'
import SwitchToggle from '@/components/form/SwitchToggle.vue'
import StatusBadge from '@/components/badge/StatusBadge.vue'
import Avatar from '@/components/media/Avatar.vue'
import LoadingSpinner from '@/components/loading/LoadingSpinner.vue'
import ErrorState from '@/components/empty/ErrorState.vue'
import ToastNotification from '@/components/toast/ToastNotification.vue'
import Icon from '@/components/visual/Icon.vue'
import PasswordStrength from '@/components/form/PasswordStrength.vue'

// Composables
const route = useRoute()
const router = useRouter()
const toast = useToast()
const usersStore = useUsersStore()
const rolesStore = useRolesStore()

// State
const loading = ref(false)
const loadingUser = ref(false)
const showCancelModal = ref(false)
const showSuccessToast = ref(false)
const errors = reactive({})
const validFields = reactive({})
const user = ref(null)

const form = reactive({
  firstName: '',
  lastName: '',
  email: '',
  phone: '',
  phoneCountry: 'TR',
  phoneVerified: false,
  roleId: null,
  departmentId: null,
  status: 'active',
  password: '',
  passwordConfirmation: '',
  bio: '',
  requirePasswordChange: false,
  enableTwoFactor: false
})

const originalForm = ref({})

// Options
const roleOptions = ref([])
const departmentOptions = ref([
  { value: 1, label: 'Administration', description: 'Administrative staff' },
  { value: 2, label: 'Medical', description: 'Medical professionals' },
  { value: 3, label: 'Nursing', description: 'Nursing staff' },
  { value: 4, label: 'IT Support', description: 'Technical support' },
  { value: 5, label: 'Billing', description: 'Billing and finance' },
  { value: 6, label: 'HR', description: 'Human resources' }
])

const statusOptions = ref([
  { value: 'active', label: 'Active', description: 'User can access the system' },
  { value: 'inactive', label: 'Inactive', description: 'User cannot access the system' },
  { value: 'pending', label: 'Pending', description: 'Awaiting activation' }
])

// Breadcrumb
const breadcrumbItems = computed(() => [
  { label: 'Dashboard', href: '/dashboard' },
  { label: 'Users', href: '/users' },
  { label: user.value ? `${user.value.first_name} ${user.value.last_name}` : 'Edit User', current: true }
])

// Computed
const formCompletionPercentage = computed(() => {
  const totalFields = 5 // firstName, lastName, email, roleId, status
  const completedFields = [
    form.firstName,
    form.lastName,
    form.email,
    form.roleId,
    form.status
  ].filter(field => field && field.toString().trim()).length
  
  return Math.round((completedFields / totalFields) * 100)
})

const hasChanges = computed(() => {
  return JSON.stringify(form) !== JSON.stringify(originalForm.value)
})

const isFormValid = computed(() => {
  const basicValid = !loading.value && 
                    form.firstName.trim() && 
                    form.lastName.trim() && 
                    form.email.trim() && 
                    form.roleId && 
                    form.status && 
                    Object.keys(errors).length === 0

  // If password is provided, validate it
  if (form.password) {
    return basicValid && 
           form.password.length >= 8 && 
           form.password === form.passwordConfirmation
  }

  return basicValid
})

// Methods
const formatDate = (dateString) => {
  if (!dateString) return 'Never'
  const date = new Date(dateString)
  return date.toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  })
}

const validateField = (fieldName) => {
  // Clear previous error
  delete errors[fieldName]
  delete validFields[fieldName]

  switch (fieldName) {
    case 'firstName':
      if (!form.firstName.trim()) {
        errors.firstName = 'First name is required'
      } else if (form.firstName.trim().length < 2) {
        errors.firstName = 'First name must be at least 2 characters'
      } else {
        validFields.firstName = true
      }
      break

    case 'lastName':
      if (!form.lastName.trim()) {
        errors.lastName = 'Last name is required'
      } else if (form.lastName.trim().length < 2) {
        errors.lastName = 'Last name must be at least 2 characters'
      } else {
        validFields.lastName = true
      }
      break

    case 'email':
      const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/
      if (!form.email.trim()) {
        errors.email = 'Email address is required'
      } else if (!emailRegex.test(form.email)) {
        errors.email = 'Please enter a valid email address'
      } else {
        validFields.email = true
      }
      break

    case 'phone':
      if (form.phone && !/^[\+]?[1-9][\d]{0,15}$/.test(form.phone.replace(/\s/g, ''))) {
        errors.phone = 'Please enter a valid phone number'
      }
      break

    case 'roleId':
      if (!form.roleId) {
        errors.roleId = 'Role selection is required'
      } else {
        validFields.roleId = true
      }
      break

    case 'status':
      if (!form.status) {
        errors.status = 'Status selection is required'
      } else {
        validFields.status = true
      }
      break

    case 'password':
      if (form.password && form.password.length < 8) {
        errors.password = 'Password must be at least 8 characters long'
      } else if (form.password && !/(?=.*[a-z])(?=.*[A-Z])(?=.*\d)/.test(form.password)) {
        errors.password = 'Password must contain uppercase, lowercase, and number'
      } else if (form.password) {
        validFields.password = true
      }
      break

    case 'passwordConfirmation':
      if (form.password && !form.passwordConfirmation) {
        errors.passwordConfirmation = 'Please confirm your password'
      } else if (form.password && form.password !== form.passwordConfirmation) {
        errors.passwordConfirmation = 'Passwords do not match'
      } else if (form.password) {
        validFields.passwordConfirmation = true
      }
      break
  }
}

const validateForm = () => {
  // Clear all errors
  Object.keys(errors).forEach(key => delete errors[key])
  Object.keys(validFields).forEach(key => delete validFields[key])

  // Validate all required fields
  validateField('firstName')
  validateField('lastName')
  validateField('email')
  validateField('phone')
  validateField('roleId')
  validateField('status')
  
  // Only validate password if provided
  if (form.password) {
    validateField('password')
    validateField('passwordConfirmation')
  }

  return Object.keys(errors).length === 0
}

const fetchUser = async () => {
  loadingUser.value = true
  try {
    const response = await usersStore.fetchUser(route.params.id)
    user.value = response.data
    
    // Populate form with user data
    const userData = {
      firstName: user.value.first_name || '',
      lastName: user.value.last_name || '',
      email: user.value.email || '',
      phone: user.value.phone || '',
      phoneCountry: user.value.phone_country || 'TR',
      phoneVerified: user.value.phone_verified || false,
      roleId: user.value.role_id || null,
      departmentId: user.value.department_id || null,
      status: user.value.status || 'active',
      password: '',
      passwordConfirmation: '',
      bio: user.value.bio || '',
      requirePasswordChange: user.value.require_password_change || false,
      enableTwoFactor: user.value.two_factor_enabled || false
    }
    
    Object.assign(form, userData)
    originalForm.value = { ...userData }
    
  } catch (error) {
    console.error('Failed to fetch user:', error)
    toast.error('Failed to load user data')
  } finally {
    loadingUser.value = false
  }
}

const saveUser = async () => {
  if (!validateForm()) {
    toast.error('Please correct the errors before submitting')
    return
  }

  loading.value = true
  try {
    const userData = {
      first_name: form.firstName,
      last_name: form.lastName,
      email: form.email,
      phone: form.phone,
      phone_country: form.phoneCountry,
      role_id: form.roleId,
      department_id: form.departmentId,
      status: form.status,
      bio: form.bio,
      require_password_change: form.requirePasswordChange,
      two_factor_enabled: form.enableTwoFactor
    }

    // Only include password if it's being changed
    if (form.password) {
      userData.password = form.password
      userData.password_confirmation = form.passwordConfirmation
    }

    await usersStore.updateUser(route.params.id, userData)
    
    // Update original form to reflect saved state
    originalForm.value = { ...form }
    
    showSuccessToast.value = true
    toast.success(`User ${form.firstName} ${form.lastName} updated successfully`)
    
    // Refresh user data
    await fetchUser()
    
  } catch (error) {
    if (error.response?.data?.errors) {
      Object.keys(error.response.data.errors).forEach(key => {
        errors[key] = error.response.data.errors[key][0]
      })
    }
    toast.error('Failed to update user. Please try again.')
  } finally {
    loading.value = false
  }
}

const handleCancel = () => {
  if (hasChanges.value) {
    showCancelModal.value = true
  } else {
    router.push('/users')
  }
}

const confirmCancel = () => {
  showCancelModal.value = false
  router.push('/users')
}

const resetForm = () => {
  if (user.value) {
    Object.assign(form, originalForm.value)
    Object.keys(errors).forEach(key => delete errors[key])
    Object.keys(validFields).forEach(key => delete validFields[key])
  }
}

const loadData = async () => {
  try {
    // Load roles
    const rolesResponse = await rolesStore.fetchRoles()
    roleOptions.value = rolesResponse.data.map(role => ({
      value: role.id,
      label: role.name,
      description: role.description
    }))
  } catch (error) {
    console.error('Failed to load data:', error)
    toast.error('Failed to load form data')
  }
}

// Phone handlers
const handleCountryChange = (country) => {
  form.phoneCountry = country.iso
}

const handlePhoneValidation = (validation) => {
  if (validation.errors && validation.errors.length > 0) {
    errors.phone = validation.errors[0]
  } else {
    delete errors.phone
    if (validation.is_valid) {
      validFields.phone = true
    }
  }
}

// Watch for form changes to trigger validation
watch(() => form.password, () => {
  if (form.passwordConfirmation) {
    validateField('passwordConfirmation')
  }
})

// Initialize component
onMounted(async () => {
  await loadData()
  await fetchUser()
})

// Prevent navigation with unsaved changes
window.addEventListener('beforeunload', (e) => {
  if (hasChanges.value) {
    e.preventDefault()
    e.returnValue = ''
  }
})
</script>

<style scoped>
/* Custom animations */
@keyframes slideInUp {
  from {
    opacity: 0;
    transform: translateY(20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

@keyframes fadeInScale {
  from {
    opacity: 0;
    transform: scale(0.95);
  }
  to {
    opacity: 1;
    transform: scale(1);
  }
}

.animate-slide-up {
  animation: slideInUp 0.3s ease-out;
}

.animate-fade-scale {
  animation: fadeInScale 0.4s ease-out;
}

/* Form styling enhancements */
:deep(.form-control) {
  transition: all 0.2s ease;
}

:deep(.form-control:focus-within) {
  transform: translateY(-1px);
  box-shadow: 0 4px 12px rgba(90, 17, 136, 0.15);
}

/* Enhanced user overview styling */
.user-overview {
  background: linear-gradient(135deg, #f8f9fa 0%, #fff 100%);
}

/* Activity cards hover effect */
.activity-card {
  transition: all 0.2s ease;
}

.activity-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
 }
 </style> 