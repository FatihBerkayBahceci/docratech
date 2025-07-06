<!--
  DocraTech Medical Platform - Enhanced Input Text Component
  Features: Accessibility, Validation, Icons, Light Mode Design
  Brand Colors: DocraTech Light Mode Only
-->

<template>
  <div 
    class="form-field"
    :class="fieldClasses"
  >
    <!-- Label -->
    <label 
      v-if="label"
      :for="inputId"
      class="form-label"
      :class="{ 'text-docratech-error': hasError }"
    >
      {{ label }}
      <span v-if="required" class="text-docratech-error ml-1" aria-label="required">*</span>
    </label>

    <!-- Input Container -->
    <div 
      class="input-container"
      :class="containerClasses"
    >
      <!-- Prefix Icon -->
      <span 
        v-if="prefixIcon"
        class="input-icon input-icon-prefix"
        aria-hidden="true"
      >
        <component :is="prefixIcon" class="w-5 h-5" />
      </span>

      <!-- Input Element -->
      <input
        :id="inputId"
        ref="inputRef"
        :type="inputType"
        :class="inputClasses"
        :placeholder="placeholder"
        :value="modelValue"
        :disabled="disabled"
        :readonly="readonly"
        :required="required"
        :aria-invalid="hasError"
        :aria-describedby="hasError ? `${inputId}-error` : helpText ? `${inputId}-help` : undefined"
        :autocomplete="autocomplete"
        :spellcheck="spellcheck"
        :maxlength="maxlength"
        :minlength="minlength"
        :pattern="pattern"
        @input="handleInput"
        @focus="handleFocus"
        @blur="handleBlur"
        @keydown="handleKeydown"
        @paste="handlePaste"
      />

      <!-- Suffix Elements -->
      <div v-if="hasSuffix" class="input-suffix">
        <!-- Clear Button -->
        <button
          v-if="clearable && modelValue && !disabled && !readonly"
          type="button"
          class="input-clear-btn"
          :aria-label="`Clear ${label || 'input'}`"
          @click="clearInput"
        >
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
          </svg>
        </button>

        <!-- Password Toggle -->
        <button
          v-if="type === 'password'"
          type="button"
          class="input-toggle-btn"
          :aria-label="showPassword ? 'Hide password' : 'Show password'"
          @click="togglePassword"
        >
          <svg v-if="showPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.878L18 18m-6.122-9.122a3 3 0 014.243 4.243M9.878 9.878L3 3m6.878 6.878L18 18"></path>
          </svg>
          <svg v-else class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
          </svg>
        </button>

        <!-- Suffix Icon -->
        <span 
          v-if="suffixIcon"
          class="input-icon input-icon-suffix"
          aria-hidden="true"
        >
          <component :is="suffixIcon" class="w-5 h-5" />
        </span>

        <!-- Loading Spinner -->
        <span 
          v-if="loading"
          class="input-loading"
          aria-hidden="true"
        >
          <div class="loading-spinner text-docratech-primary"></div>
        </span>
      </div>
    </div>

    <!-- Character Count -->
    <div 
      v-if="showCharCount && maxlength"
      class="input-char-count"
      :class="{ 'text-docratech-error': characterCount > maxlength }"
    >
      {{ characterCount }}/{{ maxlength }}
    </div>

    <!-- Help Text -->
    <p 
      v-if="helpText && !hasError"
      :id="`${inputId}-help`"
      class="form-help"
    >
      {{ helpText }}
    </p>

    <!-- Error Message -->
    <Transition name="error-slide">
      <p 
        v-if="hasError"
        :id="`${inputId}-error`"
        class="form-error"
        role="alert"
        aria-live="polite"
      >
        {{ error }}
      </p>
    </Transition>
  </div>
</template>

<script setup>
import { ref, computed, nextTick, watch } from 'vue'

/**
 * DocraTech Medical Platform - Enhanced Input Text Component
 * Features: Full accessibility, validation, icons, clear button, password toggle
 */

defineOptions({ 
  name: 'InputText',
  inheritAttrs: false 
})

// Props
const props = defineProps({
  // v-model
  modelValue: {
    type: [String, Number],
    default: ''
  },
  
  // Basic attributes
  type: {
    type: String,
    default: 'text',
    validator: (value) => [
      'text', 'email', 'password', 'tel', 'url', 'search', 'number'
    ].includes(value)
  },
  placeholder: {
    type: String,
    default: ''
  },
  
  // Label and help
  label: {
    type: String,
    default: ''
  },
  helpText: {
    type: String,
    default: ''
  },
  
  // Validation
  error: {
    type: String,
    default: ''
  },
  required: {
    type: Boolean,
    default: false
  },
  pattern: {
    type: String,
    default: null
  },
  maxlength: {
    type: Number,
    default: null
  },
  minlength: {
    type: Number,
    default: null
  },
  
  // State
  disabled: {
    type: Boolean,
    default: false
  },
  readonly: {
    type: Boolean,
    default: false
  },
  loading: {
    type: Boolean,
    default: false
  },
  
  // Appearance
  size: {
    type: String,
    default: 'md',
    validator: (value) => ['sm', 'md', 'lg'].includes(value)
  },
  
  // Icons
  prefixIcon: {
    type: [String, Object],
    default: null
  },
  suffixIcon: {
    type: [String, Object],
    default: null
  },
  
  // Features
  clearable: {
    type: Boolean,
    default: false
  },
  showCharCount: {
    type: Boolean,
    default: false
  },
  
  // Input attributes
  autocomplete: {
    type: String,
    default: 'off'
  },
  spellcheck: {
    type: Boolean,
    default: false
  },
  
  // Custom ID
  id: {
    type: String,
    default: null
  }
})

// Emits
const emit = defineEmits([
  'update:modelValue',
  'focus',
  'blur',
  'input',
  'keydown',
  'clear',
  'paste'
])

// Template refs
const inputRef = ref(null)

// Local state
const focused = ref(false)
const showPassword = ref(false)

// Computed properties
const inputId = computed(() => props.id || `input-${Math.random().toString(36).substr(2, 9)}`)

const hasError = computed(() => !!props.error)

const characterCount = computed(() => {
  return props.modelValue ? props.modelValue.toString().length : 0
})

const inputType = computed(() => {
  if (props.type === 'password') {
    return showPassword.value ? 'text' : 'password'
  }
  return props.type
})

const hasSuffix = computed(() => {
  return props.clearable || 
         props.type === 'password' || 
         props.suffixIcon || 
         props.loading
})

const fieldClasses = computed(() => [
  'space-y-2',
  {
    'form-field--focused': focused.value,
    'form-field--error': hasError.value,
    'form-field--disabled': props.disabled
  }
])

const containerClasses = computed(() => [
  'input-container',
  `input-container--${props.size}`,
  {
    'input-container--focused': focused.value,
    'input-container--error': hasError.value,
    'input-container--disabled': props.disabled || props.readonly,
    'input-container--has-prefix': props.prefixIcon,
    'input-container--has-suffix': hasSuffix.value
  }
])

const inputClasses = computed(() => [
  'input-field',
  {
    'input-field--has-prefix': props.prefixIcon,
    'input-field--has-suffix': hasSuffix.value
  }
])

// Methods
const handleInput = (event) => {
  emit('update:modelValue', event.target.value)
  emit('input', event)
}

const handleFocus = (event) => {
  focused.value = true
  emit('focus', event)
}

const handleBlur = (event) => {
  focused.value = false
  emit('blur', event)
}

const handleKeydown = (event) => {
  emit('keydown', event)
}

const handlePaste = (event) => {
  emit('paste', event)
}

const clearInput = () => {
  emit('update:modelValue', '')
  emit('clear')
  nextTick(() => {
    inputRef.value?.focus()
  })
}

const togglePassword = () => {
  showPassword.value = !showPassword.value
}

const focus = () => {
  nextTick(() => {
    inputRef.value?.focus()
  })
}

const blur = () => {
  nextTick(() => {
    inputRef.value?.blur()
  })
}

const select = () => {
  nextTick(() => {
    inputRef.value?.select()
  })
}

// Expose public methods
defineExpose({
  focus,
  blur,
  select,
  inputRef
})
</script>

<style scoped>
/* Container Styles */
.input-container {
  @apply relative flex items-center bg-white border-2 border-gray-300 rounded-xl transition-all duration-200;
}

.input-container--sm {
  @apply min-h-[36px];
}

.input-container--md {
  @apply min-h-[44px];
}

.input-container--lg {
  @apply min-h-[52px];
}

.input-container--focused {
  @apply border-[#5A1188] ring-2 ring-[#5A1188] ring-opacity-20;
}

.input-container--error {
  @apply border-red-500 ring-2 ring-red-500 ring-opacity-20;
}

.input-container--disabled {
  @apply bg-gray-50 border-gray-200 opacity-60 cursor-not-allowed;
}

.input-container:hover:not(.input-container--disabled):not(.input-container--focused) {
  @apply border-[#5A1188] border-opacity-60;
}

/* Input Field Styles */
.input-field {
  @apply flex-1 bg-transparent border-0 outline-none text-gray-900 placeholder-gray-400;
  @apply px-4 py-3 text-base leading-tight;
}

.input-field--sm {
  @apply px-3 py-2 text-sm;
}

.input-field--lg {
  @apply px-5 py-4 text-lg;
}

.input-field--has-prefix {
  @apply pl-2;
}

.input-field--has-suffix {
  @apply pr-2;
}

.input-field:disabled {
  @apply cursor-not-allowed;
}

/* Icon Styles */
.input-icon {
  @apply flex items-center justify-center text-gray-400;
}

.input-icon-prefix {
  @apply pl-3 pr-1;
}

.input-icon-suffix {
  @apply pl-1 pr-3;
}

/* Suffix Elements */
.input-suffix {
  @apply flex items-center space-x-1 pr-2;
}

.input-clear-btn,
.input-toggle-btn {
  @apply p-1 text-gray-400 hover:text-gray-600 transition-colors rounded;
  @apply focus:outline-none focus:ring-2 focus:ring-[#5A1188] focus:ring-opacity-50;
}

.input-clear-btn:hover,
.input-toggle-btn:hover {
  @apply bg-gray-100;
}

/* Loading Spinner */
.input-loading {
  @apply flex items-center justify-center pr-1;
}

/* Character Count */
.input-char-count {
  @apply text-xs text-gray-500 text-right;
}

/* Error Animation */
.error-slide-enter-active {
  @apply transition-all duration-300 ease-out;
}

.error-slide-leave-active {
  @apply transition-all duration-200 ease-in;
}

.error-slide-enter-from {
  @apply opacity-0 transform -translate-y-2;
}

.error-slide-leave-to {
  @apply opacity-0 transform -translate-y-1;
}

/* Focus States for Accessibility */
.input-field:focus {
  @apply outline-none;
}

/* High Contrast Mode */
@media (prefers-contrast: high) {
  .input-container {
    @apply border-4;
  }
}

/* Reduced Motion */
@media (prefers-reduced-motion: reduce) {
  .input-container,
  .input-clear-btn,
  .input-toggle-btn,
  .error-slide-enter-active,
  .error-slide-leave-active {
    @apply transition-none;
  }
}

/* Print Styles */
@media print {
  .input-container {
    @apply border-gray-400 bg-white;
  }
  
  .input-clear-btn,
  .input-toggle-btn {
    @apply hidden;
  }
}
</style>
