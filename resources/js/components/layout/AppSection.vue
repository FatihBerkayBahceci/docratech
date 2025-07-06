<!--
  Developer: DocraTech Team | Fatih Berkay Bahceci
  Language: American English (US)
  License: MIT
  Project: DocraTech Medical Website Platform
-->

<template>
  <section
    :class="sectionClass"
    role="region"
    tabindex="-1"
    v-motion-fade
    :aria-labelledby="title ? 'section-title' : null"
  >
    <div v-if="title || description || $slots.header" class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4 mb-6">
      <slot name="header">
        <div class="flex-1 min-w-0">
          <h2
            v-if="title"
            class="font-semibold text-2xl md:text-3xl leading-tight mb-1 text-[#5A1188] dark:text-[#D1A7F9]"
            :id="title ? 'section-title' : null"
          >{{ title }}</h2>
          <p v-if="description" class="text-sm md:text-base text-gray-500 dark:text-gray-400">{{ description }}</p>
        </div>
      </slot>
      <div v-if="$slots.actions" class="flex items-center gap-2">
        <slot name="actions" />
      </div>
    </div>
    <div class="w-full">
      <slot />
    </div>
  </section>
</template>

<script setup>
// Developer: DocraTech Team | Fatih Berkay Bahceci
import { computed } from 'vue'

const props = defineProps({
  title: { type: String, default: null },
  description: { type: String, default: null },
  variant: {
    type: String,
    default: 'default',
    validator: v => ['default', 'card', 'bordered'].includes(v)
  },
  spacing: {
    type: String,
    default: 'md',
    validator: v => ['sm', 'md', 'lg'].includes(v)
  }
})

const sectionClass = computed(() => {
  let base = 'w-full focus:outline-none transition-colors duration-300';
  // Card & bordered
  if (props.variant === 'card') {
    base += ' bg-white dark:bg-[#21183A] border border-[#E9D4FA] dark:border-[#472E7A] rounded-2xl shadow-md';
  }
  if (props.variant === 'bordered') {
    base += ' border border-[#E9D4FA] dark:border-[#472E7A] rounded-2xl';
  }
  // Spacing
  if (props.spacing === 'sm') base += ' px-4 py-4';
  if (props.spacing === 'md') base += ' px-8 py-8';
  if (props.spacing === 'lg') base += ' px-12 py-12';
  // Margin bottom for section separation
  base += ' mb-8';
  return base;
});
</script>

<!-- 
  If you use a motion plugin (like VueUse Motion), you can add fade/slide effects easily.
  If not, just remove v-motion-fade and rely on Tailwind transitions.
-->
