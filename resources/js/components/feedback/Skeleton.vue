<!--
  Project: Enterprise Health Skeleton
  Developer: DocraTech Team - Fatih Berkay Bahceci
-->

<template>
  <div
    class="relative skeleton-base overflow-hidden"
    :class="[
      roundedClass,
      typeClass,
      animated ? 'skeleton-animated' : ''
    ]"
    :style="{ width, height }"
    aria-busy="true"
    aria-live="polite"
    aria-label="Loading..."
    tabindex="0"
  >
    <template v-if="type === 'text'">
      <div
        v-for="(line, idx) in lines"
        :key="idx"
        class="skeleton-line"
        :style="{ width: line.width || '100%', height: line.height || '16px' }"
      ></div>
    </template>

    <template v-else-if="type === 'avatar'">
      <div class="skeleton-avatar"></div>
    </template>

    <template v-else-if="type === 'card'">
      <div class="skeleton-card">
        <div class="skeleton-card-header">
          <div class="skeleton-avatar skeleton-avatar-sm"></div>
          <div class="skeleton-card-title"></div>
        </div>
        <div class="skeleton-card-content">
          <div class="skeleton-line"></div>
          <div class="skeleton-line" style="width: 80%"></div>
          <div class="skeleton-line" style="width: 60%"></div>
        </div>
      </div>
    </template>

    <template v-else-if="type === 'table'">
      <div class="skeleton-table">
        <div class="skeleton-table-header">
          <div v-for="i in 4" :key="i" class="skeleton-table-cell"></div>
        </div>
        <div v-for="row in 3" :key="row" class="skeleton-table-row">
          <div v-for="cell in 4" :key="cell" class="skeleton-table-cell"></div>
        </div>
      </div>
    </template>

    <slot v-else></slot>
  </div>
</template>

<script setup>
// Project: Enterprise Health Skeleton
// Developer: DocraTech Team - Fatih Berkay Bahceci

import { computed } from 'vue'

const props = defineProps({
  type: {
    type: String,
    default: 'block',
    validator: v => ['block', 'text', 'avatar', 'card', 'table'].includes(v)
  },
  width: { type: String, default: '100%' },
  height: { type: String, default: '20px' },
  rounded: {
    type: String,
    default: 'md',
    validator: v => ['none', 'sm', 'md', 'lg', 'full'].includes(v)
  },
  animated: { type: Boolean, default: true },
  lines: {
    type: Array,
    default: () => [
      { width: '100%', height: '16px' },
      { width: '80%', height: '16px' },
      { width: '60%', height: '16px' }
    ]
  }
})

const roundedClass = computed(() => ({
  'rounded-none': props.rounded === 'none',
  'rounded-sm': props.rounded === 'sm',
  'rounded-md': props.rounded === 'md',
  'rounded-lg': props.rounded === 'lg',
  'rounded-full': props.rounded === 'full'
}))
const typeClass = computed(() => ({
  'flex flex-col gap-2': props.type === 'text'
}))
</script>

<style scoped>
/* Base brandkit skeleton gradient & shimmer */
.skeleton-base {
  background: linear-gradient(90deg, #2A183D 0%, #181124 100%);
  min-width: 32px;
  min-height: 16px;
}
.skeleton-animated::after {
  content: '';
  position: absolute;
  top: 0; left: -120%;
  width: 120%; height: 100%;
  background: linear-gradient(
    92deg,
    transparent 0%,
    #9D38CF55 35%,
    #6D488D77 45%,
    #5A118855 60%,
    transparent 100%
  );
  animation: skeleton-shimmer 1.2s infinite linear;
  z-index: 1;
  pointer-events: none;
}
@keyframes skeleton-shimmer {
  100% { left: 100%; }
}

/* Text skeleton lines */
.skeleton-line {
  background: #2A183D;
  border-radius: 8px;
  min-height: 12px;
  animation: skeleton-pulse 1.8s ease-in-out infinite;
  z-index: 2;
}
@keyframes skeleton-pulse {
  0%, 100% { opacity: 0.65; }
  50% { opacity: 1; }
}
/* Avatar skeleton */
.skeleton-avatar {
  width: 40px; height: 40px;
  border-radius: 50%;
  background: #2A183D;
  z-index: 2;
}
.skeleton-avatar-sm { width: 32px; height: 32px; }
.skeleton-avatar-lg { width: 64px; height: 64px; }
/* Card skeleton */
.skeleton-card {
  background: #181124;
  border-radius: 18px;
  padding: 18px;
  box-shadow: 0 2px 16px #5A118822;
  border: 1px solid #2A183D;
}
.skeleton-card-header {
  display: flex;
  align-items: center;
  gap: 14px;
  margin-bottom: 18px;
}
.skeleton-card-title {
  width: 60%;
  height: 18px;
  background: #2A183D;
  border-radius: 8px;
  animation: skeleton-pulse 1.7s ease-in-out infinite;
}
.skeleton-card-content {
  display: flex;
  flex-direction: column;
  gap: 8px;
}
/* Table skeleton */
.skeleton-table {
  background: #181124;
  border-radius: 18px;
  overflow: hidden;
  box-shadow: 0 2px 12px #5A118822;
  border: 1px solid #2A183D;
}
.skeleton-table-header {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 1px;
  background: #2A183D;
  padding: 1px;
}
.skeleton-table-row {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 1px;
  background: #2A183D;
  padding: 1px;
}
.skeleton-table-cell {
  background: #181124;
  height: 38px;
  animation: skeleton-pulse 1.8s ease-in-out infinite;
  z-index: 2;
}
.skeleton-table-header .skeleton-table-cell {
  background: #231541;
  height: 42px;
}

/* Brandkit radius classes */
.rounded-none { border-radius: 0 !important; }
.rounded-sm { border-radius: 6px !important; }
.rounded-md { border-radius: 12px !important; }
.rounded-lg { border-radius: 18px !important; }
.rounded-full { border-radius: 50% !important; }

/* Accessibility: focus style */
.skeleton-base:focus-visible {
  outline: 2.5px solid #9D38CF;
  outline-offset: 2px;
}

/* Responsive adjustments */
@media (max-width: 640px) {
  .skeleton-card, .skeleton-table {
    padding: 12px;
    border-radius: 12px;
  }
}
</style>
