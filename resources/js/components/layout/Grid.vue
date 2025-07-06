<!--
Developer: DocraTech Team - Fatih Berkay Bahceci
Component: Grid
Project: DocraTech Medical Website Platform
Language: American English (US)
License: MIT
Enterprise-ready, utility-first, responsive, a11y friendly.
-->

<template>
  <div
    :class="computedClasses"
    :style="computedStyle"
    role="list"
    :data-testid="testId"
    tabindex="-1"
  >
    <slot />
  </div>
</template>

<script setup>
import { computed, toRefs } from 'vue'

const props = defineProps({
  cols: {
    type: [Number, String, Object],
    default: 1
    // Object: { sm: 1, md: 2, lg: 3, xl: 4, '2xl': 6 }
  },
  gap: {
    type: String,
    default: 'md',
    validator: v => ['none', 'sm', 'md', 'lg', 'xl'].includes(v)
  },
  align: {
    type: String,
    default: 'stretch',
    validator: v => ['start', 'center', 'end', 'stretch', 'baseline'].includes(v)
  },
  justify: {
    type: String,
    default: 'start',
    validator: v => ['start', 'center', 'end', 'between', 'around', 'evenly'].includes(v)
  },
  wrap: {
    type: Boolean,
    default: true
  },
  dense: { // extra for grid-auto-flow: dense
    type: Boolean,
    default: false
  },
  autoFlow: {
    type: String,
    default: 'row',
    validator: v => ['row', 'column', 'dense', 'row dense', 'column dense'].includes(v)
  },
  minWidth: {
    type: [String, Number],
    default: null // like '200px'
  },
  testId: {
    type: String,
    default: 'grid'
  }
})

const { cols, gap, align, justify, wrap, dense, autoFlow, minWidth } = toRefs(props)

// Responsive grid columns logic
const gridColsClass = computed(() => {
  if (typeof cols.value === 'object') {
    // Responsive prop: { sm: 1, md: 2, lg: 3 }
    return Object.entries(cols.value)
      .map(([breakpoint, n]) =>
        typeof n === 'number'
          ? `${breakpoint}:grid-cols-${n}`
          : n === 'fit'
          ? `${breakpoint}:grid-cols-[repeat(auto-fit,minmax(200px,1fr))]`
          : n === 'fill'
          ? `${breakpoint}:grid-cols-[repeat(auto-fill,minmax(200px,1fr))]`
          : ''
      )
      .join(' ')
  }
  // Classic single value
  if (cols.value === 'fit')
    return 'grid-cols-[repeat(auto-fit,minmax(200px,1fr))]'
  if (cols.value === 'fill')
    return 'grid-cols-[repeat(auto-fill,minmax(200px,1fr))]'
  if (typeof cols.value === 'number' || ['1','2','3','4','6','12'].includes(String(cols.value)))
    return `grid-cols-${cols.value}`
  return ''
})

// Gap mapping
const gapClass = computed(() => ({
  none: 'gap-0',
  sm: 'gap-2',
  md: 'gap-4',
  lg: 'gap-6',
  xl: 'gap-8'
})[gap.value])

// Align / Justify
const alignClass = computed(() => ({
  start: 'items-start',
  center: 'items-center',
  end: 'items-end',
  stretch: 'items-stretch',
  baseline: 'items-baseline'
})[align.value])

const justifyClass = computed(() => ({
  start: 'justify-items-start',
  center: 'justify-items-center',
  end: 'justify-items-end',
  between: 'justify-between',
  around: 'justify-around',
  evenly: 'justify-evenly'
})[justify.value])

const computedClasses = computed(() =>
  [
    'grid',
    gridColsClass.value,
    gapClass.value,
    alignClass.value,
    justifyClass.value,
    { 'flex-wrap': wrap.value },
    { 'grid-flow-dense': dense.value },
    autoFlow.value !== 'row' ? `grid-flow-${autoFlow.value.replace(' ', '-')}` : '',
    'transition-all duration-300'
  ]
)

// Dynamic style for minWidth/column auto-fit
const computedStyle = computed(() =>
  minWidth.value
    ? { gridTemplateColumns: `repeat(auto-fit, minmax(${minWidth.value}, 1fr))` }
    : undefined
)
</script>

<!-- Minimal style as all layout is tailwind utility -->
<style scoped>
/* Minimal, use tailwind for grid/gap/align etc. */
</style>
