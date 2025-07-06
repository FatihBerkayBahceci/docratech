<template>
  <div class="px-6 py-6">
    <!-- Page Header -->
    <div class="bg-white border-b border-gray-200 p-6 rounded-lg shadow-sm">
      <div class="flex items-center justify-between">
        <div>
          <h1 class="text-3xl font-bold text-gray-900">Settings</h1>
          <p class="text-gray-600 mt-2">Manage system settings and configurations</p>
        </div>
        <div class="flex items-center gap-3">
          <AppButton
            @click="saveSettings"
            variant="primary"
            :loading="loading"
          >
            <Icon name="save" class="w-4 h-4 mr-2" />
            Save Settings
          </AppButton>
        </div>
      </div>
    </div>

    <div class="max-w-4xl mx-auto space-y-6">
      <!-- General Settings -->
      <AppCard>
        <template #header>
          <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
            General Settings
          </h3>
          <p class="text-sm text-gray-600 dark:text-gray-400">
            Configure basic system settings and preferences.
          </p>
        </template>

        <div class="space-y-6">
          <FormGroup label="Application Name" required>
            <InputText
              v-model="generalSettings.appName"
              placeholder="Enter application name"
              :error="errors.appName"
              required
            />
          </FormGroup>

          <FormGroup label="Application URL" required>
            <InputText
              v-model="generalSettings.appUrl"
              type="url"
              placeholder="https://example.com"
              :error="errors.appUrl"
              required
            />
          </FormGroup>

          <FormGroup label="Default Language">
            <Select
              v-model="generalSettings.defaultLanguage"
              :options="languageOptions"
              placeholder="Select default language"
            />
          </FormGroup>

          <FormGroup label="Default Timezone">
            <Select
              v-model="generalSettings.defaultTimezone"
              :options="timezoneOptions"
              placeholder="Select default timezone"
            />
          </FormGroup>

          <FormGroup label="Date Format">
            <Select
              v-model="generalSettings.dateFormat"
              :options="dateFormatOptions"
              placeholder="Select date format"
            />
          </FormGroup>

          <FormGroup label="Time Format">
            <Select
              v-model="generalSettings.timeFormat"
              :options="timeFormatOptions"
              placeholder="Select time format"
            />
          </FormGroup>
        </div>
      </AppCard>

      <!-- Security Settings -->
      <AppCard>
        <template #header>
          <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
            Security Settings
          </h3>
          <p class="text-sm text-gray-600 dark:text-gray-400">
            Configure security policies and authentication settings.
          </p>
        </template>

        <div class="space-y-6">
          <div class="flex items-center justify-between">
            <div>
              <h4 class="text-sm font-medium text-gray-900 dark:text-white">
                Require Two-Factor Authentication
              </h4>
              <p class="text-sm text-gray-600 dark:text-gray-400">
                Force all users to enable two-factor authentication
              </p>
            </div>
            <SwitchToggle
              v-model="securitySettings.requireTwoFactor"
              @update:modelValue="updateSecuritySettings"
            />
          </div>

          <div class="flex items-center justify-between">
            <div>
              <h4 class="text-sm font-medium text-gray-900 dark:text-white">
                Session Timeout
              </h4>
              <p class="text-sm text-gray-600 dark:text-gray-400">
                Automatically log out users after inactivity
              </p>
            </div>
            <Select
              v-model="securitySettings.sessionTimeout"
              :options="sessionTimeoutOptions"
              class="w-32"
            />
          </div>

          <div class="flex items-center justify-between">
            <div>
              <h4 class="text-sm font-medium text-gray-900 dark:text-white">
                Password Policy
              </h4>
              <p class="text-sm text-gray-600 dark:text-gray-400">
                Enforce strong password requirements
              </p>
            </div>
            <SwitchToggle
              v-model="securitySettings.enforcePasswordPolicy"
              @update:modelValue="updateSecuritySettings"
            />
          </div>

          <FormGroup label="Minimum Password Length">
            <NumberInput
              v-model="securitySettings.minPasswordLength"
              :min="6"
              :max="32"
              placeholder="8"
            />
          </FormGroup>

          <div class="flex items-center justify-between">
            <div>
              <h4 class="text-sm font-medium text-gray-900 dark:text-white">
                Account Lockout
              </h4>
              <p class="text-sm text-gray-600 dark:text-gray-400">
                Lock accounts after failed login attempts
              </p>
            </div>
            <SwitchToggle
              v-model="securitySettings.enableAccountLockout"
              @update:modelValue="updateSecuritySettings"
            />
          </div>

          <FormGroup label="Failed Login Attempts">
            <NumberInput
              v-model="securitySettings.maxFailedAttempts"
              :min="3"
              :max="10"
              placeholder="5"
            />
          </FormGroup>
        </div>
      </AppCard>

      <!-- Email Settings -->
      <AppCard>
        <template #header>
          <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
            Email Settings
          </h3>
          <p class="text-sm text-gray-600 dark:text-gray-400">
            Configure email server and notification settings.
          </p>
        </template>

        <div class="space-y-6">
          <FormGroup label="SMTP Host" required>
            <InputText
              v-model="emailSettings.smtpHost"
              placeholder="smtp.gmail.com"
              :error="errors.smtpHost"
              required
            />
          </FormGroup>

          <FormGroup label="SMTP Port" required>
            <NumberInput
              v-model="emailSettings.smtpPort"
              :min="1"
              :max="65535"
              placeholder="587"
              :error="errors.smtpPort"
              required
            />
          </FormGroup>

          <FormGroup label="SMTP Username" required>
            <InputText
              v-model="emailSettings.smtpUsername"
              placeholder="Enter SMTP username"
              :error="errors.smtpUsername"
              required
            />
          </FormGroup>

          <FormGroup label="SMTP Password" required>
            <InputText
              v-model="emailSettings.smtpPassword"
              type="password"
              placeholder="Enter SMTP password"
              :error="errors.smtpPassword"
              required
            />
          </FormGroup>

          <FormGroup label="Encryption">
            <Select
              v-model="emailSettings.encryption"
              :options="encryptionOptions"
              placeholder="Select encryption type"
            />
          </FormGroup>

          <FormGroup label="From Email" required>
            <InputText
              v-model="emailSettings.fromEmail"
              type="email"
              placeholder="noreply@example.com"
              :error="errors.fromEmail"
              required
            />
          </FormGroup>

          <FormGroup label="From Name" required>
            <InputText
              v-model="emailSettings.fromName"
              placeholder="DocRatech"
              :error="errors.fromName"
              required
            />
          </FormGroup>
        </div>
      </AppCard>

      <!-- Notification Settings -->
      <AppCard>
        <template #header>
          <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
            Notification Settings
          </h3>
          <p class="text-sm text-gray-600 dark:text-gray-400">
            Configure notification preferences and channels.
          </p>
        </template>

        <div class="space-y-6">
          <div class="flex items-center justify-between">
            <div>
              <h4 class="text-sm font-medium text-gray-900 dark:text-white">
                Email Notifications
              </h4>
              <p class="text-sm text-gray-600 dark:text-gray-400">
                Send notifications via email
              </p>
            </div>
            <SwitchToggle
              v-model="notificationSettings.emailEnabled"
              @update:modelValue="updateNotificationSettings"
            />
          </div>

          <div class="flex items-center justify-between">
            <div>
              <h4 class="text-sm font-medium text-gray-900 dark:text-white">
                Push Notifications
              </h4>
              <p class="text-sm text-gray-600 dark:text-gray-400">
                Send browser push notifications
              </p>
            </div>
            <SwitchToggle
              v-model="notificationSettings.pushEnabled"
              @update:modelValue="updateNotificationSettings"
            />
          </div>

          <div class="flex items-center justify-between">
            <div>
              <h4 class="text-sm font-medium text-gray-900 dark:text-white">
                SMS Notifications
              </h4>
              <p class="text-sm text-gray-600 dark:text-gray-400">
                Send notifications via SMS
              </p>
            </div>
            <SwitchToggle
              v-model="notificationSettings.smsEnabled"
              @update:modelValue="updateNotificationSettings"
            />
          </div>

          <FormGroup label="Notification Frequency">
            <Select
              v-model="notificationSettings.frequency"
              :options="frequencyOptions"
              placeholder="Select notification frequency"
            />
          </FormGroup>
        </div>
      </AppCard>
    </div>
  </div>
</template>

<script>
import { ref, reactive, onMounted } from 'vue'
import { settingsService } from '@/services/api'
import AppCard from '@/components/card/AppCard.vue'
import AppButton from '@/components/button/AppButton.vue'
import FormGroup from '@/components/form/FormGroup.vue'
import InputText from '@/components/form/InputText.vue'
import NumberInput from '@/components/form/NumberInput.vue'
import Select from '@/components/form/Select.vue'
import SwitchToggle from '@/components/form/SwitchToggle.vue'
import Icon from '@/components/visual/Icon.vue'

export default {
  name: 'Settings',
  components: {
    AppCard,
    AppButton,
    FormGroup,
    InputText,
    NumberInput,
    Select,
    SwitchToggle,
    Icon
  },
  setup() {
    const loading = ref(false)
    const errors = reactive({})

    const generalSettings = reactive({
      appName: '',
      appUrl: '',
      defaultLanguage: 'en',
      defaultTimezone: 'UTC',
      dateFormat: 'Y-m-d',
      timeFormat: 'H:i'
    })

    const securitySettings = reactive({
      requireTwoFactor: false,
      sessionTimeout: 30,
      enforcePasswordPolicy: true,
      minPasswordLength: 8,
      enableAccountLockout: true,
      maxFailedAttempts: 5
    })

    const emailSettings = reactive({
      smtpHost: '',
      smtpPort: 587,
      smtpUsername: '',
      smtpPassword: '',
      encryption: 'tls',
      fromEmail: '',
      fromName: ''
    })

    const notificationSettings = reactive({
      emailEnabled: true,
      pushEnabled: false,
      smsEnabled: false,
      frequency: 'immediate'
    })

    // Options for selects
    const languageOptions = [
      { value: 'en', label: 'English' },
      { value: 'tr', label: 'Turkish' },
      { value: 'es', label: 'Spanish' },
      { value: 'fr', label: 'French' },
      { value: 'de', label: 'German' }
    ]

    const timezoneOptions = [
      { value: 'UTC', label: 'UTC' },
      { value: 'America/New_York', label: 'Eastern Time' },
      { value: 'America/Chicago', label: 'Central Time' },
      { value: 'America/Denver', label: 'Mountain Time' },
      { value: 'America/Los_Angeles', label: 'Pacific Time' },
      { value: 'Europe/London', label: 'London' },
      { value: 'Europe/Paris', label: 'Paris' },
      { value: 'Asia/Tokyo', label: 'Tokyo' }
    ]

    const dateFormatOptions = [
      { value: 'Y-m-d', label: 'YYYY-MM-DD' },
      { value: 'm/d/Y', label: 'MM/DD/YYYY' },
      { value: 'd/m/Y', label: 'DD/MM/YYYY' },
      { value: 'M d, Y', label: 'Jan 01, 2024' }
    ]

    const timeFormatOptions = [
      { value: 'H:i', label: '24-hour (13:30)' },
      { value: 'h:i A', label: '12-hour (1:30 PM)' }
    ]

    const sessionTimeoutOptions = [
      { value: 15, label: '15 minutes' },
      { value: 30, label: '30 minutes' },
      { value: 60, label: '1 hour' },
      { value: 120, label: '2 hours' },
      { value: 240, label: '4 hours' }
    ]

    const encryptionOptions = [
      { value: 'tls', label: 'TLS' },
      { value: 'ssl', label: 'SSL' },
      { value: 'none', label: 'None' }
    ]

    const frequencyOptions = [
      { value: 'immediate', label: 'Immediate' },
      { value: 'hourly', label: 'Hourly' },
      { value: 'daily', label: 'Daily' },
      { value: 'weekly', label: 'Weekly' }
    ]

    const loadSettings = async () => {
      try {
        const [general, security, email, notifications] = await Promise.all([
          settingsService.getGeneralSettings(),
          settingsService.getSecuritySettings(),
          settingsService.getNotificationSettings()
        ])

        Object.assign(generalSettings, general.data)
        Object.assign(securitySettings, security.data)
        Object.assign(emailSettings, email.data)
        Object.assign(notificationSettings, notifications.data)
      } catch (error) {
        console.error('Failed to load settings:', error)
      }
    }

    const saveSettings = async () => {
      loading.value = true
      try {
        await Promise.all([
          settingsService.updateGeneralSettings(generalSettings),
          settingsService.updateSecuritySettings(securitySettings),
          settingsService.updateNotificationSettings(notificationSettings)
        ])

        // Show success message
        console.log('Settings saved successfully')
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

    const updateSecuritySettings = async () => {
      try {
        await settingsService.updateSecuritySettings(securitySettings)
      } catch (error) {
        console.error('Failed to update security settings:', error)
      }
    }

    const updateNotificationSettings = async () => {
      try {
        await settingsService.updateNotificationSettings(notificationSettings)
      } catch (error) {
        console.error('Failed to update notification settings:', error)
      }
    }

    onMounted(() => {
      loadSettings()
    })

    return {
      generalSettings,
      securitySettings,
      emailSettings,
      notificationSettings,
      errors,
      loading,
      languageOptions,
      timezoneOptions,
      dateFormatOptions,
      timeFormatOptions,
      sessionTimeoutOptions,
      encryptionOptions,
      frequencyOptions,
      saveSettings,
      updateSecuritySettings,
      updateNotificationSettings
    }
  }
}
</script> 