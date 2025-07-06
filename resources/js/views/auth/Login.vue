<!--
  DocraTech Medical Platform - Login Page
  Features: Modern design, accessibility, animations, responsive layout
  Brand Colors: DocraTech Light Mode Only
-->

<template>
  <div class="login-page">
    <!-- Background with Medical Pattern -->
    <div class="login-background">
      <div class="bg-pattern-grid opacity-5"></div>
      <div class="floating-elements">
        <div class="floating-element floating-element--1"></div>
        <div class="floating-element floating-element--2"></div>
        <div class="floating-element floating-element--3"></div>
      </div>
    </div>

    <!-- Main Content -->
    <div class="login-container">
      <!-- Left Section - Branding -->
      <div class="login-branding">
        <div class="brand-content">
          <!-- Logo -->
          <div class="brand-logo">
            <div class="logo-icon">
              <!-- Medical Cross Icon -->
              <svg class="w-12 h-12 text-primary" fill="currentColor" viewBox="0 0 24 24">
                <path d="M19 8h-2V6a2 2 0 0 0-2-2H9a2 2 0 0 0-2 2v2H5a2 2 0 0 0-2 2v4a2 2 0 0 0 2 2h2v2a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2v-2h2a2 2 0 0 0 2-2v-4a2 2 0 0 0-2-2zM9 6h6v10H9V6zm8 10v-2h2v2h-2z"/>
              </svg>
            </div>
            <h1 class="brand-title text-gradient-primary">DocraTech</h1>
          </div>

          <!-- Brand Description -->
          <div class="brand-description">
            <h2 class="description-title">Medical Website Platform</h2>
            <p class="description-text">
              Empowering healthcare professionals with modern, secure, and user-friendly 
              website management tools designed specifically for the medical industry.
            </p>
          </div>

          <!-- Features List -->
          <div class="features-list">
            <div class="feature-item">
              <div class="feature-icon">
                <svg class="w-5 h-5 text-docratech-success" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                </svg>
              </div>
              <span>HIPAA Compliant Security</span>
            </div>
            <div class="feature-item">
              <div class="feature-icon">
                <svg class="w-5 h-5 text-docratech-success" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                </svg>
              </div>
              <span>Patient Management Tools</span>
            </div>
            <div class="feature-item">
              <div class="feature-icon">
                <svg class="w-5 h-5 text-docratech-success" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                </svg>
              </div>
              <span>Modern Website Builder</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Right Section - Login Form -->
      <div class="login-form-section">
        <div class="form-container">
          <!-- Form Header -->
          <div class="form-header">
            <h2 class="form-title">Welcome Back</h2>
            <p class="form-subtitle">
              Sign in to your DocraTech account to continue managing your medical practice.
            </p>
          </div>

          <!-- Login Form Card -->
          <AppCard 
            variant="elevated" 
            class="login-card"
            :class="{ 'shake': formError }"
          >
            <form @submit.prevent="handleLogin" class="login-form" novalidate>
              <!-- Email Field -->
              <div class="form-group">
                <InputText
                  v-model="form.email"
                  type="email"
                  label="Email Address"
                  placeholder="Enter your email address"
                  :error="errors.email"
                  :disabled="loading"
                  required
                  autocomplete="email"
                  @focus="clearError('email')"
                  @input="validateField('email')"
                  prefixIcon="mail"
                />
              </div>

              <!-- Password Field -->
              <div class="form-group">
                <InputText
                  v-model="form.password"
                  type="password"
                  label="Password"
                  placeholder="Enter your password"
                  :error="errors.password"
                  :disabled="loading"
                  required
                  autocomplete="current-password"
                  @focus="clearError('password')"
                  @input="validateField('password')"
                />
              </div>

              <!-- Form Options -->
              <div class="form-options">
                <label class="remember-checkbox">
                  <input
                    v-model="form.remember"
                    type="checkbox"
                    class="checkbox"
                    :disabled="loading"
                  >
                  <span class="checkmark"></span>
                  <span class="checkbox-label">Remember me for 30 days</span>
                </label>

                <router-link 
                  to="/forgot-password" 
                  class="forgot-link"
                  :class="{ 'pointer-events-none opacity-50': loading }"
                >
                  Forgot password?
                </router-link>
              </div>

              <!-- Submit Button -->
              <AppButton
                type="submit"
                variant="primary"
                size="lg"
                :loading="loading"
                :disabled="!isFormValid || loading"
                fullWidth
                class="submit-button"
                loadingText="Signing you in..."
              >
                Sign In to DocraTech
              </AppButton>

              <!-- Error Message -->
              <Transition name="error-slide">
                <div v-if="formError" class="form-error" role="alert">
                  <svg class="w-5 h-5 text-docratech-error" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                  </svg>
                  {{ formError }}
                </div>
              </Transition>
            </form>
          </AppCard>

          <!-- Additional Options -->
          <div class="additional-options">
            <div class="divider">
              <span class="divider-text">Need an account?</span>
            </div>
            
            <router-link 
              to="/register" 
              class="register-link"
              :class="{ 'pointer-events-none opacity-50': loading }"
            >
              <AppButton 
                variant="outline" 
                size="lg" 
                fullWidth
                :disabled="loading"
              >
                Create DocraTech Account
              </AppButton>
            </router-link>
          </div>

          <!-- Footer -->
          <div class="form-footer">
            <p class="footer-text">
              By signing in, you agree to our 
              <a href="#" class="footer-link">Terms of Service</a> and 
              <a href="#" class="footer-link">Privacy Policy</a>.
            </p>
          </div>
        </div>
      </div>
    </div>

    <!-- Loading Overlay -->
    <Transition name="fade">
      <div 
        v-if="loading" 
        class="loading-overlay"
        aria-hidden="true"
      >
        <div class="loading-content">
          <div class="loading-spinner w-8 h-8 text-primary"></div>
          <p class="loading-text">Signing you in...</p>
        </div>
      </div>
    </Transition>
  </div>
</template>

<script setup>
import { ref, computed, nextTick, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import AppCard from '@/components/card/AppCard.vue'
import AppButton from '@/components/button/AppButton.vue'
import InputText from '@/components/form/InputText.vue'

/**
 * DocraTech Medical Platform - Login Page
 * Features: Modern UI, form validation, accessibility, responsive design
 */

// Composables
const router = useRouter()
const authStore = useAuthStore()

// Reactive state
const loading = ref(false)
const formError = ref('')

const form = ref({
  email: '',
  password: '',
  remember: false
})

const errors = ref({})

// Computed properties
const isFormValid = computed(() => {
  return form.value.email && 
         form.value.password && 
         !errors.value.email && 
         !errors.value.password
})

// Validation functions
const validateEmail = (email) => {
  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/
  if (!email) {
    return 'Email address is required'
  }
  if (!emailRegex.test(email)) {
    return 'Please enter a valid email address'
  }
  return null
}

const validatePassword = (password) => {
  if (!password) {
    return 'Password is required'
  }
  if (password.length < 6) {
    return 'Password must be at least 6 characters long'
  }
  return null
}

const validateField = (field) => {
  const value = form.value[field]
  let error = null

  switch (field) {
    case 'email':
      error = validateEmail(value)
      break
    case 'password':
      error = validatePassword(value)
      break
  }

  if (error) {
    errors.value[field] = error
  } else {
    delete errors.value[field]
  }

  return !error
}

const validateForm = () => {
  const emailValid = validateField('email')
  const passwordValid = validateField('password')
  return emailValid && passwordValid
}

const clearError = (field) => {
  if (errors.value[field]) {
    delete errors.value[field]
  }
  if (formError.value) {
    formError.value = ''
  }
}

// Main login handler
const handleLogin = async () => {
  // Clear previous errors
  formError.value = ''
  
  // Validate form
  if (!validateForm()) {
    formError.value = 'Please correct the errors above'
    return
  }

  loading.value = true

  try {
    // Call the auth store login method
    const response = await authStore.login({
      email: form.value.email,
      password: form.value.password,
      remember: form.value.remember
    })
    // Suspended kontrolü
    if (response && response.status === 'suspended') {
      router.replace({ name: 'suspended' })
      return
    }
    // Redirect to dashboard or intended page
    const redirectPath = router.currentRoute.value.query.redirect || '/dashboard'
    router.push(redirectPath)
  } catch (error) {
    console.error('Login error:', error)
    
    // Handle different types of errors
    let errorMessage = 'An unexpected error occurred. Please try again.'
    
    if (error.response?.status === 422) {
      // Validation errors
      if (error.response.data.errors?.email) {
        errorMessage = error.response.data.errors.email[0]
      } else if (error.response.data.message) {
        errorMessage = error.response.data.message
      }
    } else if (error.response?.status === 401) {
      errorMessage = 'Invalid email or password. Please check your credentials and try again.'
    } else if (error.response?.status >= 500) {
      errorMessage = 'Server error. Please try again later.'
    } else if (error.message) {
      errorMessage = error.message
    }
    
    formError.value = errorMessage
    
    // Auto-clear error after 5 seconds
    setTimeout(() => {
      formError.value = ''
    }, 5000)
    
  } finally {
    loading.value = false
  }
}

// Lifecycle hooks
onMounted(() => {
  console.log('🏥 DocraTech Login Page Mounted')
  
  // Focus first input
  nextTick(() => {
    const emailInput = document.querySelector('input[type="email"]')
    if (emailInput) {
      emailInput.focus()
    }
  })
})

// Auto-clear form error after some time
const clearFormError = () => {
  if (formError.value) {
    setTimeout(() => {
      formError.value = ''
    }, 5000)
  }
}
</script>

<style scoped>
/* Page Layout */
.login-page {
  @apply min-h-screen relative overflow-hidden bg-docratech-background;
}

.login-background {
  @apply absolute inset-0 z-0;
}

.floating-elements {
  @apply absolute inset-0;
}

.floating-element {
  @apply absolute rounded-full bg-gradient-to-br from-primary to-primaryAccent opacity-10;
  animation: float 6s ease-in-out infinite;
}

.floating-element--1 {
  @apply w-64 h-64 -top-32 -left-32;
  animation-delay: 0s;
}

.floating-element--2 {
  @apply w-48 h-48 top-1/4 -right-24;
  animation-delay: 2s;
}

.floating-element--3 {
  @apply w-80 h-80 -bottom-40 left-1/4;
  animation-delay: 4s;
}

@keyframes float {
  0%, 100% { transform: translateY(0px) rotate(0deg); }
  50% { transform: translateY(-20px) rotate(180deg); }
}

/* Main Container */
.login-container {
  @apply relative z-10 min-h-screen flex items-center;
}

/* Branding Section */
.login-branding {
  @apply hidden lg:flex lg:w-1/2 xl:w-2/5 p-12 items-center justify-center;
}

.brand-content {
  @apply max-w-lg space-y-8;
}

.brand-logo {
  @apply flex items-center space-x-4;
}

.logo-icon {
  @apply p-3 bg-gradient-to-br from-primary to-primaryAccent rounded-2xl shadow-glow;
}

.brand-title {
  @apply text-4xl font-bold;
}

.brand-description {
  @apply space-y-4;
}

.description-title {
  @apply text-2xl font-semibold text-docratech-text;
}

.description-text {
  @apply text-lg text-docratech-textSecondary leading-relaxed;
}

.features-list {
  @apply space-y-3;
}

.feature-item {
  @apply flex items-center space-x-3;
}

.feature-icon {
  @apply flex-shrink-0 w-6 h-6 flex items-center justify-center;
}

/* Form Section */
.login-form-section {
  @apply w-full lg:w-1/2 xl:w-3/5 flex items-center justify-center p-6 lg:p-12;
}

.form-container {
  @apply w-full max-w-md space-y-6;
}

.form-header {
  @apply text-center space-y-2;
}

.form-title {
  @apply text-3xl font-bold text-docratech-text;
}

.form-subtitle {
  @apply text-docratech-textSecondary leading-relaxed;
}

/* Login Card */
.login-card {
  @apply transform transition-all duration-300;
}

.login-card.shake {
  animation: shake 0.5s ease-in-out;
}

.login-form {
  @apply space-y-6;
}

.form-group {
  @apply space-y-2;
}

/* Form Options */
.form-options {
  @apply flex items-center justify-between;
}

.remember-checkbox {
  @apply flex items-center space-x-3 cursor-pointer select-none;
}

.checkbox {
  @apply sr-only;
}

.checkmark {
  @apply w-5 h-5 border-2 border-docratech-border rounded bg-white transition-all duration-200;
  @apply flex items-center justify-center;
}

.checkbox:checked + .checkmark {
  @apply bg-primary border-primary;
}

.checkbox:checked + .checkmark::after {
  content: '';
  @apply w-2 h-2 bg-white rounded-sm;
}

.checkbox-label {
  @apply text-sm text-docratech-textSecondary;
}

.forgot-link {
  @apply text-sm text-primary hover:text-primaryAccent transition-colors;
  @apply focus:outline-none focus:ring-2 focus:ring-primary focus:ring-opacity-50 rounded;
}

/* Submit Button */
.submit-button {
  @apply mt-8;
}

/* Form Error */
.form-error {
  @apply flex items-center space-x-2 p-4 bg-red-50 border border-red-200 rounded-xl text-sm;
}

/* Additional Options */
.additional-options {
  @apply space-y-4;
}

.divider {
  @apply relative text-center;
}

.divider::before {
  content: '';
  @apply absolute top-1/2 left-0 right-0 h-px bg-docratech-border;
}

.divider-text {
  @apply inline-block px-4 bg-docratech-background text-sm text-docratech-textMuted;
}

.register-link {
  @apply block;
}

/* Footer */
.form-footer {
  @apply text-center;
}

.footer-text {
  @apply text-xs text-docratech-textMuted leading-relaxed;
}

.footer-link {
  @apply text-primary hover:text-primaryAccent transition-colors;
}

/* Loading Overlay */
.loading-overlay {
  @apply fixed inset-0 bg-white bg-opacity-90 backdrop-blur-sm z-50;
  @apply flex items-center justify-center;
}

.loading-content {
  @apply text-center space-y-4;
}

.loading-text {
  @apply text-docratech-textSecondary font-medium;
}

/* Error Animation */
.error-slide-enter-active {
  @apply transition-all duration-300 ease-out;
}

.error-slide-leave-active {
  @apply transition-all duration-200 ease-in;
}

.error-slide-enter-from {
  @apply opacity-0 transform -translate-y-2;
}

.error-slide-leave-to {
  @apply opacity-0 transform -translate-y-1;
}

/* Responsive Design */
@media (max-width: 1024px) {
  .login-branding {
    @apply hidden;
  }
  
  .login-form-section {
    @apply w-full;
  }
}

@media (max-width: 640px) {
  .form-container {
    @apply max-w-none;
  }
  
  .form-header {
    @apply space-y-1;
  }
  
  .form-title {
    @apply text-2xl;
  }
  
  .form-subtitle {
    @apply text-sm;
  }
  
  .form-options {
    @apply flex-col items-start space-y-3;
  }
}

/* Accessibility */
@media (prefers-reduced-motion: reduce) {
  .floating-element,
  .login-card,
  .shake {
    animation: none !important;
    transition: none !important;
  }
}

/* Print Styles */
@media print {
  .login-page {
    @apply bg-white;
  }
  
  .login-background,
  .floating-elements,
  .loading-overlay {
    @apply hidden;
  }
}
</style> 