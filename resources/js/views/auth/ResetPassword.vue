<template>
  <div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-blue-50 to-indigo-100 dark:from-gray-900 dark:to-gray-800 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
      <!-- Header -->
      <div class="text-center">
        <div class="mx-auto h-12 w-12 bg-green-600 rounded-lg flex items-center justify-center mb-4">
          <Icon name="lock-closed" class="w-6 h-6 text-white" />
        </div>
        <h2 class="text-3xl font-bold text-gray-900 dark:text-white">
          Reset Password
        </h2>
        <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
          Enter your new password below.
        </p>
      </div>

      <!-- Reset Password Form -->
      <AppCard class="p-6">
        <form @submit.prevent="handleResetPassword" class="space-y-6">
          <FormGroup label="New Password" required>
            <InputText
              v-model="form.password"
              type="password"
              placeholder="Enter new password"
              :error="errors.password"
              required
            />
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
              Password must be at least 8 characters long
            </p>
          </FormGroup>

          <FormGroup label="Confirm New Password" required>
            <InputText
              v-model="form.passwordConfirmation"
              type="password"
              placeholder="Confirm new password"
              :error="errors.passwordConfirmation"
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
            <Icon name="check" class="w-5 h-5 mr-2" />
            Reset Password
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
import { ref, reactive, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import AppCard from '@/components/card/AppCard.vue'
import AppButton from '@/components/button/AppButton.vue'
import FormGroup from '@/components/form/FormGroup.vue'
import InputText from '@/components/form/InputText.vue'
import Icon from '@/components/visual/Icon.vue'

export default {
  name: 'ResetPassword',
  components: {
    AppCard,
    AppButton,
    FormGroup,
    InputText,
    Icon
  },
  setup() {
    const route = useRoute()
    const router = useRouter()
    const authStore = useAuthStore()
    const loading = ref(false)
    const errors = reactive({})

    const form = reactive({
      token: '',
      email: '',
      password: '',
      passwordConfirmation: ''
    })

    const validateForm = () => {
      errors.value = {}

      if (!form.password) {
        errors.password = 'Password is required'
      } else if (form.password.length < 8) {
        errors.password = 'Password must be at least 8 characters'
      }

      if (!form.passwordConfirmation) {
        errors.passwordConfirmation = 'Please confirm your password'
      } else if (form.password !== form.passwordConfirmation) {
        errors.passwordConfirmation = 'Passwords do not match'
      }

      return Object.keys(errors.value).length === 0
    }

    const handleResetPassword = async () => {
      if (!validateForm()) return

      loading.value = true
      try {
        await authStore.resetPassword({
          token: form.token,
          email: form.email,
          password: form.password,
          password_confirmation: form.passwordConfirmation
        })
        
        // Show success message
        authStore.showToast({
          type: 'success',
          title: 'Password Reset',
          message: 'Your password has been reset successfully. You can now log in with your new password.'
        })
        
        // Redirect to login
        router.push('/login')
      } catch (error) {
        if (error.response?.data?.errors) {
          Object.keys(error.response.data.errors).forEach(key => {
            errors[key] = error.response.data.errors[key][0]
          })
        } else {
          authStore.showToast({
            type: 'error',
            title: 'Password Reset Failed',
            message: error.response?.data?.message || 'An error occurred while resetting your password.'
          })
        }
      } finally {
        loading.value = false
      }
    }

    onMounted(() => {
      // Get token and email from URL parameters
      form.token = route.query.token || ''
      form.email = route.query.email || ''
      
      if (!form.token || !form.email) {
        authStore.showToast({
          type: 'error',
          title: 'Invalid Reset Link',
          message: 'The password reset link is invalid or has expired.'
        })
        router.push('/forgot-password')
      }
    })

    return {
      form,
      errors,
      loading,
      handleResetPassword
    }
  }
}
</script> 