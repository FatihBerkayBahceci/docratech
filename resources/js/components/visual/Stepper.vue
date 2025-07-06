<!-- Developer: DocraTech Team - Fatih Berkay Bahceci -->
<template>
  <div :class="['w-full', variant === 'vertical' ? 'flex flex-col' : '']">
    <!-- Header -->
    <div :class="['flex', variant === 'vertical' ? 'flex-col items-start' : 'items-center', 'mb-6 gap-6']">
      <div
        v-for="(step, index) in steps"
        :key="index"
        class="relative flex items-center group"
        :class="variant === 'vertical' ? 'flex-col items-start' : 'flex-1'"
      >
        <!-- Step Indicator -->
        <div
          @click="goToStep(index)"
          :class="[
            'w-10 h-10 rounded-full flex items-center justify-center font-semibold text-sm z-10 transition-all duration-300 cursor-pointer',
            isStepCompleted(index) ? 'bg-green-500 text-white scale-110' :
            isStepActive(index) ? 'bg-gradient-to-r from-[#5A1188] to-[#9D38CF] text-white ring-4 ring-[#5A118880]' :
            'bg-gray-700 text-gray-300 hover:scale-105'
          ]"
        >
          <Icon v-if="step.icon && isStepCompleted(index)" :name="step.icon" size="sm" class="text-white" />
          <span v-else>{{ index + 1 }}</span>
        </div>

        <!-- Step Label -->
        <div class="ml-4" :class="variant === 'vertical' ? 'mt-2 ml-0' : ''">
          <h3 class="text-sm font-semibold text-white">{{ step.title }}</h3>
          <p v-if="step.description" class="text-xs text-gray-400 mt-1">{{ step.description }}</p>
        </div>

        <!-- Connector -->
        <div
          v-if="index < steps.length - 1"
          :class="[
            variant === 'vertical'
              ? 'w-0.5 h-8 ml-4 mt-2'
              : 'h-0.5 w-full absolute left-1/2 top-5',
            isStepCompleted(index) ? 'bg-green-500' : 'bg-gray-700'
          ]"
        />
      </div>
    </div>

    <!-- Step Content -->
    <Transition name="stepper-content" mode="out-in">
      <div :key="activeStep" class="bg-[#1a122b] p-6 rounded-2xl shadow-lg border border-[#2a183d]">
        <slot
          :name="`step-${activeStep}`"
          :step="steps[activeStep]"
          :stepIndex="activeStep"
          :isActive="true"
          :isCompleted="isStepCompleted(activeStep)"
          :isFirst="activeStep === 0"
          :isLast="activeStep === steps.length - 1"
          :goNext="goNext"
          :goPrevious="goPrevious"
          :goToStep="goToStep"
        />
      </div>
    </Transition>

    <!-- Actions -->
    <div v-if="showActions" class="flex justify-between items-center mt-6 pt-4 border-t border-[#2a183d]">
      <AppButton
        v-if="activeStep > 0"
        variant="secondary"
        size="medium"
        @click="goPrevious"
        :disabled="!canGoPrevious"
      >
        <Icon name="chevron-left" size="sm" class="mr-2" />
        Geri
      </AppButton>

      <div class="flex gap-2">
        <AppButton
          v-if="activeStep < steps.length - 1"
          variant="primary"
          size="medium"
          @click="goNext"
          :disabled="!canGoNext"
        >
          İleri
          <Icon name="chevron-right" size="sm" class="ml-2" />
        </AppButton>

        <AppButton
          v-if="activeStep === steps.length - 1"
          variant="success"
          size="medium"
          @click="complete"
          :disabled="!canComplete"
        >
          <Icon name="check" size="sm" class="mr-2" />
          Tamamla
        </AppButton>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue'
import Icon from './Icon.vue'
import AppButton from '../button/AppButton.vue'

const props = defineProps({
  steps: { type: Array, required: true },
  currentStep: { type: Number, default: 0 },
  completedSteps: { type: Array, default: () => [] },
  variant: { type: String, default: 'default' },
  animated: { type: Boolean, default: true },
  showActions: { type: Boolean, default: true },
  allowSkip: { type: Boolean, default: false }
})

const emit = defineEmits(['update:currentStep', 'step-change', 'complete', 'next', 'previous'])

const activeStep = ref(props.currentStep)

watch(() => props.currentStep, (val) => (activeStep.value = val))

const isStepCompleted = (index) => props.completedSteps.includes(index) || index < activeStep.value
const isStepActive = (index) => index === activeStep.value
const isStepDisabled = (index) => !props.allowSkip && index > activeStep.value && !isStepCompleted(index)

const canGoNext = computed(() => activeStep.value < props.steps.length - 1)
const canGoPrevious = computed(() => activeStep.value > 0)
const canComplete = computed(() => activeStep.value === props.steps.length - 1)

function goToStep(index) {
  if (isStepDisabled(index)) return
  activeStep.value = index
  emit('update:currentStep', index)
  emit('step-change', { step: index, stepData: props.steps[index] })
}

function goNext() {
  if (canGoNext.value) {
    goToStep(activeStep.value + 1)
    emit('next', { step: activeStep.value, stepData: props.steps[activeStep.value] })
  }
}

function goPrevious() {
  if (canGoPrevious.value) {
    goToStep(activeStep.value - 1)
    emit('previous', { step: activeStep.value, stepData: props.steps[activeStep.value] })
  }
}

function complete() {
  emit('complete', { step: activeStep.value, stepData: props.steps[activeStep.value] })
}
</script>

<style scoped>
.stepper-content-enter-active,
.stepper-content-leave-active {
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}
.stepper-content-enter-from {
  opacity: 0;
  transform: translateY(10px);
}
.stepper-content-leave-to {
  opacity: 0;
  transform: translateY(-10px);
}
</style>
