<!--
  Proje: Kurumsal Sağlık Platformu UI Core Theme Provider
  Developer: DocraTech Team - Fatih Berkay Bahceci
  Brandkit'e %100 uyumlu, modern ve animasyonlu ThemeProvider bileşeni
-->

<template>
  <div
    class="theme-provider relative min-h-screen flex flex-col transition-colors duration-500"
    :class="{
      'theme-dark': isDarkMode,
      'theme-light': !isDarkMode
    }"
    :data-theme="isDarkMode ? 'dark' : 'light'"
    tabindex="0"
    aria-live="polite"
    @keydown.esc="emitThemeReset"
  >
    <!-- Mikro animasyon: Fade-in ve slot geçişleri -->
    <Transition name="fade-slide" mode="out-in">
      <slot />
    </Transition>

    <!-- Theme değişimi gösterimi (micro interaction) -->
    <transition name="theme-badge">
      <span
        v-if="showThemeBadge"
        class="fixed bottom-6 right-6 z-[9999] px-4 py-2 rounded-2xl shadow-lg bg-gradient-to-r from-docratech-primaryDark to-docratech-primaryLight text-white text-xs font-semibold tracking-wider select-none pointer-events-none animate-bounce"
        aria-live="polite"
        aria-atomic="true"
      >
        {{ isDarkMode ? 'Karanlık Mod' : 'Aydınlık Mod' }}
      </span>
    </transition>
  </div>
</template>

<script setup>
// Developer: DocraTech Team - Fatih Berkay Bahceci
import { ref, computed, watch, onMounted, onBeforeUnmount, nextTick } from 'vue';

// Props tanımı (TypeScript-like)
const props = defineProps({
  theme: {
    type: String,
    default: 'auto', // "light" | "dark" | "auto"
    validator: value => ['light', 'dark', 'auto'].includes(value)
  },
  darkMode: {
    type: Boolean,
    default: false
  },
  autoDetect: {
    type: Boolean,
    default: true
  }
});

const emit = defineEmits(['theme-change', 'theme-reset']);

// Refs & states
const systemDark = ref(false);
const isMounted = ref(false);
const showThemeBadge = ref(false);
let badgeTimeout = null;

// Computed: Aktif dark mode?
const isDarkMode = computed(() => {
  if (props.theme === 'dark') return true;
  if (props.theme === 'light') return false;
  if (props.theme === 'auto' && props.autoDetect) return systemDark.value;
  return props.darkMode;
});

// CSS değişkenlerini güncelle
function setThemeVars(mode) {
  const variables = mode === 'dark' ? darkVars : lightVars;
  Object.entries(variables).forEach(([key, value]) => {
    document.documentElement.style.setProperty(key, value);
  });
  // Meta theme-color güncelle (mobile browserlar için)
  const meta = document.querySelector('meta[name="theme-color"]');
  if (meta) meta.setAttribute('content', variables['--color-background']);
}

// DocRatech Brandkit + Gradient + Enterprise palette (tümü custom)
const lightVars = {
  '--color-primary': '#5A1188', // docratech.primaryDark
  '--color-primary-accent': '#9D38CF', // docratech.primaryLight
  '--color-primary-medium': '#6D488D', // docratech.secondary
  '--color-primary-dark': '#2A183D', // docratech.accent
  '--color-gradient': 'linear-gradient(90deg, #5A1188 0%, #9D38CF 100%)',
  '--color-bg-main': '#181124', // docratech.background
  '--color-bg-panel': '#221B38', // docratech.surface
  '--color-bg-card': '#221B38', // docratech.card
  '--color-bg-hover': '#1A1530', // docratech.cardDark
  '--color-bg-disabled': '#3A2A4D', // docratech.borderLight
  '--color-border': '#2A183D', // docratech.border
  '--color-border-light': '#3A2A4D', // docratech.borderLight
  '--color-border-accent': '#9D38CF', // docratech.primaryLight
  '--color-text': '#F3F0FF', // docratech.text
  '--color-text-secondary': '#B8AEE0', // docratech.textSecondary
  '--color-text-muted': '#7C6FAF', // docratech.textMuted
  '--color-shadow': 'rgba(157, 56, 207, 0.07)',
  '--color-success': '#23D2A2',
  '--color-warning': '#F3B801',
  '--color-danger': '#FF4473',
  '--color-info': '#2ED7E7',
  '--border-radius': '1.5rem',
  '--shadow-xl': '0 16px 64px 0 rgba(157, 56, 207, 0.18)',
  '--transition': 'all 0.35s cubic-bezier(.6,0,.2,1)',
  '--font-family': 'Inter, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Arial, sans-serif'
};

const darkVars = {
  ...lightVars,
  '--color-bg-main': '#18122B', // docratech.backgroundDark
  '--color-bg-panel': '#221B38', // docratech.surface
  '--color-bg-card': '#1A1530', // docratech.cardDark
  '--color-bg-hover': '#2A183D', // docratech.accent
  '--color-bg-disabled': '#3A2A4D', // docratech.borderLight
  '--color-border': '#2A183D', // docratech.border
  '--color-border-light': '#3A2A4D', // docratech.borderLight
  '--color-border-accent': '#9D38CF', // docratech.primaryLight
  '--color-text': '#F3F0FF', // docratech.text
  '--color-text-secondary': '#B8AEE0', // docratech.textSecondary
  '--color-text-muted': '#7C6FAF', // docratech.textMuted
  '--color-shadow': 'rgba(90, 17, 136, 0.25)'
};

// Sistem dark-mode değişimini dinle
function systemThemeListener(e) {
  systemDark.value = e.matches;
}

onMounted(() => {
  // Sistem dark-mode algılama
  if (props.autoDetect) {
    const mediaQuery = window.matchMedia('(prefers-color-scheme: dark)');
    systemDark.value = mediaQuery.matches;
    mediaQuery.addEventListener('change', systemThemeListener);
  }
  setThemeVars(isDarkMode.value ? 'dark' : 'light');
  isMounted.value = true;
});

// Temada değişiklik
watch(isDarkMode, async (val, oldVal) => {
  setThemeVars(val ? 'dark' : 'light');
  emit('theme-change', val ? 'dark' : 'light');
  // Mikro etkileşim: tema rozeti göster
  showThemeBadge.value = true;
  clearTimeout(badgeTimeout);
  badgeTimeout = setTimeout(() => (showThemeBadge.value = false), 1600);
});

watch(() => props.theme, () => setThemeVars(isDarkMode.value ? 'dark' : 'light'));

// Temizleme (memory leak önleme)
onBeforeUnmount(() => {
  if (props.autoDetect) {
    const mediaQuery = window.matchMedia('(prefers-color-scheme: dark)');
    mediaQuery.removeEventListener('change', systemThemeListener);
  }
  clearTimeout(badgeTimeout);
});

// ESC ile tema sıfırla
function emitThemeReset() {
  emit('theme-reset');
}
</script>

<style scoped>
/* Animasyonlar */
.fade-slide-enter-active, .fade-slide-leave-active {
  transition: all 0.45s cubic-bezier(.6,0,.2,1);
}
.fade-slide-enter-from, .fade-slide-leave-to {
  opacity: 0;
  transform: translateY(24px) scale(0.98);
}
.fade-slide-enter-to, .fade-slide-leave-from {
  opacity: 1;
  transform: translateY(0) scale(1);
}
.theme-badge-enter-active, .theme-badge-leave-active {
  transition: opacity 0.3s;
}
.theme-badge-enter-from, .theme-badge-leave-to {
  opacity: 0;
}
.theme-badge-enter-to, .theme-badge-leave-from {
  opacity: 1;
}

/* Temel responsive ve background animasyonu */
.theme-provider {
  min-height: 100vh;
  font-family: var(--font-family);
  background: var(--color-bg-main);
  color: var(--color-text);
  transition: background 0.5s, color 0.35s, box-shadow 0.4s;
  will-change: background, color;
}
.theme-dark {
  color-scheme: dark;
}
.theme-light {
  color-scheme: light;
}
</style>
