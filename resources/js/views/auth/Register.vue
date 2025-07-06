<template>
  <div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-blue-50 to-indigo-100 dark:from-gray-900 dark:to-gray-800 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
      <!-- Header -->
      <div class="text-center">
        <div class="mx-auto h-12 w-12 bg-blue-600 rounded-lg flex items-center justify-center mb-4">
          <Icon name="user-plus" class="w-6 h-6 text-white" />
        </div>
        <h2 class="text-3xl font-bold text-gray-900 dark:text-white">
          Create Account
        </h2>
        <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
          Join DocRatech and start managing your medical platform
        </p>
      </div>

      <!-- Register Form -->
      <AppCard class="p-6">
        <form @submit.prevent="handleRegister" class="space-y-6">
          <!-- Name Fields -->
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
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

          <!-- Email -->
          <FormGroup label="Email Address" required>
            <InputText
              v-model="form.email"
              type="email"
              placeholder="Enter email address"
              :error="errors.email"
              required
            />
          </FormGroup>

          <!-- Password Fields -->
          <FormGroup label="Password" required>
            <InputText
              v-model="form.password"
              type="password"
              placeholder="Enter password"
              :error="errors.password"
              required
            />
          </FormGroup>

          <FormGroup label="Confirm Password" required>
            <InputText
              v-model="form.passwordConfirmation"
              type="password"
              placeholder="Confirm password"
              :error="errors.passwordConfirmation"
              required
            />
          </FormGroup>

          <!-- Terms -->
          <div class="flex items-start">
            <Checkbox
              v-model="form.agreeToTerms"
              :error="errors.agreeToTerms"
              required
            />
            <div class="ml-3 text-sm">
              <label class="text-gray-700 dark:text-gray-300">
                I agree to the
                <a href="#" class="text-blue-600 hover:text-blue-500 dark:text-blue-400">
                  Terms of Service
                </a>
                and
                <a href="#" class="text-blue-600 hover:text-blue-500 dark:text-blue-400">
                  Privacy Policy
                </a>
              </label>
            </div>
          </div>

          <!-- Submit Button -->
          <AppButton
            type="submit"
            variant="primary"
            size="lg"
            class="w-full"
            :loading="loading"
          >
            <Icon name="user-plus" class="w-5 h-5 mr-2" />
            Create Account
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
                Or continue with
              </span>
            </div>
          </div>
        </div>

        <!-- Social Login -->
        <div class="mt-6 grid grid-cols-2 gap-3">
          <AppButton variant="outline" size="lg" class="w-full">
            <Icon name="google" class="w-5 h-5 mr-2" />
            Google
          </AppButton>
          
          <AppButton variant="outline" size="lg" class="w-full">
            <Icon name="microsoft" class="w-5 h-5 mr-2" />
            Microsoft
          </AppButton>
        </div>
      </AppCard>

      <!-- Login Link -->
      <div class="text-center">
        <p class="text-sm text-gray-600 dark:text-gray-400">
          Already have an account?
          <router-link
            to="/login"
            class="font-medium text-blue-600 hover:text-blue-500 dark:text-blue-400"
          >
            Sign in here
          </router-link>
        </p>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, reactive } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import AppCard from '@/components/card/AppCard.vue'
import AppButton from '@/components/button/AppButton.vue'
import FormGroup from '@/components/form/FormGroup.vue'
import InputText from '@/components/form/InputText.vue'
import Checkbox from '@/components/form/Checkbox.vue'
import Icon from '@/components/visual/Icon.vue'

export default {
  name: 'Register',
  components: {
    AppCard,
    AppButton,
    FormGroup,
    InputText,
    Checkbox,
    Icon
  },
  setup() {
    const router = useRouter()
    const route = useRoute()
    const authStore = useAuthStore()

    const loading = ref(false)
    const errors = reactive({})

    const form = reactive({
      firstName: '',
      lastName: '',
      email: '',
      password: '',
      passwordConfirmation: '',
      agreeToTerms: false
    })

    const validateForm = () => {
      errors.value = {}

      if (!form.firstName) {
        errors.firstName = 'First name is required'
      }

      if (!form.lastName) {
        errors.lastName = 'Last name is required'
      }

      if (!form.email) {
        errors.email = 'Email is required'
      } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(form.email)) {
        errors.email = 'Please enter a valid email address'
      }

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

      if (!form.agreeToTerms) {
        errors.agreeToTerms = 'You must agree to the terms and conditions'
      }

      return Object.keys(errors.value).length === 0
    }

    const handleRegister = async () => {
      if (!validateForm()) return

      loading.value = true
      try {
        await authStore.register({
          first_name: form.firstName,
          last_name: form.lastName,
          email: form.email,
          password: form.password,
          password_confirmation: form.passwordConfirmation
        })

        // Redirect to intended page or dashboard
        const redirectTo = route.query.redirect || '/dashboard'
        router.push(redirectTo)
      } catch (error) {
        if (error.response?.data?.errors) {
          Object.keys(error.response.data.errors).forEach(key => {
            errors[key] = error.response.data.errors[key][0]
          })
        } else {
          authStore.showToast({
            type: 'error',
            title: 'Registration Failed',
            message: error.response?.data?.message || 'An error occurred during registration'
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
      handleRegister
    }
  }
}
</script> 