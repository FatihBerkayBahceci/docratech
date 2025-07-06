<!--
  Project: Kurumsal Sağlık Platformu Frontend
  Component: AccordionItem (Enterprise-Ready)
  Developer: DocraTech Team - Fatih Berkay Bahceci
-->

<template>
  <div
    :class="itemClasses"
    :style="itemStyles"
    :tabindex="disabled ? -1 : 0"
    @keydown.enter.space.prevent="!disabled && toggle()"
    @focus="onFocus"
    @blur="onBlur"
    aria-disabled="disabled"
    data-accordion-item
  >
    <button
      type="button"
      :id="`accordion-header-${id}`"
      :class="headerClasses"
      @click="toggle"
      :aria-expanded="isOpen"
      :aria-controls="`accordion-content-${id}`"
      :disabled="disabled"
      :tabindex="disabled ? -1 : 0"
      class="outline-none group transition-all"
      ref="headerRef"
    >
      <div class="flex items-center justify-between w-full">
        <div class="flex items-center gap-3">
          <Icon
            v-if="icon"
            :name="icon"
            size="md"
            class="text-[#9D38CF] transition-transform duration-400"
            :class="{
              'scale-105 drop-shadow-md': isOpen,
              'opacity-60': disabled,
            }"
            :animated="accordionAnimated"
          />
          <div class="text-left">
            <h3
              class="text-base md:text-lg font-semibold text-white drop-shadow-sm transition-all"
              :class="{'text-[#9D38CF]': isOpen, 'text-[#6D488D]': !isOpen}"
            >
              {{ title }}
            </h3>
            <p
              v-if="subtitle"
              class="text-xs md:text-sm text-[#b9a7cf] mt-0.5 font-inter"
              :class="{'opacity-90': isOpen, 'opacity-70': !isOpen}"
            >
              {{ subtitle }}
            </p>
          </div>
        </div>
        <Icon
          :name="isOpen ? 'chevron-up' : 'chevron-down'"
          size="sm"
          class="transition-transform duration-400 text-[#9D38CF] group-hover:scale-110"
          :class="{ 'rotate-180': isOpen, 'opacity-40': disabled }"
          :animated="accordionAnimated"
        />
      </div>
      <!-- Ripple mikro etkileşim -->
      <span class="ripple-effect" aria-hidden="true" />
    </button>

    <Transition
      name="accordion-content"
      @enter="onEnter"
      @leave="onLeave"
      appear
    >
      <section
        v-if="isOpen"
        :id="`accordion-content-${id}`"
        :class="contentClasses"
        role="region"
        :aria-labelledby="`accordion-header-${id}`"
        tabindex="0"
      >
        <div class="px-3 md:px-4 pb-4 pt-2 animate-fade-in-up">
          <slot />
        </div>
      </section>
    </Transition>

    <!-- Loading & Skeleton state -->
    <Transition name="fade">
      <div
        v-if="loading"
        class="w-full h-10 rounded-xl bg-gradient-to-r from-[#5A1188] via-[#181124] to-[#9D38CF] animate-pulse opacity-70 mt-3"
      />
    </Transition>
  </div>
</template>

<script setup>
import { computed, inject, ref, watch, onMounted } from 'vue'
import Icon from './Icon.vue'

const props = defineProps({
  title: { type: String, required: true },
  subtitle: { type: String, default: null },
  icon: { type: String, default: null },
  defaultOpen: { type: Boolean, default: false },
  disabled: { type: Boolean, default: false },
  loading: { type: Boolean, default: false },
})

const emit = defineEmits(['toggle', 'open', 'close'])

const id = ref(`accordion-${Math.random().toString(36).substr(2, 9)}`)
const isOpen = ref(props.defaultOpen)
const hasFocus = ref(false)

const accordionVariant = inject('accordionVariant', 'default')
const accordionAnimated = inject('accordionAnimated', true)
const accordionMultiple = inject('accordionMultiple', false)

const itemClasses = computed(() => [
  'accordion-item',
  'rounded-2xl mb-1 transition-all duration-300',
  'shadow-lg shadow-[#5A118824] border border-transparent',
  'bg-[#2A183D]/70 backdrop-blur',
  'hover:shadow-xl hover:border-[#9D38CF]',
  'focus-within:ring-2 focus-within:ring-[#9D38CF99] focus-within:border-[#9D38CF]',
  'outline-none',
  {
    'border border-[#6D488D] bg-[#181124]/80': accordionVariant === 'bordered',
    'bg-[#2A183D] border-0 shadow-[0_2px_16px_#5A118822]': accordionVariant === 'separated',
    'opacity-50 pointer-events-none select-none': props.disabled,
    'scale-105': isOpen.value,
    'ring-2 ring-[#9D38CF88]': hasFocus.value,
  },
])

const headerClasses = computed(() => [
  'w-full flex items-center px-3 md:px-4 py-3 rounded-xl transition-all duration-300',
  'gap-2 focus:outline-none font-inter select-none',
  'cursor-pointer',
  'bg-transparent',
  {
    'bg-gradient-to-r from-[#181124] to-[#2A183D] hover:from-[#5A1188] hover:to-[#9D38CF]': isOpen.value && !props.disabled,
    'hover:bg-[#181124]/70': !isOpen.value && !props.disabled,
    'cursor-not-allowed bg-[#181124]/40': props.disabled,
    'ring-2 ring-[#9D38CF99]': hasFocus.value,
  },
])

const contentClasses = computed(() => [
  'overflow-hidden border-t border-[#6D488D]/30',
  'bg-[#1d1533] rounded-b-2xl shadow-inner shadow-[#9D38CF11] transition-all',
  'animate-accordion-bounce',
])

const itemStyles = computed(() => ({
  '--item-scale': isOpen.value ? 1.02 : 1,
  '--item-shadow': isOpen.value ? '0 8px 24px #5A118855' : '0 2px 8px #18112444',
}))

function toggle() {
  if (props.disabled) return
  isOpen.value = !isOpen.value
  emit('toggle', isOpen.value)
  isOpen.value ? emit('open') : emit('close')
}

function onEnter(el) {
  if (!accordionAnimated) return
  el.style.height = '0'
  el.style.opacity = '0.7'
  el.style.transition = 'all 0.44s cubic-bezier(0.45, 0.01, 0.33, 1)'
  requestAnimationFrame(() => {
    el.style.height = el.scrollHeight + 'px'
    el.style.opacity = '1'
  })
}
function onLeave(el) {
  if (!accordionAnimated) return
  el.style.transition = 'all 0.35s cubic-bezier(0.44, 0.05, 0.3, 1)'
  el.style.height = '0'
  el.style.opacity = '0'
}

function onFocus() {
  hasFocus.value = true
}
function onBlur() {
  hasFocus.value = false
}

// Accessible default open toggle
onMounted(() => {
  if (props.defaultOpen) emit('open')
})

</script>

<style scoped>
/* Macro accordion animation (height, opacity) */
.accordion-content-enter-active,
.accordion-content-leave-active {
  transition: all 0.44s cubic-bezier(0.44, 0.05, 0.3, 1);
  will-change: height, opacity;
}
.accordion-content-enter-from,
.accordion-content-leave-to {
  opacity: 0;
  height: 0;
  transform: translateY(-10px) scaleY(0.99);
}
.accordion-content-enter-to,
.accordion-content-leave-from {
  opacity: 1;
  height: auto;
  transform: none;
}

/* Micro: bounce, focus */
@keyframes accordion-bounce {
  0% { transform: scale(0.97);}
  60% { transform: scale(1.02);}
  100% { transform: scale(1);}
}
.animate-accordion-bounce {
  animation: accordion-bounce 0.35s;
}

/* Ripple effect for click/press */
.ripple-effect {
  position: absolute;
  left: 50%; top: 50%;
  transform: translate(-50%,-50%);
  width: 0; height: 0;
  border-radius: 9999px;
  background: radial-gradient(circle, #9D38CF44 40%, transparent 80%);
  pointer-events: none;
  opacity: 0.2;
  transition: width 0.35s, height 0.35s, opacity 0.4s;
}
button:active .ripple-effect {
  width: 180%;
  height: 300%;
  opacity: 0.3;
  transition: width 0.2s, height 0.25s, opacity 0.3s;
}

/* Fade micro for skeleton loading */
.fade-enter-active, .fade-leave-active {
  transition: opacity 0.35s cubic-bezier(0.44, 0.05, 0.3, 1);
}
.fade-enter-from, .fade-leave-to {
  opacity: 0;
}

/* Animate slot content fade-in-up */
@keyframes fade-in-up {
  from { opacity: 0; transform: translateY(14px);}
  to   { opacity: 1; transform: none;}
}
.animate-fade-in-up {
  animation: fade-in-up 0.38s cubic-bezier(0.44, 0.05, 0.3, 1);
}
</style>
