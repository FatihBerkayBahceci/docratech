<!--
Proje: Enterprise Sağlık Platformu
Bileşen: Advanced Mobile Menu
Developer: DocraTech Team - Fatih Berkay Bahceci
Açıklama: Marka uyumlu, profesyonel animasyonlu ve kullanıcı dostu mobil menü bileşeni.
-->
<template>
  <Teleport to="body">
    <Transition name="overlay-fade">
      <div v-if="isOpen" class="fixed inset-0 z-[9999] bg-black bg-opacity-50 backdrop-blur-sm" @click="close"></div>
    </Transition>

    <Transition name="menu-slide">
      <div v-if="isOpen" class="fixed inset-y-0 right-0 z-[10000] w-80 bg-[#181124] shadow-2xl overflow-y-auto flex flex-col">
        <div class="flex justify-between items-center p-4 border-b border-[#6D488D]">
          <div class="flex items-center gap-2">
            <img v-if="logo" :src="logo" :alt="logoAlt" class="h-8 w-auto rounded" />
            <span class="text-white font-semibold text-lg">{{ title }}</span>
          </div>
          <button @click="close" class="text-[#9D38CF] hover:text-white transition duration-300">
            <Icon name="x" size="lg" />
          </button>
        </div>

        <nav class="flex-1 px-4 py-6">
          <div v-for="section in sections" :key="section.id" class="mb-6">
            <h3 class="text-xs uppercase tracking-wide font-semibold text-[#9D38CF] mb-3">{{ section.title }}</h3>
            <div class="flex flex-col gap-2">
              <a
                v-for="item in section.items"
                :key="item.id"
                :href="item.href"
                class="flex items-center gap-3 px-3 py-2 rounded-xl transition duration-300"
                :class="item.active ? 'bg-gradient-to-r from-[#5A1188] to-[#9D38CF] text-white shadow-lg' : 'text-gray-300 hover:bg-[#5A1188] hover:text-white'"
                @click="handleItemClick(item)"
              >
                <Icon :name="item.icon" size="md" />
                <span class="flex-1">{{ item.label }}</span>
                <Icon v-if="item.external" name="external-link" size="sm" />
              </a>
            </div>
          </div>
        </nav>

        <div v-if="$slots.footer" class="p-4 border-t border-[#6D488D] bg-[#2A183D]">
          <slot name="footer" />
        </div>
      </div>
    </Transition>
  </Teleport>
</template>

<script setup>
import { computed } from 'vue'
import Icon from './Icon.vue'

const props = defineProps({
  modelValue: Boolean,
  logo: String,
  logoAlt: { type: String, default: 'Logo' },
  title: String,
  sections: { type: Array, default: () => [] }
})

const emit = defineEmits(['update:modelValue', 'item-click'])

const isOpen = computed({
  get: () => props.modelValue,
  set: (value) => emit('update:modelValue', value)
})

const close = () => { isOpen.value = false }

const handleItemClick = (item) => {
  emit('item-click', item)
  if (item.closeOnClick !== false) close()
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

.menu-slide-enter-active,
.menu-slide-leave-active {
  transition: transform 0.3s ease;
}
.menu-slide-enter-from,
.menu-slide-leave-to {
  transform: translateX(100%);
}

::-webkit-scrollbar {
  width: 6px;
}
::-webkit-scrollbar-track {
  background: transparent;
}
::-webkit-scrollbar-thumb {
  background: #6D488D;
  border-radius: 3px;
}
::-webkit-scrollbar-thumb:hover {
  background: #9D38CF;
}

@media (max-width: 480px) {
  .w-80 {
    width: 100vw;
  }
}
</style>