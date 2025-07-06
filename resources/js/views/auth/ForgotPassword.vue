<template>
  <div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-blue-50 to-indigo-100 dark:from-gray-900 dark:to-gray-800 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
      <!-- Header -->
      <div class="text-center">
        <div class="mx-auto h-12 w-12 bg-blue-600 rounded-lg flex items-center justify-center mb-4">
          <Icon name="key" class="w-6 h-6 text-white" />
        </div>
        <h2 class="text-3xl font-bold text-gray-900 dark:text-white">
          Forgot Password
        </h2>
        <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
          Enter your email address and we'll send you a link to reset your password.
        </p>
      </div>

      <!-- Forgot Password Form -->
      <AppCard class="p-6">
        <form @submit.prevent="handleForgotPassword" class="space-y-6">
          <FormGroup label="Email Address" required>
            <InputText
              v-model="form.email"
              type="email"
              placeholder="Enter email address"
              :error="errors.email"
              required
            />
          </FormGroup>

          <!-- Submit Button -->
          <AppButton
            type="submit"
            variant="primary"
            size="lg"
            class="w-full"
            :loading="loading"
          >
            <Icon name="paper-airplane" class="w-5 h-5 mr-2" />
            Send Reset Link
          </AppButton>
        </form>

        <!-- Divider -->
        <div class="mt-6">
          <div class="relative">
            <div class="absolute inset-0 flex items-center">
              <div class="w-full border-t border-gray-300 dark:border-gray-600"></div>
            </div>
            <div class="relative flex justify-center text-sm">
              <span class="px-2 bg-white dark:bg-gray-800 text-gray-500">
                Or
              </span>
            </div>
          </div>
        </div>

        <!-- Back to Login -->
        <div class="mt-6 text-center">
          <p class="text-sm text-gray-600 dark:text-gray-400">
            Remember your password?
            <router-link
              to="/login"
              class="font-medium text-blue-600 hover:text-blue-500 dark:text-blue-400"
            >
              Sign in here
            </router-link>
          </p>
        </div>
      </AppCard>
    </div>
  </div>
</template>

<script>
import { ref, reactive } from 'vue'
import { useAuthStore } from '@/stores/auth'
import AppCard from '@/components/card/AppCard.vue'
import AppButton from '@/components/button/AppButton.vue'
import FormGroup from '@/components/form/FormGroup.vue'
import InputText from '@/components/form/InputText.vue'
import Icon from '@/components/visual/Icon.vue'

export default {
  name: 'ForgotPassword',
  components: {
    AppCard,
    AppButton,
    FormGroup,
    InputText,
    Icon
  },
  setup() {
    const authStore = useAuthStore()
    const loading = ref(false)
    const errors = reactive({})

    const form = reactive({
      email: ''
    })

    const validateForm = () => {
      errors.value = {}

      if (!form.email) {
        errors.email = 'Email is required'
      } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(form.email)) {
        errors.email = 'Please enter a valid email address'
      }

      return Object.keys(errors.value).length === 0
    }

    const handleForgotPassword = async () => {
      if (!validateForm()) return

      loading.value = true
      try {
        await authStore.forgotPassword(form.email)
        
        // Show success message
        authStore.showToast({
          type: 'success',
          title: 'Reset Link Sent',
          message: 'Check your email for password reset instructions.'
        })
      } catch (error) {
        if (error.response?.data?.errors) {
          Object.keys(error.response.data.errors).forEach(key => {
            errors[key] = error.response.data.errors[key][0]
          })
        } else {
          authStore.showToast({
            type: 'error',
            title: 'Failed to Send Reset Link',
            message: error.response?.data?.message || 'An error occurred while sending the reset link.'
          })
        }
      } finally {
        loading.value = false
      }
    }

    return {
      form,
      errors,
      loading,
      handleForgotPassword
    }
  }
}
</script> 