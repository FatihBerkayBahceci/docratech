<template>
  <AppLayout>
    <template #header>
      <PageHeader
        title="Create New User"
        subtitle="Add a new user to your organization"
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
            <Icon name="user-plus" class="w-4 h-4 mr-2" />
            Create User
          </AppButton>
        </template>
      </PageHeader>
    </template>

    <div class="max-w-4xl mx-auto space-y-8">
      <!-- Progress Indicator -->
      <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100">
        <div class="flex items-center justify-between mb-4">
          <h3 class="text-lg font-semibold text-gray-900">User Creation Progress</h3>
          <div class="flex items-center gap-2">
            <span class="text-sm text-gray-500">Step 1 of 1</span>
            <!-- Progress status indicator -->
            <div v-if="formCompletionPercentage === 100 && isFormValid" class="flex items-center gap-1">
              <Icon name="check-circle" class="w-4 h-4 text-green-500" />
              <span class="text-green-600 text-sm font-medium">Ready</span>
            </div>
            <div v-else-if="formCompletionPercentage === 100 && !isFormValid" class="flex items-center gap-1">
              <Icon name="exclamation-triangle" class="w-4 h-4 text-amber-500" />
              <span class="text-amber-600 text-sm font-medium">Validation Required</span>
            </div>
            <div v-else-if="formCompletionPercentage > 70" class="flex items-center gap-1">
              <Icon name="clock" class="w-4 h-4 text-blue-500" />
              <span class="text-blue-600 text-sm font-medium">Almost Done</span>
            </div>
          </div>
        </div>
        <div class="w-full bg-gray-200 rounded-full h-2">
          <div class="bg-gradient-to-r from-[#5A1188] to-[#9D38CF] h-2 rounded-full transition-all duration-500" 
               :style="{ width: formCompletionPercentage + '%' }">
          </div>
        </div>
        <div class="flex justify-between items-center mt-2">
          <p class="text-sm text-gray-600">{{ formCompletionPercentage }}% complete</p>
          <div class="flex items-center gap-4 text-xs text-gray-500">
            <span class="font-medium">Required fields: {{ [form.firstName, form.lastName, form.email, form.roleId, form.status, form.password, form.passwordConfirmation].filter(field => field && field.toString().trim()).length }}/7</span>
            <!-- Missing fields indicator -->
            <div v-if="!isFormValid && formCompletionPercentage === 100" class="flex items-center gap-1">
              <span class="w-2 h-2 bg-red-400 rounded-full animate-pulse"></span>
              <span class="text-red-600">Validation issues</span>
            </div>
          </div>
        </div>
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
                <p class="text-sm text-gray-600">Basic details about the user</p>
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
                <p class="text-sm text-gray-600">Password and authentication configuration</p>
              </div>
            </div>
          </template>

          <div class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <FormGroup label="Password" required>
                <InputText
                  v-model="form.password"
                  type="password"
                  placeholder="Enter secure password"
                  :error="errors.password"
                  :success="validFields.password"
                  required
                  @blur="validateField('password')"
                />
                <div class="mt-2">
                  <PasswordStrength :password="form.password" />
                </div>
              </FormGroup>

              <FormGroup label="Confirm Password" required>
                <InputText
                  v-model="form.passwordConfirmation"
                  type="password"
                  placeholder="Confirm password"
                  :error="errors.passwordConfirmation"
                  :success="validFields.passwordConfirmation"
                  required
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
                  <p class="text-sm text-gray-600">Force user to change password on first login</p>
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

              <div class="flex items-center justify-between p-4 bg-white rounded-lg border border-gray-200">
                <div>
                  <h5 class="text-sm font-medium text-gray-900">Send Welcome Email</h5>
                  <p class="text-sm text-gray-600">Send email with login credentials and welcome message</p>
                </div>
                <SwitchToggle v-model="form.sendWelcomeEmail" />
              </div>
            </div>
          </div>
        </AppCard>

        <!-- Permissions Section -->
        <AppCard v-if="availablePermissions.length > 0">
          <template #header>
            <div class="flex items-center space-x-3">
              <div class="w-8 h-8 bg-gradient-to-r from-[#5A1188] to-[#9D38CF] rounded-lg flex items-center justify-center">
                <Icon name="key" class="w-4 h-4 text-white" />
              </div>
              <div>
                <h3 class="text-lg font-semibold text-gray-900">Additional Permissions</h3>
                <p class="text-sm text-gray-600">Grant specific permissions beyond role defaults</p>
              </div>
            </div>
          </template>

          <div class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
              <div
                v-for="permission in availablePermissions"
                :key="permission.id"
                class="flex items-center p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors"
              >
                <Checkbox
                  v-model="form.permissions"
                  :value="permission.id"
                  :label="permission.name"
                  :description="permission.description"
                />
              </div>
            </div>
          </div>
        </AppCard>

        <!-- Enterprise Validation Feedback -->
        <div v-if="!isFormValid" class="bg-amber-50 border border-amber-200 rounded-xl p-6 shadow-sm">
          <div class="flex items-start gap-3">
            <Icon name="exclamation-triangle" class="w-5 h-5 text-amber-600 flex-shrink-0 mt-0.5" />
            <div class="flex-1">
              <h4 class="text-sm font-semibold text-amber-800 mb-2">Form Validation Required</h4>
              <div class="space-y-2">
                <div v-if="!form.firstName.trim()" class="flex items-center gap-2 text-sm text-amber-700">
                  <span class="w-1 h-1 bg-amber-600 rounded-full"></span>
                  First Name is required
                </div>
                <div v-if="!form.lastName.trim()" class="flex items-center gap-2 text-sm text-amber-700">
                  <span class="w-1 h-1 bg-amber-600 rounded-full"></span>
                  Last Name is required
                </div>
                <div v-if="!form.email.trim()" class="flex items-center gap-2 text-sm text-amber-700">
                  <span class="w-1 h-1 bg-amber-600 rounded-full"></span>
                  Email Address is required
                </div>
                <div v-if="!form.roleId" class="flex items-center gap-2 text-sm text-amber-700">
                  <span class="w-1 h-1 bg-amber-600 rounded-full"></span>
                  Role selection is required
                </div>
                <div v-if="!form.status" class="flex items-center gap-2 text-sm text-amber-700">
                  <span class="w-1 h-1 bg-amber-600 rounded-full"></span>
                  Status selection is required
                </div>
                <div v-if="form.password.length < 8" class="flex items-center gap-2 text-sm text-amber-700">
                  <span class="w-1 h-1 bg-amber-600 rounded-full"></span>
                  Password must be at least 8 characters long
                </div>
                <div v-if="form.password && !/(?=.*[a-z])(?=.*[A-Z])(?=.*\d)/.test(form.password)" class="flex items-center gap-2 text-sm text-amber-700">
                  <span class="w-1 h-1 bg-amber-600 rounded-full"></span>
                  Password must contain uppercase, lowercase, and number
                </div>
                <div v-if="form.password !== form.passwordConfirmation" class="flex items-center gap-2 text-sm text-amber-700">
                  <span class="w-1 h-1 bg-amber-600 rounded-full"></span>
                  Passwords do not match
                </div>
                <div v-if="Object.keys(errors).length > 0" class="flex items-center gap-2 text-sm text-amber-700">
                  <span class="w-1 h-1 bg-amber-600 rounded-full"></span>
                  {{ Object.keys(errors).length }} validation error(s) found
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Success indicator when form is valid -->
        <div v-else class="bg-green-50 border border-green-200 rounded-xl p-6 shadow-sm">
          <div class="flex items-center gap-3">
            <Icon name="check-circle" class="w-5 h-5 text-green-600" />
            <span class="text-sm font-medium text-green-800">Form validation passed - Ready to create user</span>
          </div>
        </div>

        <!-- Form Actions -->
        <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100">
          <div class="flex justify-between items-center">
            <div class="text-sm text-gray-600">
              <Icon name="shield-check" class="w-4 h-4 inline mr-1" />
              All data is encrypted and secure
            </div>
            <div class="flex space-x-4">
              <AppButton
                type="button"
                variant="outline"
                @click="resetForm"
                :disabled="loading"
              >
                Reset Form
              </AppButton>
              <AppButton
                type="submit"
                variant="primary"
                :loading="loading"
                :disabled="!isFormValid"
                :title="!isFormValid ? 'Please complete all required fields and fix validation errors' : ''"
              >
                <Icon name="user-plus" class="w-4 h-4 mr-2" />
                Create User
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
      title="User Created Successfully"
      :message="`${form.firstName} ${form.lastName} has been added to the system.`"
      @close="showSuccessToast = false"
    />
  </AppLayout>
</template>

<script setup>
import { ref, reactive, computed, onMounted, watch } from 'vue'
import { useRouter } from 'vue-router'
import { useUsersStore } from '@/stores/users'
import { useRolesStore } from '@/stores/roles'
import { usePermissionsStore } from '@/stores/permissions'
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
import Checkbox from '@/components/form/Checkbox.vue'
import SwitchToggle from '@/components/form/SwitchToggle.vue'
import ToastNotification from '@/components/toast/ToastNotification.vue'
import Icon from '@/components/visual/Icon.vue'
import PasswordStrength from '@/components/form/PasswordStrength.vue'

// Composables
const router = useRouter()
const toast = useToast()
const usersStore = useUsersStore()
const rolesStore = useRolesStore()
const permissionsStore = usePermissionsStore()

// State
const loading = ref(false)
const showCancelModal = ref(false)
const showSuccessToast = ref(false)
const errors = reactive({})
const validFields = reactive({})

const form = reactive({
  firstName: '',
  lastName: '',
  email: '',
  phone: '',
  phoneCountry: 'TR',
  roleId: null,
  departmentId: null,
  status: 'active',
  password: '',
  passwordConfirmation: '',
  bio: '',
  requirePasswordChange: true,
  sendWelcomeEmail: true,
  enableTwoFactor: false,
  permissions: []
})

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

const availablePermissions = ref([])

// Breadcrumb
const breadcrumbItems = ref([
  { label: 'Dashboard', href: '/dashboard' },
  { label: 'Users', href: '/users' },
  { label: 'Create User', current: true }
])

// Computed
const formCompletionPercentage = computed(() => {
  const totalFields = 7 // firstName, lastName, email, roleId, status, password, passwordConfirmation
  const completedFields = [
    form.firstName,
    form.lastName,
    form.email,
    form.roleId,
    form.status,
    form.password,
    form.passwordConfirmation
  ].filter(field => field && field.toString().trim()).length
  
  return Math.round((completedFields / totalFields) * 100)
})

const isFormValid = computed(() => {
  return !loading.value && 
         form.firstName.trim() && 
         form.lastName.trim() && 
         form.email.trim() && 
         form.roleId && 
         form.status && 
         form.password.length >= 8 &&
         form.password === form.passwordConfirmation &&
         Object.keys(errors).length === 0
})

const hasUnsavedChanges = computed(() => {
  return form.firstName || form.lastName || form.email || form.phone || 
         form.roleId || form.bio || form.password
})

// Methods
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
      if (!form.password) {
        errors.password = 'Password is required'
      } else if (form.password.length < 8) {
        errors.password = 'Password must be at least 8 characters long'
      } else if (!/(?=.*[a-z])(?=.*[A-Z])(?=.*\d)/.test(form.password)) {
        errors.password = 'Password must contain uppercase, lowercase, and number'
      } else {
        validFields.password = true
      }
      break

    case 'passwordConfirmation':
      if (!form.passwordConfirmation) {
        errors.passwordConfirmation = 'Please confirm your password'
      } else if (form.password !== form.passwordConfirmation) {
        errors.passwordConfirmation = 'Passwords do not match'
      } else {
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
  validateField('password')
  validateField('passwordConfirmation')

  return Object.keys(errors).length === 0
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
      password: form.password,
      password_confirmation: form.passwordConfirmation,
      bio: form.bio,
      require_password_change: form.requirePasswordChange,
      send_welcome_email: form.sendWelcomeEmail,
      enable_two_factor: form.enableTwoFactor,
      permissions: form.permissions
    }

    await usersStore.createUser(userData)
    
    showSuccessToast.value = true
    toast.success(`User ${form.firstName} ${form.lastName} created successfully`)
    
    // Redirect after short delay
    setTimeout(() => {
      router.push('/users')
    }, 2000)
    
  } catch (error) {
    if (error.response?.data?.errors) {
      Object.keys(error.response.data.errors).forEach(key => {
        errors[key] = error.response.data.errors[key][0]
      })
    }
    toast.error('Failed to create user. Please try again.')
  } finally {
    loading.value = false
  }
}

const handleCancel = () => {
  if (hasUnsavedChanges.value) {
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
  Object.assign(form, {
    firstName: '',
    lastName: '',
    email: '',
    phone: '',
    phoneCountry: 'TR',
    roleId: null,
    departmentId: null,
    status: 'active',
    password: '',
    passwordConfirmation: '',
    bio: '',
    requirePasswordChange: true,
    sendWelcomeEmail: true,
    enableTwoFactor: false,
    permissions: []
  })
  
  Object.keys(errors).forEach(key => delete errors[key])
  Object.keys(validFields).forEach(key => delete validFields[key])
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

const loadData = async () => {
  try {
    // Load roles
    const rolesResponse = await rolesStore.fetchRoles()
    roleOptions.value = rolesResponse.data.map(role => ({
      value: role.id,
      label: role.name,
      description: role.description
    }))

    // Load permissions
    const permissionsResponse = await permissionsStore.fetchPermissions()
    availablePermissions.value = permissionsResponse.data.map(permission => ({
      id: permission.id,
      name: permission.name,
      description: permission.description
    }))
  } catch (error) {
    console.error('Failed to load data:', error)
    toast.error('Failed to load form data')
  }
}

// Watch for form changes to trigger validation
watch(() => form.password, () => {
  if (form.passwordConfirmation) {
    validateField('passwordConfirmation')
  }
})

// Initialize component
onMounted(() => {
  loadData()
})

// Prevent navigation with unsaved changes
window.addEventListener('beforeunload', (e) => {
  if (hasUnsavedChanges.value) {
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

.animate-slide-up {
  animation: slideInUp 0.3s ease-out;
}

/* Form styling enhancements */
:deep(.form-control) {
  transition: all 0.2s ease;
}

:deep(.form-control:focus-within) {
  transform: translateY(-1px);
  box-shadow: 0 4px 12px rgba(90, 17, 136, 0.15);
}

/* Progress bar styling */
.progress-bar {
  background: linear-gradient(90deg, #5A1188 0%, #9D38CF 100%);
  transition: width 0.5s ease;
}
</style> 