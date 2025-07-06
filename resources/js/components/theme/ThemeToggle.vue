<!--
  Proje: Kurumsal Sağlık Platformu - Tema Toggle Bileşeni
  Developer: DocraTech Team - Fatih Berkay Bahceci
  Brandkit uyumlu, mikro animasyonlu ve erişilebilir tema butonu
-->

<template>
  <button
    class="theme-toggle flex items-center relative group focus:outline-none"
    :class="[
      sizeClass,
      { 'theme-toggle-with-label': showLabel }
    ]"
    @click="toggleTheme"
    :title="toggleTitle"
    :aria-pressed="isDarkMode"
    :aria-label="toggleTitle"
    tabindex="0"
    @keydown.enter.prevent="toggleTheme"
    @keydown.space.prevent="toggleTheme"
  >
    <span class="sr-only">{{ toggleTitle }}</span>
    <span class="theme-toggle-content flex items-center gap-2 relative z-10">
      <!-- Brandkit SVG Iconlar -->
      <span class="relative inline-block w-[20px] h-[20px] md:w-[24px] md:h-[24px]">
        <!-- Sun Icon (Light) -->
        <svg
          v-show="!isDarkMode"
          key="sun"
          class="absolute inset-0 theme-toggle-icon theme-toggle-light transition-all"
          width="100%" height="100%" viewBox="0 0 24 24"
          fill="none"
          aria-hidden="true"
        >
          <circle cx="12" cy="12" r="5" fill="#FDE68A"/>
          <g stroke="#FBBF24" stroke-width="1.5">
            <line x1="12" y1="2" x2="12" y2="5"/>
            <line x1="12" y1="19" x2="12" y2="22"/>
            <line x1="2" y1="12" x2="5" y2="12"/>
            <line x1="19" y1="12" x2="22" y2="12"/>
            <line x1="4.93" y1="4.93" x2="7.05" y2="7.05"/>
            <line x1="16.95" y1="16.95" x2="19.07" y2="19.07"/>
            <line x1="4.93" y1="19.07" x2="7.05" y2="16.95"/>
            <line x1="16.95" y1="7.05" x2="19.07" y2="4.93"/>
          </g>
        </svg>
        <!-- Moon Icon (Dark) -->
        <svg
          v-show="isDarkMode"
          key="moon"
          class="absolute inset-0 theme-toggle-icon theme-toggle-dark transition-all"
          width="100%" height="100%" viewBox="0 0 24 24"
          fill="none"
          aria-hidden="true"
        >
          <path
            d="M21 13.5A9 9 0 1 1 11.5 3a7 7 0 0 0 9.5 10.5z"
            fill="#9D38CF"
            stroke="#5A1188"
            stroke-width="1.2"
          />
        </svg>
      </span>
      <span
        v-if="showLabel"
        class="theme-toggle-label select-none text-[15px] font-medium tracking-tight"
      >{{ themeLabel }}</span>
    </span>
    <span
      class="theme-toggle-track absolute right-3 z-0"
      aria-hidden="true"
    >
      <span
        class="theme-toggle-thumb"
        :class="{ 'theme-toggle-thumb-dark': isDarkMode }"
      ></span>
    </span>
  </button>
</template>

<script setup>
// Developer: DocraTech Team - Fatih Berkay Bahceci
import { computed, defineProps, defineEmits } from 'vue';

// Brandkit: responsive spacing ve sizing
const props = defineProps({
  theme: {
    type: String,
    default: 'light',
    validator: value => ['light', 'dark', 'auto'].includes(value)
  },
  size: {
    type: String,
    default: 'md',
    validator: value => ['sm', 'md', 'lg'].includes(value)
  },
  showLabel: {
    type: Boolean,
    default: false
  }
});
const emit = defineEmits(['update:theme', 'change']);

// Computed dark-mode (system aware!)
const isDarkMode = computed(() => {
  if (props.theme === 'dark') return true;
  if (props.theme === 'light') return false;
  if (props.theme === 'auto') {
    return window.matchMedia('(prefers-color-scheme: dark)').matches;
  }
  return false;
});
const themeLabel = computed(() =>
  props.theme === 'auto'
    ? (isDarkMode.value ? 'Karanlık' : 'Açık')
    : (props.theme === 'dark' ? 'Karanlık' : 'Açık')
);
const toggleTitle = computed(() =>
  `Temayı ${isDarkMode.value ? 'açık' : 'karanlık'} moda geçir`
);

// Buton boyut class
const sizeClass = computed(() => ({
  'px-3 py-2 text-sm min-w-[44px] h-[44px]': props.size === 'md',
  'px-2 py-1 text-xs min-w-[36px] h-[36px]': props.size === 'sm',
  'px-4 py-3 text-base min-w-[52px] h-[52px]': props.size === 'lg'
}));

function toggleTheme() {
  const newTheme = isDarkMode.value ? 'light' : 'dark';
  emit('update:theme', newTheme);
  emit('change', newTheme);
}
</script>

<style scoped>
.theme-toggle {
  /* Brandkit palette ve gradient */
  background: var(--color-bg-panel, #22183A);
  border: 2px solid var(--color-border, #6D488D);
  border-radius: var(--border-radius, 1.5rem);
  font-family: var(--font-family, Inter);
  color: var(--color-text, #FFF);
  font-weight: 500;
  outline: none;
  box-shadow: 0 2px 12px 0 var(--color-shadow, rgba(154,56,207,0.08));
  transition: 
    background 0.28s cubic-bezier(.6,0,.2,1),
    border 0.22s cubic-bezier(.6,0,.2,1),
    color 0.15s,
    box-shadow 0.28s cubic-bezier(.6,0,.2,1);
  overflow: visible;
  min-width: 44px;
}
.theme-toggle:hover,
.theme-toggle:focus-visible {
  background: var(--color-gradient, linear-gradient(90deg, #5A1188 0%, #9D38CF 100%));
  border-color: var(--color-primary-accent, #9D38CF);
  color: #fff;
  box-shadow: 0 4px 24px 0 var(--color-shadow, rgba(154,56,207,0.12));
  outline: none;
}
.theme-toggle:focus-visible {
  box-shadow:
    0 0 0 4px var(--color-border-light, #6D488D),
    0 4px 24px 0 var(--color-shadow, rgba(154,56,207,0.12));
  border-color: var(--color-primary, #5A1188);
}
.theme-toggle-content {
  z-index: 2;
  position: relative;
}
.theme-toggle-label {
  transition: color 0.22s, font-size 0.25s;
  color: var(--color-text-secondary, #E4DDF5);
}
.theme-toggle-track {
  display: inline-block;
  width: 38px;
  height: 20px;
  border-radius: 12px;
  background: var(--color-border-light, #6D488D);
  box-shadow: 0 2px 8px 0 var(--color-shadow, rgba(154,56,207,0.08));
  position: absolute;
  right: 0;
  top: 50%;
  transform: translateY(-50%);
  transition: background 0.25s cubic-bezier(.6,0,.2,1);
  pointer-events: none;
}
.theme-toggle-thumb {
  display: block;
  position: absolute;
  top: 2px;
  left: 2px;
  width: 16px;
  height: 16px;
  border-radius: 50%;
  background: var(--color-bg-card, #24163C);
  box-shadow: 0 2px 8px 0 var(--color-shadow, rgba(154,56,207,0.13));
  transition:
    left 0.29s cubic-bezier(.7,0,.3,1),
    background 0.18s cubic-bezier(.6,0,.2,1),
    box-shadow 0.18s;
}
.theme-toggle-thumb-dark {
  left: 20px;
  background: var(--color-primary-accent, #9D38CF);
  box-shadow: 0 4px 16px 0 rgba(154,56,207,0.22);
}
.theme-toggle-icon {
  transition:
    opacity 0.25s cubic-bezier(.7,0,.3,1),
    transform 0.32s cubic-bezier(.7,0,.3,1);
  opacity: 1;
}
.theme-toggle-light {
  opacity: 1;
  transform: scale(1) rotate(0deg);
}
.theme-toggle-dark {
  opacity: 1;
  transform: scale(1) rotate(0deg);
}
.theme-toggle[aria-pressed="true"] .theme-toggle-light {
  opacity: 0;
  transform: scale(0.7) rotate(40deg);
}
.theme-toggle[aria-pressed="true"] .theme-toggle-dark {
  opacity: 1;
  transform: scale(1.05) rotate(-7deg);
}
.theme-toggle[aria-pressed="false"] .theme-toggle-light {
  opacity: 1;
  transform: scale(1.05) rotate(0deg);
}
.theme-toggle[aria-pressed="false"] .theme-toggle-dark {
  opacity: 0;
  transform: scale(0.7) rotate(-25deg);
}
/* Size classes */
.px-2 { padding-left: 0.5rem; padding-right: 0.5rem; }
.px-3 { padding-left: 0.75rem; padding-right: 0.75rem; }
.px-4 { padding-left: 1rem; padding-right: 1rem; }
.py-1 { padding-top: 0.25rem; padding-bottom: 0.25rem; }
.py-2 { padding-top: 0.5rem; padding-bottom: 0.5rem; }
.py-3 { padding-top: 0.75rem; padding-bottom: 0.75rem; }
/* Responsive icon/track/thumb için ek media query istersen ekleyebilirsin */
.theme-toggle-with-label { min-width: 120px; justify-content: space-between; }
</style>
