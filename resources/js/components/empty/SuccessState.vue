<!--
  Project: Enterprise Health SuccessState
  Developer: DocraTech Team - Fatih Berkay Bahceci
-->

<template>
  <section
    class="w-full flex flex-col items-center justify-center text-center py-16 px-4 sm:py-12 bg-docratech-background rounded-2xl shadow-2xl max-w-xl mx-auto min-h-[320px] focus:outline-none transition-all duration-400"
    role="status"
    aria-live="polite"
    tabindex="0"
    :aria-label="title"
  >
    <!-- Success Icon -->
    <transition name="fade-pop" appear>
      <div
        class="mb-6 animate-bounce-slow"
        aria-hidden="true"
        key="icon"
      >
        <Icon :name="icon" size="xl"
          class="text-green-400 drop-shadow-glow-success"
        />
      </div>
    </transition>

    <!-- Success Title -->
    <transition name="fade-up" appear>
      <h2
        class="success-title text-2xl font-semibold mb-2 select-none bg-gradient-to-r from-green-400 via-docratech-primaryLight to-docratech-primaryDark bg-clip-text text-transparent tracking-tight"
        key="title"
      >
        {{ title }}
      </h2>
    </transition>

    <!-- Success Description -->
    <transition name="fade-up-delay" appear>
      <p
        class="text-green-200 text-base max-w-[420px] mx-auto mb-4"
        key="desc"
      >
        {{ description }}
      </p>
    </transition>

    <!-- Details Info Box (optional) -->
    <transition name="fade-up-delay" appear>
      <div v-if="details" class="w-full max-w-lg mb-6" key="details">
        <div
          class="px-4 py-3 bg-green-900/80 border border-green-400 text-green-400 text-xs rounded-xl font-mono text-left whitespace-pre-wrap break-all shadow-lg transition-all duration-300 outline-none"
          tabindex="0"
        >
          {{ details }}
        </div>
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
          class="min-w-[120px] transition-all duration-200 focus-visible:ring-4 focus-visible:ring-docratech-primaryLight/40"
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
// Project: Enterprise Health SuccessState
// Developer: DocraTech Team - Fatih Berkay Bahceci

import Icon from '../visual/Icon.vue'
import AppButton from '../button/AppButton.vue'

const props = defineProps({
  title: {
    type: String,
    default: 'Success!'
  },
  description: {
    type: String,
    default: 'Your action has been completed successfully.'
  },
  icon: {
    type: String,
    default: 'check-circle'
  },
  details: {
    type: String,
    default: null
  },
  actions: {
    type: Array,
    default: () => ([
      {
        key: 'continue',
        label: 'Continue',
        icon: 'arrow-right',
        variant: 'primary'
      },
      {
        key: 'close',
        label: 'Close',
        icon: 'x',
        variant: 'outline'
      }
    ])
  }
})
const emit = defineEmits(['action'])

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
  50% { transform: translateY(-10px);}
}
.drop-shadow-glow-success {
  filter: drop-shadow(0 0 14px rgba(110, 231, 183, 0.6)) drop-shadow(0 0 6px rgba(157, 56, 207, 0.53));
}
.animate-bounce-slow {
  animation: bounce-slow 1.2s infinite cubic-bezier(.52,1.52,.5,1);
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
  animation-delay: 0.18s;
}

/* Success Title Brand Gradient */
.success-title {
  background: linear-gradient(92deg, theme('colors.green.400') 0%, theme('colors.docratech.primaryLight') 60%, theme('colors.docratech.primaryDark') 100%);
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
    min-height: 180px;
  }
  .success-title { font-size: 1.25rem; }
}
</style>
