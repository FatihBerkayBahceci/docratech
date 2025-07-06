<!--
  Project: Enterprise Health ErrorState
  Developer: DocraTech Team - Fatih Berkay Bahceci
-->

<template>
  <section
    class="w-full flex flex-col items-center justify-center text-center py-16 px-4 sm:py-12 bg-[#181124] rounded-2xl shadow-2xl max-w-xl mx-auto min-h-[340px] focus:outline-none transition-all duration-400"
    role="alert"
    aria-live="assertive"
    tabindex="0"
    :aria-label="title"
  >
    <!-- Error Icon -->
    <transition name="fade-pop" appear>
      <div
        class="mb-6 animate-bounce-slow"
        aria-hidden="true"
        key="icon"
      >
        <Icon :name="icon" size="xl"
          class="text-[#FF6073] drop-shadow-glow"
        />
      </div>
    </transition>

    <!-- Error Title -->
    <transition name="fade-up" appear>
      <h2
        class="error-title text-2xl font-semibold mb-2 select-none bg-gradient-to-r from-[#FF6073] via-[#9D38CF] to-[#5A1188] bg-clip-text text-transparent tracking-tight"
        key="title"
      >
        {{ title }}
      </h2>
    </transition>

    <!-- Error Description -->
    <transition name="fade-up-delay" appear>
      <p
        class="text-[#E1D9EC] text-base max-w-[420px] mx-auto mb-4"
        key="desc"
      >
        {{ description }}
      </p>
    </transition>

    <!-- Error Details Collapsible -->
    <transition name="fade-up-delay" appear>
      <div v-if="details" class="w-full max-w-lg mb-6" key="details">
        <details
          ref="detailsRef"
          class="group rounded-xl border border-[#442866] bg-[#2A183D]/60 overflow-hidden transition-all duration-300 focus-within:ring-2 focus-within:ring-[#9D38CF]"
        >
          <summary
            class="flex items-center gap-2 px-4 py-3 cursor-pointer font-medium text-[#C9C6D5] text-sm group-open:text-[#FF6073] transition-colors outline-none"
            tabindex="0"
            role="button"
            @keydown.enter.prevent="toggleDetails"
            @keydown.space.prevent="toggleDetails"
          >
            <Icon name="chevron-down" size="sm" aria-hidden="true" class="transition-transform group-open:rotate-180" />
            Error Details
          </summary>
          <pre
            class="px-4 py-3 bg-[#2A183D] text-[#FF6073] text-xs rounded-b-xl font-mono text-left whitespace-pre-wrap break-all border-t border-[#442866]"
          >{{ details }}</pre>
        </details>
      </div>
    </transition>

    <!-- Action Buttons -->
    <transition name="fade-up-delay" appear>
      <div v-if="actions && actions.length" class="flex flex-wrap items-center justify-center gap-4 w-full mt-2" key="actions">
        <AppButton
          v-for="action in actions"
          :key="action.key"
          :variant="action.variant || 'primary'"
          :size="action.size ? (action.size === 'sm' ? 'small' : action.size === 'md' ? 'medium' : action.size === 'lg' ? 'large' : action.size) : 'medium'"
          @click="handleAction(action)"
          type="button"
          class="min-w-[120px] transition-all duration-200 focus-visible:ring-4 focus-visible:ring-[#9D38CF]/40"
          :aria-label="`Action: ${action.label}`"
        >
          <Icon v-if="action.icon" :name="action.icon" size="sm" class="mr-2" aria-hidden="true" />
          {{ action.label }}
        </AppButton>
      </div>
    </transition>
  </section>
</template>

<script setup>
// Project: Enterprise Health ErrorState
// Developer: DocraTech Team - Fatih Berkay Bahceci

import { ref } from 'vue'
import Icon from '../visual/Icon.vue'
import AppButton from '../button/AppButton.vue'

const props = defineProps({
  title: {
    type: String,
    default: 'An error occurred'
  },
  description: {
    type: String,
    default: 'Something went wrong while processing your request. Please try again.'
  },
  icon: {
    type: String,
    default: 'alert-triangle'
  },
  details: {
    type: String,
    default: null
  },
  actions: {
    type: Array,
    default: () => ([
      {
        key: 'retry',
        label: 'Try Again',
        icon: 'refresh',
        variant: 'primary'
      },
      {
        key: 'back',
        label: 'Go Back',
        icon: 'arrow-left',
        variant: 'outline'
      }
    ])
  }
})
const emit = defineEmits(['action'])

// Details accordion keyboard toggle
const detailsRef = ref(null)
function toggleDetails(e) {
  // Native toggle
  if (detailsRef.value) detailsRef.value.open = !detailsRef.value.open
}

function handleAction(action) {
  emit('action', action)
}
</script>

<style scoped>
/* Animations */
@keyframes fade-pop-in {
  0% { opacity: 0; transform: scale(.9) translateY(16px);}
  100% { opacity: 1; transform: scale(1) translateY(0);}
}
@keyframes fade-up-in {
  0% { opacity: 0; transform: translateY(24px);}
  100% { opacity: 1; transform: translateY(0);}
}
@keyframes bounce-slow {
  0%, 100% { transform: translateY(0);}
  50% { transform: translateY(-8px);}
}
.drop-shadow-glow {
  filter: drop-shadow(0 0 16px #FF607377) drop-shadow(0 0 8px #9D38CF77);
}
.animate-bounce-slow {
  animation: bounce-slow 1.4s infinite cubic-bezier(.52,1.52,.5,1);
}
/* Transition classes */
.fade-pop-enter-active, .fade-pop-leave-active {
  animation: fade-pop-in 0.5s cubic-bezier(.52,1.52,.5,1) both;
}
.fade-up-enter-active, .fade-up-leave-active {
  animation: fade-up-in 0.4s cubic-bezier(.52,1.52,.5,1) both;
}
.fade-up-delay-enter-active, .fade-up-delay-leave-active {
  animation: fade-up-in 0.7s cubic-bezier(.52,1.52,.5,1) both;
  animation-delay: 0.16s;
}

/* Custom title gradient for error */
.error-title {
  /* Brandkit kırmızıdan mor geçişli gradient başlık */
  background: linear-gradient(92deg, #FF6073 0%, #9D38CF 60%, #5A1188 100%);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
  text-fill-color: transparent;
}

/* Responsive */
@media (max-width: 640px) {
  section {
    padding: 2.5rem 1rem;
    border-radius: 1.25rem;
    min-height: 220px;
  }
  .error-title { font-size: 1.25rem; }
}
</style>
