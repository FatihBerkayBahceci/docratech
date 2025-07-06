<!--
Developer: DocraTech Team - Fatih Berkay Bahceci
Component: Image (Enterprise, hatasız)
-->
<template>
  <div
    class="relative inline-block overflow-hidden group transition-all"
    :class="roundedClass"
    :style="{ width, height, aspectRatio }"
    role="img"
    :aria-label="alt || 'image'"
    tabindex="0"
  >
    <transition name="fade">
      <!-- IMAGE -->
      <img
        v-if="src && !hasError"
        :src="src"
        :alt="alt"
        class="w-full h-full object-cover block transition-transform duration-300 group-hover:scale-105"
        :class="[fitClass, isLoading ? 'opacity-0' : 'opacity-100']"
        @load="handleLoad"
        @error="handleError"
        loading="lazy"
        draggable="false"
        :style="blur ? { filter: isLoading ? 'blur(16px)' : 'none' } : {}"
      />
      <!-- SKELETON LOADING -->
      <div v-else-if="isLoading && src && !hasError" class="absolute inset-0 w-full h-full z-10 pointer-events-none">
        <div class="skeleton-shimmer bg-gradient-to-r from-[#ede9fe] via-[#9D38CF33] to-[#5A118855]"></div>
      </div>
      <!-- ERROR -->
      <div v-else-if="hasError" class="image-error absolute inset-0 flex flex-col items-center justify-center">
        <svg width="32" height="32" viewBox="0 0 32 32" fill="none">
          <circle cx="16" cy="16" r="15" fill="#fef2f2" stroke="#fecaca" stroke-width="2"/>
          <path d="M16 10v6M16 22h.01" stroke="#ef4444" stroke-width="2" stroke-linecap="round"/>
        </svg>
        <span class="mt-1 text-[#ef4444] text-[.93em] font-medium">Resim yüklenemedi</span>
      </div>
      <!-- FALLBACK -->
      <div v-else-if="fallback && !src" class="image-fallback flex flex-col items-center justify-center w-full h-full">
        <slot name="fallback">
          <svg viewBox="0 0 40 40" width="38" height="38" aria-hidden="true">
            <defs>
              <linearGradient id="avatarGradient" x1="0" y1="0" x2="1" y2="1">
                <stop offset="0%" stop-color="#5A1188"/>
                <stop offset="100%" stop-color="#9D38CF"/>
              </linearGradient>
            </defs>
            <rect x="0" y="0" width="40" height="40" rx="9" fill="url(#avatarGradient)" opacity="0.14"/>
            <path d="M13 27L17.2 21.4C17.6 20.8 18.5 20.7 18.9 21.3L22.2 25.9C22.5 26.3 23.1 26.3 23.4 25.8L27 20" stroke="#9D38CF" stroke-width="2" stroke-linecap="round"/>
            <circle cx="14" cy="15" r="3" fill="#9D38CF"/>
          </svg>
          <span class="mt-2 text-[#6D28D9] font-medium text-[.98em]">{{ fallback }}</span>
        </slot>
      </div>
      <!-- PLACEHOLDER -->
      <div v-else class="image-placeholder flex flex-col items-center justify-center w-full h-full">
        <svg width="36" height="36" viewBox="0 0 40 40" fill="none">
          <rect x="0" y="0" width="40" height="40" rx="8" fill="#f3f4f6"/>
          <path d="M10 27l6-8 6 8 8-12" stroke="#9D38CF" stroke-width="2" stroke-linecap="round"/>
        </svg>
      </div>
    </transition>
  </div>
</template>


<script setup>
import { ref, computed, watch } from 'vue'

const props = defineProps({
  src: String,
  alt: String,
  width: { type: String, default: 'auto' },
  height: { type: String, default: 'auto' },
  aspectRatio: String,
  fit: { type: String, default: 'cover', validator: v => ['cover','contain','fill','none','scale-down'].includes(v) },
  rounded: { type: String, default: 'md', validator: v => ['none','sm','md','lg','full'].includes(v) },
  blur: { type: Boolean, default: false },
  fallback: String
})

const isLoading = ref(!!props.src)
const hasError = ref(false)

const fitClass = computed(() => `object-${props.fit}`)
const roundedClass = computed(() =>
  props.rounded === 'full'
    ? 'rounded-full'
    : props.rounded === 'lg'
      ? 'rounded-2xl'
      : props.rounded === 'md'
        ? 'rounded-xl'
        : props.rounded === 'sm'
          ? 'rounded-md'
          : 'rounded-none'
)

function handleLoad() {
  isLoading.value = false
  hasError.value = false
}
function handleError() {
  isLoading.value = false
  hasError.value = true
}
// Watch for src change
watch(() => props.src, (val) => {
  isLoading.value = !!val
  hasError.value = false
})
</script>

<style scoped>
.skeleton-shimmer {
  width: 100%;
  height: 100%;
  border-radius: inherit;
  animation: shimmer 1.45s infinite linear;
  background: linear-gradient(90deg,#ede9fe 0%,#9D38CF22 40%,#ede9fe 100%);
  background-size: 200% 100%;
}
@keyframes shimmer {
  0% { background-position: 200% 0; }
  100% { background-position: -200% 0; }
}
.image-fallback, .image-placeholder {
  background: linear-gradient(120deg, #f9fafb 70%, #ede9fe 100%);
  border-radius: inherit;
  color: #9D38CF;
  min-height: 100%;
  min-width: 100%;
  text-align: center;
  animation: fallback-in .3s cubic-bezier(.22,1,.36,1);
}
@keyframes fallback-in {
  0% { opacity: 0; transform: scale(.93);}
  100% { opacity: 1; transform: scale(1);}
}
.image-error {
  background: #fef2f2;
  border-radius: inherit;
  color: #ef4444;
  border: 1px solid #fecaca;
  animation: error-in .28s cubic-bezier(.22,1,.36,1);
}
@keyframes error-in {
  0% { opacity: 0; transform: scale(.93);}
  100% { opacity: 1; transform: scale(1);}
}

/* Fade Transition */
.fade-enter-active, .fade-leave-active { transition: opacity .2s; }
.fade-enter-from, .fade-leave-to { opacity: 0; }
.group:focus-visible {
  outline: 2px solid #5A1188;
  outline-offset: 2px;
}
</style>
