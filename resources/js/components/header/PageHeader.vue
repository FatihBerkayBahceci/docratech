<!--
  Project: Corporate Healthcare Platform UI Library
  Component: PageHeader
  Developer: DocraTech Team - Fatih Berkay Bahceci
-->

<template>
  <header
    :class="[
      'w-full',
      'transition-all',
      sizeClass,
      variantClass,
      'mb-8'
    ]"
    role="banner"
  >
    <nav v-if="$slots.breadcrumb" class="mb-3 pt-2 border-t border-[#2A183D]/60 dark:border-[#3e255b]/40 animate-fadeIn" aria-label="Breadcrumb">
      <slot name="breadcrumb" />
    </nav>

    <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-5">
      <div class="flex-1 min-w-0">
        <h1
          v-if="title"
          class="text-white text-2xl md:text-3xl font-semibold tracking-tight leading-tight animate-slideInDown"
        >
          {{ title }}
        </h1>
        <p
          v-if="subtitle"
          class="mt-2 text-[#B1A9C7] text-base md:text-lg font-normal animate-fadeIn"
        >
          {{ subtitle }}
        </p>
        <div class="mt-2 animate-fadeIn">
          <slot name="description" />
        </div>
      </div>
      <div
        v-if="$slots.actions"
        class="flex flex-row flex-wrap items-center gap-2 md:gap-3 mt-4 md:mt-0 animate-slideInRight"
      >
        <slot name="actions" />
      </div>
    </div>
  </header>
</template>

<script setup>
// Developer: DocraTech Team - Fatih Berkay Bahceci

import { computed } from 'vue'

const props = defineProps({
  title: String,
  subtitle: String,
  size: { type: String, default: 'default', validator: v => ['small', 'default', 'large'].includes(v) },
  variant: { type: String, default: 'default', validator: v => ['default', 'elevated', 'minimal'].includes(v) }
})

const sizeClass = computed(() => ({
  small: 'pt-4 pb-3 mb-4',
  default: 'pt-6 pb-6 mb-8',
  large: 'pt-10 pb-8 mb-10'
}[props.size] || 'pt-6 pb-6'))

const variantClass = computed(() => ({
  default: 'border-b border-[#2A183D]/80',
  elevated: 'rounded-2xl bg-gradient-to-br from-[#2A183D]/90 to-[#181124]/95 shadow-lg border-none px-6',
  minimal: 'border-none bg-transparent px-0 py-3'
}[props.variant] || 'border-b border-[#2A183D]/80'))
</script>

<style scoped>
@keyframes fadeIn {
  0% { opacity: 0;}
  100% { opacity: 1;}
}
@keyframes slideInDown {
  0% { opacity: 0; transform: translateY(-12px);}
  100% { opacity: 1; transform: translateY(0);}
}
@keyframes slideInRight {
  0% { opacity: 0; transform: translateX(16px);}
  100% { opacity: 1; transform: translateX(0);}
}
.animate-fadeIn { animation: fadeIn 0.42s cubic-bezier(.4,0,.2,1) both; }
.animate-slideInDown { animation: slideInDown 0.32s cubic-bezier(.4,0,.2,1) both; }
.animate-slideInRight { animation: slideInRight 0.36s cubic-bezier(.4,0,.2,1) both; }
</style>
