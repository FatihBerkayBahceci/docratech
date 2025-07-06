<template>
  <div class="min-h-screen flex flex-col items-center justify-center bg-gradient-to-br from-purple-100 via-indigo-50 to-white px-4 py-12">
    <div class="max-w-lg w-full bg-white/90 rounded-3xl shadow-2xl p-10 flex flex-col items-center border border-purple-100">
      <div class="flex flex-col items-center mb-6">
        <div class="w-20 h-20 rounded-full bg-gradient-to-br from-purple-500 to-indigo-600 flex items-center justify-center shadow-lg mb-4">
          <svg class="w-12 h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-12.728 12.728M5.636 5.636l12.728 12.728" />
          </svg>
        </div>
        <h1 class="text-3xl font-extrabold text-gray-900 mb-2 text-center">Account Suspended</h1>
        <p class="text-lg text-gray-600 text-center">Your account has been temporarily suspended.<br />You cannot access any features at this time.</p>
      </div>
      <div class="w-full bg-purple-50 border border-purple-200 rounded-xl p-4 mb-6 text-center">
        <p class="text-purple-700 font-medium">If you believe this is a mistake or need assistance, please contact our support team.</p>
        <a href="mailto:support@docratech.com" class="text-indigo-600 underline font-semibold mt-2 inline-block">support@docratech.com</a>
      </div>
      <button @click="logout" class="w-full py-3 bg-gradient-to-r from-purple-600 to-indigo-600 text-white rounded-xl font-bold text-lg shadow-lg hover:scale-105 transition mb-2">Log Out</button>
      <div class="text-xs text-gray-400 mt-2">DocraTech Medical Platform &copy; 2024</div>
    </div>
  </div>
</template>

<script setup>
import { useAuthStore } from '@/stores/auth'
import { useRouter } from 'vue-router'

const authStore = useAuthStore()
const router = useRouter()

function logout() {
  authStore.logout()
  router.replace({ name: 'login' })
}
</script>

<style scoped>
body, html {
  background: linear-gradient(135deg, #ede9fe 0%, #f0f4ff 100%);
}
</style> 