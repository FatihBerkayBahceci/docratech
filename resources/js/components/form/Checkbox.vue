<!--
  Project: Medical Health Platform - Checkbox UI Design
  Component: Checkbox
  Developer: DocraTech Team - Fatih Berkay Bahceci
-->

<template>
  <label
    class="flex items-start gap-3 cursor-pointer user-select-none relative mb-3 group"
    :class="[
      modelValue ? 'checked' : '',
      error ? 'error' : '',
      disabled ? 'opacity-60 pointer-events-none' : ''
    ]"
    :aria-checked="modelValue"
    :aria-disabled="disabled"
    :aria-invalid="!!error"
    tabindex="0"
    @keydown.space.prevent="toggle"
    @keydown.enter.prevent="toggle"
    role="checkbox"
  >
    <!-- Invisible native input for accessibility -->
    <input
      type="checkbox"
      class="sr-only"
      :checked="modelValue"
      :disabled="disabled"
      :aria-label="label"
      @change="$emit('update:modelValue', $event.target.checked)"
      tabindex="-1"
    />
    <!-- Custom checkbox visual -->
    <span
      class="flex items-center justify-center w-6 h-6 rounded-xl border-2 transition-all duration-300
        border-[var(--color-border)] bg-[var(--color-bg-checkbox)] shadow-md
        group-hover:border-[var(--color-accent)] group-focus-visible:ring-2 group-focus-visible:ring-[var(--color-accent)]
        relative"
      :class="{
        'border-[var(--color-primary)] bg-[var(--color-bg-checked)] shadow-[0_0_0_4px_rgba(157,56,207,0.08)]': modelValue,
        'border-[var(--color-error)] shadow-[0_0_0_4px_rgba(239,68,68,0.10)]': error
      }"
      aria-hidden="true"
    >
      <svg
        v-if="modelValue"
        class="w-4 h-4 pointer-events-none animate-tick-in"
        viewBox="0 0 20 20"
        fill="none"
        stroke="var(--color-primary)"
        stroke-width="2.5"
        stroke-linecap="round"
        stroke-linejoin="round"
      >
        <polyline points="5 11 9 15 15 7" />
      </svg>
    </span>
    <!-- Label & Description -->
    <div class="flex flex-col min-w-0">
      <span
        class="font-medium text-[var(--color-text)] text-base transition-colors duration-200"
        :class="{
          'text-[var(--color-error)]': error,
          'opacity-70': disabled
        }"
      >
        {{ label }}
      </span>
      <span
        v-if="description"
        class="text-sm text-[var(--color-desc)] mt-0.5"
      >
        {{ description }}
      </span>
      <transition name="fade-slide">
        <span
          v-if="error"
          class="text-xs text-[var(--color-error)] mt-1 font-medium select-text"
        >
          {{ error }}
        </span>
      </transition>
    </div>
  </label>
</template>

<script setup>
// Developer: DocraTech Team - Fatih Berkay Bahceci

defineProps({
  modelValue: Boolean,
  label: { type: String, required: true },
  description: String,
  error: String,
  disabled: Boolean
});

// Klavye erişimi için:
const emit = defineEmits(['update:modelValue']);
function toggle() {
  if (!props.disabled) emit('update:modelValue', !props.modelValue);
}
</script>

<style scoped>
/* Brandkit renklerini CSS custom property ile tanımla */
:root {
  --color-primary: #5A1188;
  --color-accent: #9D38CF;
  --color-bg-checkbox: #181124;
  --color-bg-checked: #2A183D;
  --color-border: #6D488D;
  --color-text: #FFF;
  --color-desc: #B1A9C7;
  --color-error: #ef4444;
}
/* Animasyon: Checkbox tick mikro animasyonu */
@keyframes tick-in {
  from {
    stroke-dasharray: 24;
    stroke-dashoffset: 24;
    opacity: 0;
  }
  to {
    stroke-dasharray: 24;
    stroke-dashoffset: 0;
    opacity: 1;
  }
}
.animate-tick-in {
  animation: tick-in 0.28s cubic-bezier(0.22, 1, 0.36, 1) forwards;
}

/* Fade-slide mikro animasyon (hata mesajı için) */
.fade-slide-enter-active, .fade-slide-leave-active {
  transition: all 0.3s cubic-bezier(0.22, 1, 0.36, 1);
}
.fade-slide-enter-from, .fade-slide-leave-to {
  opacity: 0;
  transform: translateY(8px);
}
</style>
