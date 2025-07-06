<!--
Developer: DocraTech Team
License: MIT
Project: DocraTech Medical Website Platform
Version: 3.0 - Enterprise Success Notification
Description: Professional success notification with animations
-->

<template>
  <Teleport to="body">
    <Transition name="notification" appear>
      <div
        v-if="isVisible"
        class="fixed top-4 right-4 z-50 max-w-md"
      >
        <div class="relative bg-white rounded-2xl shadow-2xl border border-gray-200 overflow-hidden">
          <!-- Success gradient header -->
          <div class="absolute inset-0 bg-gradient-to-r from-green-500 via-emerald-500 to-green-600 opacity-10"></div>
          
          <!-- Animated success checkmark -->
          <div class="absolute -top-2 -right-2 w-16 h-16">
            <div class="w-full h-full bg-gradient-to-br from-green-400 to-emerald-500 rounded-full flex items-center justify-center shadow-lg animate-bounce">
              <CheckIcon class="w-8 h-8 text-white animate-pulse" />
            </div>
          </div>
          
          <!-- Content -->
          <div class="relative p-6 pr-12">
            <div class="flex items-start gap-4">
              <!-- Success icon -->
              <div class="flex-shrink-0 mt-1">
                <div class="w-12 h-12 bg-gradient-to-br from-green-100 to-emerald-100 rounded-xl flex items-center justify-center">
                  <CheckCircleIcon class="w-7 h-7 text-green-600" />
                </div>
              </div>
              
              <!-- Message -->
              <div class="flex-1 min-w-0">
                <div class="flex items-center gap-2 mb-2">
                  <div class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></div>
                  <h4 class="text-lg font-bold text-gray-900">Success!</h4>
                </div>
                
                <p 
                  class="text-gray-700 leading-relaxed"
                  v-html="message"
                ></p>
                
                <!-- User info if provided -->
                <div v-if="userInfo" class="mt-3 p-3 bg-green-50 rounded-lg border border-green-200">
                  <div class="flex items-center gap-3">
                    <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center">
                      <UserIcon class="w-5 h-5 text-green-600" />
                    </div>
                    <div>
                      <p class="font-medium text-green-800">{{ userInfo.name }}</p>
                      <p class="text-sm text-green-600">{{ userInfo.email }}</p>
                    </div>
                  </div>
                </div>
                
                <!-- Action completed timestamp -->
                <div class="mt-4 flex items-center gap-2 text-sm text-gray-500">
                  <ClockIcon class="w-4 h-4" />
                  <span>{{ formattedTime }}</span>
                </div>
              </div>
            </div>
            
            <!-- Progress bar animation -->
            <div class="mt-4 h-1 bg-gray-100 rounded-full overflow-hidden">
              <div 
                class="h-full bg-gradient-to-r from-green-500 to-emerald-500 rounded-full transition-all duration-300 ease-out"
                :style="{ width: progressWidth + '%' }"
              ></div>
            </div>
          </div>
          
          <!-- Close button -->
          <button
            @click="close"
            class="absolute top-3 right-3 w-8 h-8 bg-gray-100 hover:bg-gray-200 rounded-lg flex items-center justify-center transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2"
          >
            <XMarkIcon class="w-5 h-5 text-gray-600" />
          </button>
          
          <!-- Bottom gradient line -->
          <div class="h-1 bg-gradient-to-r from-green-500 via-emerald-500 to-green-600"></div>
        </div>
      </div>
    </Transition>
  </Teleport>
</template>

<script setup>
import { computed, ref, onMounted } from 'vue'
import { 
  CheckIcon, 
  CheckCircleIcon, 
  XMarkIcon, 
  UserIcon, 
  ClockIcon 
} from '@heroicons/vue/24/outline'

const props = defineProps({
  isVisible: {
    type: Boolean,
    default: false
  },
  message: {
    type: String,
    required: true
  },
  userInfo: {
    type: Object,
    default: null
  },
  duration: {
    type: Number,
    default: 5000
  },
  autoClose: {
    type: Boolean,
    default: true
  }
})

const emit = defineEmits(['close'])

const progressWidth = ref(100)
const currentTime = ref(new Date())

const formattedTime = computed(() => {
  return currentTime.value.toLocaleTimeString('en-US', {
    hour: '2-digit',
    minute: '2-digit',
    second: '2-digit'
  })
})

const close = () => {
  emit('close')
}

onMounted(() => {
  if (props.autoClose && props.duration > 0) {
    // Start progress bar animation
    const startTime = Date.now()
    const interval = setInterval(() => {
      const elapsed = Date.now() - startTime
      const remaining = Math.max(0, props.duration - elapsed)
      progressWidth.value = (remaining / props.duration) * 100
      
      if (remaining <= 0) {
        clearInterval(interval)
        close()
      }
    }, 50)
    
    // Simple fallback timer
    const fallbackTimer = setTimeout(() => {
      close()
    }, props.duration)
    
    // Cleanup on component unmount
    return () => {
      clearInterval(interval)
      clearTimeout(fallbackTimer)
    }
  }
})
</script>

<style scoped>
/* Notification Animation */
.notification-enter-active {
  transition: all 0.6s cubic-bezier(0.16, 1, 0.3, 1);
}

.notification-leave-active {
  transition: all 0.4s ease;
}

.notification-enter-from {
  opacity: 0;
  transform: translateX(100%) scale(0.9);
}

.notification-leave-to {
  opacity: 0;
  transform: translateX(100%) scale(0.95);
}

/* Custom animations */
@keyframes success-pop {
  0% { transform: scale(0) rotate(-45deg); }
  50% { transform: scale(1.2) rotate(-22.5deg); }
  100% { transform: scale(1) rotate(0deg); }
}

.animate-success-pop {
  animation: success-pop 0.6s cubic-bezier(0.68, -0.55, 0.265, 1.55);
}

@keyframes floating {
  0%, 100% { transform: translateY(0px); }
  50% { transform: translateY(-10px); }
}

.animate-floating {
  animation: floating 3s ease-in-out infinite;
}
</style> 