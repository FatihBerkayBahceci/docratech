<template>
  <div
    v-if="isVisible"
    class="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-50 animate-fadeIn"
    @click="handleBackdropClick"
  >
    <div
      class="bg-white rounded-3xl shadow-2xl w-full max-w-2xl mx-4 animate-slideUp border border-gray-200/50"
      @click.stop
    >
      <!-- Header -->
      <div class="relative px-8 py-6 bg-gradient-to-r from-blue-600 to-purple-600 text-white rounded-t-3xl">
        <div class="flex items-center gap-4">
          <div class="w-12 h-12 bg-white/20 rounded-2xl flex items-center justify-center backdrop-blur-sm">
            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
            </svg>
          </div>
          <div>
            <h3 class="text-xl font-bold">Send Email</h3>
            <p class="text-blue-100 text-sm">Send a message to {{ recipientName }}</p>
          </div>
        </div>
        
        <!-- Close Button -->
        <button
          @click="closeDialog"
          class="absolute top-6 right-6 w-10 h-10 bg-white/20 hover:bg-white/30 rounded-xl flex items-center justify-center transition-colors duration-200"
        >
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
          </svg>
        </button>
      </div>

      <!-- Form -->
      <form @submit.prevent="handleSubmit" class="p-8 space-y-6">
        <!-- Recipient Info -->
        <div class="bg-gray-50 rounded-2xl p-4 border border-gray-200">
          <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
              <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
              </svg>
            </div>
            <div>
              <p class="font-semibold text-gray-900">{{ recipientName }}</p>
              <p class="text-sm text-gray-600">{{ recipientEmail }}</p>
            </div>
          </div>
        </div>

        <!-- Subject Field -->
        <div class="space-y-2">
          <label for="subject" class="block text-sm font-semibold text-gray-700">
            Subject <span class="text-red-500">*</span>
          </label>
          <input
            id="subject"
            v-model="form.subject"
            type="text"
            required
            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200 bg-gray-50 focus:bg-white"
            placeholder="Enter email subject..."
          />
        </div>

        <!-- Message Field -->
        <div class="space-y-2">
          <label for="message" class="block text-sm font-semibold text-gray-700">
            Message <span class="text-red-500">*</span>
          </label>
          <textarea
            id="message"
            v-model="form.body"
            :rows="8"
            required
            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200 bg-gray-50 focus:bg-white resize-none"
            placeholder="Enter your message..."
          ></textarea>
        </div>

        <!-- Actions -->
        <div class="flex items-center justify-between pt-4">
          <div class="flex items-center gap-2 text-sm text-gray-500">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            Email will be sent from DocraTech system
          </div>
          
          <div class="flex items-center gap-3">
            <button
              type="button"
              @click="closeDialog"
              class="px-6 py-3 text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-xl font-medium transition-colors duration-200"
              :disabled="isLoading"
            >
              Cancel
            </button>
            
            <button
              type="submit"
              class="px-8 py-3 bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white rounded-xl font-medium transition-all duration-200 flex items-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed"
              :disabled="isLoading || !isFormValid"
            >
              <svg v-if="isLoading" class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              <svg v-else class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
              </svg>
              {{ isLoading ? 'Sending...' : 'Send Email' }}
            </button>
          </div>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue'

// Props
const props = defineProps({
  isVisible: {
    type: Boolean,
    default: false
  },
  recipientName: {
    type: String,
    required: true
  },
  recipientEmail: {
    type: String,
    required: true
  },
  defaultSubject: {
    type: String,
    default: 'Message from DocraTech Admin'
  },
  defaultBody: {
    type: String,
    default: ''
  }
})

// Emits
const emit = defineEmits(['close', 'send'])

// State
const isLoading = ref(false)
const form = ref({
  subject: '',
  body: ''
})

// Computed
const isFormValid = computed(() => {
  return form.value.subject.trim() && form.value.body.trim()
})

// Watchers
watch(() => props.isVisible, (visible) => {
  if (visible) {
    // Reset form when dialog opens
    form.value.subject = props.defaultSubject
    form.value.body = props.defaultBody || getDefaultEmailBody()
  }
})

// Methods
const getDefaultEmailBody = () => {
  const recipientFirstName = props.recipientName.split(' ')[0]
  return `Dear ${recipientFirstName},

This is a message from DocraTech administration.



Best regards,
DocraTech Team`
}

const handleBackdropClick = () => {
  if (!isLoading.value) {
    closeDialog()
  }
}

const closeDialog = () => {
  if (!isLoading.value) {
    emit('close')
  }
}

const handleSubmit = async () => {
  if (!isFormValid.value || isLoading.value) return
  
  isLoading.value = true
  
  try {
    await emit('send', {
      subject: form.value.subject.trim(),
      body: form.value.body.trim()
    })
  } finally {
    isLoading.value = false
  }
}
</script>

<style scoped>
@keyframes fadeIn {
  from { opacity: 0; }
  to { opacity: 1; }
}

@keyframes slideUp {
  from { 
    opacity: 0;
    transform: translateY(20px) scale(0.95);
  }
  to { 
    opacity: 1;
    transform: translateY(0) scale(1);
  }
}

.animate-fadeIn {
  animation: fadeIn 0.3s ease-out;
}

.animate-slideUp {
  animation: slideUp 0.3s ease-out;
}
</style> 