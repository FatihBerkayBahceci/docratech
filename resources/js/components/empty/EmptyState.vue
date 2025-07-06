<!--
  Project: Enterprise Health EmptyState
  Developer: DocraTech Team - Fatih Berkay Bahceci
-->

<template>
  <section
    class="w-full flex flex-col items-center justify-center text-center py-16 px-4 sm:py-12 bg-[#181124] rounded-2xl shadow-xl max-w-xl mx-auto transition-all duration-400 focus:outline-none focus-visible:ring-4 focus-visible:ring-[#9D38CF]/50"
    role="alert"
    aria-live="polite"
    tabindex="0"
    :aria-label="title"
  >
    <transition name="fade-pop" mode="out-in" appear>
      <component
        v-if="icon"
        :is="IconComponent"
        class="mb-6 text-[3rem] sm:text-[2.25rem] text-[#9D38CF] drop-shadow-glow pointer-events-none select-none animate-bounce-slow"
        aria-hidden="true"
        key="icon"
      />
    </transition>

    <transition name="fade-up" appear>
      <h2
        class="empty-title font-semibold text-[1.375rem] sm:text-[1.125rem] tracking-tight mb-2 text-white"
        key="title"
      >
        {{ title }}
      </h2>
    </transition>

    <transition name="fade-up-delay" appear>
      <p
        v-if="description"
        class="empty-desc text-[#C9C6D5] text-base max-w-[380px] mx-auto mb-4"
        key="desc"
      >
        {{ description }}
      </p>
    </transition>

    <!-- Action Button -->
    <transition name="fade-up-delay" appear>
      <button
        v-if="actionLabel"
        type="button"
        @click="$emit('action')"
        class="group gradient-btn px-6 py-2 mt-2 rounded-2xl font-semibold text-lg shadow-md focus-visible:ring-4 focus-visible:ring-[#9D38CF]/40 transition-all duration-300 relative overflow-hidden"
        :aria-label="`Perform action: ${actionLabel}`"
        tabindex="0"
        key="action"
      >
        <span class="relative z-10">{{ actionLabel }}</span>
        <!-- Gradient overlay for micro interaction -->
        <span
          class="absolute inset-0 opacity-0 group-hover:opacity-100 group-focus-visible:opacity-100 transition-opacity duration-300 bg-gradient-to-br from-[#9D38CF]/70 to-[#5A1188]/60"
        ></span>
      </button>
    </transition>

    <!-- Skeleton Loading State -->
    <transition name="fade-pop" appear>
      <div v-if="loading" class="w-full mt-6 flex flex-col items-center gap-2 animate-pulse" key="loading">
        <div class="h-12 w-12 rounded-full bg-[#2A183D]/60" />
        <div class="h-5 w-2/3 rounded-lg bg-[#2A183D]/50" />
        <div class="h-4 w-1/2 rounded-lg bg-[#2A183D]/40" />
      </div>
    </transition>
  </section>
</template>

<script setup>
// Project: Enterprise Health EmptyState
// Developer: DocraTech Team - Fatih Berkay Bahceci

import { computed } from 'vue'
import { defineProps, defineEmits } from 'vue'

// Brandkit Icon desteği: harici iconları otomatik işlemek için component mapping
const props = defineProps({
  icon: { type: String, default: '' },
  title: { type: String, required: true },
  description: { type: String, default: '' },
  actionLabel: { type: String, default: '' },
  loading: { type: Boolean, default: false }
})
const emit = defineEmits(['action'])

// Dinamik icon desteği: Brandkit ikon veya Bootstrap Icons SVG olarak işlenebilir.
const iconMap = {
  // Kendi SVG veya Iconify kullanımı örneği
  info: {
    template: `<svg fill="none" viewBox="0 0 32 32" stroke="currentColor"><circle cx="16" cy="16" r="14" stroke-width="2.5" /><rect x="15" y="12" width="2" height="8" rx="1" fill="currentColor"/><rect x="15" y="9" width="2" height="2" rx="1" fill="currentColor"/></svg>`
  },
  // Diğer iconlar eklenebilir
}

const IconComponent = computed(() => {
  if (props.icon && iconMap[props.icon]) return iconMap[props.icon]
  // Eğer icon prop'u bir class ise eski şekilde bırak
  return {
    template: `<i class="bi ${props.icon}"></i>`
  }
})
</script>

<style scoped>
/* Brandkit Animations */
@keyframes fade-pop-in {
  0% {
    opacity: 0;
    transform: scale(.9) translateY(16px);
  }
  100% {
    opacity: 1;
    transform: scale(1) translateY(0);
  }
}
@keyframes fade-up-in {
  0% { opacity: 0; transform: translateY(24px);}
  100% { opacity: 1; transform: translateY(0);}
}
@keyframes bounce-slow {
  0%, 100% { transform: translateY(0);}
  50% { transform: translateY(-8px);}
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
  animation-delay: 0.15s;
}
.animate-bounce-slow {
  animation: bounce-slow 1.2s infinite cubic-bezier(.52,1.52,.5,1);
}
.drop-shadow-glow {
  filter: drop-shadow(0 0 12px #9D38CF88);
}

/* Gradient Button (Brandkit) */
.gradient-btn {
  background: linear-gradient(90deg, #5A1188 0%, #9D38CF 100%);
  color: #FFF;
  border: none;
  outline: none;
  position: relative;
  z-index: 1;
  box-shadow: 0 2px 24px 0 #5A118855;
}
.gradient-btn:hover, .gradient-btn:focus-visible {
  background: linear-gradient(90deg, #6D488D 0%, #9D38CF 100%);
  box-shadow: 0 0 0 3px #9D38CF77;
}
.gradient-btn span:last-child {
  pointer-events: none;
  border-radius: inherit;
}

/* Focus-visible ring: Tailwind ile de eklenebilir */
:focus-visible {
  outline: none;
}

/* Responsive spacing ve font */
@media (max-width: 640px) {
  section {
    padding: 2.5rem 1rem;
    border-radius: 1.25rem;
  }
}

/* Skeleton loader renkleri ve responsive uyum */
</style>
