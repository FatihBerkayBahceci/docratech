<!--  
  Project: Kurumsal Sağlık Platformu Frontend  
  Component: Accordion (Yeniden Tasarlanmış, Enterprise-Level)  
  Developer: DocraTech Team - Fatih Berkay Bahceci  
-->

<template>
  <div
    class="accordion"
    :class="accordionClasses"
    role="presentation"
    :data-animated="animated"
  >
    <slot />
    <!-- Loading/Skeleton örneği -->
    <transition v-if="loading" name="fade">
      <div class="w-full h-12 rounded-xl bg-gradient-to-r from-[#181124] via-[#2A183D] to-[#181124] animate-pulse opacity-70 mt-2" />
    </transition>
  </div>
</template>

<script setup>
import { computed, toRefs } from 'vue'

/**
 * Props (TypeScript-like)
 */
const props = defineProps({
  variant: {
    type: String,
    default: 'default',
    validator: (value) =>
      ['default', 'bordered', 'separated'].includes(value),
  },
  animated: {
    type: Boolean,
    default: true,
  },
  multiple: {
    type: Boolean,
    default: false,
  },
  loading: {
    type: Boolean,
    default: false,
  },
})

const { variant, animated } = toRefs(props)

/**
 * Brandkit uyumlu dynamic Tailwind + custom classes
 */
const accordionClasses = computed(() => [
  'flex flex-col',
  'gap-3',
  'transition-all duration-300',
  'rounded-2xl',
  'p-2 md:p-4',
  'bg-[#181124]/95',
  'shadow-xl',
  'font-inter',
  'select-none',
  {
    'border border-[#6D488D] dark:border-[#2A183D] shadow-[0_4px_24px_#5A118833]':
      variant.value === 'bordered',
    'divide-y divide-[#2A183D] space-y-0': variant.value === 'separated',
    '': variant.value === 'default',
    'hover:shadow-[0_6px_32px_#9D38CF44]': true,
  },
])

/**
 * Provide - slot içindeki AccordionItem'lar için
 */
provide('accordionVariant', props.variant)
provide('accordionAnimated', props.animated)
provide('accordionMultiple', props.multiple)
</script>

<style scoped>
.accordion {
  --accordion-transition: cubic-bezier(0.42, 0, 0.15, 1.0);
  /* Brandkit uyumlu gölge, gradient vs */
  background: var(--accordion-bg, linear-gradient(120deg, #181124 70%, #2A183D 100%));
  /* Animasyon için min-h yüksekliği */
  min-height: 24px;
}

.fade-enter-active, .fade-leave-active {
  transition: opacity 0.4s var(--accordion-transition);
}
.fade-enter-from, .fade-leave-to {
  opacity: 0;
}

/* Accordion item macro animasyonları */
.accordion-item-enter-active,
.accordion-item-leave-active {
  transition:
    max-height 0.35s var(--accordion-transition),
    opacity 0.3s var(--accordion-transition),
    box-shadow 0.3s var(--accordion-transition),
    background 0.25s;
}
.accordion-item-enter-from {
  opacity: 0;
  max-height: 0;
  box-shadow: none;
}
.accordion-item-enter-to {
  opacity: 1;
  max-height: 400px;
  box-shadow: 0 8px 24px #5A118855;
}
.accordion-item-leave-to {
  opacity: 0;
  max-height: 0;
  box-shadow: none;
}

/* Mikro etkileşim: hover, focus, active */
.accordion:focus-within {
  box-shadow: 0 0 0 2px #9D38CF99;
  outline: none;
}
.accordion:active {
  background: linear-gradient(90deg, #5A1188 30%, #9D38CF 100%);
}
</style>
