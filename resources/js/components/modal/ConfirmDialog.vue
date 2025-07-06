<!--
Developer: DocraTech Team
License: MIT
Project: DocraTech Medical Website Platform
Version: 3.0 - Enterprise Confirm Dialog
Description: Professional confirmation dialog with animations and branding
-->

<template>
  <Teleport to="body">
    <Transition name="modal-backdrop" appear>
      <div
        v-if="isVisible"
        class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm"
        @click="handleBackdropClick"
      >
        <Transition name="modal-content" appear>
          <div
            v-if="isVisible"
            class="relative w-full max-w-lg bg-white rounded-2xl shadow-2xl overflow-hidden border border-gray-200"
            @click.stop
          >
            <!-- Gradient Header -->
            <div class="relative overflow-hidden">
              <div class="absolute inset-0 bg-gradient-to-r from-blue-600 via-purple-600 to-blue-800"></div>
              <div class="absolute inset-0 bg-gradient-to-br from-transparent via-white/10 to-transparent"></div>
              
              <div class="relative px-6 py-6 text-white">
                <div class="flex items-center gap-4">
                  <!-- Icon -->
                  <div class="flex-shrink-0">
                    <div 
                      class="w-16 h-16 rounded-2xl flex items-center justify-center shadow-lg transform rotate-3 hover:rotate-0 transition-transform duration-300"
                      :class="iconClasses"
                    >
                      <component 
                        :is="iconComponent" 
                        class="w-8 h-8 text-white drop-shadow-lg"
                      />
                    </div>
                  </div>
                  
                  <!-- Title & Brand -->
                  <div class="flex-1">
                    <div class="flex items-center gap-2 mb-2">
                      <div class="w-2 h-2 bg-white/60 rounded-full"></div>
                      <span class="text-sm font-medium text-white/80 uppercase tracking-wider">
                        DocraTech Platform
                      </span>
                    </div>
                    <h3 class="text-2xl font-bold text-white drop-shadow-md">
                      {{ title }}
                    </h3>
                  </div>
                </div>
              </div>
              
              <!-- Animated waves -->
              <div class="absolute bottom-0 left-0 right-0 h-4">
                <svg class="w-full h-full" viewBox="0 0 1200 120" preserveAspectRatio="none">
                  <path 
                    d="M1200,120L0,120L0,84C80,84,160,84,240,78C320,72,400,60,480,66C560,72,640,96,720,102C800,108,880,96,960,78C1040,60,1120,36,1200,24L1200,120Z" 
                    fill="white"
                    class="animate-pulse"
                  ></path>
                </svg>
              </div>
            </div>

            <!-- Content -->
            <div class="px-6 py-6">
              <!-- Message -->
              <div class="mb-6">
                <div 
                  class="text-gray-700 leading-relaxed"
                  v-html="message"
                ></div>
              </div>

              <!-- Warning Box for Danger Actions -->
              <div 
                v-if="variant === 'danger'" 
                class="mb-6 p-4 bg-red-50 border border-red-200 rounded-xl"
              >
                <div class="flex items-center gap-3 mb-3">
                  <div class="w-8 h-8 bg-red-100 rounded-lg flex items-center justify-center">
                    <ExclamationTriangleIcon class="w-5 h-5 text-red-600" />
                  </div>
                  <div>
                    <h4 class="font-semibold text-red-800">Critical Warning</h4>
                    <p class="text-sm text-red-600">This action cannot be undone</p>
                  </div>
                </div>
                
                <div class="pl-11">
                  <ul class="text-sm text-red-700 space-y-1">
                    <li class="flex items-center gap-2">
                      <div class="w-1.5 h-1.5 bg-red-400 rounded-full"></div>
                      Data will be permanently removed
                    </li>
                    <li class="flex items-center gap-2">
                      <div class="w-1.5 h-1.5 bg-red-400 rounded-full"></div>
                      All associated records will be deleted
                    </li>
                    <li class="flex items-center gap-2">
                      <div class="w-1.5 h-1.5 bg-red-400 rounded-full"></div>
                      Recovery will not be possible
                    </li>
                  </ul>
                </div>
              </div>

              <!-- Action Buttons -->
              <div class="flex items-center gap-3 justify-end">
                <!-- Cancel Button -->
                <button
                  @click="handleCancel"
                  class="px-6 py-3 text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-xl font-medium transition-all duration-200 transform hover:scale-105 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2"
                >
                  {{ cancelText }}
                </button>

                <!-- Confirm Button -->
                <button
                  @click="handleConfirm"
                  :disabled="isProcessing"
                  class="relative px-6 py-3 rounded-xl font-medium transition-all duration-300 transform hover:scale-105 focus:outline-none focus:ring-2 focus:ring-offset-2 min-w-[140px]"
                  :class="confirmButtonClasses"
                >
                  <span v-if="!isProcessing" class="flex items-center gap-2">
                    <component :is="confirmIcon" class="w-5 h-5" />
                    {{ confirmText }}
                  </span>
                  
                  <!-- Loading State -->
                  <span v-else class="flex items-center gap-2">
                    <svg class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24">
                      <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                      <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Processing...
                  </span>
                </button>
              </div>
            </div>

            <!-- Bottom Brand Line -->
            <div class="h-1 bg-gradient-to-r from-blue-500 via-purple-500 to-blue-600"></div>
          </div>
        </Transition>
      </div>
    </Transition>
  </Teleport>
</template>

<script setup>
import { computed, ref, onMounted } from 'vue'
import { 
  ExclamationTriangleIcon, 
  TrashIcon, 
  CheckIcon, 
  ExclamationCircleIcon,
  InformationCircleIcon 
} from '@heroicons/vue/24/outline'

const props = defineProps({
  isVisible: {
    type: Boolean,
    default: false
  },
  title: {
    type: String,
    default: 'Confirm Action'
  },
  message: {
    type: String,
    default: 'Are you sure you want to proceed?'
  },
  confirmText: {
    type: String,
    default: 'Confirm'
  },
  cancelText: {
    type: String,
    default: 'Cancel'
  },
  variant: {
    type: String,
    default: 'primary', // primary, danger, warning, info
    validator: (value) => ['primary', 'danger', 'warning', 'info'].includes(value)
  },
  size: {
    type: String,
    default: 'md'
  }
})

const emit = defineEmits(['confirm', 'cancel', 'close'])

const isProcessing = ref(false)

// Dynamic classes based on variant
const iconClasses = computed(() => {
  const classes = {
    primary: 'bg-gradient-to-br from-blue-500 to-blue-600',
    danger: 'bg-gradient-to-br from-red-500 to-red-600',
    warning: 'bg-gradient-to-br from-amber-500 to-orange-500',
    info: 'bg-gradient-to-br from-cyan-500 to-blue-500'
  }
  return classes[props.variant]
})

const confirmButtonClasses = computed(() => {
  if (isProcessing.value) {
    return 'bg-gray-400 cursor-not-allowed text-white'
  }
  
  const classes = {
    primary: 'bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white focus:ring-blue-500',
    danger: 'bg-gradient-to-r from-red-600 to-red-700 hover:from-red-700 hover:to-red-800 text-white focus:ring-red-500',
    warning: 'bg-gradient-to-r from-amber-500 to-orange-500 hover:from-amber-600 hover:to-orange-600 text-white focus:ring-amber-500',
    info: 'bg-gradient-to-r from-cyan-500 to-blue-500 hover:from-cyan-600 hover:to-blue-600 text-white focus:ring-cyan-500'
  }
  return classes[props.variant]
})

const iconComponent = computed(() => {
  const icons = {
    primary: CheckIcon,
    danger: TrashIcon,
    warning: ExclamationTriangleIcon,
    info: InformationCircleIcon
  }
  return icons[props.variant]
})

const confirmIcon = computed(() => {
  const icons = {
    primary: CheckIcon,
    danger: TrashIcon,
    warning: ExclamationCircleIcon,
    info: InformationCircleIcon
  }
  return icons[props.variant]
})

const handleConfirm = async () => {
  isProcessing.value = true
  
  try {
    await new Promise(resolve => setTimeout(resolve, 300)) // Small delay for UX
    emit('confirm')
  } finally {
    isProcessing.value = false
  }
}

const handleCancel = () => {
  emit('cancel')
  emit('close')
}

const handleBackdropClick = () => {
  if (!isProcessing.value) {
    handleCancel()
  }
}

// Keyboard handling
onMounted(() => {
  const handleKeydown = (e) => {
    if (e.key === 'Escape' && props.isVisible && !isProcessing.value) {
      handleCancel()
    }
    if (e.key === 'Enter' && props.isVisible && !isProcessing.value) {
      handleConfirm()
    }
  }
  
  document.addEventListener('keydown', handleKeydown)
  
  return () => {
    document.removeEventListener('keydown', handleKeydown)
  }
})
</script>

<style scoped>
/* Modal Backdrop Animation */
.modal-backdrop-enter-active,
.modal-backdrop-leave-active {
  transition: all 0.3s ease;
}

.modal-backdrop-enter-from,
.modal-backdrop-leave-to {
  opacity: 0;
  backdrop-filter: blur(0px);
}

/* Modal Content Animation */
.modal-content-enter-active {
  transition: all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
}

.modal-content-leave-active {
  transition: all 0.3s ease;
}

.modal-content-enter-from {
  opacity: 0;
  transform: scale(0.8) translateY(-20px);
}

.modal-content-leave-to {
  opacity: 0;
  transform: scale(0.95) translateY(10px);
}

/* Wave Animation */
@keyframes wave {
  0% { transform: translateX(-100px); }
  100% { transform: translateX(100px); }
}

.animate-wave {
  animation: wave 3s ease-in-out infinite;
}

/* Custom Scrollbar */
::-webkit-scrollbar {
  width: 6px;
}

::-webkit-scrollbar-track {
  background: #f1f5f9;
  border-radius: 3px;
}

::-webkit-scrollbar-thumb {
  background: linear-gradient(to bottom, #3b82f6, #8b5cf6);
  border-radius: 3px;
}

::-webkit-scrollbar-thumb:hover {
  background: linear-gradient(to bottom, #2563eb, #7c3aed);
}
</style> 