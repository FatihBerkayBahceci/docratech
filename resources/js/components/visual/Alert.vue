<!--
  Project: Kurumsal Sağlık Platformu Frontend
  Component: Alert (Enterprise-Ready, Marka Uyumlu)
  Developer: DocraTech Team - Fatih Berkay Bahceci
-->

<template>
  <Transition
    name="alert"
    appear
    @enter="onEnter"
    @leave="onLeave"
  >
    <div
      v-if="visible"
      :class="alertClasses"
      :style="alertStyles"
      role="alert"
      aria-live="assertive"
      tabindex="0"
      @keydown.esc="dismissible && handleDismiss"
    >
      <div class="flex items-start gap-3">
        <Icon
          v-if="iconToShow"
          :name="iconToShow"
          :size="iconSize"
          :class="[
            'flex-shrink-0 mt-0.5',
            'drop-shadow-sm',
            'animate-alert-icon',
            iconColorClass,
          ]"
          :animated="animated"
        />
        <div class="flex-1 min-w-0">
          <h4
            v-if="title"
            class="text-base md:text-lg font-semibold mb-0.5 font-inter tracking-tight"
            :class="{
              'text-[#9D38CF]': variant === 'primary',
              'text-[#5A1188]': variant === 'info',
              'text-[#1ee9bc]': variant === 'success',
              'text-[#FFD600]': variant === 'warning',
              'text-[#FB2773]': variant === 'danger',
              'drop-shadow-md': animated
            }"
          >
            {{ title }}
          </h4>
          <div
            class="text-sm md:text-base font-inter text-[#fff] opacity-90 break-words"
          >
            <slot />
          </div>
        </div>
        <div v-if="dismissible" class="ml-2 flex-shrink-0">
          <button
            type="button"
            class="inline-flex items-center justify-center w-7 h-7 rounded-full transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-[#9D38CF88] hover:bg-[#2A183D] hover:text-[#9D38CF] active:scale-90"
            @click="handleDismiss"
            aria-label="Alertı Kapat"
            @keydown.enter.space.prevent="handleDismiss"
          >
            <Icon name="x" size="xs" />
          </button>
        </div>
      </div>
      <div v-if="actions" class="mt-3 flex flex-row gap-2 animate-fade-in-up">
        <slot name="actions" />
      </div>
      <!-- Animated bottom border as micro-interaction -->
      <div class="h-1 rounded-b-xl mt-2 overflow-hidden" v-if="animated">
        <div :class="['w-full h-full', borderBarClass, 'animate-alert-bar']" />
      </div>
    </div>
  </Transition>
</template>

<script setup>
import { computed } from 'vue'
import Icon from './Icon.vue'

const props = defineProps({
  variant: {
    type: String,
    default: 'info',
    validator: (v) =>
      ['info', 'success', 'warning', 'danger', 'primary'].includes(v),
  },
  title: { type: String, default: null },
  icon: { type: String, default: null },
  dismissible: { type: Boolean, default: false },
  animated: { type: Boolean, default: true },
  visible: { type: Boolean, default: true },
  actions: { type: Boolean, default: false },
})
const emit = defineEmits(['dismiss'])

const variantKit = {
  info: {
    bg: 'bg-[#2A183D]/85',
    border: 'border-[#5A1188]/80',
    text: 'text-[#F6F3FF]',
    icon: 'info-circle',
    iconColor: 'text-[#5A1188]',
    bar: 'bg-gradient-to-r from-[#6D488D] via-[#5A1188] to-[#9D38CF]',
  },
  success: {
    bg: 'bg-[#181124]/90',
    border: 'border-[#1ee9bc]/70',
    text: 'text-[#D3FFF3]',
    icon: 'check-circle',
    iconColor: 'text-[#1ee9bc]',
    bar: 'bg-gradient-to-r from-[#1ee9bc] via-[#9D38CF] to-[#6D488D]',
  },
  warning: {
    bg: 'bg-[#2A183D]/90',
    border: 'border-[#FFD600]/60',
    text: 'text-[#FFD600]',
    icon: 'exclamation-triangle',
    iconColor: 'text-[#FFD600]',
    bar: 'bg-gradient-to-r from-[#FFD600] via-[#9D38CF] to-[#6D488D]',
  },
  danger: {
    bg: 'bg-[#181124]/95',
    border: 'border-[#FB2773]/80',
    text: 'text-[#FB2773]',
    icon: 'x-circle',
    iconColor: 'text-[#FB2773]',
    bar: 'bg-gradient-to-r from-[#FB2773] via-[#9D38CF] to-[#6D488D]',
  },
  primary: {
    bg: 'bg-gradient-to-r from-[#5A1188] to-[#9D38CF]',
    border: 'border-[#9D38CF]/80',
    text: 'text-[#FFF]',
    icon: 'bell',
    iconColor: 'text-[#9D38CF]',
    bar: 'bg-gradient-to-r from-[#5A1188] to-[#9D38CF]',
  },
}

const iconToShow = computed(() =>
  props.icon ? props.icon : variantKit[props.variant].icon,
)
const iconSize = computed(() => (props.title ? 'md' : 'sm'))
const iconColorClass = computed(() => variantKit[props.variant].iconColor)
const borderBarClass = computed(() => variantKit[props.variant].bar)

const alertClasses = computed(() => [
  'relative w-full max-w-2xl mx-auto p-4 border rounded-2xl shadow-2xl font-inter transition-all duration-400 select-none group overflow-hidden',
  variantKit[props.variant].bg,
  variantKit[props.variant].border,
  variantKit[props.variant].text,
  'backdrop-blur-xl',
  'focus-within:ring-2 focus-within:ring-[#9D38CF44]',
  'hover:scale-[1.025] hover:shadow-[0_4px_32px_#9D38CF44]',
  { 'cursor-pointer': props.dismissible },
])

const alertStyles = computed(() => ({
  '--alert-scale': props.visible ? 1 : 0.95,
  '--alert-opacity': props.visible ? 1 : 0,
}))

function handleDismiss() {
  emit('dismiss')
}

function onEnter(el) {
  if (props.animated) {
    el.style.transform = 'scale(0.93) translateY(-16px)'
    el.style.opacity = '0'
    el.style.boxShadow = '0 8px 40px #5A118833'
    requestAnimationFrame(() => {
      el.style.transition = 'all 0.5s cubic-bezier(0.42, 0, 0.18, 1.14)'
      el.style.transform = 'scale(1) translateY(0)'
      el.style.opacity = '1'
    })
  }
}

function onLeave(el) {
  if (props.animated) {
    el.style.transition = 'all 0.33s cubic-bezier(0.44, 0.05, 0.3, 1)'
    el.style.transform = 'scale(0.91) translateY(-18px)'
    el.style.opacity = '0'
  }
}
</script>

<style scoped>
/* Alert appear & leave */
.alert-enter-active, .alert-leave-active {
  transition: all 0.44s cubic-bezier(0.44, 0.05, 0.3, 1);
  will-change: transform, opacity;
}
.alert-enter-from, .alert-leave-to {
  opacity: 0;
  transform: scale(0.93) translateY(-14px);
}

/* Micro: Icon shimmer */
@keyframes alert-icon {
  0% { filter: drop-shadow(0 0 2px #9D38CF88);}
  50% { filter: drop-shadow(0 0 8px #9D38CF) brightness(1.2);}
  100% { filter: drop-shadow(0 0 2px #9D38CF88);}
}
.animate-alert-icon {
  animation: alert-icon 2.2s infinite;
}

/* Micro: Entrance border bar */
@keyframes alert-bar {
  0% { width: 0%; opacity: 0.6;}
  60% { width: 65%; opacity: 0.8;}
  100% { width: 100%; opacity: 1;}
}
.animate-alert-bar {
  animation: alert-bar 1.6s cubic-bezier(0.65, 0, 0.35, 1) both;
}

/* Micro: Fade in up for actions */
@keyframes fade-in-up {
  from { opacity: 0; transform: translateY(12px);}
  to   { opacity: 1; transform: none;}
}
.animate-fade-in-up {
  animation: fade-in-up 0.35s cubic-bezier(0.44, 0.05, 0.3, 1);
}
</style>
