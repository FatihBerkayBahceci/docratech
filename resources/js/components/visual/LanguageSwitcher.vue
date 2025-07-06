<!--
Proje: Enterprise Sağlık Platformu
Bileşen: Advanced Language Switcher
Developer: DocraTech Team - Fatih Berkay Bahceci
Açıklama: Marka uyumlu, mikro animasyonlu, gelişmiş dil değiştirme bileşeni.
-->
<template>
  <div class="relative" ref="switcherRef">
    <button
      @click="toggleDropdown"
      :aria-expanded="isOpen"
      class="flex items-center gap-2 px-3 py-2 bg-docratech-accent rounded-xl border border-docratech-secondary hover:border-docratech-primaryLight transition duration-300 cursor-pointer"
    >
      <img v-if="currentLanguage?.flag" :src="currentLanguage.flag" class="w-6 h-6 rounded" :alt="currentLanguage.name" />
      <span v-if="showName" class="text-sm font-medium text-white">{{ currentLanguage.code.toUpperCase() }}</span>
      <Icon name="chevron-down" class="text-[#9D38CF] transition-transform duration-300" :class="{'rotate-180': isOpen}" />
    </button>

    <Transition name="fade-scale">
      <div v-if="isOpen" class="absolute right-0 mt-2 w-64 bg-[#181124] border border-[#6D488D] rounded-xl shadow-xl overflow-hidden z-50">
        <div class="px-4 py-3 border-b border-[#6D488D]">
          <span class="text-sm font-semibold text-white">Dil Seçimi</span>
        </div>
        <div class="max-h-64 overflow-y-auto">
          <button
            v-for="language in languages"
            :key="language.code"
            @click="selectLanguage(language)"
            class="flex items-center w-full px-4 py-2 hover:bg-[#2A183D] transition duration-300"
            :class="{'bg-[#2A183D]': language.code === currentLanguage.code}"
          >
            <img v-if="language.flag" :src="language.flag" :alt="language.name" class="w-6 h-6 rounded mr-3" />
            <div class="flex-1 text-left">
              <span class="block text-sm text-white font-medium">{{ language.name }}</span>
              <span class="block text-xs text-gray-400">{{ language.nativeName }}</span>
            </div>
            <Icon v-if="language.code === currentLanguage.code" name="check" class="text-[#9D38CF]" />
          </button>
        </div>
      </div>
    </Transition>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import Icon from './Icon.vue'

const props = defineProps({
  languages: { type: Array, default: () => ([
    { code: 'tr', name: 'Türkçe', nativeName: 'Türkçe', flag: null },
    { code: 'en', name: 'English', nativeName: 'English', flag: null },
    { code: 'de', name: 'Deutsch', nativeName: 'Deutsch', flag: null }
  ]) },
  currentLanguageCode: { type: String, default: 'tr' },
  showName: { type: Boolean, default: true }
})

const emit = defineEmits(['language-change'])

const isOpen = ref(false)
const switcherRef = ref(null)

const currentLanguage = computed(() => props.languages.find(l => l.code === props.currentLanguageCode))

const toggleDropdown = () => { isOpen.value = !isOpen.value }

const selectLanguage = (lang) => {
  emit('language-change', lang)
  isOpen.value = false
}

const handleClickOutside = (e) => {
  if (!switcherRef.value.contains(e.target)) isOpen.value = false
}

onMounted(() => document.addEventListener('click', handleClickOutside))
onUnmounted(() => document.removeEventListener('click', handleClickOutside))
</script>

<style scoped>
.fade-scale-enter-active, .fade-scale-leave-active {
  transition: all 0.2s ease;
}

.fade-scale-enter-from, .fade-scale-leave-to {
  opacity: 0;
  transform: scale(0.95);
}
</style>
