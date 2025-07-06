<template>
  <div class="password-strength">
    <!-- Strength Bar -->
    <div class="password-strength-bar">
      <div class="password-strength-segments">
        <div
          v-for="segment in 4"
          :key="segment"
          class="password-strength-segment"
          :class="{
            'password-strength-segment--active': segment <= strengthLevel,
            'password-strength-segment--weak': segment <= strengthLevel && strengthLevel <= 1,
            'password-strength-segment--fair': segment <= strengthLevel && strengthLevel === 2,
            'password-strength-segment--good': segment <= strengthLevel && strengthLevel === 3,
            'password-strength-segment--strong': segment <= strengthLevel && strengthLevel === 4
          }"
        ></div>
      </div>
    </div>

    <!-- Strength Indicator -->
    <div v-if="password" class="password-strength-indicator">
      <div class="password-strength-label">
        <Icon :name="strengthIcon" :class="strengthIconClass" />
        <span :class="strengthTextClass">{{ strengthText }}</span>
      </div>
      <div class="password-strength-score">{{ strengthScore }}%</div>
    </div>

    <!-- Requirements List -->
    <div v-if="password" class="password-requirements">
      <div class="password-requirements-title">Password Requirements:</div>
      <div class="password-requirements-list">
        <div
          v-for="requirement in requirements"
          :key="requirement.key"
          class="password-requirement"
          :class="{
            'password-requirement--met': requirement.met,
            'password-requirement--unmet': !requirement.met
          }"
        >
          <Icon 
            :name="requirement.met ? 'check' : 'x'" 
            class="password-requirement-icon"
          />
          <span class="password-requirement-text">{{ requirement.text }}</span>
        </div>
      </div>
    </div>

    <!-- Tips (only show for weak passwords) -->
    <div v-if="password && strengthLevel <= 2" class="password-tips">
      <div class="password-tips-title">
        <Icon name="lightbulb" class="password-tips-icon" />
        Password Tips:
      </div>
      <ul class="password-tips-list">
        <li v-if="!hasMinLength">Use at least 8 characters</li>
        <li v-if="!hasUppercase">Include uppercase letters (A-Z)</li>
        <li v-if="!hasLowercase">Include lowercase letters (a-z)</li>
        <li v-if="!hasNumbers">Include numbers (0-9)</li>
        <li v-if="!hasSpecialChars">Consider special characters (!@#$%^&*)</li>
        <li v-if="strengthLevel <= 1">Avoid common words or patterns</li>
      </ul>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import Icon from '@/components/visual/Icon.vue'

const props = defineProps({
  password: {
    type: String,
    default: ''
  },
  minLength: {
    type: Number,
    default: 8
  }
})

// Password validation checks
const hasMinLength = computed(() => props.password.length >= props.minLength)
const hasUppercase = computed(() => /[A-Z]/.test(props.password))
const hasLowercase = computed(() => /[a-z]/.test(props.password))
const hasNumbers = computed(() => /\d/.test(props.password))
const hasSpecialChars = computed(() => /[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?]/.test(props.password))

// Additional strength factors
const hasNoRepeatingChars = computed(() => {
  if (props.password.length < 3) return true
  return !/(.)\1{2,}/.test(props.password)
})

const hasNoCommonPatterns = computed(() => {
  const commonPatterns = [
    /123456/,
    /abcdef/,
    /qwerty/,
    /password/i,
    /admin/i,
    /welcome/i
  ]
  return !commonPatterns.some(pattern => pattern.test(props.password))
})

// Requirements list
const requirements = computed(() => [
  {
    key: 'length',
    text: `At least ${props.minLength} characters`,
    met: hasMinLength.value
  },
  {
    key: 'uppercase',
    text: 'One uppercase letter',
    met: hasUppercase.value
  },
  {
    key: 'lowercase',
    text: 'One lowercase letter',
    met: hasLowercase.value
  },
  {
    key: 'numbers',
    text: 'One number',
    met: hasNumbers.value
  }
])

// Strength calculation
const strengthScore = computed(() => {
  if (!props.password) return 0
  
  let score = 0
  
  // Basic requirements (60% of score)
  if (hasMinLength.value) score += 15
  if (hasUppercase.value) score += 15
  if (hasLowercase.value) score += 15
  if (hasNumbers.value) score += 15
  
  // Bonus factors (40% of score)
  if (hasSpecialChars.value) score += 10
  if (props.password.length >= 12) score += 10
  if (hasNoRepeatingChars.value) score += 10
  if (hasNoCommonPatterns.value) score += 10
  
  return Math.min(score, 100)
})

const strengthLevel = computed(() => {
  const score = strengthScore.value
  if (score >= 90) return 4 // Strong
  if (score >= 70) return 3 // Good
  if (score >= 50) return 2 // Fair
  if (score >= 25) return 1 // Weak
  return 0 // Very Weak
})

const strengthText = computed(() => {
  switch (strengthLevel.value) {
    case 4: return 'Strong'
    case 3: return 'Good'
    case 2: return 'Fair'
    case 1: return 'Weak'
    default: return 'Very Weak'
  }
})

const strengthIcon = computed(() => {
  switch (strengthLevel.value) {
    case 4: return 'shield-check'
    case 3: return 'shield'
    case 2: return 'shield-exclamation'
    case 1: return 'exclamation-triangle'
    default: return 'x-circle'
  }
})

const strengthIconClass = computed(() => {
  switch (strengthLevel.value) {
    case 4: return 'text-green-600'
    case 3: return 'text-blue-600'
    case 2: return 'text-yellow-600'
    case 1: return 'text-orange-600'
    default: return 'text-red-600'
  }
})

const strengthTextClass = computed(() => {
  switch (strengthLevel.value) {
    case 4: return 'text-green-700 font-semibold'
    case 3: return 'text-blue-700 font-semibold'
    case 2: return 'text-yellow-700 font-medium'
    case 1: return 'text-orange-700 font-medium'
    default: return 'text-red-700 font-medium'
  }
})
</script>

<style scoped>
.password-strength {
  margin-top: 0.5rem;
  font-size: 0.875rem;
}

.password-strength-bar {
  margin-bottom: 0.75rem;
}

.password-strength-segments {
  display: flex;
  gap: 0.25rem;
  height: 4px;
}

.password-strength-segment {
  flex: 1;
  background: #E5E7EB;
  border-radius: 2px;
  transition: all 0.3s ease;
}

.password-strength-segment--active {
  opacity: 1;
}

.password-strength-segment--weak {
  background: #EF4444;
}

.password-strength-segment--fair {
  background: #F59E0B;
}

.password-strength-segment--good {
  background: #3B82F6;
}

.password-strength-segment--strong {
  background: #10B981;
}

.password-strength-indicator {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 0.75rem;
  padding: 0.5rem 0.75rem;
  background: #F8FAFC;
  border-radius: 0.5rem;
  border: 1px solid #E2E8F0;
}

.password-strength-label {
  display: flex;
  align-items: center;
  gap: 0.375rem;
}

.password-strength-label .icon {
  width: 1rem;
  height: 1rem;
}

.password-strength-score {
  font-weight: 600;
  color: #64748B;
  font-size: 0.875rem;
}

.password-requirements {
  margin-bottom: 0.75rem;
}

.password-requirements-title {
  font-weight: 600;
  color: #374151;
  margin-bottom: 0.5rem;
  font-size: 0.875rem;
}

.password-requirements-list {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 0.375rem;
}

.password-requirement {
  display: flex;
  align-items: center;
  gap: 0.375rem;
  padding: 0.25rem 0;
  transition: all 0.2s ease;
}

.password-requirement-icon {
  width: 0.875rem;
  height: 0.875rem;
  flex-shrink: 0;
}

.password-requirement--met .password-requirement-icon {
  color: #10B981;
}

.password-requirement--unmet .password-requirement-icon {
  color: #EF4444;
}

.password-requirement-text {
  font-size: 0.8125rem;
  line-height: 1.4;
}

.password-requirement--met .password-requirement-text {
  color: #065F46;
  font-weight: 500;
}

.password-requirement--unmet .password-requirement-text {
  color: #7F1D1D;
}

.password-tips {
  background: #FEF3C7;
  border: 1px solid #F59E0B;
  border-radius: 0.5rem;
  padding: 0.75rem;
}

.password-tips-title {
  display: flex;
  align-items: center;
  gap: 0.375rem;
  font-weight: 600;
  color: #92400E;
  margin-bottom: 0.5rem;
  font-size: 0.875rem;
}

.password-tips-icon {
  width: 1rem;
  height: 1rem;
  color: #F59E0B;
}

.password-tips-list {
  list-style: none;
  padding: 0;
  margin: 0;
  display: flex;
  flex-direction: column;
  gap: 0.25rem;
}

.password-tips-list li {
  font-size: 0.8125rem;
  color: #78350F;
  line-height: 1.4;
  position: relative;
  padding-left: 1rem;
}

.password-tips-list li::before {
  content: '•';
  position: absolute;
  left: 0;
  color: #F59E0B;
  font-weight: bold;
}

/* Responsive design */
@media (max-width: 640px) {
  .password-requirements-list {
    grid-template-columns: 1fr;
  }
  
  .password-strength-indicator {
    flex-direction: column;
    align-items: flex-start;
    gap: 0.5rem;
  }
}

/* Animation for requirement changes */
.password-requirement {
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.password-requirement--met {
  transform: scale(1.02);
}

/* Focus and hover effects */
.password-strength-segment--active {
  box-shadow: 0 0 0 1px rgba(255, 255, 255, 0.5);
}

/* Dark mode support (optional) */
@media (prefers-color-scheme: dark) {
  .password-strength-indicator {
    background: #1E293B;
    border-color: #334155;
  }
  
  .password-tips {
    background: rgba(245, 158, 11, 0.1);
    border-color: rgba(245, 158, 11, 0.3);
  }
  
  .password-requirements-title {
    color: #F1F5F9;
  }
  
  .password-strength-score {
    color: #CBD5E1;
  }
}
</style> 