<!--
  Project: Enterprise Health Tooltip
  Developer: DocraTech Team - Fatih Berkay Bahceci
-->

<template>
  <div
    class="inline-block relative"
    ref="triggerRef"
    @mouseenter="trigger('mouseenter')"
    @mouseleave="trigger('mouseleave')"
    @focusin="trigger('focus')"
    @focusout="trigger('blur')"
    @click="trigger('click')"
    tabindex="0"
    :aria-describedby="tooltipId"
  >
    <slot />
    <Teleport to="body">
      <transition name="tt-fade-scale">
        <div
          v-if="isVisible"
          :id="tooltipId"
          ref="tooltipRef"
          class="z-[1100] fixed pointer-events-none"
          :style="tooltipStyle"
          :class="['tt', positionClass, themeClass]"
          role="tooltip"
        >
          <div class="tt-content px-3 py-2 rounded-xl shadow-xl font-medium text-[.97em] flex items-center gap-1 select-none">
            <slot name="content">{{ content }}</slot>
          </div>
          <div class="tt-arrow"></div>
        </div>
      </transition>
    </Teleport>
  </div>
</template>

<script setup>
// Project: Enterprise Health Tooltip
// Developer: DocraTech Team - Fatih Berkay Bahceci

import { ref, computed, watch, nextTick, onBeforeUnmount } from 'vue'

// Props
const props = defineProps({
  content: String,
  position: {
    type: String,
    default: 'top',
    validator: v => ['top', 'bottom', 'left', 'right'].includes(v)
  },
  theme: {
    type: String,
    default: 'primary',
    validator: v =>
      ['dark', 'light', 'primary', 'success', 'warning', 'danger'].includes(v)
  },
  delay: { type: Number, default: 120 },
  trigger: {
    type: String,
    default: 'hover',
    validator: v => ['hover', 'click', 'focus'].includes(v)
  }
})

const isVisible = ref(false)
const tooltipRef = ref(null)
const triggerRef = ref(null)
const tooltipStyle = ref({})
let showTimeout, hideTimeout
const tooltipId = `tt_${Math.random().toString(36).substr(2, 8)}`

const positionClass = computed(() => `tt-${props.position}`)
const themeClass = computed(() => `tt-${props.theme}`)

function openTooltip() {
  clearTimeout(hideTimeout)
  showTimeout = setTimeout(() => {
    isVisible.value = true
    nextTick(positionTooltip)
  }, props.delay)
}
function closeTooltip() {
  clearTimeout(showTimeout)
  hideTimeout = setTimeout(() => { isVisible.value = false }, 90)
}

// Unified trigger
function trigger(evType) {
  if (props.trigger === 'hover') {
    if (evType === 'mouseenter') openTooltip()
    if (evType === 'mouseleave') closeTooltip()
  }
  if (props.trigger === 'focus') {
    if (evType === 'focus') openTooltip()
    if (evType === 'blur') closeTooltip()
  }
  if (props.trigger === 'click' && evType === 'click') {
    isVisible.value ? closeTooltip() : openTooltip()
  }
}

// Escape tuşu ile kapatma (accessibility)
function onKeydown(e) {
  if (e.key === 'Escape') closeTooltip()
}
watch(isVisible, val => {
  if (val) window.addEventListener('keydown', onKeydown)
  else window.removeEventListener('keydown', onKeydown)
})

function positionTooltip() {
  const tip = tooltipRef.value
  const triggerEl = triggerRef.value
  if (!tip || !triggerEl) return

  const rect = triggerEl.getBoundingClientRect()
  const tipRect = tip.getBoundingClientRect()
  let top = 0, left = 0

  switch (props.position) {
    case 'top':
      top = rect.top - tipRect.height - 10
      left = rect.left + (rect.width - tipRect.width) / 2
      break
    case 'bottom':
      top = rect.bottom + 10
      left = rect.left + (rect.width - tipRect.width) / 2
      break
    case 'left':
      top = rect.top + (rect.height - tipRect.height) / 2
      left = rect.left - tipRect.width - 10
      break
    case 'right':
      top = rect.top + (rect.height - tipRect.height) / 2
      left = rect.right + 10
      break
  }
  // Viewport sınırı kontrolü
  if (top < 6) top = 8
  if (left < 6) left = 8
  if (top + tipRect.height > window.innerHeight) {
    top = window.innerHeight - tipRect.height - 8
  }
  if (left + tipRect.width > window.innerWidth) {
    left = window.innerWidth - tipRect.width - 8
  }

  tooltipStyle.value = {
    top: `${top}px`,
    left: `${left}px`
  }
}

onBeforeUnmount(() => {
  clearTimeout(showTimeout)
  clearTimeout(hideTimeout)
  window.removeEventListener('keydown', onKeydown)
})
</script>

<style scoped>
/* Tooltip Brandkit Theme Styles */
.tt {
  min-width: 44px;
  max-width: 320px;
  pointer-events: none;
  filter: drop-shadow(0 4px 16px #2A183D77);
  font-family: inherit;
  font-size: 0.97em;
  animation: tooltip-in 0.18s cubic-bezier(.52,1.52,.5,1);
}
@keyframes tooltip-in {
  0% { opacity: 0; transform: scale(.92);}
  100% { opacity: 1; transform: scale(1);}
}
.tt-content {
  position: relative;
  z-index: 2;
  font-weight: 500;
  border-radius: 1rem;
  box-shadow: 0 6px 28px #9D38CF33;
  user-select: none;
}
.tt-primary .tt-content {
  background: linear-gradient(92deg, #5A1188 0%, #9D38CF 100%);
  color: #fff;
}
.tt-dark .tt-content {
  background: #231541;
  color: #fff;
  border: 1px solid #2A183D;
}
.tt-light .tt-content {
  background: #fff;
  color: #2A183D;
  border: 1px solid #9D38CF44;
}
.tt-success .tt-content {
  background: linear-gradient(92deg, #10b981 0%, #34d399 100%);
  color: #fff;
}
.tt-warning .tt-content {
  background: linear-gradient(92deg, #f59e0b 0%, #fbbf24 100%);
  color: #2A183D;
}
.tt-danger .tt-content {
  background: linear-gradient(92deg, #ef4444 0%, #f87171 100%);
  color: #fff;
}
/* Tooltip arrow (with theme support) */
.tt-arrow {
  position: absolute;
  width: 0; height: 0;
  z-index: 1;
}
.tt-top .tt-arrow {
  left: 50%; bottom: -10px; transform: translateX(-50%);
  border-left: 9px solid transparent;
  border-right: 9px solid transparent;
  border-top: 10px solid;
  border-top-color: inherit;
}
.tt-bottom .tt-arrow {
  left: 50%; top: -10px; transform: translateX(-50%);
  border-left: 9px solid transparent;
  border-right: 9px solid transparent;
  border-bottom: 10px solid;
  border-bottom-color: inherit;
}
.tt-left .tt-arrow {
  top: 50%; right: -10px; transform: translateY(-50%);
  border-top: 9px solid transparent;
  border-bottom: 9px solid transparent;
  border-left: 10px solid;
  border-left-color: inherit;
}
.tt-right .tt-arrow {
  top: 50%; left: -10px; transform: translateY(-50%);
  border-top: 9px solid transparent;
  border-bottom: 9px solid transparent;
  border-right: 10px solid;
  border-right-color: inherit;
}
/* Tooltip arrow color match for each theme */
.tt-primary .tt-arrow,
.tt-success .tt-arrow,
.tt-warning .tt-arrow,
.tt-danger .tt-arrow {
  border-top-color: #9D38CF;
  border-bottom-color: #9D38CF;
  border-left-color: #9D38CF;
  border-right-color: #9D38CF;
}
.tt-dark .tt-arrow {
  border-top-color: #231541;
  border-bottom-color: #231541;
  border-left-color: #231541;
  border-right-color: #231541;
}
.tt-light .tt-arrow {
  border-top-color: #fff;
  border-bottom-color: #fff;
  border-left-color: #fff;
  border-right-color: #fff;
}
/* Tooltip fade scale animation */
.tt-fade-scale-enter-active,
.tt-fade-scale-leave-active {
  transition: opacity 0.16s cubic-bezier(.52,1.52,.5,1), transform 0.19s;
}
.tt-fade-scale-enter-from, .tt-fade-scale-leave-to {
  opacity: 0;
  transform: scale(0.94);
}
</style>
