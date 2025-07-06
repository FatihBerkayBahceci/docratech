<!--
  Project: Enterprise Health Login Form
  Developer: DocraTech Team - Fatih Berkay Bahceci
-->

<template>
  <section
    class="login-form max-w-md w-full bg-white dark:bg-brand-950 rounded-2xl shadow-lg border border-brand-100 dark:border-brand-900 overflow-hidden animate-form-in"
    role="form"
    aria-labelledby="login-title"
  >
    <!-- Header -->
    <header
      class="login-form-header px-8 pt-10 pb-6 text-center bg-gradient-to-tr from-[#5A1188] to-[#9D38CF] text-white select-none"
      aria-hidden="true"
    >
      <h2 id="login-title" class="login-form-title text-3xl font-semibold mb-2 animate-title-in">
        Giriş Yap
      </h2>
      <p class="login-form-subtitle text-sm opacity-90 animate-subtitle-in">
        Hesabınıza erişmek için giriş yapın
      </p>
    </header>

    <!-- Form -->
    <form
      @submit.prevent="handleSubmit"
      class="login-form-content px-8 py-8 space-y-6"
      novalidate
      autocomplete="off"
      aria-describedby="form-error"
    >
      <FormGroup label="E-posta" :error="errors.email" for="email-input">
        <InputText
          id="email-input"
          v-model="form.email"
          type="email"
          placeholder="ornek@email.com"
          icon="bi-envelope"
          :error="!!errors.email"
          :disabled="loading"
          required
          autocomplete="email"
          aria-invalid="errors.email ? 'true' : 'false'"
          aria-describedby="email-error"
        />
      </FormGroup>

      <FormGroup label="Şifre" :error="errors.password" for="password-input">
        <InputText
          id="password-input"
          v-model="form.password"
          type="password"
          placeholder="Şifrenizi girin"
          icon="bi-lock"
          :error="!!errors.password"
          :disabled="loading"
          required
          autocomplete="current-password"
          aria-invalid="errors.password ? 'true' : 'false'"
          aria-describedby="password-error"
        />
      </FormGroup>

      <div class="login-form-options flex justify-between items-center text-sm text-brand-500 dark:text-brand-400">
        <Checkbox
          v-model="form.rememberMe"
          label="Beni hatırla"
          :disabled="loading"
          id="remember-me-checkbox"
        />
        <button
          type="button"
          @click.prevent="forgotPassword"
          class="login-form-forgot hover:text-primary transition-colors focus:outline-none focus:ring-2 focus:ring-primary rounded"
          aria-label="Şifremi unuttum"
        >
          Şifremi unuttum
        </button>
      </div>

      <transition name="fade-scale">
        <div
          v-if="error"
          id="form-error"
          class="login-form-error flex items-center gap-2 p-3 rounded-md bg-red-50 border border-red-300 text-red-600 text-sm font-semibold select-none"
          role="alert"
          tabindex="0"
        >
          <i class="bi bi-exclamation-triangle text-lg flex-shrink-0" aria-hidden="true"></i>
          <span>{{ error }}</span>
        </div>
      </transition>

      <AppButton
        type="submit"
        variant="primary"
        size="large"
        :loading="loading"
        :disabled="loading"
        class="login-form-submit w-full flex justify-center items-center gap-2"
        aria-live="polite"
        aria-busy="loading"
      >
        <i class="bi bi-box-arrow-in-right" aria-hidden="true"></i>
        Giriş Yap
      </AppButton>
    </form>

    <!-- Footer -->
    <footer
      class="login-form-footer px-8 py-6 text-center border-t border-brand-100 dark:border-brand-800 bg-brand-50 dark:bg-brand-900 text-sm text-brand-600 dark:text-brand-400 select-none"
    >
      <p>
        Hesabınız yok mu?
        <button
          type="button"
          class="login-form-footer-link text-primary font-semibold hover:underline focus:outline-none focus:ring-2 focus:ring-primary rounded"
          @click.prevent="goToRegister"
          aria-label="Kayıt ol"
        >
          Kayıt ol
        </button>
      </p>
    </footer>

    <!-- Social login slot -->
    <slot name="social-login" />
  </section>
</template>

<script setup>
// Project: Enterprise Health Login Form
// Developer: DocraTech Team - Fatih Berkay Bahceci

import { reactive, watch } from 'vue'
import FormGroup from '../form/FormGroup.vue'
import InputText from '../form/InputText.vue'
import Checkbox from '../form/Checkbox.vue'
import AppButton from '../button/AppButton.vue'

const props = defineProps({
  loading: Boolean,
  error: String,
  rememberMe: {
    type: Boolean,
    default: false
  }
})

const emit = defineEmits(['submit', 'forgot-password', 'go-to-register'])

const form = reactive({
  email: '',
  password: '',
  rememberMe: props.rememberMe
})

const errors = reactive({})

function validateForm() {
  errors.email = ''
  errors.password = ''

  if (!form.email) {
    errors.email = 'E-posta adresi gereklidir'
  } else if (!isValidEmail(form.email)) {
    errors.email = 'Geçerli bir e-posta adresi girin'
  }

  if (!form.password) {
    errors.password = 'Şifre gereklidir'
  } else if (form.password.length < 6) {
    errors.password = 'Şifre en az 6 karakter olmalıdır'
  }

  return !errors.email && !errors.password
}

function isValidEmail(email) {
  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/
  return emailRegex.test(email)
}

function handleSubmit() {
  if (validateForm()) {
    emit('submit', { ...form })
  }
}

function forgotPassword() {
  emit('forgot-password')
}

function goToRegister() {
  emit('go-to-register')
}

watch(() => props.error, (val) => {
  if (val) {
    errors.email = ''
    errors.password = ''
  }
})
</script>

<style scoped>
.login-form {
  max-width: 400px;
  width: 100%;
  border-radius: 1.5rem;
  box-shadow: 0 12px 24px rgb(90 17 136 / 0.15);
  border: 1px solid #6d488d;
  overflow: hidden;
  animation: form-in 0.5s cubic-bezier(0.22, 1, 0.36, 1);
  background-color: var(--bg, #fff);
}
@keyframes form-in {
  0% {
    opacity: 0;
    transform: translateY(20px) scale(0.95);
  }
  100% {
    opacity: 1;
    transform: translateY(0) scale(1);
  }
}

.login-form-header {
  padding: 2.5rem 2rem 1.5rem;
  text-align: center;
  background: linear-gradient(135deg, #5a1188 0%, #9d38cf 100%);
  color: white;
  user-select: none;
}

.login-form-title {
  font-size: 2rem;
  font-weight: 600;
  margin-bottom: 0.5rem;
  animation: title-in 0.6s cubic-bezier(0.22, 1, 0.36, 1);
}

@keyframes title-in {
  0% {
    opacity: 0;
    transform: translateY(-10px);
  }
  100% {
    opacity: 1;
    transform: translateY(0);
  }
}

.login-form-subtitle {
  font-size: 0.875rem;
  opacity: 0.9;
  animation: subtitle-in 0.6s cubic-bezier(0.22, 1, 0.36, 1) 0.1s both;
}

@keyframes subtitle-in {
  0% {
    opacity: 0;
    transform: translateY(-10px);
  }
  100% {
    opacity: 0.9;
    transform: translateY(0);
  }
}

.login-form-content {
  padding: 2rem;
  display: flex;
  flex-direction: column;
  gap: 1.25rem;
}

.login-form-options {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-top: 1.25rem;
  font-size: 0.875rem;
  color: var(--color-primary);
}

.login-form-forgot {
  color: var(--color-primary);
  text-decoration: none;
  font-weight: 500;
  cursor: pointer;
  transition: color 0.3s;
}

.login-form-forgot:hover,
.login-form-forgot:focus-visible {
  color: #6d28d9;
  text-decoration: underline;
  outline: none;
}

.login-form-error {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.75rem 1rem;
  background: #fee2e2;
  border: 1px solid #fecaca;
  border-radius: 0.75rem;
  color: #b91c1c;
  font-weight: 600;
  font-size: 0.875rem;
  animation: error-shake 0.5s cubic-bezier(0.22, 1, 0.36, 1);
  user-select: none;
}

@keyframes error-shake {
  0%, 100% {
    transform: translateX(0);
  }
  25% {
    transform: translateX(-4px);
  }
  75% {
    transform: translateX(4px);
  }
}

.login-form-submit {
  width: 100%;
  margin-top: 0.5rem;
}

.login-form-footer {
  padding: 1.5rem 2rem;
  text-align: center;
  border-top: 1px solid #6d488d;
  background-color: var(--bg-footer, #f7f5fb);
  color: var(--color-text-secondary, #6b7280);
  font-size: 0.875rem;
  user-select: none;
}

.login-form-footer-link {
  color: #9d38cf;
  font-weight: 600;
  cursor: pointer;
  text-decoration: none;
  margin-left: 0.25rem;
  transition: color 0.3s;
}

.login-form-footer-link:hover,
.login-form-footer-link:focus-visible {
  color: #5a1188;
  text-decoration: underline;
  outline: none;
}

@media (max-width: 480px) {
  .login-form {
    max-width: 100%;
    margin: 1rem;
  }

  .login-form-header,
  .login-form-content,
  .login-form-footer {
    padding-left: 1.5rem;
    padding-right: 1.5rem;
  }
}
</style>
