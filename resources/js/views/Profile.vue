<template>
  <div class="px-6 py-6">
    <!-- Page Header -->
    <div class="bg-white border-b border-gray-200 p-6 rounded-lg shadow-sm">
      <div class="flex items-center justify-between">
        <div>
          <h1 class="text-3xl font-bold text-gray-900">Profile</h1>
          <p class="text-gray-600 mt-2">Manage your account settings and preferences</p>
        </div>
        <div class="flex items-center gap-3">
          <AppButton
            @click="saveProfile"
            variant="primary"
            :loading="loading"
          >
            <Icon name="save" class="w-4 h-4 mr-2" />
            Save Changes
          </AppButton>
        </div>
      </div>
    </div>

    <div class="max-w-4xl mx-auto space-y-6">
      <!-- Profile Information -->
      <AppCard>
        <template #header>
          <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
            Profile Information
          </h3>
          <p class="text-sm text-gray-600 dark:text-gray-400">
            Update your account's profile information and email address.
          </p>
        </template>

        <form @submit.prevent="saveProfile" class="space-y-6">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <FormGroup label="First Name" required>
              <InputText
                v-model="form.firstName"
                placeholder="Enter first name"
                :error="errors.firstName"
                required
              />
            </FormGroup>

            <FormGroup label="Last Name" required>
              <InputText
                v-model="form.lastName"
                placeholder="Enter last name"
                :error="errors.lastName"
                required
              />
            </FormGroup>
          </div>

          <FormGroup label="Email Address" required>
            <InputText
              v-model="form.email"
              type="email"
              placeholder="Enter email address"
              :error="errors.email"
              required
            />
          </FormGroup>

          <FormGroup label="Phone Number">
            <InputText
              v-model="form.phone"
              type="tel"
              placeholder="Enter phone number"
              :error="errors.phone"
            />
          </FormGroup>

          <FormGroup label="Bio">
            <Textarea
              v-model="form.bio"
              placeholder="Tell us about yourself..."
              :error="errors.bio"
              rows="4"
            />
          </FormGroup>
        </form>
      </AppCard>

      <!-- Security Settings -->
      <AppCard>
        <template #header>
          <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
            Security Settings
          </h3>
          <p class="text-sm text-gray-600 dark:text-gray-400">
            Update your password and security preferences.
          </p>
        </template>

        <form @submit.prevent="changePassword" class="space-y-6">
          <FormGroup label="Current Password" required>
            <InputText
              v-model="passwordForm.currentPassword"
              type="password"
              placeholder="Enter current password"
              :error="passwordErrors.currentPassword"
              required
            />
          </FormGroup>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <FormGroup label="New Password" required>
              <InputText
                v-model="passwordForm.newPassword"
                type="password"
                placeholder="Enter new password"
                :error="passwordErrors.newPassword"
                required
              />
            </FormGroup>

            <FormGroup label="Confirm New Password" required>
              <InputText
                v-model="passwordForm.confirmPassword"
                type="password"
                placeholder="Confirm new password"
                :error="passwordErrors.confirmPassword"
                required
              />
            </FormGroup>
          </div>

          <div class="flex justify-end">
            <AppButton
              type="submit"
              variant="outline"
              :loading="passwordLoading"
            >
              <Icon name="key" class="w-4 h-4 mr-2" />
              Change Password
            </AppButton>
          </div>
        </form>
      </AppCard>

      <!-- Preferences -->
      <AppCard>
        <template #header>
          <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
            Preferences
          </h3>
          <p class="text-sm text-gray-600 dark:text-gray-400">
            Customize your experience and notification settings.
          </p>
        </template>

        <div class="space-y-6">
          <div class="flex items-center justify-between">
            <div>
              <h4 class="text-sm font-medium text-gray-900 dark:text-white">
                Email Notifications
              </h4>
              <p class="text-sm text-gray-600 dark:text-gray-400">
                Receive email notifications for important updates
              </p>
            </div>
            <SwitchToggle
              v-model="preferences.emailNotifications"
              @update:modelValue="updatePreferences"
            />
          </div>

          <div class="flex items-center justify-between">
            <div>
              <h4 class="text-sm font-medium text-gray-900 dark:text-white">
                Push Notifications
              </h4>
              <p class="text-sm text-gray-600 dark:text-gray-400">
                Receive push notifications in your browser
              </p>
            </div>
            <SwitchToggle
              v-model="preferences.pushNotifications"
              @update:modelValue="updatePreferences"
            />
          </div>

          <div class="flex items-center justify-between">
            <div>
              <h4 class="text-sm font-medium text-gray-900 dark:text-white">
                Two-Factor Authentication
              </h4>
              <p class="text-sm text-gray-600 dark:text-gray-400">
                Add an extra layer of security to your account
              </p>
            </div>
            <SwitchToggle
              v-model="preferences.twoFactorAuth"
              @update:modelValue="updatePreferences"
            />
          </div>
        </div>
      </AppCard>

      <!-- Account Actions -->
      <AppCard>
        <template #header>
          <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
            Account Actions
          </h3>
          <p class="text-sm text-gray-600 dark:text-gray-400">
            Manage your account and data.
          </p>
        </template>

        <div class="space-y-4">
          <div class="flex items-center justify-between p-4 border border-gray-200 dark:border-gray-700 rounded-lg">
            <div>
              <h4 class="text-sm font-medium text-gray-900 dark:text-white">
                Export Data
              </h4>
              <p class="text-sm text-gray-600 dark:text-gray-400">
                Download a copy of your personal data
              </p>
            </div>
            <AppButton variant="secondary" size="small">
              <Icon name="download" class="w-4 h-4 mr-2" />
              Export
            </AppButton>
          </div>

          <div class="flex items-center justify-between p-4 border border-gray-200 dark:border-gray-700 rounded-lg">
            <div>
              <h4 class="text-sm font-medium text-gray-900 dark:text-white">
                Delete Account
              </h4>
              <p class="text-sm text-gray-600 dark:text-gray-400">
                Permanently delete your account and all data
              </p>
            </div>
            <AppButton variant="danger" size="small">
              <Icon name="trash" class="w-4 h-4 mr-2" />
              Delete
            </AppButton>
          </div>
        </div>
      </AppCard>
    </div>
  </div>
</template>

<script>
import { ref, reactive, onMounted } from 'vue'
import { useAuthStore } from '@/stores/auth'
import AppCard from '@/components/card/AppCard.vue'
import AppButton from '@/components/button/AppButton.vue'
import FormGroup from '@/components/form/FormGroup.vue'
import InputText from '@/components/form/InputText.vue'
import Textarea from '@/components/form/Textarea.vue'
import SwitchToggle from '@/components/form/SwitchToggle.vue'
import Icon from '@/components/visual/Icon.vue'

export default {
  name: 'Profile',
  components: {
    AppCard,
    AppButton,
    FormGroup,
    InputText,
    Textarea,
    SwitchToggle,
    Icon
  },
  setup() {
    const authStore = useAuthStore()
    const loading = ref(false)
    const passwordLoading = ref(false)
    const errors = reactive({})
    const passwordErrors = reactive({})

    const form = reactive({
      firstName: '',
      lastName: '',
      email: '',
      phone: '',
      bio: ''
    })

    const passwordForm = reactive({
      currentPassword: '',
      newPassword: '',
      confirmPassword: ''
    })

    const preferences = reactive({
      emailNotifications: true,
      pushNotifications: false,
      twoFactorAuth: false
    })

    const loadProfile = async () => {
      try {
        const response = await authStore.getProfile()
        const user = response.user
        
        form.firstName = user.first_name || ''
        form.lastName = user.last_name || ''
        form.email = user.email || ''
        form.phone = user.phone || ''
        form.bio = user.bio || ''
        
        preferences.emailNotifications = user.preferences?.email_notifications ?? true
        preferences.pushNotifications = user.preferences?.push_notifications ?? false
        preferences.twoFactorAuth = user.preferences?.two_factor_auth ?? false
      } catch (error) {
        console.error('Failed to load profile:', error)
      }
    }

    const saveProfile = async () => {
      loading.value = true
      try {
        await authStore.updateProfile({
          first_name: form.firstName,
          last_name: form.lastName,
          email: form.email,
          phone: form.phone,
          bio: form.bio
        })
        
        authStore.showToast({
          type: 'success',
          title: 'Profile Updated',
          message: 'Your profile has been updated successfully'
        })
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

    const changePassword = async () => {
      if (passwordForm.newPassword !== passwordForm.confirmPassword) {
        passwordErrors.confirmPassword = 'Passwords do not match'
        return
      }

      passwordLoading.value = true
      try {
        // This would be implemented in the auth service
        await authStore.updateProfile({
          current_password: passwordForm.currentPassword,
          new_password: passwordForm.newPassword,
          new_password_confirmation: passwordForm.confirmPassword
        })
        
        passwordForm.currentPassword = ''
        passwordForm.newPassword = ''
        passwordForm.confirmPassword = ''
        
        authStore.showToast({
          type: 'success',
          title: 'Password Changed',
          message: 'Your password has been changed successfully'
        })
      } catch (error) {
        if (error.response?.data?.errors) {
          Object.keys(error.response.data.errors).forEach(key => {
            passwordErrors[key] = error.response.data.errors[key][0]
          })
        }
      } finally {
        passwordLoading.value = false
      }
    }

    const updatePreferences = async () => {
      try {
        await authStore.updateProfile({
          preferences: {
            email_notifications: preferences.emailNotifications,
            push_notifications: preferences.pushNotifications,
            two_factor_auth: preferences.twoFactorAuth
          }
        })
      } catch (error) {
        console.error('Failed to update preferences:', error)
      }
    }

    onMounted(() => {
      loadProfile()
    })

    return {
      form,
      passwordForm,
      preferences,
      errors,
      passwordErrors,
      loading,
      passwordLoading,
      saveProfile,
      changePassword,
      updatePreferences
    }
  }
}
</script> 