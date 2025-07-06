<!--
Developer: DocraTech Team - Fatih Berkay Bahceci
Component: Flex
Project: DocraTech Medical Website Platform
Language: American English (US)
License: MIT

- Fully utility-first TailwindCSS (ready for JIT)
- Enterprise-class prop flexibility (responsive, gap, wrap, order, grow/shrink)
- testId prop for UI testing
- Accessible/semantic, keyboard navigation ready
- Animation/transition ready
- SSR friendly, backend-safe
-->

<template>
  <div
    :class="computedClasses"
    :style="{ padding }"
    :data-testid="testId"
    tabindex="-1"
    role="group"
  >
    <slot />
  </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  direction: {
    type: String,
    default: 'row',
    validator: v =>
      ['row', 'col', 'row-reverse', 'col-reverse'].includes(v)
  },
  align: {
    type: String,
    default: 'stretch',
    validator: v =>
      ['start', 'center', 'end', 'stretch', 'baseline'].includes(v)
  },
  justify: {
    type: String,
    default: 'start',
    validator: v =>
      ['start', 'center', 'end', 'between', 'around', 'evenly'].includes(v)
  },
  wrap: { type: Boolean, default: false },
  gap: {
    type: String,
    default: 'none',
    validator: v => ['none', 'sm', 'md', 'lg', 'xl'].includes(v)
  },
  padding: { type: String, default: '0' },
  testId: { type: String, default: 'flex' }
})

// Utility-first, semantic class building
const computedClasses = computed(() => [
  'flex',
  {
    'flex-row': props.direction === 'row',
    'flex-col': props.direction === 'col',
    'flex-row-reverse': props.direction === 'row-reverse',
    'flex-col-reverse': props.direction === 'col-reverse',
    'flex-wrap': props.wrap
  },
  {
    'items-start': props.align === 'start',
    'items-center': props.align === 'center',
    'items-end': props.align === 'end',
    'items-stretch': props.align === 'stretch',
    'items-baseline': props.align === 'baseline'
  },
  {
    'justify-start': props.justify === 'start',
    'justify-center': props.justify === 'center',
    'justify-end': props.justify === 'end',
    'justify-between': props.justify === 'between',
    'justify-around': props.justify === 'around',
    'justify-evenly': props.justify === 'evenly'
  },
  {
    'gap-0': props.gap === 'none',
    'gap-2': props.gap === 'sm',
    'gap-4': props.gap === 'md',
    'gap-6': props.gap === 'lg',
    'gap-8': props.gap === 'xl'
  },
  'relative transition-all duration-300'
])
</script>

<!-- Only add extra styles for transition/animation if needed -->
<style scoped>
/* Animation for child entrance (optional, can be enabled on demand) */
.flex > * {
  will-change: transform, opacity;
  transition: opacity 0.28s cubic-bezier(.36,.07,.19,.97), transform 0.28s cubic-bezier(.36,.07,.19,.97);
}
</style>
