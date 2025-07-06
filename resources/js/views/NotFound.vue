<template>
  <div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-blue-50 to-indigo-100 dark:from-gray-900 dark:to-gray-800">
    <div class="max-w-md w-full text-center">
      <div class="mb-8">
        <div class="text-9xl font-bold text-blue-600 dark:text-blue-400 mb-4 animate-bounce">
          404
        </div>
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-4">
          Page Not Found
        </h1>
        <p class="text-gray-600 dark:text-gray-400 mb-8">
          The page you're looking for doesn't exist or has been moved.
        </p>
      </div>
      
      <div class="space-y-4">
        <AppButton
          @click="goHome"
          variant="primary"
          size="lg"
          class="w-full"
        >
          <Icon name="home" class="w-5 h-5 mr-2" />
          Go Home
        </AppButton>
        
        <AppButton
          @click="goBack"
          variant="outline"
          size="lg"
          class="w-full"
        >
          <Icon name="arrow-left" class="w-5 h-5 mr-2" />
          Go Back
        </AppButton>
      </div>
      
      <div class="mt-8 text-sm text-gray-500 dark:text-gray-400">
        <p>If you believe this is an error, please contact support.</p>
      </div>
    </div>
  </div>
</template>

<script>
import { useRouter } from 'vue-router'
import AppButton from '@/components/button/AppButton.vue'
import Icon from '@/components/visual/Icon.vue'

export default {
  name: 'NotFound',
  components: {
    AppButton,
    Icon
  },
  setup() {
    const router = useRouter()

    const goHome = () => {
      router.push('/dashboard')
    }

    const goBack = () => {
      router.back()
    }

    return {
      goHome,
      goBack
    }
  }
}
</script> 