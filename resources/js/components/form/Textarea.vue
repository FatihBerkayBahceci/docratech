<!--
  Project: Corporate Healthcare Platform UI Library
  Component: Textarea (Advanced)
  Developer: DocraTech Team - Fatih Berkay Bahceci
-->

<template>
  <div
    class="flex flex-col gap-1.5 mb-5 relative group"
    :class="{
      'textarea--focused': focused,
      'textarea--error': !!error,
      'textarea--disabled': disabled
    }"
  >
    <label
      v-if="label"
      class="font-semibold text-white mb-1 transition-colors text-base select-none"
      :for="textareaId"
      >{{ label }}</label>

    <span
      v-if="helper"
      class="text-xs text-[#B1A9C7] mb-1 select-none"
      >{{ helper }}</span>

    <div class="relative flex flex-col">
      <textarea
        ref="textarea"
        :id="textareaId"
        :placeholder="placeholder"
        :value="modelValue"
        :rows="rows"
        :maxlength="maxLength"
        :disabled="disabled"
        :autofocus="autofocus"
        :class="[
          'w-full min-h-[120px] max-h-[340px] bg-[#181124] text-white rounded-2xl border-2 border-[#6D488D] shadow-md font-medium text-base px-4 py-3 resize-none outline-none transition-all duration-300 placeholder-[#7C7192] leading-relaxed',
          focused && !error ? 'border-[#9D38CF] ring-4 ring-[#9D38CF22]' : '',
          error ? 'border-[#ef4444] animate-shake ring-4 ring-[#ef444422]' : '',
          disabled ? 'bg-[#231935] opacity-60 pointer-events-none border-[#3f275f]' : ''
        ]"
        @focus="handleFocus"
        @blur="focused = false"
        @input="handleInput"
        @paste="handlePaste"
        :aria-label="label || placeholder"
        :aria-invalid="!!error"
        aria-live="polite"
        aria-describedby="textarea-helper"
      ></textarea>
      <!-- Clear button (shown only if not disabled and has content) -->
      <button
        v-if="clearable && !disabled && modelValue"
        type="button"
        @click="clear"
        class="absolute top-3 right-4 z-20 bg-[#2A183D] hover:bg-[#9D38CF] text-[#B1A9C7] hover:text-white rounded-lg transition-all duration-150 px-2 py-1 flex items-center justify-center text-sm shadow focus:outline-none focus:ring-2 focus:ring-[#9D38CF] aria-selected:font-bold"
        aria-label="Clear text"
      >
        <svg width="15" height="15" fill="none" viewBox="0 0 20 20">
          <path stroke="currentColor" stroke-width="2" stroke-linecap="round" d="M6 6l8 8M14 6l-8 8"/>
        </svg>
      </button>
      <!-- Character counter with accent/alert color when near limit -->
      <div
        v-if="maxLength"
        class="absolute bottom-2 right-4 text-xs px-2 py-0.5 rounded bg-[#22173bcc] font-medium select-none pointer-events-none transition-all duration-200"
        :class="counterClass"
        aria-live="polite"
        aria-atomic="true"
      >
        {{ modelValue?.length || 0 }}/{{ maxLength }}
      </div>
    </div>
    <transition name="fade-slide">
      <div
        v-if="error"
        class="text-[#ef4444] text-sm mt-1 min-h-[20px] font-medium"
        id="textarea-helper"
        role="alert"
      >{{ error }}</div>
    </transition>
  </div>
</template>

<script setup>
// Developer: DocraTech Team - Fatih Berkay Bahceci

import { ref, computed, onMounted } from 'vue'
const props = defineProps({
  modelValue: String,
  label: String,
  helper: String,
  placeholder: { type: String, default: 'Write your text...' },
  rows: { type: Number, default: 4 },
  maxLength: Number,
  error: String,
  disabled: Boolean,
  autofocus: { type: Boolean, default: false },
  clearable: { type: Boolean, default: true }
})
const emit = defineEmits(['update:modelValue', 'clear'])

const focused = ref(false)
const textarea = ref()
const textareaId = `textarea-${Math.random().toString(36).slice(2, 8)}`

function handleFocus() {
  focused.value = true
  autoResize()
}

function handleInput(event) {
  let value = event.target.value
  // Only allow up to maxLength
  if (props.maxLength && value.length > props.maxLength) {
    value = value.slice(0, props.maxLength)
    event.target.value = value
  }
  emit('update:modelValue', value)
  autoResize()
}

function handlePaste(e) {
  if (!props.maxLength) return
  e.preventDefault()
  const pasted = (e.clipboardData || window.clipboardData).getData('text')
  const allowed = props.maxLength - (props.modelValue?.length || 0)
  const textToInsert = pasted.slice(0, allowed)
  const start = e.target.selectionStart
  const end = e.target.selectionEnd
  const newValue =
    (props.modelValue || '').slice(0, start) + textToInsert + (props.modelValue || '').slice(end)
  emit('update:modelValue', newValue)
  setTimeout(() => autoResize(), 20)
}

function autoResize() {
  if (textarea.value) {
    textarea.value.style.height = 'auto'
    textarea.value.style.height = Math.min(textarea.value.scrollHeight, 340) + 'px'
  }
}
function clear() {
  emit('update:modelValue', '')
  emit('clear')
  setTimeout(() => autoResize(), 10)
}
onMounted(() => {
  autoResize()
  if (props.autofocus && textarea.value) textarea.value.focus()
})

const counterClass = computed(() => {
  if (!props.maxLength) return ''
  const val = props.modelValue?.length || 0
  if (val >= props.maxLength) return 'text-[#ef4444] font-bold animate-pulse'
  if (val >= Math.round(props.maxLength * 0.9)) return 'text-[#9D38CF] font-semibold animate-pulse'
  if (focused.value) return 'text-[#9D38CF] font-semibold'
  if (props.error) return 'text-[#ef4444]'
  return 'text-[#B1A9C7]'
})
</script>

<style scoped>
/* Micro animation for error state */
@keyframes shake {
  0%,100% { transform: translateX(0);}
  18%,54%,88% { transform: translateX(-2px);}
  36%,72% { transform: translateX(2px);}
}
.animate-shake { animation: shake 0.35s cubic-bezier(.36,.07,.19,.97) both; }

.fade-slide-enter-active, .fade-slide-leave-active {
  transition: all 0.28s cubic-bezier(.22,1,.36,1);
}
.fade-slide-enter-from, .fade-slide-leave-to {
  opacity: 0;
  transform: translateY(8px);
}
</style>
