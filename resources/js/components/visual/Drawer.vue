<!--
Proje: Enterprise Sağlık Platformu
Bileşen: Advanced Drawer
Developer: DocraTech Team - Fatih Berkay Bahceci
Açıklama: Marka uyumlu, animasyonlu, gelişmiş Drawer bileşeni.
-->
<template>
  <Teleport to="body">
    <Transition name="overlay-fade" appear>
      <div v-if="visible" class="fixed inset-0 z-50 flex justify-end bg-black bg-opacity-60 backdrop-blur-sm" @click="handleOverlayClick">
        <Transition :name="`slide-${placement}`" appear>
          <div
            v-if="visible"
            class="relative bg-[#181124] shadow-xl overflow-hidden flex flex-col"
            :class="[drawerSize, drawerPosition]"
            @click.stop
          >
            <div class="flex items-center justify-between p-4 border-b border-[#6D488D]">
              <div class="flex items-center gap-2">
                <Icon v-if="icon" :name="icon" class="text-[#9D38CF]" size="lg" />
                <h3 class="text-xl font-semibold text-white">{{ title }}</h3>
              </div>
              <button v-if="dismissible" @click="handleClose" class="text-[#9D38CF] hover:text-white transition duration-300">
                <Icon name="x" size="lg" />
              </button>
            </div>

            <div class="flex-1 overflow-y-auto p-4">
              <slot />
            </div>

            <div v-if="$slots.footer" class="p-4 border-t border-[#6D488D] bg-[#2A183D]">
              <slot name="footer" />
            </div>
          </div>
        </Transition>
      </div>
    </Transition>
  </Teleport>
</template>

<script setup>
import { computed, watch } from 'vue'
import Icon from './Icon.vue'

const props = defineProps({
  visible: Boolean,
  placement: { type: String, default: 'right' },
  size: { type: String, default: 'md' },
  title: String,
  icon: String,
  dismissible: { type: Boolean, default: true },
  closeOnOverlay: { type: Boolean, default: true }
})

const emit = defineEmits(['close', 'update:visible'])

watch(() => props.visible, (val) => {
  document.body.style.overflow = val ? 'hidden' : ''
})

const drawerSize = computed(() => ({
  sm: 'w-64',
  md: 'w-96',
  lg: 'w-[30rem]',
  xl: 'w-[40rem]',
  full: 'w-full'
}[props.size]))

const drawerPosition = computed(() => ({
  left: 'h-full left-0',
  right: 'h-full right-0',
  top: 'w-full top-0',
  bottom: 'w-full bottom-0'
}[props.placement]))

const handleClose = () => {
  emit('close')
  emit('update:visible', false)
}

const handleOverlayClick = () => {
  if (props.closeOnOverlay) handleClose()
}
</script>

<style scoped>
.overlay-fade-enter-active,
.overlay-fade-leave-active {
  transition: opacity 0.3s ease;
}
.overlay-fade-enter-from,
.overlay-fade-leave-to {
  opacity: 0;
}

.slide-right-enter-active,
.slide-right-leave-active,
.slide-left-enter-active,
.slide-left-leave-active {
  transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.slide-right-enter-from,
.slide-right-leave-to {
  transform: translateX(100%);
}

.slide-left-enter-from,
.slide-left-leave-to {
  transform: translateX(-100%);
}

.slide-top-enter-active,
.slide-top-leave-active,
.slide-bottom-enter-active,
.slide-bottom-leave-active {
  transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.slide-top-enter-from,
.slide-top-leave-to {
  transform: translateY(-100%);
}

.slide-bottom-enter-from,
.slide-bottom-leave-to {
  transform: translateY(100%);
}
</style>