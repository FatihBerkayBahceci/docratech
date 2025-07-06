<!--
  Project: Enterprise Settings Panel
  Developer: DocraTech Team - Fatih Berkay Bahceci
-->

<template>
  <Drawer
    v-model:open="isOpen"
    :position="position"
    :size="size"
    :overlay="overlay"
    :animated="animated"
    class="bg-[#181124] text-white rounded-l-2xl shadow-2xl max-w-full"
  >
    <!-- Panel Header -->
    <template #header>
      <div class="flex items-center justify-between px-6 py-5 border-b border-[#2A183D]">
        <h2 class="flex items-center gap-2 text-xl font-semibold text-white">
          <Icon name="settings" size="lg" class="text-[#9D38CF]" />
          {{ title }}
        </h2>
        <button
          class="w-9 h-9 flex items-center justify-center rounded-lg hover:bg-[#2A183D] transition-colors"
          @click="close"
          aria-label="Ayar panelini kapat"
        >
          <Icon name="x" size="md" class="text-gray-400 hover:text-white" />
        </button>
      </div>
    </template>

    <!-- Panel Content -->
    <div class="flex-1 overflow-y-auto px-6 py-4 space-y-8 scrollbar-thin scrollbar-thumb-[#2A183D]">
      <div
        v-for="section in sections"
        :key="section.id"
        class="space-y-3"
      >
        <h3 class="flex items-center gap-2 text-base font-semibold text-white border-b border-[#2A183D] pb-2">
          <Icon v-if="section.icon" :name="section.icon" size="md" class="text-[#9D38CF]" />
          {{ section.title }}
        </h3>
        <div class="pl-4">
          <slot :name="section.id" :section="section">
            <div v-if="section.component">
              <component :is="section.component" v-bind="section.props || {}" />
            </div>
          </slot>
        </div>
      </div>
    </div>

    <!-- Panel Footer -->
    <template #footer>
      <div class="px-6 py-4 border-t border-[#2A183D] bg-[#181124]">
        <div class="flex flex-col sm:flex-row gap-3 justify-end">
          <AppButton
            variant="secondary"
            size="md"
            @click="resetSettings"
            :disabled="!hasChanges"
          >
            <Icon name="refresh" size="sm" />
            Sıfırla
          </AppButton>
          <AppButton
            variant="primary"
            size="md"
            @click="saveSettings"
            :loading="saving"
            :disabled="!hasChanges"
          >
            <Icon name="save" size="sm" />
            Kaydet
          </AppButton>
        </div>
      </div>
    </template>
  </Drawer>
</template>

<script setup>
import { computed, ref, watch } from 'vue'
import Drawer from './Drawer.vue'
import Icon from './Icon.vue'
import AppButton from '../button/AppButton.vue'

const props = defineProps({
  modelValue: { type: Boolean, default: false },
  title: { type: String, default: 'Ayarlar' },
  sections: { type: Array, default: () => [] },
  position: {
    type: String,
    default: 'right',
    validator: (val) => ['left', 'right'].includes(val)
  },
  size: {
    type: String,
    default: 'md',
    validator: (val) => ['sm', 'md', 'lg', 'xl'].includes(val)
  },
  overlay: { type: Boolean, default: true },
  animated: { type: Boolean, default: true },
  autoSave: { type: Boolean, default: false },
  description: { type: String, default: '' },
  showActions: { type: Boolean, default: true }
})

const emit = defineEmits(['update:modelValue', 'save', 'reset', 'close'])

const isOpen = computed({
  get: () => props.modelValue,
  set: (val) => emit('update:modelValue', val)
})

const saving = ref(false)
const hasChanges = ref(false)

const close = () => {
  emit('close')
  isOpen.value = false
}

const saveSettings = async () => {
  saving.value = true
  try {
    await emit('save')
    hasChanges.value = false
    if (props.autoSave) close()
  } catch (err) {
    console.error('Ayarlar kaydedilemedi:', err)
  } finally {
    saving.value = false
  }
}

const resetSettings = () => {
  emit('reset')
  hasChanges.value = false
}

// Değişiklik takibi
watch(() => props.sections, () => {
  hasChanges.value = true
}, { deep: true })
</script>

<style scoped>
/* Özel scrollbar */
.scrollbar-thin::-webkit-scrollbar {
  width: 6px;
}
.scrollbar-thin::-webkit-scrollbar-track {
  background: transparent;
}
.scrollbar-thin::-webkit-scrollbar-thumb {
  background-color: #2A183D;
  border-radius: 3px;
}
</style>
