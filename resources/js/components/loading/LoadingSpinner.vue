<!--
Developer: DocraTech Team - Fatih Berkay Bahceci
Component: LoadingSpinner (Enterprise/Brand)
Project: DocraTech Medical Website Platform
License: MIT
-->

<template>
  <div
    class="relative w-full flex flex-col items-center justify-center py-16 px-4 rounded-2xl bg-[#181124] shadow-xl"
    :class="{ 'bg-white': !dark, 'dark': dark }"
    style="min-height: 220px"
    role="status"
    aria-live="polite"
  >
    <!-- Brand Animated Logo (letter by letter) -->
    <div
      v-if="brand"
      class="flex gap-1 items-center justify-center mb-6"
      aria-label="Loading DocraTech"
    >
      <span
        v-for="(letter, i) in docratechArray"
        :key="i"
        class="brand-letter"
        :style="{
          animationDelay: (i * 0.08) + 's',
          background: `linear-gradient(90deg, #5A1188 0%, #9D38CF 100%)`,
          WebkitBackgroundClip: 'text',
          WebkitTextFillColor: 'transparent',
          fontWeight: 700
        }"
      >{{ letter }}</span>
      <!-- Modern Mor Blob Icon -->
      <span class="relative flex items-center ml-3">
        <span class="w-5 h-5 rounded-full bg-gradient-to-tr from-[#5A1188] to-[#9D38CF] animate-pulse shadow-lg shadow-[#9D38CF66]" />
        <span class="absolute left-0 top-0 w-5 h-5 rounded-full blur-[3px] bg-gradient-to-tr from-[#9D38CF77] to-[#5A118855] opacity-60 animate-pulse-slow" />
      </span>
    </div>
    <!-- Spinner & Variants -->
    <div class="relative flex items-center justify-center" :class="spinnerSizeClass">
      <template v-if="variant === 'blob'">
        <span class="absolute animate-blob1 bg-gradient-to-tr from-[#6D28D9] to-[#9D38CF] w-8 h-8 rounded-full opacity-60"></span>
        <span class="absolute animate-blob2 bg-gradient-to-tr from-[#9D38CF] to-[#6D28D9] w-6 h-6 rounded-full opacity-70"></span>
        <span class="absolute animate-blob3 bg-gradient-to-tr from-[#5A1188] to-[#9D38CF] w-4 h-4 rounded-full opacity-80"></span>
      </template>
      <template v-else-if="variant === 'wave'">
        <span
          v-for="i in 7"
          :key="i"
          class="inline-block w-2 h-2 rounded-full bg-gradient-to-tr from-[#5A1188] to-[#9D38CF] mx-1 animate-wave"
          :style="{ animationDelay: (i * 0.12) + 's' }"
        ></span>
      </template>
      <template v-else-if="variant === 'stroke'">
        <svg :class="['animate-spin-slow', 'w-full', 'h-full']" viewBox="0 0 40 40" fill="none">
          <circle
            class="stroke-morph"
            cx="20"
            cy="20"
            r="16"
            stroke="url(#gradient)"
            stroke-width="6"
            stroke-linecap="round"
            fill="none"
            stroke-dasharray="100"
            stroke-dashoffset="60"
          />
          <defs>
            <linearGradient id="gradient" x1="0" y1="0" x2="40" y2="40" gradientUnits="userSpaceOnUse">
              <stop stop-color="#5A1188"/>
              <stop offset="1" stop-color="#9D38CF"/>
            </linearGradient>
          </defs>
        </svg>
      </template>
      <template v-else>
        <!-- Classic Spinner -->
        <span class="w-full h-full border-4 border-[#ede9fe] border-t-[#5A1188] rounded-full animate-spin"></span>
      </template>
      <!-- Glow (always) -->
      <span class="absolute inset-0 m-auto w-full h-full rounded-full bg-gradient-to-tr from-[#5A118811] to-[#9D38CF33] blur-xl opacity-40 pointer-events-none" />
    </div>
    <!-- Loading Text -->
    <p v-if="text && !brand" class="mt-8 text-lg font-medium text-[#6D28D9] tracking-widest animate-textpulse">
      {{ text }}
    </p>
    <!-- A11y Hidden -->
    <span class="sr-only">Yükleniyor...</span>
  </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  text: { type: String, default: 'Yükleniyor...' },
  size: { type: String, default: 'md' }, // xs, sm, md, lg, xl
  variant: { type: String, default: 'classic' }, // classic, blob, wave, stroke
  brand: { type: Boolean, default: false }, // DocraTech yazılı animasyon
  dark: { type: Boolean, default: false }
})

const docratechArray = 'DocraTech'.split('')

const spinnerSizeClass = computed(() => ({
  xs: 'w-8 h-8',
  sm: 'w-10 h-10',
  md: 'w-16 h-16',
  lg: 'w-24 h-24',
  xl: 'w-32 h-32'
}[props.size] || 'w-16 h-16'))
</script>

<style scoped>
/* Brand Letter Animation */
.brand-letter {
  font-size: 2.3rem;
  line-height: 1;
  display: inline-block;
  opacity: 0.88;
  animation: fadeInLetter 0.4s cubic-bezier(0.22, 1, 0.36, 1) both;
}

.brand-letter:nth-child(1) { animation-delay: 0s; }
.brand-letter:nth-child(2) { animation-delay: 0.08s; }
.brand-letter:nth-child(3) { animation-delay: 0.16s; }
.brand-letter:nth-child(4) { animation-delay: 0.24s; }
.brand-letter:nth-child(5) { animation-delay: 0.32s; }
.brand-letter:nth-child(6) { animation-delay: 0.40s; }
.brand-letter:nth-child(7) { animation-delay: 0.48s; }
.brand-letter:nth-child(8) { animation-delay: 0.56s; }
.brand-letter:nth-child(9) { animation-delay: 0.64s; }

@keyframes fadeInLetter {
  0% { opacity: 0; transform: translateY(60%);}
  100% { opacity: 0.88; transform: translateY(0);}
}

/* Brand blob micro anims */
@keyframes blob1 { 0%,100%{transform:scale(1) translate(0,0);} 33%{transform:scale(1.2) translate(4px,-6px);} 66%{transform:scale(0.8) translate(-2px,4px);} }
@keyframes blob2 { 0%,100%{transform:scale(1) translate(0,0);} 25%{transform:scale(0.9) translate(-3px,2px);} 80%{transform:scale(1.2) translate(5px,-3px);} }
@keyframes blob3 { 0%,100%{transform:scale(1) translate(0,0);} 60%{transform:scale(1.2) translate(2px,4px);} }

.animate-blob1 { animation: blob1 1.9s infinite cubic-bezier(.7,.2,.3,1); }
.animate-blob2 { animation: blob2 2.2s infinite cubic-bezier(.7,.2,.3,1) 0.18s; }
.animate-blob3 { animation: blob3 2.3s infinite cubic-bezier(.7,.2,.3,1) 0.32s; }

/* Wave dots */
@keyframes wavedot {
  0%, 80%, 100% { transform: scale(0.7); opacity: 0.5;}
  40% { transform: scale(1.3); opacity: 1;}
}
.animate-wave {
  animation: wavedot 1.5s infinite cubic-bezier(.4,0,.2,1);
}

/* Slow spin for stroke */
.animate-spin-slow {
  animation: spin 2.4s linear infinite;
}
@keyframes spin { 0% { transform: rotate(0deg);} 100% { transform: rotate(360deg);} }

/* Glow & pulse for loading */
@keyframes glowpulse {
  0%,100% { opacity: 0.38; transform: scale(1);}
  60% { opacity: 0.7; transform: scale(1.12);}
}
.animate-pulse-slow { animation: glowpulse 2.2s infinite cubic-bezier(.4,0,.2,1); }

/* Text pulse */
@keyframes textpulse {
  0%,100% { opacity: 0.7;}
  50% { opacity: 1;}
}
.animate-textpulse { animation: textpulse 2.2s infinite; }

</style>
