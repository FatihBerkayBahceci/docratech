<!--
  DocraTech Medical Platform - Enterprise Phone Input Component
  Features: Country selection, validation, formatting, verification
-->

<template>
  <div class="phone-input-wrapper">
    <!-- Label -->
    <label 
      v-if="label"
      :for="inputId"
      class="form-label"
      :class="{ 'text-red-500': hasError }"
    >
      {{ label }}
      <span v-if="required" class="text-red-500 ml-1">*</span>
    </label>

    <!-- Phone Input Container -->
    <div class="phone-input-container" :class="containerClasses">
      <!-- Country Selector -->
      <div class="country-selector" ref="countrySelector">
        <button
          type="button"
          class="country-flag-btn"
          @click="toggleCountrySelector"
          :disabled="disabled"
          :aria-label="`Selected country: ${selectedCountry.name}`"
        >
          <span class="flag-emoji">{{ selectedCountry.flag }}</span>
          <span class="country-code">{{ selectedCountry.code }}</span>
          <svg class="w-4 h-4 chevron" :class="{ 'rotate-180': showCountrySelector }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
          </svg>
        </button>

        <!-- Country Dropdown -->
        <Teleport to="body">
          <div v-if="showCountrySelector" class="country-dropdown" :style="dropdownStyle">
            <div class="country-search">
              <svg class="w-4 h-4 search-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
              </svg>
              <input
                v-model="countrySearch"
                ref="countrySearchInput"
                placeholder="Search countries..."
                class="country-search-input"
                @keydown.escape="hideCountrySelector"
              />
            </div>
            <div class="country-list">
              <button
                v-for="country in filteredCountries"
                :key="country.code"
                class="country-option"
                :class="{ 'selected': country.code === selectedCountry.code }"
                @click="selectCountry(country)"
              >
                <span class="flag-emoji">{{ country.flag }}</span>
                <span class="country-info">
                  <span class="country-name">{{ country.name }}</span>
                  <span class="dial-code">{{ country.code }}</span>
                </span>
              </button>
            </div>
          </div>
        </Teleport>
      </div>

      <!-- Phone Number Input -->
      <input
        :id="inputId"
        ref="phoneInput"
        v-model="phoneNumber"
        type="tel"
        class="phone-number-input"
        :placeholder="phonePlaceholder"
        :disabled="disabled"
        :readonly="readonly"
        :maxlength="maxPhoneLength"
        @input="handlePhoneInput"
        @focus="handleFocus"
        @blur="handleBlur"
        @paste="handlePaste"
        @keydown="handleKeydown"
      />

      <!-- Validation & Action Icons -->
      <div class="phone-actions">
        <!-- Verification Badge -->
        <div v-if="isVerified" class="verification-badge verified" title="Phone verified">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
          </svg>
        </div>

        <!-- Validation Icons -->
        <div class="validation-icons">
          <svg v-if="isValid && modelValue" class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
          </svg>
          <svg v-else-if="hasError" class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
          </svg>
          <svg v-else class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
          </svg>
        </div>

        <!-- Clear Button -->
        <button
          v-if="clearable && phoneNumber && !disabled && !readonly"
          type="button"
          class="clear-btn"
          @click="clearPhone"
        >
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
          </svg>
        </button>
      </div>
    </div>

    <!-- Phone Info & Validation -->
    <div v-if="(phoneNumber || focused) && showPhoneInfo" class="phone-info">
      <div class="phone-metadata">
        <div class="phone-details">
          <span class="phone-type">{{ phoneType }}</span>
          <span v-if="carrierInfo" class="carrier">{{ carrierInfo }}</span>
          <span class="confidence-score" :class="confidenceClass">
            {{ confidenceScore }}% confidence
          </span>
        </div>
        <div v-if="formatExample" class="format-example">
          Example: {{ formatExample }}
        </div>
      </div>
    </div>

    <!-- Character Count -->
    <div v-if="showCharCount" class="char-count">
      {{ phoneNumber ? phoneNumber.length : 0 }}/{{ maxPhoneLength }}
    </div>

    <!-- Help Text -->
    <p v-if="helpText && !hasError" class="form-help">
      {{ helpText }}
    </p>

    <!-- Error Message -->
    <Transition name="error-slide">
      <div v-if="hasError" class="error-message">
        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
        </svg>
        {{ error }}
      </div>
    </Transition>

    <!-- Warnings -->
    <div v-if="warnings.length > 0" class="warnings">
      <div v-for="warning in warnings" :key="warning" class="warning-item">
        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
        </svg>
        {{ warning }}
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch, onMounted, onUnmounted, nextTick } from 'vue'

defineOptions({ name: 'PhoneInput' })

const props = defineProps({
  modelValue: {
    type: String,
    default: ''
  },
  label: {
    type: String,
    default: ''
  },
  required: {
    type: Boolean,
    default: false
  },
  disabled: {
    type: Boolean,
    default: false
  },
  readonly: {
    type: Boolean,
    default: false
  },
  error: {
    type: String,
    default: ''
  },
  helpText: {
    type: String,
    default: ''
  },
  defaultCountry: {
    type: String,
    default: 'TR'
  },
  allowedCountries: {
    type: Array,
    default: () => []
  },
  preferredCountries: {
    type: Array,
    default: () => ['TR', 'US', 'GB']
  },
  autoFormat: {
    type: Boolean,
    default: true
  },
  showPhoneInfo: {
    type: Boolean,
    default: true
  },
  showCharCount: {
    type: Boolean,
    default: true
  },
  clearable: {
    type: Boolean,
    default: true
  },
  validateOnChange: {
    type: Boolean,
    default: true
  },
  isVerified: {
    type: Boolean,
    default: false
  },
  id: {
    type: String,
    default: null
  }
})

const emit = defineEmits([
  'update:modelValue',
  'country-change',
  'validation-change',
  'focus',
  'blur',
  'verify-request'
])

// Template refs
const phoneInput = ref(null)
const countrySelector = ref(null)
const countrySearchInput = ref(null)

// State
const phoneNumber = ref('')
const focused = ref(false)
const showCountrySelector = ref(false)
const countrySearch = ref('')
const selectedCountry = ref({})
const validation = ref({})
const warnings = ref([])
const dropdownStyle = ref({})

// Countries data
const countries = ref([
  { code: '+90', name: 'Turkey', flag: '🇹🇷', iso: 'TR', example: '+90 (5XX) XXX XX XX' },
  { code: '+1', name: 'United States', flag: '🇺🇸', iso: 'US', example: '+1 (XXX) XXX-XXXX' },
  { code: '+44', name: 'United Kingdom', flag: '🇬🇧', iso: 'GB', example: '+44 XXXX XXX XXX' },
  { code: '+49', name: 'Germany', flag: '🇩🇪', iso: 'DE', example: '+49 XXX XXXXXXX' },
  { code: '+33', name: 'France', flag: '🇫🇷', iso: 'FR', example: '+33 X XX XX XX XX' },
  { code: '+1', name: 'Canada', flag: '🇨🇦', iso: 'CA', example: '+1 (XXX) XXX-XXXX' },
  { code: '+61', name: 'Australia', flag: '🇦🇺', iso: 'AU', example: '+61 X XXXX XXXX' },
  { code: '+31', name: 'Netherlands', flag: '🇳🇱', iso: 'NL', example: '+31 X XX XX XX XX' },
  { code: '+46', name: 'Sweden', flag: '🇸🇪', iso: 'SE', example: '+46 XX XXX XX XX' },
  { code: '+47', name: 'Norway', flag: '🇳🇴', iso: 'NO', example: '+47 XXX XX XXX' },
  { code: '+45', name: 'Denmark', flag: '🇩🇰', iso: 'DK', example: '+45 XX XX XX XX' },
  { code: '+358', name: 'Finland', flag: '🇫🇮', iso: 'FI', example: '+358 XX XXX XXXX' },
  { code: '+41', name: 'Switzerland', flag: '🇨🇭', iso: 'CH', example: '+41 XX XXX XX XX' },
  { code: '+43', name: 'Austria', flag: '🇦🇹', iso: 'AT', example: '+43 XXX XXXXXXX' },
  { code: '+39', name: 'Italy', flag: '🇮🇹', iso: 'IT', example: '+39 XXX XXX XXXX' },
  { code: '+34', name: 'Spain', flag: '🇪🇸', iso: 'ES', example: '+34 XXX XX XX XX' },
  { code: '+351', name: 'Portugal', flag: '🇵🇹', iso: 'PT', example: '+351 XXX XXX XXX' },
  { code: '+48', name: 'Poland', flag: '🇵🇱', iso: 'PL', example: '+48 XXX XXX XXX' },
  // Add more countries as needed
])

// Computed properties
const inputId = computed(() => props.id || `phone-input-${Math.random().toString(36).substr(2, 9)}`)

const hasError = computed(() => !!props.error)

const isValid = computed(() => validation.value.is_valid || false)

const phoneType = computed(() => {
  const type = validation.value.type || 'Unknown'
  return type.charAt(0).toUpperCase() + type.slice(1)
})

const carrierInfo = computed(() => validation.value.metadata?.carrier || null)

const confidenceScore = computed(() => validation.value.confidence_score || 0)

const confidenceClass = computed(() => {
  const score = confidenceScore.value
  if (score >= 90) return 'confidence-high'
  if (score >= 70) return 'confidence-medium'
  return 'confidence-low'
})

const formatExample = computed(() => selectedCountry.value.example || '')

const maxPhoneLength = computed(() => 20)

const phonePlaceholder = computed(() => {
  return selectedCountry.value.example?.replace(/\d/g, 'X') || 'Enter phone number'
})

const filteredCountries = computed(() => {
  if (!countrySearch.value) {
    // Show preferred countries first
    const preferred = countries.value.filter(c => props.preferredCountries.includes(c.iso))
    const others = countries.value.filter(c => !props.preferredCountries.includes(c.iso))
    return [...preferred, ...others]
  }
  
  return countries.value.filter(country => 
    country.name.toLowerCase().includes(countrySearch.value.toLowerCase()) ||
    country.code.includes(countrySearch.value) ||
    country.iso.toLowerCase().includes(countrySearch.value.toLowerCase())
  )
})

const containerClasses = computed(() => [
  'phone-container',
  {
    'phone-container--focused': focused.value,
    'phone-container--error': hasError.value,
    'phone-container--valid': isValid.value && phoneNumber.value,
    'phone-container--disabled': props.disabled,
    'phone-container--readonly': props.readonly
  }
])

// Methods
const initializeCountry = () => {
  const defaultCountry = countries.value.find(c => c.iso === props.defaultCountry)
  selectedCountry.value = defaultCountry || countries.value[0]
}

const toggleCountrySelector = () => {
  if (props.disabled || props.readonly) return
  
  showCountrySelector.value = !showCountrySelector.value
  
  if (showCountrySelector.value) {
    nextTick(() => {
      calculateDropdownPosition()
      countrySearchInput.value?.focus()
    })
  }
}

const hideCountrySelector = () => {
  showCountrySelector.value = false
  countrySearch.value = ''
}

const selectCountry = (country) => {
  selectedCountry.value = country
  hideCountrySelector()
  emit('country-change', country)
  
  // Revalidate phone number with new country
  if (phoneNumber.value && props.validateOnChange) {
    validatePhoneNumber()
  }
}

const calculateDropdownPosition = () => {
  if (!countrySelector.value) return
  
  const rect = countrySelector.value.getBoundingClientRect()
  const viewportHeight = window.innerHeight
  const dropdownHeight = 300 // Approximate height
  
  const spaceBelow = viewportHeight - rect.bottom
  const spaceAbove = rect.top
  
  if (spaceBelow < dropdownHeight && spaceAbove > dropdownHeight) {
    // Show above
    dropdownStyle.value = {
      position: 'fixed',
      top: `${rect.top - dropdownHeight}px`,
      left: `${rect.left}px`,
      width: `${rect.width}px`,
      zIndex: 1000
    }
  } else {
    // Show below
    dropdownStyle.value = {
      position: 'fixed',
      top: `${rect.bottom}px`,
      left: `${rect.left}px`,
      width: `${rect.width}px`,
      zIndex: 1000
    }
  }
}

const handlePhoneInput = (event) => {
  const value = event.target.value
  phoneNumber.value = value
  
  // Format phone number if auto-format is enabled
  if (props.autoFormat && value) {
    phoneNumber.value = formatPhoneNumber(value)
  }
  
  // Validate on change
  if (props.validateOnChange) {
    validatePhoneNumber()
  }
  
  // Emit the E.164 format
  const e164 = convertToE164(phoneNumber.value)
  emit('update:modelValue', e164)
}

const handleFocus = () => {
  focused.value = true
  emit('focus')
}

const handleBlur = () => {
  focused.value = false
  if (props.validateOnChange) {
    validatePhoneNumber()
  }
  emit('blur')
}

const handlePaste = (event) => {
  // Handle paste event with phone number normalization
  const pastedText = event.clipboardData.getData('text')
  if (pastedText) {
    event.preventDefault()
    const cleanedPhone = cleanPhoneNumber(pastedText)
    phoneNumber.value = cleanedPhone
    handlePhoneInput({ target: { value: cleanedPhone } })
  }
}

const handleKeydown = (event) => {
  // Handle special keys
  if (event.key === 'Escape') {
    hideCountrySelector()
  }
}

const clearPhone = () => {
  phoneNumber.value = ''
  validation.value = {}
  warnings.value = []
  emit('update:modelValue', '')
  nextTick(() => {
    phoneInput.value?.focus()
  })
}

const validatePhoneNumber = async () => {
  if (!phoneNumber.value) {
    validation.value = {}
    warnings.value = []
    emit('validation-change', validation.value)
    return
  }

  try {
    // Simulate API call to backend validation
    const response = await fetch('/api/phone/validate', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content
      },
      body: JSON.stringify({
        phone: phoneNumber.value,
        country: selectedCountry.value.iso
      })
    })

    const result = await response.json()
    validation.value = result
    warnings.value = result.warnings || []
    
    emit('validation-change', validation.value)
  } catch (error) {
    console.error('Phone validation error:', error)
    validation.value = { is_valid: false, errors: ['Validation failed'] }
    warnings.value = []
  }
}

const formatPhoneNumber = (phone) => {
  // Basic formatting - in a real app, use proper formatting library
  const cleaned = phone.replace(/\D/g, '')
  
  // Apply country-specific formatting
  if (selectedCountry.value.iso === 'TR') {
    if (cleaned.length >= 10) {
      return cleaned.replace(/(\d{3})(\d{3})(\d{2})(\d{2})/, '($1) $2 $3 $4')
    }
  } else if (selectedCountry.value.iso === 'US' || selectedCountry.value.iso === 'CA') {
    if (cleaned.length >= 10) {
      return cleaned.replace(/(\d{3})(\d{3})(\d{4})/, '($1) $2-$3')
    }
  }
  
  return phone
}

const cleanPhoneNumber = (phone) => {
  return phone.replace(/[^\d+]/g, '')
}

const convertToE164 = (phone) => {
  if (!phone) return ''
  
  const cleaned = cleanPhoneNumber(phone)
  
  // If already starts with +, return as is
  if (cleaned.startsWith('+')) {
    return cleaned
  }
  
  // Add country code
  return selectedCountry.value.code + cleaned.replace(/^0+/, '')
}

// Event listeners
const handleClickOutside = (event) => {
  if (showCountrySelector.value && !countrySelector.value?.contains(event.target)) {
    hideCountrySelector()
  }
}

// Lifecycle
onMounted(() => {
  initializeCountry()
  document.addEventListener('click', handleClickOutside)
  
  // Initialize with modelValue
  if (props.modelValue) {
    phoneNumber.value = props.modelValue
    if (props.validateOnChange) {
      validatePhoneNumber()
    }
  }
})

onUnmounted(() => {
  document.removeEventListener('click', handleClickOutside)
})

// Watch for external changes
watch(() => props.modelValue, (newValue) => {
  if (newValue !== phoneNumber.value) {
    phoneNumber.value = newValue || ''
  }
})

watch(() => props.defaultCountry, (newCountry) => {
  if (newCountry) {
    const country = countries.value.find(c => c.iso === newCountry)
    if (country) {
      selectedCountry.value = country
    }
  }
})

// Public methods
defineExpose({
  focus: () => phoneInput.value?.focus(),
  blur: () => phoneInput.value?.blur(),
  validate: validatePhoneNumber,
  clear: clearPhone
})
</script>

<style scoped>
/* Phone Input Styles */
.phone-input-wrapper {
  @apply space-y-2;
}

.phone-input-container {
  @apply flex items-center;
}

.phone-container {
  @apply relative flex items-center bg-white border-2 border-gray-300 rounded-xl transition-all duration-200;
}

.phone-container--focused {
  @apply border-[#5A1188] ring-2 ring-[#5A1188] ring-opacity-20;
}

.phone-container--error {
  @apply border-red-500 ring-2 ring-red-500 ring-opacity-20;
}

.phone-container--valid {
  @apply border-green-500;
}

.phone-container--disabled {
  @apply bg-gray-50 border-gray-200 opacity-60 cursor-not-allowed;
}

.phone-container--readonly {
  @apply bg-gray-50 border-gray-200;
}

.phone-container:hover:not(.phone-container--disabled):not(.phone-container--focused) {
  @apply border-[#5A1188] border-opacity-60;
}

/* Country Selector */
.country-selector {
  @apply relative;
}

.country-flag-btn {
  @apply flex items-center space-x-2 px-3 py-3 text-sm font-medium text-gray-700 bg-gray-50 border-r border-gray-300 rounded-l-xl hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-[#5A1188] focus:ring-opacity-50 transition-colors;
}

.country-flag-btn:disabled {
  @apply opacity-50 cursor-not-allowed;
}

.flag-emoji {
  @apply text-lg;
}

.country-code {
  @apply text-sm font-medium;
}

.chevron {
  @apply transition-transform duration-200;
}

/* Country Dropdown */
.country-dropdown {
  @apply bg-white border border-gray-300 rounded-xl shadow-lg max-h-80 overflow-hidden;
}

.country-search {
  @apply relative p-3 border-b border-gray-200;
}

.search-icon {
  @apply absolute left-5 top-1/2 transform -translate-y-1/2 text-gray-400;
}

.country-search-input {
  @apply w-full pl-8 pr-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#5A1188] focus:ring-opacity-50;
}

.country-list {
  @apply max-h-60 overflow-y-auto;
}

.country-option {
  @apply w-full flex items-center space-x-3 px-4 py-3 text-left hover:bg-gray-50 focus:outline-none focus:bg-gray-50 transition-colors;
}

.country-option.selected {
  @apply bg-[#5A1188] bg-opacity-10 text-[#5A1188];
}

.country-info {
  @apply flex-1 min-w-0;
}

.country-name {
  @apply block text-sm font-medium text-gray-900 truncate;
}

.dial-code {
  @apply block text-xs text-gray-500;
}

/* Phone Number Input */
.phone-number-input {
  @apply flex-1 px-4 py-3 text-base text-gray-900 placeholder-gray-400 bg-transparent border-0 outline-none;
}

.phone-number-input:disabled {
  @apply cursor-not-allowed;
}

/* Phone Actions */
.phone-actions {
  @apply flex items-center space-x-2 pr-3;
}

.verification-badge {
  @apply flex items-center justify-center w-6 h-6 rounded-full;
}

.verification-badge.verified {
  @apply bg-green-100 text-green-600;
}

.validation-icons {
  @apply flex items-center;
}

.clear-btn {
  @apply p-1 text-gray-400 hover:text-gray-600 transition-colors rounded focus:outline-none focus:ring-2 focus:ring-[#5A1188] focus:ring-opacity-50;
}

.clear-btn:hover {
  @apply bg-gray-100;
}

/* Phone Info */
.phone-info {
  @apply mt-2 p-3 bg-gray-50 rounded-lg;
}

.phone-metadata {
  @apply space-y-1;
}

.phone-details {
  @apply flex items-center space-x-2 text-sm;
}

.phone-type {
  @apply px-2 py-1 text-xs font-medium bg-blue-100 text-blue-800 rounded-full;
}

.carrier {
  @apply px-2 py-1 text-xs font-medium bg-purple-100 text-purple-800 rounded-full;
}

.confidence-score {
  @apply px-2 py-1 text-xs font-medium rounded-full;
}

.confidence-high {
  @apply bg-green-100 text-green-800;
}

.confidence-medium {
  @apply bg-yellow-100 text-yellow-800;
}

.confidence-low {
  @apply bg-red-100 text-red-800;
}

.format-example {
  @apply text-xs text-gray-500 italic;
}

/* Character Count */
.char-count {
  @apply text-xs text-gray-500 text-right;
}

/* Error & Warning Messages */
.error-message {
  @apply flex items-center text-sm text-red-600 bg-red-50 border border-red-200 rounded-lg p-3;
}

.warnings {
  @apply space-y-1;
}

.warning-item {
  @apply flex items-center text-sm text-yellow-600 bg-yellow-50 border border-yellow-200 rounded-lg p-2;
}

/* Transitions */
.error-slide-enter-active,
.error-slide-leave-active {
  @apply transition-all duration-300 ease-in-out;
}

.error-slide-enter-from {
  @apply opacity-0 transform -translate-y-2;
}

.error-slide-leave-to {
  @apply opacity-0 transform -translate-y-2;
}

/* Form helpers */
.form-label {
  @apply block text-sm font-medium text-gray-700 mb-1;
}

.form-help {
  @apply text-sm text-gray-500;
}
</style> 