<!--
  Project: Kurumsal Sağlık Platformu Frontend
  Component: AppPopover (Enterprise-Level, Brandkit Uyumlu, Erişilebilir)
  Developer: DocraTech Team - Fatih Berkay Bahceci
-->

<template>
  <div
    class="app-popover"
    ref="popoverRef"
  >
    <div
      ref="triggerRef"
      class="app-popover-trigger"
      :tabindex="0"
      role="button"
      :aria-haspopup="'tooltip'"
      :aria-expanded="isOpen.toString()"
      :aria-describedby="isOpen ? popoverId : undefined"
      @click="togglePopover"
      @mouseenter="props.trigger === 'hover' && openPopover()"
      @mouseleave="props.trigger === 'hover' && closePopover()"
      @focus="props.trigger === 'focus' && openPopover()"
      @blur="props.trigger === 'focus' && closePopover()"
      @keydown.escape.prevent="closePopover()"
    >
      <slot name="trigger" />
    </div>

    <Transition
      name="popover"
      appear
      @after-enter="onAfterEnter"
      @after-leave="onAfterLeave"
    >
      <div
        v-if="isOpen"
        :id="popoverId"
        class="app-popover-content"
        :class="popoverClasses"
        :style="popoverStyles"
        role="tooltip"
        :aria-hidden="(!isOpen).toString()"
        tabindex="-1"
        ref="popoverContentRef"
      >
        <div class="app-popover-arrow" :class="arrowClasses" />
        <div class="app-popover-header" v-if="title || $slots.header">
          <slot name="header">
            <h3 v-if="title" class="app-popover-title">{{ title }}</h3>
          </slot>
        </div>
        <div class="app-popover-body">
          <slot />
        </div>
        <div class="app-popover-footer" v-if="$slots.footer">
          <slot name="footer" />
        </div>
      </div>
    </Transition>
  </div>
</template>

<script setup>
import { ref, computed, watch, onMounted, onUnmounted, nextTick } from 'vue'

const props = defineProps({
  title: { type: String, default: null },
  placement: {
    type: String,
    default: 'top',
    validator: (val) =>
      [
        'top',
        'top-start',
        'top-end',
        'bottom',
        'bottom-start',
        'bottom-end',
        'left',
        'left-start',
        'left-end',
        'right',
        'right-start',
        'right-end',
      ].includes(val),
  },
  trigger: {
    type: String,
    default: 'click',
    validator: (val) => ['click', 'hover', 'focus'].includes(val),
  },
  offset: { type: Number, default: 8 },
  animated: { type: Boolean, default: true },
  variant: {
    type: String,
    default: 'default',
    validator: (val) => ['default', 'light', 'dark'].includes(val),
  },
})

const emit = defineEmits(['show', 'hide'])

const isOpen = ref(false)
const popoverRef = ref(null)
const triggerRef = ref(null)
const popoverContentRef = ref(null)
const popoverId = `popover-${Math.random().toString(36).slice(2, 9)}`

// Computed classes based on variant and placement
const popoverClasses = computed(() => [
  `app-popover-${props.placement}`,
  `app-popover-${props.variant}`,
  { 'app-popover-animated': props.animated },
])

const arrowClasses = computed(() => [`app-popover-arrow-${props.placement}`])

const popoverStyles = computed(() => ({
  '--popover-offset': `${props.offset}px`,
  '--popover-transition': props.animated
    ? 'all 0.25s cubic-bezier(0.4, 0, 0.2, 1)'
    : 'none',
  position: 'fixed',
  top: `${popoverPosition.top}px`,
  left: `${popoverPosition.left}px`,
}))

const popoverPosition = ref({ top: 0, left: 0 })

function updatePosition() {
  if (!triggerRef.value || !popoverContentRef.value) return

  const triggerRect = triggerRef.value.getBoundingClientRect()
  const popoverEl = popoverContentRef.value
  const popoverRect = popoverEl.getBoundingClientRect()
  const vw = window.innerWidth
  const vh = window.innerHeight
  let top = 0
  let left = 0
  const offset = props.offset

  switch (props.placement) {
    case 'top':
      top = triggerRect.top - popoverRect.height - offset
      left = triggerRect.left + triggerRect.width / 2 - popoverRect.width / 2
      break
    case 'top-start':
      top = triggerRect.top - popoverRect.height - offset
      left = triggerRect.left
      break
    case 'top-end':
      top = triggerRect.top - popoverRect.height - offset
      left = triggerRect.right - popoverRect.width
      break
    case 'bottom':
      top = triggerRect.bottom + offset
      left = triggerRect.left + triggerRect.width / 2 - popoverRect.width / 2
      break
    case 'bottom-start':
      top = triggerRect.bottom + offset
      left = triggerRect.left
      break
    case 'bottom-end':
      top = triggerRect.bottom + offset
      left = triggerRect.right - popoverRect.width
      break
    case 'left':
      top = triggerRect.top + triggerRect.height / 2 - popoverRect.height / 2
      left = triggerRect.left - popoverRect.width - offset
      break
    case 'left-start':
      top = triggerRect.top
      left = triggerRect.left - popoverRect.width - offset
      break
    case 'left-end':
      top = triggerRect.bottom - popoverRect.height
      left = triggerRect.left - popoverRect.width - offset
      break
    case 'right':
      top = triggerRect.top + triggerRect.height / 2 - popoverRect.height / 2
      left = triggerRect.right + offset
      break
    case 'right-start':
      top = triggerRect.top
      left = triggerRect.right + offset
      break
    case 'right-end':
      top = triggerRect.bottom - popoverRect.height
      left = triggerRect.right + offset
      break
  }

  // Clamp within viewport
  top = Math.min(Math.max(8, top), vh - popoverRect.height - 8)
  left = Math.min(Math.max(8, left), vw - popoverRect.width - 8)

  popoverPosition.value = { top, left }
}

function openPopover() {
  isOpen.value = true
  emit('show')
  nextTick(() => updatePosition())
}

function closePopover() {
  isOpen.value = false
  emit('hide')
}

function togglePopover() {
  if (props.trigger === 'click') {
    if (isOpen.value) closePopover()
    else openPopover()
  }
}

function handleClickOutside(event) {
  if (
    props.trigger === 'click' &&
    isOpen.value &&
    popoverRef.value &&
    !popoverRef.value.contains(event.target)
  ) {
    closePopover()
  }
}

function onAfterEnter() {
  // Focus popover content for accessibility
  popoverContentRef.value?.focus()
}

function onAfterLeave() {
  // Return focus to trigger
  triggerRef.value?.focus()
}

onMounted(() => {
  document.addEventListener('click', handleClickOutside)
  window.addEventListener('resize', updatePosition)
  window.addEventListener('scroll', updatePosition, true)
})

onUnmounted(() => {
  document.removeEventListener('click', handleClickOutside)
  window.removeEventListener('resize', updatePosition)
  window.removeEventListener('scroll', updatePosition, true)
})

watch(isOpen, (val) => {
  if (val) nextTick(() => updatePosition())
})
</script>

<style scoped>
.app-popover {
  position: relative;
  display: inline-block;
}

.app-popover-trigger {
  cursor: pointer;
  outline-offset: 3px;
  user-select: none;
}

.app-popover-trigger:focus-visible {
  outline: 2px solid #9d38cf;
  outline-offset: 4px;
  border-radius: 0.375rem;
}

.app-popover-content {
  position: fixed;
  max-width: 320px;
  min-width: 220px;
  background: #2a183d;
  border: 1px solid #6d488d;
  border-radius: 1rem;
  box-shadow: 0 12px 30px rgba(93, 35, 150, 0.3);
  z-index: 9999;
  color: #e0d9f1;
  font-family: 'Inter', sans-serif;
  outline: none;
  padding: 1rem;
  user-select: text;
}

.dark .app-popover-content {
  background: #181124;
  border-color: #9d38cf;
  box-shadow: 0 12px 30px rgba(157, 56, 207, 0.45);
  color: #ccc6f7;
}

/* Popover variants */
.app-popover-default {
  background: #2a183d;
  border-color: #6d488d;
}

.app-popover-light {
  background: #f9f9fb;
  border-color: #c7c9d9;
  color: #3f3f46;
}

.app-popover-dark {
  background: #181124;
  border-color: #9d38cf;
  color: #ccc6f7;
}

/* Arrow styles */
.app-popover-arrow {
  position: absolute;
  width: 10px;
  height: 10px;
  background: inherit;
  border: inherit;
  transform: rotate(45deg);
  box-shadow: 0 3px 8px rgba(0, 0, 0, 0.1);
}

.app-popover-arrow-top {
  bottom: -5px;
  left: 50%;
  margin-left: -5px;
  border-top: none;
  border-left: none;
}

.app-popover-arrow-bottom {
  top: -5px;
  left: 50%;
  margin-left: -5px;
  border-bottom: none;
  border-right: none;
}

.app-popover-arrow-left {
  right: -5px;
  top: 50%;
  margin-top: -5px;
  border-left: none;
  border-bottom: none;
}

.app-popover-arrow-right {
  left: -5px;
  top: 50%;
  margin-top: -5px;
  border-right: none;
  border-top: none;
}

/* Header */
.app-popover-header {
  margin-bottom: 0.75rem;
  border-bottom: 1px solid #6d488d;
  padding-bottom: 0.25rem;
}

.dark .app-popover-header {
  border-bottom-color: #9d38cf;
}

.app-popover-title {
  font-weight: 600;
  font-size: 1rem;
  margin: 0;
  color: inherit;
  user-select: none;
}

/* Body */
.app-popover-body {
  font-size: 0.9rem;
  line-height: 1.4;
  color: inherit;
}

/* Footer */
.app-popover-footer {
  margin-top: 1rem;
  border-top: 1px solid #6d488d;
  padding-top: 0.5rem;
}

.dark .app-popover-footer {
  border-top-color: #9d38cf;
}

/* Animations */
.popover-enter-active,
.popover-leave-active {
  transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
  will-change: transform, opacity;
}

.popover-enter-from,
.popover-leave-to {
  opacity: 0;
  transform: scale(0.95);
}
</style>
